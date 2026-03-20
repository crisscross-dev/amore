<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'account_type' => 'admin',
            'first_name' => 'System',
            'middle_name' => null,
            'last_name' => 'Administrator',
            'student_id' => null,
            'grade_level' => null,
            'lrn' => null,
            'department' => 'Administration',
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'contact_number' => '09123456789',
            'profile_picture' => null,
            'password' => static::$password ??= Hash::make('Admin@12345'),
            'status' => 'active',
            'first_login' => false,
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Override the generated email address.
     */
    public function withEmail(string $email): static
    {
        return $this->state(fn () => ['email' => $email]);
    }

    /**
     * Override the generated password with a hashed value.
     */
    public function withPassword(string $password): static
    {
        return $this->state(fn () => ['password' => Hash::make($password)]);
    }
}
