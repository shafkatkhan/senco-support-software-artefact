@extends('layouts.app')

@section('content')
    <section id="content">
        <div class="settings_wrap">
            <form action="{{ route('llm-settings.update') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="settings_section">
                    <div class="title">{{ __('LLM Settings') }}</div>
                    <div class="description">{{ __('Choose the AI provider and configure the API key to utilise data extraction features.') }}</div>

                    <div class="settings_options">
                        <label class="settings_option llm_option {{ $llm_provider === 'none' ? 'active' : '' }}">
                            <input type="radio" name="llm_provider" value="none" {{ $llm_provider === 'none' ? 'checked' : '' }}>
                            <div class="settings_option_content">
                                <div class="settings_option_icon">
                                    <i class="fas fa-ban text-danger" style="font-size: 25px;"></i>
                                </div>
                                <div class="text">
                                    <div class="settings_option_title">
                                        {{ __('None') }}
                                    </div>
                                    <div class="settings_option_description">
                                        {{ __('No AI features.') }}
                                    </div>
                                </div>
                            </div>
                        </label>
                        <label class="settings_option llm_option {{ $llm_provider === 'openai' ? 'active' : '' }}">
                            <input type="radio" name="llm_provider" value="openai" {{ $llm_provider === 'openai' ? 'checked' : '' }}>
                            <div class="settings_option_content">
                                <div class="settings_option_icon">
                                    <img src="{{ asset('img/openai.png') }}" alt="OpenAI Logo" style="width: 65%;">
                                </div>
                                <div class="text">
                                    <div class="settings_option_title">
                                        OpenAI
                                    </div>
                                    <div class="settings_option_description">
                                        <a href="https://openai.com/api/" target="_blank">openai.com/api</a>
                                    </div>
                                </div>
                            </div>
                        </label>
                        <label class="settings_option llm_option {{ $llm_provider === 'mistral' ? 'active' : '' }}">
                            <input type="radio" name="llm_provider" value="mistral" {{ $llm_provider === 'mistral' ? 'checked' : '' }}>
                            <div class="settings_option_content">
                                <div class="settings_option_icon">
                                    <img src="{{ asset('img/mistral.png') }}" alt="Mistral Logo" style="width: 90%;">
                                </div>
                                <div class="text">
                                    <div class="settings_option_title">
                                        Mistral AI
                                    </div>
                                    <div class="settings_option_description">
                                        <a href="https://docs.mistral.ai" target="_blank">docs.mistral.ai</a>
                                    </div>
                                </div>
                            </div>
                        </label>
                        <label class="settings_option llm_option {{ $llm_provider === 'gemini' ? 'active' : '' }}">
                            <input type="radio" name="llm_provider" value="gemini" {{ $llm_provider === 'gemini' ? 'checked' : '' }}>
                            <div class="settings_option_content">
                                <div class="settings_option_icon">
                                    <img src="{{ asset('img/gemini.png') }}" alt="Gemini Logo" style="width: 65%;">
                                </div>
                                <div class="text">
                                    <div class="settings_option_title">
                                        Google Gemini
                                    </div>
                                    <div class="settings_option_description">
                                        <a href="https://aistudio.google.com/" target="_blank">aistudio.google.com</a>
                                    </div>
                                </div>
                            </div>
                        </label>
                    </div>

                    <div class="form-group mt-4">
                        <label for="llm_api_key" class="form-label" style="font-weight: 600;">
                            {{ __('API Key') }}
                        </label>
                        <input type="text" class="form-control" name="llm_api_key" id="llm_api_key" value="{{ old('llm_api_key', $llm_api_key) }}" required placeholder="sk-ABC123...">
                        <div class="form-text text-muted">
                            {{ __('Your private API key for the selected provider.') }}
                        </div>
                    </div>
                </div>

                <div class="settings_actions">
                    <button type="submit" class="btn btn-success">{{ __('Save Changes') }}</button>
                </div>
            </form>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('.settings_option input[type="radio"]').on('change', function () {
                $('.settings_option').removeClass('active');
                $(this).closest('.settings_option').addClass('active');
                checkLlmProvider();
            });

            function checkLlmProvider() {
                var val = $('input[name="llm_provider"]:checked').val();
                if (val == 'none') {
                    $('#llm_api_key').prop('required', false).prop('disabled', true);
                } else {
                    $('#llm_api_key').prop('required', true).prop('disabled', false);
                }
            }
            checkLlmProvider();
        });
    </script>
@endpush
