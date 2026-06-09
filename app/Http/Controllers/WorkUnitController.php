<?php

namespace App\Http\Controllers;

use App\Models\WorkUnit;
use App\Http\Requests\StoreWorkUnitRequest;
use App\Http\Requests\UpdateWorkUnitRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Exception;

class WorkUnitController extends Controller
{
    /**
     * Daftar unit kerja dengan search.
     *
     * Query params:
     *  - search   : cari berdasarkan nama unit kerja
     *  - per_page : jumlah per halaman (default 10)
     */
    public function index(Request $request)
    {
        try {
            $query = WorkUnit::select('id', 'work_unit', 'created_at')
                ->withCount('employees');

            // Search berdasarkan nama unit kerja
            if ($request->filled('search')) {
                $search = $request->query('search');
                $query->where('work_unit', 'like', "%{$search}%");
            }

            $perPage = (int) $request->query('per_page', 10);
            $result = $query->latest()->paginate($perPage);

            return response()->json([
                'message' => 'Work units fetched successfully',
                'data' => $result,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while fetching work units',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Tambah unit kerja baru.
     */
    public function store(StoreWorkUnitRequest $request)
    {
        try {
            $validated = $request->validated();
            $result = WorkUnit::create($validated);
            $result->loadCount('employees');

            Cache::forget('work_units'); // Also clear the simple key used for dropdowns

            return response()->json([
                'message' => 'Work unit created successfully',
                'data' => $result,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while creating work unit',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Detail unit kerja.
     */
    public function show(string $id)
    {
        try {
            $result = WorkUnit::withCount('employees')->find($id);
            if (!$result) {
                return response()->json([
                    'message' => 'Work unit not found',
                ], 404);
            }

            return response()->json([
                'message' => 'Work unit fetched successfully',
                'data' => $result,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while fetching work unit',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update unit kerja.
     */
    public function update(UpdateWorkUnitRequest $request, string $id)
    {
        try {
            $result = WorkUnit::find($id);
            if (!$result) {
                return response()->json([
                    'message' => 'Work unit not found',
                ], 404);
            }

            $validated = $request->validated();
            $result->update($validated);
            $result->loadCount('employees');

            Cache::forget('work_units');

            return response()->json([
                'message' => 'Work unit updated successfully',
                'data' => $result,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while updating work unit',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Hapus unit kerja.
     */
    public function destroy(string $id)
    {
        try {
            $result = WorkUnit::withCount('employees')->find($id);
            if (!$result) {
                return response()->json([
                    'message' => 'Work unit not found',
                ], 404);
            }

            if ($result->employees_count > 0) {
                return response()->json([
                    'message' => 'Unit kerja tidak dapat dihapus karena masih memiliki ' . $result->employees_count . ' karyawan',
                ], 422);
            }

            // Soft delete
            $result->delete();
            Cache::forget('work_units');

            return response()->json([
                'message' => 'Work unit deleted successfully',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while deleting work unit',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
