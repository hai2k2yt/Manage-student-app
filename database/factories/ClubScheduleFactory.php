<?php

namespace Database\Factories;

use App\Enums\DayOfWeek;
use App\Enums\RoleEnum;
use App\Models\Club;
use App\Models\ClubSchedule;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ClubSchedule>
 */
class ClubScheduleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'schedule_code' => fake()->unique()->words(),
            'schedule_name' => fake()->jobTitle(),
            'club_code' => Club::pluck('club_code')->random(),
            'teacher_code' => Teacher::pluck('id')->random(),
            'day_of_week' => fake()->randomElement(DayOfWeek::values()),
        ];
    }
}
