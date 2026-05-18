<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Models\MeetingDocument;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class LaporanController extends Controller
{
    // ─────────────────────────────────────────────────────────────────────────
    // DAFTAR LAPORAN RAPAT (untuk halaman Laporan Sekretaris)
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Daftar rapat dengan status kelengkapan lampiran.
     *
     * GET /laporan/rapat
     *
     * Query params:
     *  - search   : cari berdasarkan judul rapat atau nama ruangan
     *  - date     : filter tanggal (format: Y-m-d)
     *  - status   : filter status (menunggu, berlangsung, selesai, dibatalkan)
     *  - per_page : jumlah per halaman (default 10)
     *
     * Setiap item mengembalikan:
     *  - data rapat (id, title, organizer, room, start_time, end_time, status)
     *  - lampiran.has_undangan   : bool — ada dokumen type='undangan'
     *  - lampiran.has_notulensi  : bool — ada entry di meeting_minutes
     *  - lampiran.has_dokumentasi: bool — ada dokumen type selain 'undangan'
     */
    public function index(Request $request)
    {
        try {
            // Auto-update statuses sebelum menampilkan
            $this->autoUpdateStatuses();

            $query = Meeting::with(['room:id,name,location'])
                ->withExists([
                    'documents as has_undangan' => function ($q) {
                        $q->where('type', 'undangan');
                    },
                    'documents as has_dokumentasi' => function ($q) {
                        $q->where('type', '!=', 'undangan');
                    },
                    'minutes as has_notulensi'
                ]);

            // Filter: search judul atau nama ruangan
            if ($request->filled('search')) {
                $search = $request->query('search');
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('organizer', 'like', "%{$search}%")
                      ->orWhereHas('room', function ($r) use ($search) {
                          $r->where('name', 'like', "%{$search}%");
                      });
                });
            }

            // Filter: tanggal
            if ($request->filled('date')) {
                $query->whereDate('start_time', $request->query('date'));
            }

            // Filter: status
            if ($request->filled('status')) {
                $query->where('status', $request->query('status'));
            }

            $perPage = (int) $request->query('per_page', 15);
            $meetings = $query->latest('start_time')->paginate($perPage);

            // Transform: tambahkan info lampiran
            $meetings->getCollection()->transform(function ($meeting) {
                return [
                    'id'         => $meeting->id,
                    'title'      => $meeting->title,
                    'organizer'  => $meeting->organizer,
                    'start_time' => $meeting->start_time,
                    'end_time'   => $meeting->end_time,
                    'status'     => $meeting->status,
                    'room'       => $meeting->room,
                    'lampiran'   => [
                        'has_undangan'    => (bool) $meeting->has_undangan,
                        'has_notulensi'   => (bool) $meeting->has_notulensi,
                        'has_dokumentasi' => (bool) $meeting->has_dokumentasi,
                    ],
                ];
            });

            return response()->json([
                'message' => 'Laporan rapat berhasil diambil',
                'data'    => $meetings,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat mengambil laporan rapat',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // DETAIL LAPORAN SATU RAPAT (untuk aksi "View")
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Detail lengkap laporan satu rapat.
     *
     * GET /laporan/rapat/{id}
     *
     * Mengembalikan:
     *  - data rapat lengkap
     *  - peserta + kehadiran (summary)
     *  - notulensi (konten Quill)
     *  - semua dokumen dengan URL download
     */
    public function show(string $id)
    {
        try {
            $meeting = Meeting::select('id', 'title', 'organizer', 'start_time', 'end_time', 'status', 'room_id')
                ->with([
                    'room:id,name,location',
                    'minutes',
                    'documents',
                ])
                ->withCount([
                    'participants',
                    'attendances as hadir_count' => function ($q) {
                        $q->where('status', 'hadir');
                    }
                ])
                ->find($id);

            if (!$meeting) {
                return response()->json(['message' => 'Rapat tidak ditemukan'], 404);
            }

            // Summary kehadiran
            $totalPeserta  = $meeting->participants_count;
            $hadirCount    = $meeting->hadir_count;
            $tidakHadir    = max(0, $totalPeserta - $hadirCount);

            // Dokumen + URL publik
            $documents = $meeting->documents->map(function ($doc) {
                return [
                    'id'        => $doc->id,
                    'type'      => $doc->type,
                    'file_name' => $doc->file_name,
                    'file_size' => $doc->file_size,
                    'mime_type' => $doc->mime_type,
                    'url'       => asset('storage/' . $doc->file_path),
                    'created_at' => $doc->created_at,
                ];
            });

            $responseData = [
                'meeting'            => $meeting->only([
                    'id', 'title', 'organizer', 'start_time', 'end_time', 'status',
                ]),
                'room'               => $meeting->room,
                'attendance_summary' => [
                    'total'       => $totalPeserta,
                    'hadir'       => $hadirCount,
                    'tidak_hadir' => $tidakHadir,
                ],
                'notulensi'          => $meeting->minutes,
                'documents'          => $documents,
                'lampiran'           => [
                    'has_undangan'    => $documents->where('type', 'undangan')->isNotEmpty(),
                    'has_notulensi'   => $meeting->minutes !== null,
                    'has_dokumentasi' => $documents->whereNotIn('type', ['undangan'])->isNotEmpty(),
                ],
            ];



            return response()->json([
                'message' => 'Detail laporan rapat berhasil diambil',
                'data'    => $responseData,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat mengambil detail laporan',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // EXPORT DATA RAPAT (untuk aksi "Download" — JSON untuk FE generate PDF)
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Export data lengkap satu rapat (untuk FE generate PDF).
     *
     * GET /laporan/rapat/{id}/export
     *
     * Mengembalikan semua data yang diperlukan FE untuk membuat PDF:
     *  - info rapat, peserta + kehadiran, notulensi, dokumen
     */
    public function export(string $id)
    {
        try {
            $meeting = Meeting::with([
                'room:id,name,location',
                'participants.employee:id,full_name,nip,work_unit_id,signature_path',
                'participants.employee.workUnit:id,work_unit',
                'attendances:id,meeting_id,employee_id,check_in_time,status',
                'minutes',
                'documents',
            ])
            ->withCount([
                'participants',
                'attendances as hadir_count' => function($q) {
                    $q->where('status', 'hadir');
                }
            ])
            ->find($id);

            if (!$meeting) {
                return response()->json(['message' => 'Rapat tidak ditemukan'], 404);
            }

            // Hash Map untuk absensi O(1)
            $attendancesMap = $meeting->attendances->keyBy('employee_id');

            // Peserta + status kehadiran
            $peserta = $meeting->participants->map(function ($p) use ($attendancesMap) {
                $attendance = $attendancesMap->get($p->employee_id);

                return [
                    'nama'        => $p->employee?->full_name ?? 'Karyawan (Dihapus)',
                    'nip'         => $p->employee?->nip ?? '-',
                    'unit_kerja'  => $p->employee?->workUnit?->work_unit ?? '-',
                    'status'      => $attendance ? 'Hadir' : 'Tidak Hadir',
                    'check_in'    => $attendance?->check_in_time,
                    'signature_url' => $p->employee?->signature_url,
                ];
            });

            // Dokumen + URL
            $documents = $meeting->documents->map(function ($doc) {
                return [
                    'id'        => $doc->id,
                    'type'      => $doc->type,
                    'file_name' => $doc->file_name,
                    'mime_type' => $doc->mime_type,
                    'url'       => asset('storage/' . $doc->file_path),
                ];
            });

            $exportData = [
                'rapat'       => [
                    'id'         => $meeting->id,
                    'judul'      => $meeting->title,
                    'penyelenggara' => $meeting->organizer,
                    'ruangan'    => $meeting->room?->name,
                    'lokasi'     => $meeting->room?->location,
                    'tanggal'    => Carbon::parse($meeting->start_time)->translatedFormat('d F Y'),
                    'waktu_mulai'  => Carbon::parse($meeting->start_time)->format('H:i'),
                    'waktu_selesai' => Carbon::parse($meeting->end_time)->format('H:i'),
                    'status'     => $meeting->status,
                ],
                'ringkasan_kehadiran' => [
                    'total'       => $meeting->participants_count,
                    'hadir'       => $meeting->hadir_count,
                    'tidak_hadir' => max(0, $meeting->participants_count - $meeting->hadir_count),
                ],
                'peserta'     => $peserta,
                'notulensi'   => $meeting->minutes ? [
                    'content'          => $meeting->minutes->content,
                    'notulis_name'     => $meeting->minutes->notulis_name,
                    'notulis_position' => $meeting->minutes->notulis_position,
                    'director_name'     => $meeting->minutes->director_name,
                    'director_position' => $meeting->minutes->director_position,
                ] : null,
                'dokumen'     => $documents,
            ];



            return response()->json([
                'message' => 'Data export rapat berhasil diambil',
                'data'    => $exportData,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat export laporan',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // HELPER
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Auto-update semua status rapat berdasarkan waktu sekarang.
     */
    private function autoUpdateStatuses(): void
    {
        // Simple cache lock for batch updates (once per minute)
        $lockKey = 'auto_update_all_statuses_' . floor(time() / 60);
        if (Cache::has($lockKey)) {
            return;
        }

        $now = Carbon::now();

        // Update ke selesai
        Meeting::where('status', '!=', 'dibatalkan')
            ->where('status', '!=', 'selesai')
            ->where('end_time', '<', $now)
            ->update(['status' => 'selesai']);

        // Update ke berlangsung
        Meeting::where('status', '!=', 'dibatalkan')
            ->where('status', '!=', 'berlangsung')
            ->where('start_time', '<=', $now)
            ->where('end_time', '>=', $now)
            ->update(['status' => 'berlangsung']);

        // Update ke menunggu
        Meeting::where('status', '!=', 'dibatalkan')
            ->where('status', '!=', 'menunggu')
            ->where('start_time', '>', $now)
            ->update(['status' => 'menunggu']);

        Cache::put($lockKey, true, 60);
    }
}
