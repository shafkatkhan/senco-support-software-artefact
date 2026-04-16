<?php

namespace Database\Factories;

use App\Models\Attachment;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttachmentFactory extends Factory
{
    protected $model = Attachment::class;

    public function definition()
    {
        return [
            'attachable_type' => 'App\Models\Pupil',
            'attachable_id' => 1, // will be overridden in tests
            'filename' => $this->faker->word . '.pdf',
            'file_path' => 'attachments/' . $this->faker->uuid . '.pdf',
            'mime_type' => 'application/pdf',
            'size_bytes' => $this->faker->numberBetween(1000, 100000),
        ];
    }
}
