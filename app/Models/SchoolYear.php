<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class SchoolYear extends Model
{
    use HasFactory;

    protected $fillable = [
        'year_name',
        'start_date',
        'end_date',
        'enrollment_start',
        'enrollment_end',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'enrollment_start' => 'date',
        'enrollment_end' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Get enrollments for this school year
     */
    public function enrollments()
    {
        return $this->hasMany(StudentEnrollment::class);
    }

    /**
     * Get sections for this school year
     */
    public function sections()
    {
        return $this->hasMany(Section::class);
    }

    /**
     * Scope to get only active school year
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get school years where enrollment is currently open
     */
    public function scopeEnrollmentOpen($query)
    {
        $today = Carbon::today();
        return $query->where('enrollment_start', '<=', $today)
                     ->where('enrollment_end', '>=', $today);
    }

    /**
     * Check if enrollment period is currently active
     */
    public function isEnrollmentPeriod(): bool
    {
        $today = Carbon::today();
        return $today->between($this->enrollment_start, $this->enrollment_end);
    }

    /**
     * Check if students can enroll now
     */
    public function canEnrollNow(): bool
    {
        return $this->is_active && $this->isEnrollmentPeriod();
    }
}
