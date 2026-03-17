<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeetingParticipant extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'meeting_id',
        'employee_id',
    ];

    public function meeting()
    {
        return $this->belongsTo(Meeting::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
