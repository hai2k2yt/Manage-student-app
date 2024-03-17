<?php

namespace Database\Factories;

use App\Models\ClubSchedule;
use App\Models\ClubSession;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ClubSession>
 */
class ClubSessionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'schedule_id' => ClubSchedule::pluck('id')->random(),
            'date' => fake()->dateTime(),
        ];
    }
}
