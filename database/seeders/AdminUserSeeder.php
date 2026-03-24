<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Seed the default administrator account.
     */
    public function run(): void
    {
        $adminEmail = config('app.admin_email', 'admin@gmail.com');
        $adminPassword = config('app.admin_password', 'password');

        User::updateOrCreate(
            ['email' => $adminEmail],
            [
                'account_type' => 'admin',
                'first_name' => 'Amore',
                'middle_name' => null,
                'last_name' => 'Administrator',
                'student_id' => null,
                'grade_level' => null,
                'lrn' => null,
                'department' => 'Administration',
                'email_verified_at' => now(),
                'contact_number' => '09123456789',
                'profile_picture' => null,
                'password' => Hash::make($adminPassword),
                'status' => 'active',
                'first_login' => false,
            ]
        );
    }
}
