<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name','email','nip','password','role_id','status'
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