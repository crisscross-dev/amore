<?php

namespace Tests\Feature;

use App\Models\Section;
use App\Models\SectionSubjectTeacher;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SectionAssignmentConflictTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_schedule_overlap_is_blocked_with_validation_error(): void
    {
        $admin = User::factory()->create(['account_type' => 'admin']);
        $teacher = User::factory()->create(['account_type' => 'faculty']);

        $section = Section::create([
            'name' => 'Grade 7 - Faith',
            'grade_level' => '7',
            'is_active' => true,
        ]);

        $subjectA = Subject::create([
            'name' => 'Araling Panlipunan',
            'subject_type' => 'core',
            'grade_level' => '7',
            'hours_per_week' => 3,
            'is_active' => true,
        ]);

        $subjectB = Subject::create([
            'name' => 'Edukasyon sa Pagpapakatao',
            'subject_type' => 'core',
            'grade_level' => '7',
            'hours_per_week' => 2,
            'is_active' => true,
        ]);

        $payload = [
            'teacher_ids' => [
                $subjectA->id => $teacher->id,
                $subjectB->id => $teacher->id,
            ],
            'rooms' => [
                $subjectA->id => '201',
                $subjectB->id => '202',
            ],
            'days' => [
                $subjectA->id => 'Monday',
                $subjectB->id => 'Monday',
            ],
            'start_times' => [
                $subjectA->id => '07:00',
                $subjectB->id => '07:00',
            ],
            'end_times' => [
                $subjectA->id => '07:30',
                $subjectB->id => '07:30',
            ],
        ];

        $response = $this->actingAs($admin)
            ->post(route('admin.sections.assign-subject-teachers', $section), $payload);

        $response->assertSessionHasErrors(['schedule']);
        $this->assertSame(1, SectionSubjectTeacher::count());
        $this->assertDatabaseHas('section_subject_teachers', [
            'section_id' => $section->id,
            'subject_id' => $subjectA->id,
            'teacher_id' => $teacher->id,
            'day_of_week' => 'Monday',
            'start_time' => '07:00',
            'end_time' => '07:30',
        ]);
        $this->assertDatabaseMissing('section_subject_teachers', [
            'section_id' => $section->id,
            'subject_id' => $subjectB->id,
            'teacher_id' => $teacher->id,
            'day_of_week' => 'Monday',
        ]);
    }
}
