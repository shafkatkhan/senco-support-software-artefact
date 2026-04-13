<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Services\LlmService;

abstract class RunExperimentBase extends Command
{
    /**
     * Audio file extensions to include when scanning directories.
     */
    protected $audioExtensions = ['mp3', 'wav', 'ogg', 'flac', 'aac', 'm4a', 'wma', 'webm'];

    /**
     * Whether this experiment only performs transcription (with no extraction).
     */
    protected $transcriptionOnly = false;

    /**
     * Get the storage path for this experiment's files.
     */
    abstract protected function getExperimentPath(): string;

    /**
     * Get the database table name for results.
     */
    abstract protected function getResultsTable(): string;

    /**
     * Process a single audio file (implemented by each experiment).
     */
    abstract protected function processFile(string $path, string $fileName, string $llmProvider, ?string $llmApiKey, ?string $llmTranscriptionModel, ?string $llmExtractionModel, int $index): void;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limit = $this->option('limit');
        $offset = $this->option('offset');

        $recordingsPath = $this->getExperimentPath();

        if (!is_dir($recordingsPath)) {
            $this->error("Recordings directory not found at {$recordingsPath}");
            return;
        }

        $files = $this->getAudioFiles($recordingsPath);

        // apply offset and limit
        $audioFilesToProcess = array_slice($files, $offset, $limit);

        $llm_provider = $this->option('provider');
        if ($llm_provider == null) {
            $this->error("LLM provider not specified. Use eg. --provider=openai or --provider=mistral.");
            return;
        }

        $llm_api_key = $this->option('api-key');
        $llm_transcription_model = $this->option('transcription-model');
        $llm_extraction_model = $this->hasOption('extraction-model') ? $this->option('extraction-model') : null;

        $this->info("Found " . count($files) . " total audio files. Processing " . count($audioFilesToProcess) . " files starting from offset {$offset}.");
        

        $i = 1;
        foreach ($audioFilesToProcess as $fileName) {
            $this->processFile($recordingsPath, $fileName, $llm_provider, $llm_api_key, $llm_transcription_model, $llm_extraction_model, $i);
            $i++;
        }

        $this->info("Experiment batch completed.");
    }

    /**
     * Get sorted, shuffled (deterministic seed) audio files from a directory.
     */
    protected function getAudioFiles(string $directoryPath): array
    {
        $files = array_diff(scandir($directoryPath), array('..', '.'));

        // sort files to ensure consistent ordering when using offset
        // randomise file order with fixed seed, so that processing order is reproducible
        sort($files);
        mt_srand(42);
        shuffle($files);

        $files = array_values($files);

        // filter for audio files only, ignores files like .DS_Store
        $files = array_filter($files, function ($file) {
            return in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), $this->audioExtensions);
        });

        return array_values($files);
    }

    /**
     * Check if a successful result already exists for this file + model combination.
     */
    protected function resultExists(string $fileName, ?string $llmTranscriptionModel, ?string $llmExtractionModel): bool
    {
        $query = DB::connection('experiments')->table($this->getResultsTable())
            ->where('filename', $fileName)
            ->where('llm_transcription_model', $llmTranscriptionModel)
            ->where('status', 'success');

        if (!$this->transcriptionOnly) {
            $query->where('llm_extraction_model', $llmExtractionModel);
        }

        return $query->exists();
    }

    /**
     * Calculate Word Error Rate between a reference and hypothesis transcription.
     * Returns WER as a percentage (0-100+).
     */
    protected function calculateWer(string $reference, string $hypothesis): float
    {
        // lowercase and remove punctuation for fairer comparison
        $reference = strtolower(preg_replace('/[[:punct:]]/', '', $reference));
        $hypothesis = strtolower(preg_replace('/[[:punct:]]/', '', $hypothesis));

        // normalise equivalent words
        $reference = preg_replace('/\bokay\b/', 'ok', $reference);
        $hypothesis = preg_replace('/\bokay\b/', 'ok', $hypothesis);

        $ref_words = array_values(array_filter(preg_split('/\s+/', $reference)));
        $hyp_words = array_values(array_filter(preg_split('/\s+/', $hypothesis)));

        $n = count($ref_words);
        $m = count($hyp_words);

        // build DP table for word-level Levenshtein distance
        $dp = [];
        for ($i = 0; $i <= $n; $i++) {
            $dp[$i] = array_fill(0, $m + 1, 0);
        }

        // base cases
        for ($i = 0; $i <= $n; $i++) $dp[$i][0] = $i; // deletions
        for ($j = 0; $j <= $m; $j++) $dp[0][$j] = $j; // insertions

        // fill DP table
        for ($i = 1; $i <= $n; $i++) {
            for ($j = 1; $j <= $m; $j++) {
                if ($ref_words[$i - 1] === $hyp_words[$j - 1]) {
                    $dp[$i][$j] = $dp[$i - 1][$j - 1]; // no edit needed
                } else {
                    $dp[$i][$j] = min(
                        $dp[$i - 1][$j] + 1,     // deletion
                        $dp[$i][$j - 1] + 1,     // insertion
                        $dp[$i - 1][$j - 1] + 1  // substitution
                    );
                }
            }
        }

        if ($n == 0) return 0;

        $wer = $dp[$n][$m] / $n;

        return round($wer * 100, 4);
    }

    /**
     * Get audio duration in seconds using ffprobe (from ffmpeg library).
     */
    protected function getAudioDuration(string $filePath): ?float
    {
        $cmd = "ffprobe -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 " . escapeshellarg($filePath);
        $duration = shell_exec($cmd);

        return $duration ? floatval(trim($duration)) : null;
    }
}
