<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Announcement extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'content',
        'created_by',
        'updated_by',
        'is_pinned',
        'priority',
        'audience',
        'target_audience',
        'attachments',
        'expires_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_pinned' => 'boolean',
        'attachments' => 'array',
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user who created the announcement.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the announcement.
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope a query to only include pinned announcements.
     */
    public function scopePinned(Builder $query): Builder
    {
        return $query->where('is_pinned', true);
    }

    /**
     * Scope a query to only include active (non-expired) announcements.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    /**
     * Scope a query to filter by audience.
     */
    public function scopeForAudience(Builder $query, string $audience): Builder
    {
        return $query->where(function ($q) use ($audience) {
            $q->where('audience', 'all')
              ->orWhere('audience', $audience);
        });
    }

    /**
     * Get formatted content with line breaks preserved.
     */
    public function getFormattedContentAttribute(): string
    {
        return nl2br(e($this->content));
    }

    /**
     * Check if announcement is expired.
     */
    public function getIsExpiredAttribute(): bool
    {
        if (!$this->expires_at) {
            return false;
        }
        
        return $this->expires_at->isPast();
    }

    /**
     * Get priority badge color.
     */
    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            'high' => '#dc3545',
            'medium' => '#ffc107',
            'low' => '#198754',
            default => '#6c757d',
        };
    }

    /**
     * Get priority icon.
     */
    public function getPriorityIconAttribute(): string
    {
        return match($this->priority) {
            'high' => 'fa-exclamation-circle',
            'medium' => 'fa-info-circle',
            'low' => 'fa-check-circle',
            default => 'fa-circle',
        };
    }

    /**
     * Get audience badge label.
     */
    public function getAudienceLabelAttribute(): string
    {
        return match($this->audience) {
            'all' => 'All Users',
            'faculty' => 'Faculty Only',
            'students' => 'Students Only',
            default => 'Unknown',
        };
    }
}