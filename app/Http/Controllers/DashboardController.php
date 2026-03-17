<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Meeting;
use App\Models\MeetingRoom;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Get dashboard data including summary statistics,
     * today's meetings, and room usage timeline.
     */
    public function index(Request $request)
    {
        try {
            $date = $request->query('date', Carbon::today()->toDateString());

            // statistik
            $totalEmployees = Employee::where('is_active', true)->count();
            $meetingsToday = Meeting::whereDate('start_time', $date)->count();
            $meetingsPending = Meeting::whereDate('start_time', $date)
                ->where('status', 'scheduled')
                ->count();
            $meetingsCompleted = Meeting::whereDate('start_time', $date)
                ->where('status', 'completed')
                ->count();

            // Rapat hari ini dengan detail
            $todaysMeetings = Meeting::whereDate('start_time', $date)
                ->with(['room:id,name,location', 'creator:id,username', 'participants', 'attendances'])
                ->orderBy('start_time', 'asc')
                ->get()
                ->map(function ($meeting) {
                    $participantsCount = $meeting->participants->count();
                    $attendancePresent = $meeting->attendances->where('status', 'present')->count();
                    $attendanceAbsent = $participantsCount - $attendancePresent;

                    return [
                        'id' => $meeting->id,
                        'title' => $meeting->title,
                        'start_time' => $meeting->start_time,
                        'end_time' => $meeting->end_time,
                        'status' => $meeting->status,
                        'room' => $meeting->room,
                        'creator' => $meeting->creator,
                        'participants_count' => $participantsCount,
                        'attendance_present' => $attendancePresent,
                        'attendance_absent' => $attendanceAbsent > 0 ? $attendanceAbsent : 0,
                    ];
                });

            // Penggunaan ruang
            $roomUsage = MeetingRoom::where('is_active', true)
                ->with(['meetings' => function ($query) use ($date) {
                    $query->whereDate('start_time', $date)
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
                                'start_time' => $meeting->start_time,
                                'end_time' => $meeting->end_time,
                                'status' => $meeting->status,
                            ];
                        }),
                    ];
                });

            return response()->json([
                'message' => 'Dashboard data fetched successfully',
                'data' => [
                    'date' => $date,
                    'summary' => [
                        'total_employees' => $totalEmployees,
                        'meetings_today' => $meetingsToday,
                        'meetings_pending' => $meetingsPending,
                        'meetings_completed' => $meetingsCompleted,
                    ],
                    'todays_meetings' => $todaysMeetings,
                    'room_usage' => $roomUsage,
                ],
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while fetching dashboard data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
