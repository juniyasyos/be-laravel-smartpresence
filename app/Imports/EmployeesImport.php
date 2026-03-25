<?php

namespace App\Imports;

use App\Models\Employee;
use App\Models\EmployeeType;
use App\Models\WorkUnit;
use App\Models\Position;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;

class EmployeesImport implements ToModel, WithHeadingRow
{
    private $employeeTypes;
    private $workUnits;
    private $positions;

    public function __construct()
    {
        // Cache data untuk performa lebih baik saat lookup ribuan baris
        $this->employeeTypes = EmployeeType::all()->pluck('id', 'employee_type')->toArray();
        $this->workUnits = WorkUnit::all()->pluck('id', 'work_unit')->toArray();
        $this->positions = Position::all()->pluck('id', 'position')->toArray();
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Abaikan baris kosong atau tanpa NIK
        if (!isset($row['nik']) || empty(trim($row['nik']))) {
            return null;
        }

        $nip = trim($row['nik']);

        // Lookup ID relasi berdasarkan nama dari Excel (fallback menggunakan id default 1 jika tidak ditemukan/matching)
        $employeeTypeName = trim($row['jenis_tenaga'] ?? '');
        $employeeTypeId = $this->employeeTypes[$employeeTypeName] ?? 1; 

        $workUnitName = trim($row['unit_kerja'] ?? '');
        $workUnitId = $this->workUnits[$workUnitName] ?? null;

        $positionName = trim($row['jabatan'] ?? '');
        $positionId = $this->positions[$positionName] ?? 1;

        $isActive = true;
        $statusStr = strtolower(trim($row['status_aktif'] ?? 'aktif'));
        if ($statusStr === 'non-aktif' || $statusStr === 'nonaktif' || $statusStr === 'non aktif') {
            $isActive = false;
        }

        // Cek jika sudah ada berdasarkan NIK (NIP)
        $employee = Employee::where('nip', $nip)->first();

        if ($employee) {
            // Update
            $employee->update([
                'full_name' => trim($row['nama_lengkap']),
                'email' => trim($row['email'] ?? null),
                'phone' => trim($row['no_hp'] ?? null),
                'employee_type_id' => $employeeTypeId,
                'work_unit_id' => $workUnitId,
                'position_id' => $positionId,
                'is_active' => $isActive,
            ]);
            return null; // Return null agar ToModel tidak mencoba membuat record lagi
        }

        return new Employee([
            'nip' => $nip,
            'full_name' => trim($row['nama_lengkap']),
            'email' => trim($row['email'] ?? null),
            'phone' => trim($row['no_hp'] ?? null),
            'employee_type_id' => $employeeTypeId,
            'work_unit_id' => $workUnitId,
            'position_id' => $positionId,
            'is_active' => $isActive,
        ]);
    }
}
