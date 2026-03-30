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
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function setNameAttribute($value): void
    {
        if ($value === null) {
            $this->attributes['name'] = null;
            return;
        }

        $normalized = trim((string) $value);
        $this->attributes['name'] = mb_strtoupper($normalized, 'UTF-8');
    }

    public function gradeLevels()
    {
        return $this->hasMany(SubjectGradeLevel::class);
    }

    public function sectionTeachers()
    {
        return $this->hasMany(SectionSubjectTeacher::class);
    }
}
