<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Pupil;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FamilyMember>
 */
class FamilyMemberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $relations = ['Parent', 'Sibling', 'Guardian', 'Grandparent', 'Aunt', 'Uncle'];

        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'dob' => fake()->dateTimeBetween('-50 years', '-2 years')->format('Y-m-d'),
            'relation' => fake()->randomElement($relations),
            'pupil_id' => Pupil::factory(),
        ];
    }
}
