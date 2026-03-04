<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Professional;
use App\Models\Pupil;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Diagnosis>
 */
class DiagnosisFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $diagnoses = [
            'Autism Spectrum Disorder',
            'ADHD',
            'Dyslexia',
            'Dyspraxia',
            'Dyscalculia',
            'Auditory Processing Disorder',
            'Sensory Processing Disorder',
        ];

        return [
            'pupil_id' => Pupil::factory(),
            'date' => $this->faker->date(),
            'name' => $this->faker->randomElement($diagnoses),
            'professional_id' => $this->faker->boolean(80) ? Professional::inRandomOrder()->value('id') : null,
            'description' => $this->faker->sentence(),
            'recommendations' => $this->faker->paragraph(),
        ];
    }
}
