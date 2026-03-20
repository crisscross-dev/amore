<?php

namespace Database\Seeders;

use App\Models\FacultyPosition;
use Illuminate\Database\Seeder;

class FacultyPositionSeeder extends Seeder
{
    public function run(): void
    {
        $positions = [
            [
                'name' => 'Principal',
                'code' => 'PRINCIPAL',
                'description' => 'Oversees the entire academic community and strategic direction.',
                'category' => 'administrative',
                'hierarchy_level' => 1,
            ],
            [
                'name' => 'Assistant Principal',
                'code' => 'ASSISTANT_PRINCIPAL',
                'description' => 'Supports the principal in managing academic programs and operations.',
                'category' => 'administrative',
                'hierarchy_level' => 2,
            ],
            [
                'name' => 'Department Head',
                'code' => 'DEPARTMENT_HEAD',
                'description' => 'Leads a subject department and coordinates curriculum execution.',
                'category' => 'administrative',
                'hierarchy_level' => 3,
            ],
            [
                'name' => 'Academic Coordinator',
                'code' => 'ACADEMIC_COORDINATOR',
                'description' => 'Coordinates academic initiatives and assessment standards.',
                'category' => 'support',
                'hierarchy_level' => 4,
            ],
            [
                'name' => 'Senior Teacher',
                'code' => 'SENIOR_TEACHER',
                'description' => 'Provides mentorship and advanced instruction within the department.',
                'category' => 'teaching',
                'hierarchy_level' => 5,
            ],
            [
                'name' => 'Teacher',
                'code' => 'TEACHER',
                'description' => 'Delivers classroom instruction and student engagement.',
                'category' => 'teaching',
                'hierarchy_level' => 6,
            ],
            [
                'name' => 'Guidance Counselor',
                'code' => 'GUIDANCE_COUNSELOR',
                'description' => 'Supports student wellbeing and development programs.',
                'category' => 'support',
                'hierarchy_level' => 7,
            ],
        ];

        foreach ($positions as $position) {
            FacultyPosition::updateOrCreate(
                ['code' => $position['code']],
                [
                    'name' => $position['name'],
                    'description' => $position['description'],
                    'category' => $position['category'],
                    'hierarchy_level' => $position['hierarchy_level'],
                    'is_active' => true,
                ]
            );
        }
    }
}
