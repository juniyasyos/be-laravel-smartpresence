<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Models\MeetingParticipant;
use App\Models\Attendance;
use App\Models\Employee;
use App\Http\Requests\StoreMeetingRequest;
use App\Http\Requests\UpdateMeetingRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Exception;

class MeetingController extends Controller
{
    /**
     * Daftar rapat dengan search & filter.
     *
     * Query params:
     *  - search   : cari berdasarkan judul rapat
     *  - status   : filter status (menunggu, berlangsung, selesai)
     *  - room_id  : filter ruang rapat
     *  - date     : filter tanggal (format: Y-m-d)
     *  - per_page : jumlah per halaman (default 10)
     */
    public function index(Request $request)
    {
        try {
            // Auto-update statuses before listing
            $this->autoUpdateStatuses();

            $query = Meeting::with(['room:id,name,location'])->withCount('participants');

            // Search berdasarkan judul
            if ($request->filled('search')) {
                $search = $request->query('search');
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('organizer', 'like', "%{$search}%");
                });
            }

            // Filter status
            if ($request->filled('status')) {
                $query->where('status', $request->query('status'));
            }

            // Filter ruang rapat
            if ($request->filled('room_id')) {
                $query->where('room_id', $request->query('room_id'));
            }

            // Filter tanggal
            if ($request->filled('date')) {
                $date = $request->query('date');
                $query->whereDate('start_time', $date);
            }

            $perPage = $request->query('per_page', 10);
            $result  = $query->latest('start_time')->paginate($perPage);

            // Append participant count
            $result->getCollection()->transform(function ($meeting) {
                $meeting->participant_count = $meeting->participants_count;
                return $meeting;
            });

            return response()->json([
                'message' => 'Meetings fetched successfully',
                'data'    => $result,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while fetching meetings',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Buat rapat baru dengan peserta.
     */
    public function store(StoreMeetingRequest $request)
    {
        try {
            $validated = $request->validated();

            // Create the meeting
            $meeting = Meeting::create([
                'title'      => $validated['title'],
                'organizer'  => $validated['organizer'] ?? null,
                'room_id'    => $validated['room_id'],
                'start_time' => $validated['start_time'],
                'end_time'   => $validated['end_time'],
                'status'     => 'menunggu',
                'created_by' => auth()->id() ?? 1,
            ]);

            // Collect participant employee IDs
            $employeeIds = $this->resolveParticipantIds($validated);

            // Create participants in bulk
            $participants = array_map(function ($employeeId) use ($meeting) {
                return [
                    'meeting_id'  => $meeting->id,
                    'employee_id' => $employeeId,
                    'created_at'  => now(),
                ];
            }, $employeeIds);

            MeetingParticipant::insert($participants);

            $meeting->load(['room', 'participants.employee.workUnit']);
            $meeting->participant_count = $meeting->participants->count();



            return response()->json([
                'message' => 'Meeting created successfully',
                'data'    => $meeting,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while creating meeting',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Detail rapat dengan peserta dan status kehadiran.
     */
    public function show(Request $request, string $id)
    {
        try {
            // Auto-update status for this meeting
            $this->autoUpdateStatuses($id);

            $meeting = Meeting::with([
                'room:id,name,location',
                'participants.employee:id,full_name,nip,work_unit_id',
                'participants.employee.workUnit:id,work_unit',
                'attendances:id,meeting_id,employee_id,check_in_time,status',
            ])->find($id);

            if (!$meeting) {
                return response()->json([
                    'message' => 'Meeting not found',
                ], 404);
            }

            // Create a hash map for attendances O(1) lookup
            $attendancesMap = $meeting->attendances->keyBy('employee_id');

            // Build participant list with attendance status
            $participantsWithAttendance = $meeting->participants->map(function ($participant) use ($attendancesMap) {
                $attendance = $attendancesMap->get($participant->employee_id);

                return [
                    'id'            => $participant->id,
                    'employee_id'   => $participant->employee_id,
                    'employee'      => $participant->employee,
                    'status'        => $attendance ? 'hadir' : 'tidak_hadir',
                    'check_in_time' => $attendance?->check_in_time,
                ];
            });

            $attendanceSummary = [
                'total'       => $participantsWithAttendance->count(),
                'hadir'       => $participantsWithAttendance->where('status', 'hadir')->count(),
                'tidak_hadir' => $participantsWithAttendance->where('status', 'tidak_hadir')->count(),
            ];

            // Filter peserta berdasarkan nama (setelah hitung summary agar summary tetap semua data)
            if ($request->filled('search')) {
                $search = strtolower($request->query('search'));
                $participantsWithAttendance = $participantsWithAttendance->filter(function ($item) use ($search) {
                    $name = $item['employee']->full_name ?? '';
                    return str_contains(strtolower($name), $search);
                });
            }

            $responseData = [
                'meeting'                    => $meeting,
                'participants_with_attendance' => $participantsWithAttendance->values(),
                'attendance_summary'          => $attendanceSummary,
            ];

            return response()->json([
                'message' => 'Meeting fetched successfully',
                'data'    => $responseData,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while fetching meeting',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update rapat.
     */
    public function update(UpdateMeetingRequest $request, string $id)
    {
        try {
            $meeting = Meeting::find($id);
            if (!$meeting) {
                return response()->json([
                    'message' => 'Meeting not found',
                ], 404);
            }

            $validated = $request->validated();

            // Update meeting fields
            $meeting->update([
                'title'      => $validated['title'] ?? $meeting->title,
                'organizer'  => array_key_exists('organizer', $validated) ? $validated['organizer'] : $meeting->organizer,
                'room_id'    => $validated['room_id'] ?? $meeting->room_id,
                'start_time' => $validated['start_time'] ?? $meeting->start_time,
                'end_time'   => $validated['end_time'] ?? $meeting->end_time,
            ]);

            // Re-evaluate the status after times are updated
            $this->autoUpdateStatuses($meeting->id);

            // Fetch fresh status for the rest of the flow
            $meeting->refresh();

            // Update participants if provided and not ongoing
            $hasParticipantChanges = isset($validated['participant_employee_ids']) || isset($validated['participant_work_unit_ids']);
            if ($hasParticipantChanges && $meeting->status !== 'berlangsung') {
                $employeeIds = $this->resolveParticipantIds($validated);

                // Remove old participants
                $meeting->participants()->delete();

                // Re-create participants in bulk
                $participants = array_map(function ($employeeId) use ($meeting) {
                    return [
                        'meeting_id'  => $meeting->id,
                        'employee_id' => $employeeId,
                        'created_at'  => now(),
                    ];
                }, $employeeIds);

                MeetingParticipant::insert($participants);
            }

            $meeting->load(['room', 'participants.employee.workUnit']);
            $meeting->participant_count = $meeting->participants->count();



            return response()->json([
                'message' => 'Meeting updated successfully',
                'data'    => $meeting,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while updating meeting',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Hapus rapat.
     */
    public function destroy(string $id)
    {
        try {
            $meeting = Meeting::find($id);
            if (!$meeting) {
                return response()->json([
                    'message' => 'Meeting not found',
                ], 404);
            }

            // Delete related data
            $meeting->attendances()->delete();
            $meeting->participants()->delete();
            $meeting->delete();



            return response()->json([
                'message' => 'Meeting deleted successfully',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while deleting meeting',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Scan barcode (NIP) untuk absensi.
     * Frontend mengirim NIP dari USB barcode scanner.
     */
    public function scanBarcode(Request $request, string $id)
    {
        try {
            $request->validate([
                'nip' => 'required|string',
            ]);

            $meeting = Meeting::find($id);
            if (!$meeting) {
                return response()->json([
                    'message' => 'Meeting not found',
                ], 404);
            }

            // Find employee by NIP
            $employee = Employee::where('nip', $request->nip)->first();
            if (!$employee) {
                return response()->json([
                    'message' => 'Karyawan dengan NIP tersebut tidak ditemukan',
                ], 404);
            }

            // Check if employee is a participant
            $participant = MeetingParticipant::where('meeting_id', $id)
                ->where('employee_id', $employee->id)
                ->first();

            if (!$participant) {
                return response()->json([
                    'message' => 'Karyawan bukan peserta rapat ini',
                ], 422);
            }

            // Check if already attended
            $existingAttendance = Attendance::where('meeting_id', $id)
                ->where('employee_id', $employee->id)
                ->first();

            if ($existingAttendance) {
                return response()->json([
                    'message' => 'Karyawan sudah diabsenkan sebelumnya',
                    'data'    => [
                        'employee'   => $employee->load(['workUnit']),
                        'attendance' => $existingAttendance,
                    ],
                ], 200);
            }

            // Create attendance record
            $attendance = Attendance::create([
                'meeting_id'    => $id,
                'employee_id'   => $employee->id,
                'check_in_time' => Carbon::now(),
                'status'        => 'hadir',
                'verified_by'   => auth()->id() ?? null,
            ]);

            $employee->load(['workUnit']);



            return response()->json([
                'message' => 'Absensi berhasil dicatat',
                'data'    => [
                    'employee'   => $employee,
                    'attendance' => $attendance,
                ],
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while processing barcode scan',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Manual attendance: ubah tidak hadir → hadir.
     */
    public function manualAttendance(Request $request, string $id, string $participantId)
    {
        try {
            $meeting = Meeting::find($id);
            if (!$meeting) {
                return response()->json([
                    'message' => 'Meeting not found',
                ], 404);
            }

            $participant = MeetingParticipant::where('meeting_id', $id)
                ->where('id', $participantId)
                ->first();

            if (!$participant) {
                return response()->json([
                    'message' => 'Peserta tidak ditemukan di rapat ini',
                ], 404);
            }

            // Check if already attended
            $existingAttendance = Attendance::where('meeting_id', $id)
                ->where('employee_id', $participant->employee_id)
                ->first();

            $status = $request->input('status', 'hadir');
            
            // Handle if status passed as boolean
            if (is_bool($status)) {
                $status = $status ? 'hadir' : 'tidak_hadir';
            }

            if ($status === 'tidak_hadir') {
                if ($existingAttendance) {
                    $existingAttendance->delete();
                }
                
                $participant->load(['employee.workUnit']);
                

                
                return response()->json([
                    'message' => 'Status kehadiran berhasil diubah menjadi tidak hadir',
                    'data'    => [
                        'participant' => $participant,
                        'attendance'  => null,
                    ],
                ], 200);
            }

            if ($existingAttendance) {
                return response()->json([
                    'message' => 'Karyawan sudah diabsenkan sebelumnya',
                    'data'    => $existingAttendance,
                ], 200);
            }

            // Create attendance record
            $attendance = Attendance::create([
                'meeting_id'    => $id,
                'employee_id'   => $participant->employee_id,
                'check_in_time' => Carbon::now(),
                'status'        => 'hadir',
                'verified_by'   => auth()->id() ?? null,
                'notes'         => 'Manual attendance',
            ]);

            $participant->load(['employee.workUnit']);



            return response()->json([
                'message' => 'Absensi manual berhasil dicatat',
                'data'    => [
                    'participant' => $participant,
                    'attendance'  => $attendance,
                ],
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while processing manual attendance',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Batch update statuses — dipanggil dari route atau internal.
     */
    public function updateStatuses()
    {
        try {
            $this->autoUpdateStatuses();

            return response()->json([
                'message' => 'Meeting statuses updated successfully',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while updating meeting statuses',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Resolve participant IDs from work_unit_ids and employee_ids.
     */
    private function resolveParticipantIds(array $validated): array
    {
        $employeeIds = [];

        // From work units (divisions)
        if (!empty($validated['participant_work_unit_ids'])) {
            $fromUnits = Employee::whereIn('work_unit_id', $validated['participant_work_unit_ids'])
                ->where('is_active', true)
                ->pluck('id')
                ->toArray();
            $employeeIds = array_merge($employeeIds, $fromUnits);
        }

        // From individual employee IDs
        if (!empty($validated['participant_employee_ids'])) {
            $employeeIds = array_merge($employeeIds, $validated['participant_employee_ids']);
        }

        // Remove duplicates
        return array_unique($employeeIds);
    }

    /**
     * Auto-update meeting statuses based on current time.
     * - menunggu:     now < start_time
     * - berlangsung:  start_time <= now <= end_time
     * - selesai:      now > end_time
     */
    private function autoUpdateStatuses(?string $meetingId = null)
    {
        $now = Carbon::now();

        if ($meetingId) {
            $meeting = Meeting::where('status', '!=', 'dibatalkan')->find($meetingId);
            if ($meeting) {
                $newStatus = $meeting->status;
                if ($now->greaterThan($meeting->end_time)) {
                    $newStatus = 'selesai';
                } elseif ($now->greaterThanOrEqualTo($meeting->start_time) && $now->lessThanOrEqualTo($meeting->end_time)) {
                    $newStatus = 'berlangsung';
                } else {
                    $newStatus = 'menunggu';
                }

                if ($newStatus !== $meeting->status) {
                    $meeting->update(['status' => $newStatus]);
                }
            }
            return;
        }

        // Use a simple cache lock for batch updates
        $lockKey = 'auto_update_batch_statuses_' . floor(time() / 60);
        if (Cache::has($lockKey)) {
            return;
        }

        // Batch update
        Meeting::where('status', '!=', 'dibatalkan')
            ->where('status', '!=', 'selesai')
            ->where('end_time', '<', $now)
            ->update(['status' => 'selesai']);

        Meeting::where('status', '!=', 'dibatalkan')
            ->where('status', '!=', 'berlangsung')
            ->where('start_time', '<=', $now)
            ->where('end_time', '>=', $now)
            ->update(['status' => 'berlangsung']);

        Meeting::where('status', '!=', 'dibatalkan')
            ->where('status', '!=', 'menunggu')
            ->where('start_time', '>', $now)
            ->update(['status' => 'menunggu']);

        Cache::put($lockKey, true, 60);
    }
}
