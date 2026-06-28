<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Meeting;
use App\Models\MeetingRoom;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    /**
     * Get dashboard data including summary statistics,
     * today's meetings, and room usage timeline.
     */
    public function index(Request $request)
    {
        try {
            $requestedDate = $request->query('date', Carbon::today()->toDateString());
            $todayDate = Carbon::today()->toDateString();
            $isToday = $requestedDate === $todayDate;

            $cacheKey = 'dashboard_data_' . $requestedDate . ($isToday ? '_' . floor(time() / 60) : '');

            $data = Cache::remember($cacheKey, 60, function () use ($requestedDate, $todayDate, $isToday) {
                // Auto-update meeting statuses
                $this->autoUpdateStatuses($todayDate);
                if (!$isToday) {
                    $this->autoUpdateStatuses($requestedDate);
                }

                // Statistik - Always use today
                $totalEmployees = Employee::where('is_active', true)->count();
                $meetingsStats = Meeting::whereDate('start_time', $todayDate)
                    ->selectRaw('count(*) as total')
                    ->selectRaw("count(case when status = 'menunggu' then 1 end) as pending")
                    ->selectRaw("count(case when status = 'selesai' then 1 end) as completed")
                    ->first();

                $meetingsToday = $meetingsStats->total ?? 0;
                $meetingsPending = $meetingsStats->pending ?? 0;
                $meetingsCompleted = $meetingsStats->completed ?? 0;

                // Rapat hari ini dengan detail
                $todaysMeetings = Meeting::whereDate('start_time', $todayDate)
                    ->select('id', 'title', 'start_time', 'end_time', 'status', 'room_id', 'created_by', 'organizer')
                    ->with([
                        'room:id,name,location',
                        'creator:id,name'
                    ])
                    ->withCount([
                        'participants',
                        'attendances as attendance_present' => function ($query) {
                            $query->where('status', 'hadir');
                        }
                    ])
                    ->orderBy('start_time', 'asc')
                    ->get()
                    ->map(function ($meeting) {
                        $participantsCount = $meeting->participants_count;
                        $attendancePresent = $meeting->attendance_present;
                        $attendanceAbsent = max(0, $participantsCount - $attendancePresent);

                        return [
                            'id' => $meeting->id,
                            'title' => $meeting->title,
                            'start_time' => $meeting->start_time->toIso8601String(),
                            'end_time' => $meeting->end_time->toIso8601String(),
                            'status' => $meeting->status,
                            'room' => $meeting->room,
                            'creator' => $meeting->creator,
                            'participants_count' => $participantsCount,
                            'attendance_present' => $attendancePresent,
                            'attendance_absent' => $attendanceAbsent,
                        ];
                    });

                // Penggunaan ruang
                $roomUsage = MeetingRoom::where('is_active', true)
                    ->select('id', 'name')
                    ->with(['meetings' => function ($query) use ($requestedDate) {
                        $query->whereDate('start_time', $requestedDate)
                            ->select('id', 'title', 'room_id', 'start_time', 'end_time', 'status')
                            ->orderBy('start_time', 'asc');
                    }])
                    ->get()
                    ->map(function ($room) {
                        return [
                            'id' => $room->id,
                            'name' => $room->name,
                            'meetings' => $room->meetings->map(function ($meeting) {
                                return [
                                    'id' => $meeting->id,
                                    'title' => $meeting->title,
                                    'start_time' => $meeting->start_time->toIso8601String(),
                                    'end_time' => $meeting->end_time->toIso8601String(),
                                    'status' => $meeting->status,
                                ];
                            }),
                        ];
                    });

                return [
                    'date' => $todayDate,
                    'summary' => [
                        'total_employees' => $totalEmployees,
                        'meetings_today' => $meetingsToday,
                        'meetings_pending' => $meetingsPending,
                        'meetings_completed' => $meetingsCompleted,
                    ],
                    'todays_meetings' => $todaysMeetings,
                    'room_usage' => $roomUsage,
                ];
            });

            return response()->json([
                'message' => 'Dashboard data fetched successfully',
                'data' => $data,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while fetching dashboard data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Auto-update meeting statuses based on current time.
     * - menunggu:     now < start_time
     * - berlangsung:  start_time <= now <= end_time
     * - selesai:      now > end_time
     */
    private function autoUpdateStatuses(string $date)
    {
        // Use a simple cache lock to prevent redundant updates in the same minute
        $lockKey = 'auto_update_statuses_' . $date . '_' . floor(time() / 60);
        if (Cache::has($lockKey)) {
            return;
        }

        $now = Carbon::now();

        Meeting::whereDate('start_time', $date)
            ->where('status', '!=', 'dibatalkan')
            ->where('status', '!=', 'selesai')
            ->where('end_time', '<', $now)
            ->update(['status' => 'selesai']);

        Meeting::whereDate('start_time', $date)
            ->where('status', '!=', 'dibatalkan')
            ->where('status', '!=', 'berlangsung')
            ->where('start_time', '<=', $now)
            ->where('end_time', '>=', $now)
            ->update(['status' => 'berlangsung']);

        Meeting::whereDate('start_time', $date)
            ->where('status', '!=', 'dibatalkan')
            ->where('status', '!=', 'menunggu')
            ->where('start_time', '>', $now)
            ->update(['status' => 'menunggu']);

        Cache::put($lockKey, true, 60);
    }
}
