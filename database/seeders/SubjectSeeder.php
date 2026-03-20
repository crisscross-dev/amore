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
        $subjects = [
            [
                'name' => 'Mathematics',
                'description' => 'Foundational mathematics covering number sense, algebra, and problem-solving.',
                'subject_type' => null,
                'grade_level' => 'all',
                'hours_per_week' => 4,
            ],
            [
                'name' => 'Filipino',
                'description' => 'Paglinang ng kasanayan sa wika, pagbasa, at pagsulat sa Filipino.',
                'subject_type' => null,
                'grade_level' => 'all',
                'hours_per_week' => 4,
            ],
            [
                'name' => 'English',
                'description' => 'Develops reading comprehension, writing, and oral communication skills.',
                'subject_type' => null,
                'grade_level' => 'all',
                'hours_per_week' => 4,
            ],
            [
                'name' => 'Science',
                'description' => 'Integrated science focusing on biology, chemistry, and physics concepts.',
                'subject_type' => null,
                'grade_level' => 'all',
                'hours_per_week' => 4,
            ],
            [
                'name' => 'Technology and Livelihood Education (TLE)',
                'description' => 'Practical skills in technology, entrepreneurship, and livelihood.',
                'subject_type' => null,
                'grade_level' => 'all',
                'hours_per_week' => 3,
            ],
            [
                'name' => 'Edukasyon sa Pagpapakatao (ESP)',
                'description' => 'Values education focusing on character formation and ethics.',
                'subject_type' => null,
                'grade_level' => 'all',
                'hours_per_week' => 2,
            ],
            [
                'name' => 'Araling Panlipunan (AP)',
                'description' => 'Social studies covering history, geography, and civics.',
                'subject_type' => null,
                'grade_level' => 'all',
                'hours_per_week' => 3,
            ],
            [
                'name' => 'Good Manners and Right Conduct (GMRC)',
                'description' => 'Character-building lessons on respect, discipline, and integrity.',
                'subject_type' => null,
                'grade_level' => 'all',
                'hours_per_week' => 1,
            ],
            [
                'name' => 'Physical Education and Health',
                'description' => 'Promotes fitness, teamwork, and healthy lifestyles.',
                'subject_type' => null,
                'grade_level' => 'all',
                'hours_per_week' => 2,
            ],
        ];

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
