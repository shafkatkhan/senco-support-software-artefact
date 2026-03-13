<div class="form-group mb-3">
    <label>{{ isset($for_create) ? 'Additional Attachments' : 'Add Additional Attachments' }}</label>
    <input type="file" class="form-control" name="{{ $name ?? 'additional_attachments[]' }}" multiple>
    <small class="form-text text-muted">{{ isset($for_create) ? 'You can select multiple files. Any files uploaded to the file extraction box will also be saved.' : 'You can select multiple files to be added to the existing attachments.' }}</small>
</div>