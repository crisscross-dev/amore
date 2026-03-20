<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'grade_level',
        'description',
        'capacity',
        'academic_year',
        'is_active',
        'adviser_id',
    ];

    public function students()
    {
        return $this->hasMany(User::class, 'section_id')->where('account_type', 'student');
    }

    public function adviser()
    {
        return $this->belongsTo(User::class, 'adviser_id');
    }

    public function subjectTeachers()
    {
        return $this->hasMany(SectionSubjectTeacher::class);
    }
}
