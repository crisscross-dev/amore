<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubjectGradeLevel extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_id',
        'grade_level',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
