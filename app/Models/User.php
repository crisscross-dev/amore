<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'account_type',
        'current_grade_level',
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'contact_number',
        'password',
        'custom_id',
        'grade_level',
        'lrn',
        'profile_picture',
        'status',
        'faculty_position_id',
        'department',
        'first_login',
        'position_assigned_date',
        'assigned_by',
        'section_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'grade_level' => 'string',
            'lrn' => 'string',
            'position_assigned_date' => 'date',
        ];
    }

    /**
     * Mutator for LRN validation and formatting (ensures exactly 12 digits)
     */
    public function setLrnAttribute($value)
    {
        if ($value) {
            // Remove any non-numeric characters and ensure exactly 12 digits
            $cleaned = preg_replace('/\D/', '', $value);
            $this->attributes['lrn'] = strlen($cleaned) === 12 ? $cleaned : null;
            return;
        }

        $this->attributes['lrn'] = null;
    }

    /**
     * Accessor for formatted grade level
     */
    public function getFormattedGradeLevelAttribute()
    {
        return $this->grade_level ? "Grade {$this->grade_level}" : null;
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            // 1. Year part
            $year = now()->format('Y');



            // 2. Role code based on account_type

            $type = strtolower($user->account_type);
            $roleCodes = [
                'student' => '01',
                'faculty' => '02',
                'admin'   => '03',
            ];
            $roleCode = $roleCodes[$type] ?? '00';

            // 3. Sequence number for this role in this year
            $lastUser = self::where('account_type', $user->account_type)
                ->whereYear('created_at', now()->year)
                ->orderBy('id', 'desc')
                ->first();

            $nextNumber = $lastUser
                ? ((int)substr($lastUser->custom_id, -4)) + 1
                : 1;

            $sequence = str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

            // 4. Final format
            $user->custom_id = "{$year}-{$roleCode}-{$sequence}";
        });
    }

    /**
     * Position assigned to the faculty member.
     */
    public function facultyPosition()
    {
        return $this->belongsTo(FacultyPosition::class);
    }

    /**
     * Admin user who assigned the current position.
     */
    public function positionAssignee()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /**
     * Section assigned to the student.
     */
    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function advisedSections()
    {
        return $this->hasMany(Section::class, 'adviser_id');
    }

    public function subjectTeachingAssignments()
    {
        return $this->hasMany(SectionSubjectTeacher::class, 'teacher_id');
    }

    /**
     * Grade entries for the student.
     */
    public function gradeEntries()
    {
        return $this->hasMany(GradeEntry::class, 'student_id');
    }
}
