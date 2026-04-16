<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Pupil;
use App\Models\RecordType;
use App\Models\Professional;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Record>
 */

class RecordFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $titles = [
            'Initial Assessment', 'Review Meeting', 'Home Visit Report', 'Intervention Log', 'Progress Update', 'Incident Report', 'Observation Session', 'Referral Form', 'Case Closure'
        ];

        return [
            'pupil_id' => Pupil::inRandomOrder()->value('id') ?? Pupil::factory(),
            'record_type_id' => RecordType::inRandomOrder()->value('id') ?? RecordType::factory(),
            'professional_id' => $this->faker->boolean(80) ? (Professional::inRandomOrder()->value('id') ?? Professional::factory()) : null,
            'title' => $this->faker->boolean(70) ? $this->faker->randomElement($titles) : null,
            'date' => $this->faker->dateTimeBetween('-2 years', 'now')->format('Y-m-d'),
            'reference_number' => $this->faker->boolean(40) ? strtoupper($this->faker->bothify('REF-##??')) : null,
            'description' => $this->faker->paragraphs(rand(1, 3), true),
            'outcome' => $this->faker->boolean(60) ? $this->faker->sentence() : null,
        ];
    }
}
