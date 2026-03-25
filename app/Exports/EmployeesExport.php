<?php

namespace App\Exports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EmployeesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    private $rowNumber = 0;

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Employee::with(['employeeType', 'workUnit', 'position'])->latest()->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Lengkap',
            'NIK',
            'Email',
            'No. HP',
            'Jenis Tenaga',
            'Unit Kerja',
            'Jabatan',
            'Status Aktif'
        ];
    }

    public function map($employee): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            $employee->full_name,
            $employee->nip,
            $employee->email,
            $employee->phone,
            $employee->employeeType ? $employee->employeeType->employee_type : '-',
            $employee->workUnit ? $employee->workUnit->work_unit : '-',
            $employee->position ? $employee->position->position : '-',
            $employee->is_active ? 'Aktif' : 'Non-Aktif',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
