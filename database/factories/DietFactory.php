<?php

namespace Database\Factories;

use App\Models\Diet;
use App\Models\Pupil;
use App\Models\Subject;
use App\Models\Proficiency;
use Illuminate\Database\Eloquent\Factories\Factory;

class DietFactory extends Factory
{
    protected $model = Diet::class;

    public function definition()
    {
        return [
            'pupil_id' => Pupil::factory(),
            'subject_id' => Subject::factory(),
            'proficiency_id' => null,
        ];
    }
}
