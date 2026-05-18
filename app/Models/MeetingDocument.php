<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MeetingDocument extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'meeting_id',
        'type',
        'file_name',
        'file_path',
        'file_size',
        'mime_type',
        'director_name',
        'director_position',
        'director_signed_at',
        'notulis_name',
        'notulis_position',
        'notulis_signed_at',
        'uploaded_by'
    ];

    public function meeting()
    {
        return $this->belongsTo(Meeting::class);
    }
}