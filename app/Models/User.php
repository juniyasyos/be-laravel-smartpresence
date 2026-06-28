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

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function syncRoles(array $roles)
    {
        $roleIds = [];
        foreach ($roles as $roleName) {
            $roleModel = \App\Models\Role::firstOrCreate(['role' => $roleName]);
            $roleIds[] = $roleModel->id;
        }
        
        $this->roles()->sync($roleIds);
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

    public function unitKerjas()
    {
        return $this->belongsToMany(WorkUnit::class, 'user_unit_kerja', 'user_id', 'unit_kerja_id')->withTimestamps();
    }
}