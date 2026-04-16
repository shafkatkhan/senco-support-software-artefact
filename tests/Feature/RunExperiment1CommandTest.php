<?php

namespace Tests\Feature;

use App\Console\Commands\RunExperiment1;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class RunExperiment1CommandTest extends ExperimentCommandTestCase
{
    public function test_processes_audio_and_extraction_results(): void
    {
        $this->prepareExperimentsDatabase();
        Storage::fake('local');
        Storage::makeDirectory('experiments/experiment1/recordings');
        Storage::put('experiments/experiment1/recordings/afrikaans1.wav', $this->silentWav());
        Http::fake([
            '*' => Http::sequence()
                ->push(['text' => 'Please call Stella. Ask her to bring snow peas.'], 200)
                ->push([
                    'choices' => [[
                        'message' => [
                            'content' => json_encode([
                                'person' => 'Stella',
                                'items' => ['snow peas', 'blue cheese', 'snack', 'plastic snake', 'toy frog'],
                                'brother' => 'Bob',
                                'bags_count' => '3',
                                'bags_color' => 'RED',
                                'meeting_day' => 'Wednesday',
                                'meeting_location' => 'train station',
                            ]),
                        ],
                    ]],
                ], 200),
        ]);

        $this->artisan('app:run-experiment1', [
            '--provider' => 'openai',
            '--api-key' => 'secret',
            '--transcription-model' => 'whisper-1',
            '--extraction-model' => 'gpt-test',
        ])
            ->expectsOutput('Found 1 total audio files. Processing 1 files starting from offset 0.')
            ->expectsOutput('1: Processing afrikaans1.wav...')
            ->expectsOutput('Experiment batch completed.')
            ->assertExitCode(0);

        $this->assertDatabaseHas('experiment1_results', [
            'filename' => 'afrikaans1.wav',
            'speaker_native_language' => 'afrikaans',
            'status' => 'success',
            'llm_provider' => 'openai',
            'llm_transcription_model' => 'whisper-1',
            'llm_extraction_model' => 'gpt-test',
        ], 'experiments');
        $this->assertDatabaseHas('experiment1_extractions', [
            'person' => 'Stella',
            'brother' => 'Bob',
            'bags_count' => 3,
            'bags_color' => 'RED',
            'meeting_day' => 'Wednesday',
            'meeting_location' => 'train station',
        ], 'experiments');
    }

    public function test_records_failed_processing(): void
    {
        $this->prepareExperimentsDatabase();
        Storage::fake('local');
        Storage::makeDirectory('experiments/experiment1/recordings');
        Storage::put('experiments/experiment1/recordings/english1.wav', $this->silentWav());
        Http::fake(['*' => Http::response('Broken', 500)]);

        $this->artisan('app:run-experiment1', [
            '--provider' => 'openai',
            '--api-key' => 'secret',
            '--transcription-model' => 'whisper-1',
            '--extraction-model' => 'gpt-test',
        ])
            ->expectsOutput('1: Processing english1.wav...')
            ->expectsOutput('Failed processing english1.wav: API error: Broken')
            ->expectsOutput('Experiment batch completed.')
            ->assertExitCode(0);

        $this->assertDatabaseHas('experiment1_results', [
            'filename' => 'english1.wav',
            'status' => 'failed',
            'error_message' => 'API error: Broken',
        ], 'experiments');
    }

    public function test_skips_existing_successful_result(): void
    {
        $this->prepareExperimentsDatabase();
        Storage::fake('local');
        Storage::makeDirectory('experiments/experiment1/recordings');
        Storage::put('experiments/experiment1/recordings/english1.wav', $this->silentWav());
        DB::connection('experiments')->table('experiment1_results')->insert([
            'filename' => 'english1.wav',
            'llm_transcription_model' => 'whisper-1',
            'llm_extraction_model' => 'gpt-test',
            'status' => 'success',
        ]);

        $this->artisan('app:run-experiment1', [
            '--provider' => 'openai',
            '--api-key' => 'secret',
            '--transcription-model' => 'whisper-1',
            '--extraction-model' => 'gpt-test',
        ])
            ->expectsOutput('Skipping english1.wav - already processed with whisper-1 + gpt-test.')
            ->assertExitCode(0);
    }

    public function test_extraction_accuracy_helper_handles_edge_cases(): void
    {
        $command = new RunExperiment1();
        $method = new \ReflectionMethod($command, 'calculateExtractionAccuracy');
        $method->setAccessible(true);

        $this->assertEquals(0, $method->invoke($command, [], []));
        $this->assertEquals(75.0, $method->invoke($command, [
            'person' => 'Stella',
            'items' => ['snow peas', 'blue cheese'],
            'bags_count' => 3,
        ], [
            'person' => ' stella ',
            'items' => ['Snow Peas'],
            'bags_count' => '3',
        ]));
    }
}
