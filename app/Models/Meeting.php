<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    protected $fillable = [
        'title',
        'organizer',
        'room_id',
        'start_time',
        'end_time',
        'status',
        'created_by'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function room()
    {
        return $this->belongsTo(MeetingRoom::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function participants()
    {
        return $this->hasMany(MeetingParticipant::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}