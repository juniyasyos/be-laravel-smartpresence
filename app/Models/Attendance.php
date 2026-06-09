<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'meeting_id',
        'employee_id',
        'check_in_time',
        'status',
        'verified_by',
        'notes'
    ];

    public function meeting()
    {
        return $this->belongsTo(Meeting::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class)->withTrashed();
    }
}