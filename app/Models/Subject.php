<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'subject_type',
        'grade_level',
        'hours_per_week',
        'is_active',
    ];

    protected $casts = [
        'hours_per_week' => 'integer',
        'is_active' => 'boolean',
    ];

    public function gradeLevels()
    {
        return $this->hasMany(SubjectGradeLevel::class);
    }

    public function sectionTeachers()
    {
        return $this->hasMany(SectionSubjectTeacher::class);
    }
}
