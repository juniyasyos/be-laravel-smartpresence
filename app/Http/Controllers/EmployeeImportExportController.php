<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EmployeesExport;
use App\Imports\EmployeesImport;
use Exception;

class EmployeeImportExportController extends Controller
{
    /**
     * Download template atau export semua data employee
     */
    public function export()
    {
        try {
            return Excel::download(new EmployeesExport, 'Data_Karyawan_' . date('Ymd_His') . '.xlsx');
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Gagal mengekspor data karyawan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Import data dari file Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240', // Maksimal 10MB
        ]);

        try {
            Excel::import(new EmployeesImport, $request->file('file'));

            return response()->json([
                'message' => 'Data karyawan berhasil diimpor',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat mengimpor data karyawan. Pastikan format file sesuai.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
