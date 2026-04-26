<?php

namespace App\Http\Controllers;

use App\Models\MeetingRoom;
use App\Http\Requests\StoreMeetingRoomRequest;
use App\Http\Requests\UpdateMeetingRoomRequest;
use Illuminate\Http\Request;
use Exception;

class MeetingsRoomController extends Controller
{
    /**
     * Daftar ruang rapat dengan search.
     *
     * Query params:
     *  - search   : cari berdasarkan nama atau lokasi
     *  - per_page : jumlah per halaman (default 10)
     */
    public function index(Request $request)
    {
        try {
            $query = MeetingRoom::query();

            // Search berdasarkan nama atau lokasi
            if ($request->filled('search')) {
                $search = $request->query('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('location', 'like', "%{$search}%");
                });
            }

            $perPage = $request->query('per_page', 10);
            $result = $query->latest()->paginate($perPage);

            return response()->json([
                'message' => 'Meeting rooms fetched successfully',
                'data' => $result,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while fetching meeting rooms',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Tambah ruang rapat baru.
     */
    public function store(StoreMeetingRoomRequest $request)
    {
        try {
            $validated = $request->validated();
            $result = MeetingRoom::create($validated);

            return response()->json([
                'message' => 'Meeting room created successfully',
                'data' => $result,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while creating meeting room',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Detail ruang rapat.
     */
    public function show(string $id)
    {
        try {
            $result = MeetingRoom::find($id);
            if (!$result) {
                return response()->json([
                    'message' => 'Meeting room not found',
                ], 404);
            }

            return response()->json([
                'message' => 'Meeting room fetched successfully',
                'data' => $result,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while fetching meeting room',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update ruang rapat.
     */
    public function update(UpdateMeetingRoomRequest $request, string $id)
    {
        try {
            $result = MeetingRoom::find($id);
            if (!$result) {
                return response()->json([
                    'message' => 'Meeting room not found',
                ], 404);
            }

            $validated = $request->validated();
            $result->update($validated);

            return response()->json([
                'message' => 'Meeting room updated successfully',
                'data' => $result,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while updating meeting room',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Toggle status aktif ruang rapat.
     */
    public function toggleStatus(string $id)
    {
        try {
            $result = MeetingRoom::find($id);
            if (!$result) {
                return response()->json([
                    'message' => 'Meeting room not found',
                ], 404);
            }

            $result->update(['is_active' => !$result->is_active]);

            return response()->json([
                'message' => 'Meeting room status updated successfully',
                'data' => $result,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while toggling meeting room status',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Hapus ruang rapat.
     */
    public function destroy(string $id)
    {
        try {
            $result = MeetingRoom::find($id);
            if (!$result) {
                return response()->json([
                    'message' => 'Meeting room not found',
                ], 404);
            }

            $result->delete();

            return response()->json([
                'message' => 'Meeting room deleted successfully',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while deleting meeting room',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}