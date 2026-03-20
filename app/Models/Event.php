<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Event extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
        'event_type',
        'color',
        'is_all_day',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_all_day' => 'boolean',
    ];

    /**
     * Get the user who created the event.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope a query to only include events for a specific month.
     */
    public function scopeForMonth($query, int $year, int $month)
    {
        return $query->whereYear('start_date', $year)
                    ->whereMonth('start_date', $month)
                    ->orderBy('start_date', 'asc');
    }

    /**
     * Scope a query to only include upcoming events.
     */
    public function scopeUpcoming($query, int $limit = 10)
    {
        return $query->where('start_date', '>=', now())
                    ->orderBy('start_date', 'asc')
                    ->limit($limit);
    }

    /**
     * Get event type display name.
     */
    public function getTypeNameAttribute(): string
    {
        return match($this->event_type) {
            'meeting' => 'Meeting',
            'deadline' => 'Deadline',
            'holiday' => 'Holiday',
            'exam' => 'Examination',
            'sports' => 'Sports Event',
            default => 'General Event',
        };
    }

    /**
     * Get event type icon.
     */
    public function getTypeIconAttribute(): string
    {
        return match($this->event_type) {
            'meeting' => 'fa-users',
            'deadline' => 'fa-clipboard-check',
            'holiday' => 'fa-calendar-times',
            'exam' => 'fa-pencil-alt',
            'sports' => 'fa-trophy',
            default => 'fa-calendar',
        };
    }
}

