<?php

namespace Database\Factories;

use App\Enums\DayOfWeek;
use App\Enums\RoleEnum;
use App\Models\Club;
use App\Models\ClubSchedule;
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
            'club_id' => Club::pluck('id')->random(),
            'teacher_id' => User::where('role', RoleEnum::TEACHER)->pluck('id')->random(),
            'day_of_week' => fake()->randomElement(DayOfWeek::values()),
            'start_time' => fake()->time(),
            'end_time' => fake()->time()
        ];
    }
}
