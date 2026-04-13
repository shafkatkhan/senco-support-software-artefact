<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Services\LlmService;

class RunExperiment1 extends RunExperimentBase
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:run-experiment1 {--limit=5 : The number of recordings to process} {--offset=0 : The offset to start from} {--provider= : The LLM provider to use (openai, mistral, or gemini)} {--api-key= : The API key to use} {--transcription-model= : The transcription model to use} {--extraction-model= : The extraction model to use}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs transcription and extraction experiments on a batch of audio recordings';

    /**
     * The known transcription of the reading passage.
     */
    protected $expectedTranscription = "Please call Stella. Ask her to bring these things with her from the store: Six spoons of fresh snow peas, five thick slabs of blue cheese, and maybe a snack for her brother Bob. We also need a small plastic snake and a big toy frog for the kids. She can scoop these things into three red bags, and we will go meet her Wednesday at the train station.";

    /**
     * The known extraction expected values.
     */
    protected $expectedExtraction = [
        'person' => 'Stella',
        'items' => [
            'snow peas',
            'blue cheese',
            'snack',
            'plastic snake',
            'toy frog'
        ],
        'brother' => 'Bob',
        'bags_count' => 3,
        'bags_color' => 'red',
        'meeting_day' => 'Wednesday',
        'meeting_location' => 'train station'
    ];

    protected function getExperimentPath(): string
    {
        return Storage::path('experiments/experiment1/recordings');
    }

    protected function getResultsTable(): string
    {
        return 'experiment1_results';
    }

    protected function processFile(string $path, string $fileName, string $llmProvider, ?string $llmApiKey, ?string $llmTranscriptionModel, ?string $llmExtractionModel, int $index): void
    {
        gc_collect_cycles(); // free memory between files

        ########################################################################

        // skip if a successful result already exists for this file, with both models
        if ($this->resultExists($fileName, $llmTranscriptionModel, $llmExtractionModel)) {
            $this->info("Skipping {$fileName} - already processed with {$llmTranscriptionModel} + {$llmExtractionModel}.");
            return;
        }

        $fullPath = $path . '/' . $fileName;
        $this->info("{$index}: Processing {$fileName}...");

        // determine speaker native language from filename (e.g., afrikaans1.mp3 -> afrikaans)
        $speakerNativeLanguage = preg_replace('/[0-9]+\.[a-zA-Z0-9]+$/', '', $fileName);

        // calculate audio duration using ffprobe
        $audioDurationSeconds = $this->getAudioDuration($fullPath) ?? 0; 
        
        $transcript = '';
        $wer = 0;
        $status = 'success';
        $errorMessage = null;
        $extractedJson = '';
        $extractionAccuracy = 0;
        $extractionTimeMs = 0;
        $transcriptionTimeMs = 0;
        $transcriptionTimeStart = microtime(true);

        try {
            // transcription
            $transcript = LlmService::transcribeAudio($fullPath, $fileName, $llmProvider, $llmTranscriptionModel, $llmApiKey);
            $transcriptionTimeEnd = microtime(true);
            $transcriptionTimeMs = round(($transcriptionTimeEnd - $transcriptionTimeStart) * 1000);

            // calculate WER
            $wer = $this->calculateWer($this->expectedTranscription, $transcript);

            // extraction
            $instructions = "
                person (name of the main person mentioned),
                items (array of specific food/items/toys mentioned, excluding quantity or descriptive words),
                brother (name of the brother mentioned),
                bags_count (number of bags mentioned as an integer),
                bags_color (colour of the bags mentioned),
                meeting_day (day of the meeting),
                meeting_location (location of the meeting)
            ";            
            $jsonInstructions = 
                "Return a JSON object with EXACTLY these keys: " . 
                $instructions . 
                "Do not guess. Use null if missing.";
            $extractionTimeStart = microtime(true);
            $extractedData = LlmService::processRequest($transcript, $jsonInstructions, null, null, $llmProvider, $llmExtractionModel, $llmApiKey);
            $extractionTimeEnd = microtime(true);
            $extractionTimeMs = round(($extractionTimeEnd - $extractionTimeStart) * 1000);

            $extractedJson = json_encode($extractedData);

            // calculate extraction accuracy
            $extractionAccuracy = $this->calculateExtractionAccuracy($this->expectedExtraction, $extractedData);

        } catch (\Exception $e) {
            $status = 'failed';
            $errorMessage = $e->getMessage();
            $this->error("Failed processing {$fileName}: {$errorMessage}");
            
            if ($transcriptionTimeMs == 0) {
                $transcriptionTimeMs = round((microtime(true) - $transcriptionTimeStart) * 1000);
            }
        }

        $totalTimeMs = $transcriptionTimeMs + $extractionTimeMs;

        // insert into DB
        $resultId = DB::connection('experiments')->table('experiment1_results')->insertGetId([
            'filename' => $fileName,
            'speaker_native_language' => $speakerNativeLanguage,
            'audio_duration_seconds' => $audioDurationSeconds,
            'transcript_text' => $transcript,
            'wer' => $wer,
            'transcription_time_ms' => $transcriptionTimeMs,
            'extracted_json' => $extractedJson,
            'extraction_accuracy' => $extractionAccuracy,
            'extraction_time_ms' => $extractionTimeMs,
            'total_time_ms' => $totalTimeMs,
            'status' => $status,
            'error_message' => $errorMessage,
            'llm_provider' => $llmProvider,
            'llm_transcription_model' => $llmTranscriptionModel,
            'llm_extraction_model' => $llmExtractionModel,
            'processed_at' => now(),
        ]);

        // insert individual extraction fields
        if (!empty($extractedData)) {
            DB::connection('experiments')->table('experiment1_extractions')->insert([
                'result_id' => $resultId,
                'person' => $extractedData['person'] ?? null,
                'items' => isset($extractedData['items']) ? json_encode($extractedData['items']) : null,
                'has_snow_peas' => isset($extractedData['items']) && in_array('snow peas', array_map('strtolower', $extractedData['items'])),
                'has_blue_cheese' => isset($extractedData['items']) && in_array('blue cheese', array_map('strtolower', $extractedData['items'])),
                'has_snack' => isset($extractedData['items']) && in_array('snack', array_map('strtolower', $extractedData['items'])),
                'has_plastic_snake' => isset($extractedData['items']) && in_array('plastic snake', array_map('strtolower', $extractedData['items'])),
                'has_toy_frog' => isset($extractedData['items']) && in_array('toy frog', array_map('strtolower', $extractedData['items'])),
                'brother' => $extractedData['brother'] ?? null,
                'bags_count' => $extractedData['bags_count'] ?? null,
                'bags_color' => $extractedData['bags_color'] ?? null,
                'meeting_day' => $extractedData['meeting_day'] ?? null,
                'meeting_location' => $extractedData['meeting_location'] ?? null,
            ]);
        }
        
        $this->info("Completed {$fileName}. Status: {$status}. Total Time: {$totalTimeMs}ms");
    }

    private function calculateExtractionAccuracy($expected, $actual)
    {
        if (empty($expected)) return 0;
        
        // count total keys, expanding arrays so each element counts individually
        $totalKeys = 0;
        $correctMatches = 0;

        foreach ($expected as $key => $expectedValue) {
            $actualValue = $actual[$key] ?? null;

            if (is_array($expectedValue)) {
                // each array item counts as its own key
                $totalKeys += count($expectedValue);

                if (is_array($actualValue)) {
                    $expectedArray = array_map(function($val) { return strtolower(trim($val)); }, $expectedValue);
                    $actualArray = array_map(function($val) { return strtolower(trim($val)); }, $actualValue);

                    foreach ($expectedArray as $item) {
                        if (in_array($item, $actualArray)) {
                            $correctMatches++;
                        }
                    }
                }
                // if actualValue is not an array, 0 matches for all items in this key
            } else {
                $totalKeys++;

                if ($expectedValue === $actualValue) {
                    $correctMatches++;
                } else if (is_string($expectedValue) && is_string($actualValue) && strcasecmp(trim($expectedValue), trim($actualValue)) == 0) {
                    $correctMatches++;
                } else if ($expectedValue == $actualValue) { // loose comparison for integers like bags_count vs "3"
                    $correctMatches++;
                }
            }
        }

        if ($totalKeys == 0) return 0;

        return round(($correctMatches / $totalKeys) * 100, 4);
    }
}
