<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Medication>
 */
class MedicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $medicines = ['Paracetamol', 'Ibuprofen', 'Salbutamol', 'Amoxicillin', 'Antihistamine', 'Epipen', 'Ritalin', 'Insulin'];
        $dosages = ['500mg', '200mg', '10ml', '1 tablet', '2 puffs', '5mg'];
        $frequencies = ['Once daily', 'Twice daily', 'Three times daily', 'As needed', 'Every 4 hours'];
        $times_of_day = ['Morning', 'Evening', 'Bedtime', '12:00 PM', 'With meals'];
        $administration_methods = ['Oral', 'Inhaler', 'Injection', 'Topical'];

        return [
            'name' => fake()->randomElement($medicines),
            'dosage' => fake()->randomElement($dosages),
            'frequency' => fake()->randomElement($frequencies),
            'time_of_day' => fake()->randomElement($times_of_day),
            'administration_method' => fake()->randomElement($administration_methods),
            'start_date' => fake()->dateTimeBetween('-1 year', 'now'),
            'end_date' => fake()->optional(0.3)->dateTimeBetween('now', '+6 months'),
            'expiry_date' => fake()->dateTimeBetween('+1 year', '+2 years'),
            'storage_instructions' => fake()->optional()->sentence(),
            'self_administer' => fake()->boolean(20),
            'pupil_id' => \App\Models\Pupil::factory(),
        ];
    }
}
