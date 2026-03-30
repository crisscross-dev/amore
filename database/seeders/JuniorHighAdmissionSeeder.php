<?php

namespace Database\Seeders;

use App\Models\Admission;
use Illuminate\Database\Seeder;

class JuniorHighAdmissionSeeder extends Seeder
{
    /**
     * Seed 5 Junior High admission records.
     */
    public function run(): void
    {
        $faker = fake('en_PH');
        $year = now()->format('Y');

        for ($i = 1; $i <= 5; $i++) {
            $dob = $faker->dateTimeBetween('-16 years', '-12 years');
            $schoolType = $faker->randomElement(['Public', 'Private']);
            $isPrivate = $schoolType === 'Private';

            Admission::updateOrCreate(
                [
                    'applicant_id' => sprintf('JHS-%s-%04d', $year, $i),
                ],
                [
                    'school_level' => 'jhs',
                    'grade_level' => 'Grade ' . $faker->numberBetween(7, 10),
                    'lrn' => str_pad((string) $faker->unique()->numberBetween(100000000000, 999999999999), 12, '0', STR_PAD_LEFT),
                    'last_name' => $faker->lastName(),
                    'first_name' => $faker->firstName(),
                    'middle_name' => $faker->optional()->firstName(),
                    'suffix' => $faker->optional()->randomElement(['Jr.', 'Sr.', 'III']),
                    'dob' => $dob,
                    'age' => now()->diffInYears($dob),
                    'gender' => $faker->randomElement(['Male', 'Female']),
                    'citizenship' => 'Filipino',
                    'religion' => $faker->randomElement(['Roman Catholic', 'Christian', 'Iglesia ni Cristo', 'Islam']),
                    'height' => $faker->randomFloat(2, 135, 175),
                    'weight' => $faker->randomFloat(2, 35, 75),
                    'address' => $faker->address(),
                    'phone' => '09' . $faker->numerify('#########'),
                    'email' => strtolower(sprintf('jhs.seed.%d.%s@amore.test', $i, $year)),
                    'school_type' => $schoolType,
                    'private_type' => $isPrivate ? $faker->randomElement(['ESC', 'Non-ESC']) : null,
                    'student_esc_no' => $isPrivate ? strtoupper($faker->bothify('??######')) : null,
                    'esc_school_id' => $isPrivate ? strtoupper($faker->bothify('??####')) : null,
                    'school_name' => $faker->company() . ' High School',
                    'strand' => null,
                    'tvl_specialization' => null,
                    'mother_name' => $faker->name('female'),
                    'mother_occupation' => $faker->jobTitle(),
                    'father_name' => $faker->name('male'),
                    'father_occupation' => $faker->jobTitle(),
                    'emergency_contact_name' => $faker->name(),
                    'emergency_contact_relationship' => $faker->randomElement(['Mother', 'Father', 'Guardian', 'Sibling']),
                    'emergency_contact_phone' => '09' . $faker->numerify('#########'),
                    'status' => 'pending',
                    'remarks' => null,
                    'approved_by' => null,
                    'approved_at' => null,
                    'approval_notes' => null,
                    'rejection_reason' => null,
                    'temp_password' => null,
                ]
            );
        }
    }
}
