<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Pupil;
use App\Models\MeetingType;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Meeting>
 */
class MeetingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'pupil_id' => Pupil::inRandomOrder()->value('id') ?? Pupil::factory(),
            'meeting_type_id' => MeetingType::inRandomOrder()->value('id') ?? MeetingType::factory(),
            'date' => $this->faker->dateTimeBetween('-2 years', 'now')->format('Y-m-d'),
            'title' => $this->faker->sentence(3),
            'participants' => $this->faker->name() . ', ' . $this->faker->name(),
            'discussion' => $this->faker->paragraphs(2, true),
            'recommendations' => $this->faker->boolean(60) ? $this->faker->paragraph() : null,
        ];
    }
}
