<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

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
            'date' => $this->faker->date(),
            'name' => $this->faker->randomElement($diagnoses),
            'carried_out_by' => 'Dr. ' . $this->faker->lastName,
            'description' => $this->faker->sentence(),
            'recommendations' => $this->faker->paragraph(),
            'pupil_id' => \App\Models\Pupil::factory(),
        ];
    }
}
