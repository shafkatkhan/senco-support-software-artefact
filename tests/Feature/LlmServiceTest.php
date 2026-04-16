<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Services\LlmService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class LlmServiceTest extends TestCase
{
    use RefreshDatabase;

    protected array $temporaryFiles = [];

    protected function tearDown(): void
    {
        foreach ($this->temporaryFiles as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }

        parent::tearDown();
    }

    public function test_transcribe_audio_throws_when_ai_features_are_disabled(): void
    {
        Setting::set('llm_provider', 'none');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('AI features are disabled.');

        LlmService::transcribeAudio($this->audioPath(), 'audio.mp3');
    }

    public function test_transcribe_audio_throws_when_api_key_is_missing(): void
    {
        Setting::set('llm_provider', 'openai');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('OPENAI_API_KEY is not set.');

        LlmService::transcribeAudio($this->audioPath(), 'audio.mp3');
    }

    public function test_transcribe_audio_with_gemini(): void
    {
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    ['content' => ['parts' => [['text' => 'Gemini transcript']]]],
                ],
            ]),
        ]);

        $transcript = LlmService::transcribeAudio($this->audioPath(), 'audio.mp3', 'gemini', 'gemini-test', 'secret');

        $this->assertEquals('Gemini transcript', $transcript);
        Http::assertSent(function ($request) {
            return str_contains((string) $request->url(), 'gemini-test:generateContent')
                && $request['contents'][0]['parts'][0]['text'] === 'Transcribe the following audio exactly as spoken.';
        });
    }

    public function test_transcribe_audio_throws_when_api_request_fails(): void
    {
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response('Bad request', 400),
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('API error: Bad request');

        LlmService::transcribeAudio($this->audioPath(), 'audio.mp3', 'gemini', null, 'secret');
    }

    public function test_transcribe_audio_with_openai_upload_endpoint(): void
    {
        Http::fake([
            'api.openai.com/v1/audio/transcriptions' => Http::response(['text' => 'OpenAI transcript']),
        ]);

        $transcript = LlmService::transcribeAudio($this->audioPath(), 'audio.mp3', 'openai', 'whisper-test', 'secret');

        $this->assertEquals('OpenAI transcript', $transcript);
        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.openai.com/v1/audio/transcriptions';
        });
    }

    public function test_transcribe_audio_with_mistral_diarize_upload_endpoint(): void
    {
        Http::fake([
            'api.mistral.ai/v1/audio/transcriptions' => Http::response(['text' => 'Mistral transcript']),
        ]);

        $transcript = LlmService::transcribeAudio($this->audioPath(), 'audio.mp3', 'mistral', 'voxtral-diarize-latest', 'secret');

        $this->assertEquals('Mistral transcript', $transcript);
        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.mistral.ai/v1/audio/transcriptions';
        });
    }

    public function test_transcribe_audio_with_mistral_voxtral_small_chat_endpoint(): void
    {
        Http::fake([
            'api.mistral.ai/v1/chat/completions' => Http::response([
                'choices' => [
                    ['message' => ['content' => 'Small model transcript']],
                ],
            ]),
        ]);

        $transcript = LlmService::transcribeAudio($this->audioPath(), 'audio.mp3', 'mistral', 'voxtral-small-latest', 'secret');

        $this->assertEquals('Small model transcript', $transcript);
    }

    public function test_transcribe_audio_throws_when_voxtral_small_request_fails(): void
    {
        Http::fake([
            'api.mistral.ai/v1/chat/completions' => Http::response('Bad request', 400),
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('API error: Bad request');

        LlmService::transcribeAudio($this->audioPath(), 'audio.mp3', 'mistral', 'voxtral-small-latest', 'secret');
    }

    public function test_transcribe_audio_throws_when_upload_request_fails(): void
    {
        Http::fake([
            'api.openai.com/v1/audio/transcriptions' => Http::response('Bad request', 400),
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('API error: Bad request');

        LlmService::transcribeAudio($this->audioPath(), 'audio.mp3', 'openai', 'whisper-test', 'secret');
    }

    public function test_transcribe_audio_throws_for_unsupported_provider(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Unsupported LLM provider: unknown');

        LlmService::transcribeAudio($this->audioPath(), 'audio.mp3', 'unknown', null, 'secret');
    }

    public function test_transcribe_audio_throws_when_no_transcript_is_generated(): void
    {
        Http::fake([
            'api.openai.com/v1/audio/transcriptions' => Http::response(['text' => '']),
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('No transcript text generated.');

        LlmService::transcribeAudio($this->audioPath(), 'audio.mp3', 'openai', 'whisper-test', 'secret');
    }

    public function test_process_request_throws_when_ai_features_are_disabled(): void
    {
        Setting::set('llm_provider', 'none');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('AI features are disabled.');

        LlmService::processRequest('Extract data');
    }

    public function test_process_request_throws_when_api_key_is_missing(): void
    {
        Setting::set('llm_provider', 'mistral');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('MISTRAL_API_KEY is not set.');

        LlmService::processRequest('Extract data');
    }

    public function test_process_request_with_gemini_file_and_instructions(): void
    {
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    ['content' => ['parts' => [['text' => '{"name":"Gemini"}']]]],
                ],
            ]),
        ]);

        $data = LlmService::processRequest('Extract data', 'Return JSON', $this->documentPath(), 'application/pdf', 'gemini', 'gemini-test', 'secret');

        $this->assertEquals(['name' => 'Gemini'], $data);
        Http::assertSent(function ($request) {
            return isset($request['systemInstruction'])
                && $request['contents'][0]['parts'][0]['inline_data']['mime_type'] === 'application/pdf';
        });
    }

    public function test_process_request_throws_when_gemini_request_fails(): void
    {
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response('Bad request', 400),
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('API error: Bad request');

        LlmService::processRequest('Extract data', null, null, null, 'gemini', null, 'secret');
    }

    public function test_process_request_with_openai_image_file(): void
    {
        Http::fake([
            'api.openai.com/v1/chat/completions' => Http::response([
                'choices' => [
                    ['message' => ['content' => '{"provider":"openai"}']],
                ],
            ]),
        ]);

        $data = LlmService::processRequest('Extract image data', null, $this->imagePath(), 'image/png', 'openai', null, 'secret');

        $this->assertEquals(['provider' => 'openai'], $data);
    }

    public function test_process_request_with_openai_document_file(): void
    {
        Http::fake([
            'api.openai.com/v1/chat/completions' => Http::response([
                'choices' => [
                    ['message' => ['content' => '{"provider":"openai-file"}']],
                ],
            ]),
        ]);

        $data = LlmService::processRequest('Extract file data', null, $this->documentPath(), 'application/pdf', 'openai', null, 'secret');

        $this->assertEquals(['provider' => 'openai-file'], $data);
    }

    public function test_process_request_with_mistral_image_and_document_files(): void
    {
        Http::fake([
            'api.mistral.ai/v1/chat/completions' => Http::sequence()
                ->push(['choices' => [['message' => ['content' => '{"kind":"image"}']]]])
                ->push(['choices' => [['message' => ['content' => '{"kind":"document"}']]]]),
        ]);

        $imageData = LlmService::processRequest('Extract image data', null, $this->imagePath(), 'image/png', 'mistral', null, 'secret');
        $documentData = LlmService::processRequest('Extract document data', null, $this->documentPath(), 'application/pdf', 'mistral', null, 'secret');

        $this->assertEquals(['kind' => 'image'], $imageData);
        $this->assertEquals(['kind' => 'document'], $documentData);
    }

    public function test_process_request_with_text_only_prompt(): void
    {
        Http::fake([
            'api.mistral.ai/v1/chat/completions' => Http::response([
                'choices' => [
                    ['message' => ['content' => '{"provider":"mistral"}']],
                ],
            ]),
        ]);

        $data = LlmService::processRequest('Extract text data', 'Return JSON', null, null, 'mistral', 'model-test', 'secret');

        $this->assertEquals(['provider' => 'mistral'], $data);
        Http::assertSent(function ($request) {
            return $request['model'] === 'model-test'
                && $request['messages'][0]['role'] === 'system'
                && $request['messages'][1]['role'] === 'user';
        });
    }

    public function test_process_request_throws_when_api_request_fails(): void
    {
        Http::fake([
            'api.openai.com/v1/chat/completions' => Http::response('Bad request', 400),
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('API error: Bad request');

        LlmService::processRequest('Extract data', null, null, null, 'openai', null, 'secret');
    }

    public function test_process_request_throws_for_unsupported_provider(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Unsupported LLM provider: unknown');

        LlmService::processRequest('Extract data', null, null, null, 'unknown', null, 'secret');
    }

    public function test_process_request_returns_empty_array_for_invalid_json(): void
    {
        Http::fake([
            'api.openai.com/v1/chat/completions' => Http::response([
                'choices' => [
                    ['message' => ['content' => 'not-json']],
                ],
            ]),
        ]);

        $data = LlmService::processRequest('Extract data', null, null, null, 'openai', null, 'secret');

        $this->assertEquals([], $data);
    }

    public function test_extract_data_from_audio_file_returns_transcript_and_data(): void
    {
        Setting::set('llm_provider', 'openai');
        Setting::set('llm_api_key', 'secret');
        Http::fake([
            'api.openai.com/v1/audio/transcriptions' => Http::response(['text' => 'Audio transcript']),
            'api.openai.com/v1/chat/completions' => Http::response([
                'choices' => [
                    ['message' => ['content' => '{"title":"Audio"}']],
                ],
            ]),
        ]);

        $extraction = LlmService::extractDataFromFile(
            UploadedFile::fake()->createWithContent('audio.mp3', 'audio-content'),
            '"title"'
        );

        $this->assertEquals('Audio transcript', $extraction['transcript']);
        $this->assertEquals(['title' => 'Audio'], $extraction['data']);
    }

    public function test_extract_data_from_document_file_returns_data_without_transcript(): void
    {
        Setting::set('llm_provider', 'openai');
        Setting::set('llm_api_key', 'secret');
        Http::fake([
            'api.openai.com/v1/chat/completions' => Http::response([
                'choices' => [
                    ['message' => ['content' => '{"title":"Document"}']],
                ],
            ]),
        ]);

        $extraction = LlmService::extractDataFromFile(
            UploadedFile::fake()->createWithContent('document.pdf', 'document-content'),
            '"title"'
        );

        $this->assertNull($extraction['transcript']);
        $this->assertEquals(['title' => 'Document'], $extraction['data']);
    }

    public function test_extract_and_respond_returns_successful_json(): void
    {
        Setting::set('llm_provider', 'openai');
        Setting::set('llm_api_key', 'secret');
        Http::fake([
            'api.openai.com/v1/chat/completions' => Http::response([
                'choices' => [
                    ['message' => ['content' => '{"title":"Document"}']],
                ],
            ]),
        ]);

        $request = Request::create('/extract', 'POST', [], [], [
            'file' => UploadedFile::fake()->createWithContent('document.pdf', 'document-content'),
        ]);

        $response = LlmService::extractAndRespond($request, '"title"');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($response->getData(true)['success']);
        $this->assertEquals(['title' => 'Document'], $response->getData(true)['data']);
    }

    public function test_extract_and_respond_returns_error_json(): void
    {
        Setting::set('llm_provider', 'none');
        $request = Request::create('/extract', 'POST', [], [], [
            'file' => UploadedFile::fake()->createWithContent('document.pdf', 'document-content'),
        ]);

        $response = LlmService::extractAndRespond($request, '"title"');

        $this->assertEquals(422, $response->getStatusCode());
        $this->assertFalse($response->getData(true)['success']);
        $this->assertEquals('AI features are disabled.', $response->getData(true)['error']);
    }

    protected function audioPath(): string
    {
        return $this->temporaryFile('audio-content');
    }

    protected function documentPath(): string
    {
        return $this->temporaryFile('document-content');
    }

    protected function imagePath(): string
    {
        return $this->temporaryFile('image-content');
    }

    protected function temporaryFile(string $contents): string
    {
        $path = tempnam(sys_get_temp_dir(), 'senco_llm_test_');
        file_put_contents($path, $contents);
        $this->temporaryFiles[] = $path;

        return $path;
    }
}
