<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    /**
     * Seed the application's subjects table.
     */
    public function run(): void
    {
        $subjects = [];

        $grade7to8Subjects = [
            'Filipino',
            'English',
            'Math',
            'Science',
            'AP',
            'GMRC',
            'TLE',
            'MAPEH - Music & Arts',
            'MAPEH - PE & Health',
        ];

        $grade9to10Subjects = [
            'Filipino',
            'English',
            'Math',
            'Science',
            'AP',
            'ESP',
            'TLE',
            'MAPEH - Music',
            'MAPEH - Arts',
            'MAPEH - PE',
            'MAPEH - Health',
        ];

        // Remove standalone MAPEH for JHS grades where components are used.
        Subject::query()
            ->whereIn('grade_level', ['7', '8', '9', '10'])
            ->where('name', 'MAPEH')
            ->delete();

        foreach (['7', '8'] as $gradeLevel) {
            foreach ($grade7to8Subjects as $subjectName) {
                $subjects[] = [
                    'name' => mb_strtoupper(trim($subjectName), 'UTF-8'),
                    'description' => null,
                    'subject_type' => null,
                    'grade_level' => $gradeLevel,
                ];
            }
        }

        foreach (['9', '10'] as $gradeLevel) {
            foreach ($grade9to10Subjects as $subjectName) {
                $subjects[] = [
                    'name' => mb_strtoupper(trim($subjectName), 'UTF-8'),
                    'description' => null,
                    'subject_type' => null,
                    'grade_level' => $gradeLevel,
                ];
            }
        }

        foreach ($subjects as $subject) {
            Subject::updateOrCreate(
                [
                    'name' => $subject['name'],
                    'grade_level' => $subject['grade_level'],
                ],
                $subject
            );
        }
    }
}

//php artisan db:seed --class=Database\Seeders\SubjectSeeder