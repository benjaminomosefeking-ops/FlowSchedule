<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shift extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'title',
        'description',
        'type',
        'required_skill',
        'start_time',
        'end_time',
        'status',
        'user_id',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    /**
     * Get the user assigned to this shift.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the duration of the shift in hours.
     */
    public function getDurationInHours(): float
    {
        return $this->start_time->diffInHours($this->end_time);
    }

    /**
     * Scope a query to only include upcoming shifts.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('start_time', '>', now())
            ->orderBy('start_time', 'asc');
    }

    /**
     * Scope a query to only include shifts for a specific week.
     */
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('start_time', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }
}
