<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeetingAssignment extends Model
{
    const UPDATED_AT = null;

    protected $fillable = ['meeting_id', 'user_id', 'assigned_by'];

public function meeting()
{
    return $this->belongsTo(Meeting::class);
}

public function user()
{
    return $this->belongsTo(User::class);
}

public function assignedBy()
{
    return $this->belongsTo(User::class, 'assigned_by');
}
}