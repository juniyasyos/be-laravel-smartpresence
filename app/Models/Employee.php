<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use SoftDeletes;
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
            ? Storage::url($this->signature_path)
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