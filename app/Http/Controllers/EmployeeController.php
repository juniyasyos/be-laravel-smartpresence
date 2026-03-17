<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\storeEmployeeRequest;
use App\Http\Requests\updateEmployeeRequest;
use App\Models\Employee;
use App\Models\EmployeeType;
use App\Models\WorkUnit;
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
            $query = Employee::with(['employeeType', 'workUnit', 'position']);

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
                $query->where('work_unit_id', $request->query('work_unit_id'));
            }

            $perPage = $request->query('per_page', 10);
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
    public function store(storeEmployeeRequest $request)
    {
        try {
            $validated = $request->validated();
            $result = Employee::create($validated);
            $result->load(['employeeType', 'workUnit', 'position']);

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
     * Detail karyawan berdasarkan ID.
     */
    public function show(string $id)
    {
        try {
            $result = Employee::with(['employeeType', 'workUnit', 'position'])->find($id);
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
    public function update(updateEmployeeRequest $request, string $id)
    {
        try {
            $result = Employee::find($id);
            if (!$result) {
                return response()->json([
                    'message' => 'Employee not found',
                ], 404);
            }

            $validated = $request->validated();
            $result->update($validated);
            $result->load(['employeeType', 'workUnit', 'position']);

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

            $result->delete();

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
            $result = EmployeeType::all();
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
            $result = WorkUnit::all();
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
}