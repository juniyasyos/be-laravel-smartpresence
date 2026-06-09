<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeetingMinutes extends Model
{
    protected $fillable = [
        'meeting_id',
        'content',
        'notulis_name',
        'notulis_position',
        'director_name',
        'director_position',
        'created_by',
        'updated_by',
    ];

    public function meeting()
    {
        return $this->belongsTo(Meeting::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
