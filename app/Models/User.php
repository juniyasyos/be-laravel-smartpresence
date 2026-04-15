<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'username','email','password','role_id','is_active'
    ];

    protected $hidden = [
        'password',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function meetingsCreated()
    {
        return $this->hasMany(Meeting::class, 'created_by');
    }

    public function meetingAssignments()
    {
        return $this->hasMany(MeetingAssignment::class, 'user_id');
    }

    public function meetingAssignedBy()
    {
        return $this->hasMany(MeetingAssignment::class, 'assigned_by');
    }

    public function attendancesVerified()
    {
        return $this->hasMany(Attendance::class, 'verified_by');
    }

    public function meetingDocuments()
    {
        return $this->hasMany(MeetingDocument::class, 'uploaded_by');
    }
}