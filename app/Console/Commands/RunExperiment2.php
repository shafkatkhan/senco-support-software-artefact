<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Services\LlmService;

class RunExperiment2 extends RunExperimentBase
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:run-experiment2 {--limit=5 : The number of recordings to process} {--offset=0 : The offset to start from} {--provider= : The LLM provider to use (openai, mistral, or gemini)} {--api-key= : The API key to use} {--transcription-model= : The transcription model to use}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs transcription experiments on audio recordings with per-file ground truth transcripts';

    protected function getExperimentPath(): string
    {
        return Storage::path('experiments/experiment2');
    }

    protected function getResultsTable(): string
    {
        return 'experiment2_results';
    }

    /**
     * This experiment only performs transcription (no extraction).
     */
    protected $transcriptionOnly = true;

    protected function processFile(string $path, string $fileName, string $llmProvider, ?string $llmApiKey, ?string $llmTranscriptionModel, ?string $llmExtractionModel, int $index): void
    {
        gc_collect_cycles(); // free memory between files

        ########################################################################

        // skip if a successful result already exists for this file + model
        if ($this->resultExists($fileName, $llmTranscriptionModel, $llmExtractionModel)) {
            $this->info("Skipping {$fileName} - already processed with {$llmTranscriptionModel}.");
            return;
        }

        $fullPath = $path . '/' . $fileName;
        $this->info("{$index}: Processing {$fileName}...");

        // load the ground truth transcript from the corresponding .txt file
        $groundTruthPath = $path . '/' . pathinfo($fileName, PATHINFO_FILENAME) . '.txt';
        if (!file_exists($groundTruthPath)) {
            $this->error("Ground truth file not found for {$fileName} at {$groundTruthPath}. Skipping.");
            return;
        }
        $expectedTranscription = file_get_contents($groundTruthPath);
        
        // convert files to UTF-8 so the WER calculation doesn't break due to null bytes
        $encoding = mb_detect_encoding($expectedTranscription, ['UTF-8', 'UTF-16LE', 'UTF-16BE'], true);
        if ($encoding && $encoding !== 'UTF-8') {
            $expectedTranscription = mb_convert_encoding($expectedTranscription, 'UTF-8', $encoding);
        }
        
        // strip out any invisible Byte Order Marks
        $expectedTranscription = preg_replace('/^\xEF\xBB\xBF/', '', $expectedTranscription);

        // filter out diarisation speaker labels like "D: " or "P: " at the start of lines
        $expectedTranscription = preg_replace('/^[A-Za-z]+:\s*/m', '', $expectedTranscription);
        $expectedTranscription = trim($expectedTranscription);

        // calculate audio duration using ffprobe
        $audioDurationSeconds = $this->getAudioDuration($fullPath) ?? 0;

        $transcript = '';
        $wer = 0;
        $status = 'success';
        $errorMessage = null;
        $transcriptionTimeMs = 0;
        $transcriptionTimeStart = microtime(true);

        try {
            // transcription
            $transcript = LlmService::transcribeAudio($fullPath, $fileName, $llmProvider, $llmTranscriptionModel, $llmApiKey);
            $transcriptionTimeEnd = microtime(true);
            $transcriptionTimeMs = round(($transcriptionTimeEnd - $transcriptionTimeStart) * 1000);

            // calculate WER against the given ground truth
            $wer = $this->calculateWer($expectedTranscription, $transcript);

        } catch (\Exception $e) {
            $status = 'failed';
            $errorMessage = $e->getMessage();
            $this->error("Failed processing {$fileName}: {$errorMessage}");

            if ($transcriptionTimeMs == 0) {
                $transcriptionTimeMs = round((microtime(true) - $transcriptionTimeStart) * 1000);
            }
        }

        // insert into DB
        DB::connection('experiments')->table('experiment2_results')->insert([
            'filename' => $fileName,
            'audio_duration_seconds' => $audioDurationSeconds,
            'transcript_text' => $transcript,
            'wer' => $wer,
            'transcription_time_ms' => $transcriptionTimeMs,
            'status' => $status,
            'error_message' => $errorMessage,
            'llm_provider' => $llmProvider,
            'llm_transcription_model' => $llmTranscriptionModel,
            'processed_at' => now(),
        ]);

        $this->info("Completed {$fileName}. Status: {$status}. Time: {$transcriptionTimeMs}ms");
    }
}
