<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Pupil;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $titles = [
            'Joined School', 'Left School', 'Changed Class', 'Suspension', 'Exclusion', 'Award Received', 'Achievement', 'Attendance Issue', 'Health Incident', 'Safeguarding Concern'
        ];

        return [
            'pupil_id' => Pupil::inRandomOrder()->value('id') ?? Pupil::factory(),
            'title' => $this->faker->randomElement($titles),
            'date' => $this->faker->dateTimeBetween('-2 years', 'now')->format('Y-m-d'),
            'reference_number' => $this->faker->boolean(40) ? strtoupper($this->faker->bothify('EVT-##??')) : null,
            'description' => $this->faker->paragraphs(rand(1, 3), true),
            'outcome' => $this->faker->boolean(60) ? $this->faker->sentence() : null,
        ];
    }
}
