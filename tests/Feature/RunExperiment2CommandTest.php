<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class RunExperiment2CommandTest extends ExperimentCommandTestCase
{
    public function test_processes_audio_with_ground_truth_transcript(): void
    {
        $this->prepareExperimentsDatabase();
        Storage::fake('local');
        Storage::makeDirectory('experiments/experiment2');
        Storage::put('experiments/experiment2/session1.wav', $this->silentWav());
        Storage::put('experiments/experiment2/session1.txt', mb_convert_encoding("D: Hello.\nP: How are you.", 'UTF-16LE', 'UTF-8'));
        Http::fake([
            '*' => Http::response(['text' => 'Hello how are you'], 200),
        ]);

        $this->artisan('app:run-experiment2', [
            '--provider' => 'openai',
            '--api-key' => 'secret',
            '--transcription-model' => 'whisper-1',
        ])
            ->expectsOutput('Found 1 total audio files. Processing 1 files starting from offset 0.')
            ->expectsOutput('1: Processing session1.wav...')
            ->expectsOutput('Experiment batch completed.')
            ->assertExitCode(0);

        $this->assertDatabaseHas('experiment2_results', [
            'filename' => 'session1.wav',
            'transcript_text' => 'Hello how are you',
            'status' => 'success',
            'llm_provider' => 'openai',
            'llm_transcription_model' => 'whisper-1',
        ], 'experiments');
    }

    public function test_skips_when_ground_truth_file_is_missing(): void
    {
        $this->prepareExperimentsDatabase();
        Storage::fake('local');
        Storage::makeDirectory('experiments/experiment2');
        Storage::put('experiments/experiment2/session1.wav', $this->silentWav());

        $this->artisan('app:run-experiment2', [
            '--provider' => 'openai',
            '--api-key' => 'secret',
            '--transcription-model' => 'whisper-1',
        ])
            ->expectsOutput('1: Processing session1.wav...')
            ->expectsOutput('Ground truth file not found for session1.wav at ' . Storage::path('experiments/experiment2') . '/session1.txt. Skipping.')
            ->assertExitCode(0);

        $this->assertDatabaseCount('experiment2_results', 0, 'experiments');
    }

    public function test_records_failed_processing(): void
    {
        $this->prepareExperimentsDatabase();
        Storage::fake('local');
        Storage::makeDirectory('experiments/experiment2');
        Storage::put('experiments/experiment2/session1.wav', $this->silentWav());
        Storage::put('experiments/experiment2/session1.txt', 'Transcript');
        Http::fake(['*' => Http::response('Broken', 500)]);

        $this->artisan('app:run-experiment2', [
            '--provider' => 'openai',
            '--api-key' => 'secret',
            '--transcription-model' => 'whisper-1',
        ])
            ->expectsOutput('1: Processing session1.wav...')
            ->expectsOutput('Failed processing session1.wav: API error: Broken')
            ->assertExitCode(0);

        $this->assertDatabaseHas('experiment2_results', [
            'filename' => 'session1.wav',
            'status' => 'failed',
            'error_message' => 'API error: Broken',
        ], 'experiments');
    }

    public function test_skips_existing_successful_result(): void
    {
        $this->prepareExperimentsDatabase();
        Storage::fake('local');
        Storage::makeDirectory('experiments/experiment2');
        Storage::put('experiments/experiment2/session1.wav', $this->silentWav());
        Storage::put('experiments/experiment2/session1.txt', 'Transcript');
        DB::connection('experiments')->table('experiment2_results')->insert([
            'filename' => 'session1.wav',
            'llm_transcription_model' => 'whisper-1',
            'status' => 'success',
        ]);

        $this->artisan('app:run-experiment2', [
            '--provider' => 'openai',
            '--api-key' => 'secret',
            '--transcription-model' => 'whisper-1',
        ])
            ->expectsOutput('Skipping session1.wav - already processed with whisper-1.')
            ->assertExitCode(0);
    }
}
