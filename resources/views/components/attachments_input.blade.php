<div class="form-group mb-3">
    <label>{{ isset($for_create) ? __('Additional Attachments') : __('Add Additional Attachments') }}</label>
    <input type="file" class="form-control" name="additional_attachments[]" multiple>
    <small class="form-text text-muted">{{ isset($for_create) ? __('You can select multiple files. Any files uploaded to the file extraction box will also be saved.') : __('You can select multiple files to be added to the existing attachments.') }}</small>
</div>
<div class="form-group mb-3">
    <div class="form-check">
        <input id="attachment_recording_consent_checkbox" type="checkbox" class="form-check-input attachment_consent_checkbox" name="attachment_recording_consent" value="1" disabled>
        <label class="form-check-label" for="attachment_recording_consent_checkbox">
            {{ __('I confirm that consent has been obtained from all recorded parties.') }}
        </label>
    </div>
    <small class="form-text text-muted">
        {{ __('This confirmation is only required when an audio file is uploaded.') }}
    </small>
</div>