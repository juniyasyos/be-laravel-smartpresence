<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MeetingRoom extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'name','location','capacity','is_active'
    ];

    public function meetings()
    {
        return $this->hasMany(Meeting::class,'room_id');
    }
}