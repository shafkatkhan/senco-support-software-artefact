<div class="file_extraction_box">
    <div class="upload_icon"><i class="fas fa-upload"></i></div>
    <div class="label">
        {{ __('Click to upload, or drag & drop a file here for smart data extraction') }}
    </div>
    <div class="filename"></div>
    <button type="button" disabled>
        {{ __('Extract Data') }}
    </button>
    <div class="status"></div>
    <!-- hidden input for form submission -->
    <input type="file" name="llm_attachment" style="display: none;">
</div>

<div class="transcript_wrapper">
    <div class="transcript_container mt-3" style="display: none;">
        <label class="d-flex justify-content-between align-items-center">
            {{ __('AI-Extracted Transcript') }}
            <button type="button" class="btn btn-sm btn-link text-decoration-none toggle_transcript_btn">{{ __('Hide Transcript') }}</button>
        </label>
        <textarea name="llm_transcript" class="form-control" rows="5" placeholder="{{ __('AI-Extracted Transcript') }}"></textarea>
    </div>
    <div class="d-flex justify-content-end">
        <button type="button" class="btn btn-sm btn-outline-primary show_transcript_btn" style="display: none;">
            {{ __('Show/Edit Transcript') }}
        </button>
    </div>
</div>