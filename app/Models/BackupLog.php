<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BackupLog extends Model
{
    protected $fillable = [
        'name',
        'type',
        'status',
        'file_path',
        'file_size',
        'error_message',
        'started_at',
        'completed_at',
        'created_by',
    ];

    protected $casts = [
        'file_size'    => 'integer',
        'started_at'   => 'datetime',
        'completed_at' => 'datetime',
    ];

    /* ─── Relationships ─── */

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /* ─── Helpers ─── */

    /**
     * Human-readable duration between started_at and completed_at.
     */
    public function getDurationAttribute(): ?string
    {
        if (!$this->started_at || !$this->completed_at) {
            return null;
        }

        $seconds = $this->started_at->diffInSeconds($this->completed_at);

        if ($seconds < 60) {
            return sprintf('00:%02d', $seconds);
        }

        $minutes = intdiv($seconds, 60);
        $secs    = $seconds % 60;

        return sprintf('%02d:%02d', $minutes, $secs);
    }
}
