<?php

namespace Tests\Feature;

use App\Console\Commands\RunExperimentBase;
use Illuminate\Support\Facades\Storage;

class RunExperimentBaseCommandTest extends ExperimentCommandTestCase
{
    public function test_reports_missing_directory(): void
    {
        Storage::fake('local');

        $this->artisan('app:run-experiment1', ['--provider' => 'openai'])
            ->expectsOutput('Recordings directory not found at ' . Storage::path('experiments/experiment1/recordings'))
            ->assertExitCode(0);
    }

    public function test_requires_provider(): void
    {
        Storage::fake('local');
        Storage::makeDirectory('experiments/experiment1/recordings');
        Storage::put('experiments/experiment1/recordings/sample.wav', $this->silentWav());

        $this->artisan('app:run-experiment1')
            ->expectsOutput('LLM provider not specified. Use eg. --provider=openai or --provider=mistral.')
            ->assertExitCode(0);
    }

    public function test_helpers_filter_audio_files_and_calculate_wer(): void
    {
        Storage::fake('local');
        Storage::makeDirectory('experiments/base');
        Storage::put('experiments/base/a.txt', 'text');
        Storage::put('experiments/base/b.MP3', $this->silentWav());
        Storage::put('experiments/base/c.wav', $this->silentWav());
        $command = new RunExperimentHarness();

        $files = $command->audioFiles(Storage::path('experiments/base'));

        $this->assertEqualsCanonicalizing(['b.MP3', 'c.wav'], $files);
        $this->assertEquals(0.0, $command->wer('', 'anything'));
        $this->assertEquals(0.0, $command->wer('Okay, now', 'ok now'));
        $this->assertEquals(50.0, $command->wer('one two', 'one three'));
        $this->assertNotNull($command->duration(Storage::path('experiments/base/c.wav')));
    }
}

class RunExperimentHarness extends RunExperimentBase
{
    protected $signature = 'test:run-experiment-harness {--limit=5} {--offset=0} {--provider=} {--api-key=} {--transcription-model=} {--extraction-model=}';

    protected function getExperimentPath(): string
    {
        return Storage::path('experiments/base');
    }

    protected function getResultsTable(): string
    {
        return 'experiment_results';
    }

    protected function processFile(string $path, string $fileName, string $llmProvider, ?string $llmApiKey, ?string $llmTranscriptionModel, ?string $llmExtractionModel, int $index): void
    {
        //
    }

    public function audioFiles(string $directoryPath): array
    {
        return $this->getAudioFiles($directoryPath);
    }

    public function wer(string $reference, string $hypothesis): float
    {
        return $this->calculateWer($reference, $hypothesis);
    }

    public function duration(string $filePath): ?float
    {
        return $this->getAudioDuration($filePath);
    }
}
