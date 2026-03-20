<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Admission extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'applicant_id',
        'school_level',
    'grade_level',
        'lrn',
        'user_id',
        'last_name',
        'first_name',
        'middle_name',
        'dob',
        'age',
        'gender',
        'citizenship',
        'religion',
        'height',
        'weight',
        'address',
        'phone',
        'email',
        'school_type',
        'private_type',
        'student_esc_no',
        'esc_school_id',
        'school_name',
        'strand',
        'tvl_specialization',
        'mother_name',
        'mother_occupation',
        'father_name',
        'father_occupation',
        'emergency_contact_name',
        'emergency_contact_relationship',
        'emergency_contact_phone',
        'status',
        'remarks',
        'approved_by',
        'approved_at',
        'approval_notes',
        'rejection_reason',
        'temp_password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'dob' => 'date',
        'height' => 'decimal:2',
        'weight' => 'decimal:2',
        'approved_at' => 'datetime',
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
     * Scope for pending admissions
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for approved admissions
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for rejected admissions
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Scope for JHS admissions
     */
    public function scopeJhs($query)
    {
        return $query->where('school_level', 'jhs');
    }

    /**
     * Scope for SHS admissions
     */
    public function scopeShs($query)
    {
        return $query->where('school_level', 'shs');
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
            default => 'secondary',
        };
    }

    /**
     * Get school level display name
     */
    public function getSchoolLevelDisplayAttribute()
    {
        return strtoupper($this->school_level);
    }
}
