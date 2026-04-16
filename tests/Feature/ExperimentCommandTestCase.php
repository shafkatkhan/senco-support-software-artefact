<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

abstract class ExperimentCommandTestCase extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $group = \App\Models\UserGroup::factory()->create();
        \App\Models\User::factory()->create(['user_group_id' => $group->id]);
    }

    protected function prepareExperimentsDatabase(): void
    {
        config([
            'database.connections.experiments' => [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
                'foreign_key_constraints' => true,
            ],
        ]);
        DB::purge('experiments');

        Schema::connection('experiments')->create('experiment1_results', function ($table) {
            $table->id();
            $table->string('filename')->nullable();
            $table->string('speaker_native_language')->nullable();
            $table->float('audio_duration_seconds')->nullable();
            $table->text('transcript_text')->nullable();
            $table->float('wer')->nullable();
            $table->integer('transcription_time_ms')->nullable();
            $table->text('extracted_json')->nullable();
            $table->float('extraction_accuracy')->nullable();
            $table->integer('extraction_time_ms')->nullable();
            $table->integer('total_time_ms')->nullable();
            $table->string('status')->nullable();
            $table->text('error_message')->nullable();
            $table->string('llm_provider')->nullable();
            $table->string('llm_transcription_model')->nullable();
            $table->string('llm_extraction_model')->nullable();
            $table->timestamp('processed_at')->nullable();
        });

        Schema::connection('experiments')->create('experiment1_extractions', function ($table) {
            $table->id();
            $table->integer('result_id')->nullable();
            $table->string('person')->nullable();
            $table->text('items')->nullable();
            $table->boolean('has_snow_peas')->nullable();
            $table->boolean('has_blue_cheese')->nullable();
            $table->boolean('has_snack')->nullable();
            $table->boolean('has_plastic_snake')->nullable();
            $table->boolean('has_toy_frog')->nullable();
            $table->string('brother')->nullable();
            $table->integer('bags_count')->nullable();
            $table->string('bags_color')->nullable();
            $table->string('meeting_day')->nullable();
            $table->string('meeting_location')->nullable();
        });

        Schema::connection('experiments')->create('experiment2_results', function ($table) {
            $table->id();
            $table->string('filename')->nullable();
            $table->float('audio_duration_seconds')->nullable();
            $table->text('transcript_text')->nullable();
            $table->float('wer')->nullable();
            $table->integer('transcription_time_ms')->nullable();
            $table->string('status')->nullable();
            $table->text('error_message')->nullable();
            $table->string('llm_provider')->nullable();
            $table->string('llm_transcription_model')->nullable();
            $table->timestamp('processed_at')->nullable();
        });
    }

    protected function silentWav(): string
    {
        $sampleRate = 8000;
        $samples = str_repeat("\0\0", 800);
        $dataSize = strlen($samples);

        return 'RIFF'
            . pack('V', 36 + $dataSize)
            . 'WAVEfmt '
            . pack('VvvVVvv', 16, 1, 1, $sampleRate, $sampleRate * 2, 2, 16)
            . 'data'
            . pack('V', $dataSize)
            . $samples;
    }
}
