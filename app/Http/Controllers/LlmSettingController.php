<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class LlmSettingController extends Controller
{
    public function index()
    {
        $title = __('LLM Settings');
        $llm_provider = Setting::get('llm_provider');
        $llm_api_key = Setting::get('llm_api_key');

        return view('llm_settings', compact('title', 'llm_provider', 'llm_api_key'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'llm_provider' => 'required|in:none,openai,mistral,gemini',
            'llm_api_key' => 'required_unless:llm_provider,none',
        ]);

        Setting::set('llm_provider', $request->llm_provider);
        Setting::set('llm_api_key', $request->llm_api_key);

        return back()->with('success', __(':type settings updated successfully!', ['type' => 'LLM']));
    }
}
