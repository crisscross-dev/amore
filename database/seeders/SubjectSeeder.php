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
            'EsP',
            'TLE',
            'MAPEH - Music',
            'MAPEH - Arts',
            'MAPEH - PE',
            'MAPEH - Health',
        ];

        // Remove standalone MAPEH for grades where components are used.
        Subject::query()
            ->whereIn('grade_level', ['7', '8', '9', '10'])
            ->where('name', 'MAPEH')
            ->delete();

        foreach (['7', '8'] as $gradeLevel) {
            foreach ($grade7to8Subjects as $subjectName) {
                $subjects[] = [
                    'name' => $subjectName,
                    'description' => null,
                    'subject_type' => null,
                    'grade_level' => $gradeLevel,
                    'hours_per_week' => null,
                ];
            }
        }

        foreach (['9', '10'] as $gradeLevel) {
            foreach ($grade9to10Subjects as $subjectName) {
                $subjects[] = [
                    'name' => $subjectName,
                    'description' => null,
                    'subject_type' => null,
                    'grade_level' => $gradeLevel,
                    'hours_per_week' => null,
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