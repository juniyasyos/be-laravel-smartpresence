<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'full_name',
        'nip',
        'employee_type_id',
        'work_unit_id',
        'email',
        'phone',
        'signature_path',
        'is_active'
    ];

    protected $appends = ['signature_url'];

    /**
     * Accessor untuk mendapatkan URL tanda tangan.
     */
    public function getSignatureUrlAttribute()
    {
        return $this->signature_path
            ? asset('storage/' . $this->signature_path)
            : null;
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
    public function employeeType()
    {
        return $this->belongsTo(EmployeeType::class);
    }
    public function workUnit()
    {
        return $this->belongsTo(WorkUnit::class);
    }
}