<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentEnrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'school_year_id',
        'current_grade_level',
        'enrolling_grade_level',
        'section_id',
        'status',
        'admin_remarks',
        'approved_by',
        'approved_at',
        'enrollment_date',
    ];

    protected $casts = [
        'enrollment_date' => 'datetime',
        'approved_at' => 'datetime',
    ];

    /**
     * Get the student who enrolled
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get the school year
     */
    public function schoolYear()
    {
        return $this->belongsTo(SchoolYear::class);
    }

    /**
     * Get the assigned section
     */
    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    /**
     * Get the admin who approved
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get enrollment documents
     */
    public function documents()
    {
        return $this->hasMany(StudentEnrollmentDocument::class, 'enrollment_id');
    }

    /**
     * Scope to get pending enrollments
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get approved enrollments
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope to filter by grade level
     */
    public function scopeByGradeLevel($query, $gradeLevel)
    {
        return $query->where('enrolling_grade_level', $gradeLevel);
    }

    /**
     * Approve the enrollment
     */
    public function approve($sectionId = null, $adminId, $remarks = null)
    {
        $this->update([
            'status' => 'approved',
            'section_id' => $sectionId,
            'approved_by' => $adminId,
            'approved_at' => now(),
            'admin_remarks' => $remarks,
        ]);

        // Update student's grade level and section
        $this->student->update([
            'grade_level' => $this->enrolling_grade_level,
            'current_grade_level' => $this->enrolling_grade_level,
            'section_id' => $sectionId,
        ]);

        // Send email notification
        // Mail::to($this->student->email)->send(new EnrollmentApproved($this));
    }

    /**
     * Reject the enrollment
     */
    public function reject($adminId, $remarks)
    {
        $this->update([
            'status' => 'rejected',
            'approved_by' => $adminId,
            'approved_at' => now(),
            'admin_remarks' => $remarks,
        ]);

        // Send email notification
        // Mail::to($this->student->email)->send(new EnrollmentRejected($this));
    }

    /**
     * Upload a document
     */
    public function uploadDocument($documentType, $documentName, $filePath)
    {
        return $this->documents()->create([
            'document_type' => $documentType,
            'document_name' => $documentName,
            'file_path' => $filePath,
        ]);
    }
}
