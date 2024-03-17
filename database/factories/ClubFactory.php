<?php

namespace Database\Factories;

use App\Enums\RoleEnum;
use App\Models\Club;
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
            'name' => fake()->company(),
            'teacher_id' => User::where('role', RoleEnum::TEACHER)->pluck('id')->random()
        ];
    }
}
