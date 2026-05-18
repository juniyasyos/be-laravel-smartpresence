<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Meeting extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'organizer',
        'room_id',
        'start_time',
        'end_time',
        'status',
        'created_by',
        'deleted_at'
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

    public function minutes()
    {
        return $this->hasOne(MeetingMinutes::class);
    }

    public function documents()
    {
        return $this->hasMany(MeetingDocument::class);
    }
}