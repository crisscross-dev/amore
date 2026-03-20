<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShsAdmission extends Model
{
    use HasFactory;

    protected $table = 'shs_admissions';

    protected $fillable = [
        'applicant_id',
        'lrn',
        'user_id',
        'first_name',
        'middle_name',
        'last_name',
        'birthdate',
        'age',
        'gender',
        'citizenship',
        'religion',
        'height',
        'weight',
        'phone_number',
        'email',
        'address',
        'applying_for_grade',
        'school_year',
        'strand',
        'tvl_specialization',
        'previous_school',
        'school_type',
        'private_school_type',
        'esc_student_no',
        'esc_school_id',
        'father_name',
        'father_occupation',
        'mother_maiden_name',
        'mother_occupation',
        'application_status',
        'application_date',
        'confirm_details',
        'status',
        'approved_by',
        'approved_at',
        'approval_notes',
        'rejection_reason',
    ];

    protected $casts = [
        'birthdate' => 'date',
        'application_date' => 'datetime',
        'approved_at' => 'datetime',
        'confirm_details' => 'boolean',
        'age' => 'integer',
        'height' => 'decimal:2',
        'weight' => 'decimal:2',
    ];

    /**
     * Get the user that owns the admission
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who approved the admission
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get full name accessor
     */
    public function getFullNameAttribute()
    {
        return trim("{$this->first_name} {$this->middle_name} {$this->last_name}");
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            'waitlisted' => 'secondary',
            default => 'secondary',
        };
    }

    /**
     * Get status icon
     */
    public function getStatusIconAttribute()
    {
        return match($this->status) {
            'pending' => 'fa-clock',
            'approved' => 'fa-check-circle',
            'rejected' => 'fa-times-circle',
            'waitlisted' => 'fa-hourglass-half',
            default => 'fa-question-circle',
        };
    }

    /**
     * Scope for pending applications
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for approved applications
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for rejected applications
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Scope for waitlisted applications
     */
    public function scopeWaitlisted($query)
    {
        return $query->where('status', 'waitlisted');
    }

    /**
     * Scope by strand
     */
    public function scopeByStrand($query, $strand)
    {
        return $query->where('strand', $strand);
    }
}
