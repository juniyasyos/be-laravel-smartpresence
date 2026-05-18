<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Models\Employee;
use App\Models\EmployeeType;
use App\Models\WorkUnit;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Exception;

class EmployeeController extends Controller
{
    /**
     * Daftar karyawan dengan search & filter.
     *
     * Query params:
     *  - search       : cari berdasarkan nama atau NIP
     *  - employee_type_id : filter jenis tenaga
     *  - work_unit_id     : filter unit kerja
     *  - per_page         : jumlah per halaman (default 10)
     */
    public function index(Request $request)
    {
        try {
            $query = Employee::select('id', 'full_name', 'nip', 'employee_type_id', 'work_unit_id', 'phone', 'email', 'signature_path')
                ->with([
                    'employeeType:id,employee_type',
                    'workUnit:id,work_unit'
                ]);

            // Search berdasarkan nama atau NIP
            if ($request->filled('search')) {
                $search = $request->query('search');
                $query->where(function ($q) use ($search) {
                    $q->where('full_name', 'like', "%{$search}%")
                      ->orWhere('nip', 'like', "%{$search}%");
                });
            }

            // Filter berdasarkan jenis tenaga
            if ($request->filled('employee_type_id')) {
                $query->where('employee_type_id', $request->query('employee_type_id'));
            }

            // Filter berdasarkan unit kerja
            if ($request->filled('work_unit_id')) {
                $workUnitId = $request->query('work_unit_id');
                // Jika work_unit_id null atau "none", tampilkan hanya karyawan tanpa unit kerja
                if ($workUnitId === 'All' || $workUnitId === null) {
                    $query->whereNull('work_unit_id');
                } else {
                    // Jika ada unit kerja spesifik, tampilkan karyawan dari unit itu PLUS yang tidak punya unit kerja
                    $query->where(function ($q) use ($workUnitId) {
                        $q->where('work_unit_id', $workUnitId)
                          ->orWhereNull('work_unit_id');
                    });
                }
            }

            $perPage = (int) $request->query('per_page', 25);
            $result = $query->latest()->paginate($perPage);

            return response()->json([
                'message' => 'Employees fetched successfully',
                'data' => $result,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while fetching employees',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Tambah karyawan baru.
     */
    public function store(StoreEmployeeRequest $request)
    {
        try {
            $validated = $request->validated();

            // Handle signature file upload
            if ($request->hasFile('signature')) {
                $path = $request->file('signature')->store('signatures', 'public');
                $validated['signature_path'] = $path;
            }

            // Remove signature file object from validated data to keep it clean
            unset($validated['signature']);

            $result = Employee::create($validated);
            $result->load(['employeeType', 'workUnit']);

            $this->clearEmployeeCache();

            return response()->json([
                'message' => 'Employee created successfully',
                'data' => $result,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while creating employee',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Detail karyawan.
     */
    public function show(string $id)
    {
        try {
            $cacheKey = 'employees_show_' . $id;
            $result = Cache::remember($cacheKey, 3600, function () use ($id) {
                return Employee::with(['employeeType', 'workUnit'])->find($id);
            });
            if (!$result) {
                return response()->json([
                    'message' => 'Employee not found',
                ], 404);
            }

            return response()->json([
                'message' => 'Employee fetched successfully',
                'data' => $result,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while fetching employee',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update data karyawan.
     */
    public function update(UpdateEmployeeRequest $request, string $id)
    {
        try {
            $result = Employee::find($id);
            if (!$result) {
                return response()->json([
                    'message' => 'Employee not found',
                ], 404);
            }

            $validated = $request->validated();

            // Handle signature file upload
            if ($request->hasFile('signature')) {
                // Delete old signature if exists
                if ($result->signature_path && Storage::disk('public')->exists($result->signature_path)) {
                    Storage::disk('public')->delete($result->signature_path);
                }
                $path = $request->file('signature')->store('signatures', 'public');
                $validated['signature_path'] = $path;
            }

            // Handle signature removal (when client sends remove_signature = true)
            if ($request->boolean('remove_signature')) {
                if ($result->signature_path && Storage::disk('public')->exists($result->signature_path)) {
                    Storage::disk('public')->delete($result->signature_path);
                }
                $validated['signature_path'] = null;
            }

            // Remove signature and remove_signature from validated data before update
            unset($validated['signature'], $validated['remove_signature']);

            $result->update($validated);
            $result->load(['employeeType', 'workUnit']);

            $this->clearEmployeeCache();

            return response()->json([
                'message' => 'Employee updated successfully',
                'data' => $result,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while updating employee',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Hapus karyawan.
     */
    public function destroy(string $id)
    {
        try {
            $result = Employee::find($id);
            if (!$result) {
                return response()->json([
                    'message' => 'Employee not found',
                ], 404);
            }

            // Soft delete — file signature tetap tersimpan agar bisa di-restore
            $result->delete();

            $this->clearEmployeeCache();

            return response()->json([
                'message' => 'Employee deleted successfully',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while deleting employee',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Daftar jenis tenaga untuk dropdown.
     */
    public function employeeTypes()
    {
        try {
            $result = Cache::rememberForever('employee_types', function () {
                return EmployeeType::select('id', 'employee_type')->get();
            });
            return response()->json([
                'message' => 'Employee types fetched successfully',
                'data' => $result,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while fetching employee types',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Daftar unit kerja untuk dropdown.
     */
    public function workUnits()
    {
        try {
            $result = Cache::rememberForever('work_units', function () {
                $units = WorkUnit::select('id', 'work_unit')->get();
                
                // Tambahkan opsi "none" untuk karyawan tanpa unit kerja
                $noneOption = (object)[
                    'id' => null,
                    'work_unit' => 'none',
                ];
                
                return collect($units)->prepend($noneOption);
            });
            
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
     * Clear all employee-related cache keys.
     */
    private function clearEmployeeCache(): void
    {
        Cache::forget('employee_types');
        Cache::forget('work_units');
    }
}