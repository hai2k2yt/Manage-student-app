<?php

namespace Database\Factories;

use App\Enums\RoleEnum;
use App\Models\Club;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Club>
 */
class ClubFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'club_code' => fake()->unique()->words(),
            'name' => fake()->company(),
            'teacher_code' => Teacher::pluck('teacher_code')->random()
        ];
    }
}
