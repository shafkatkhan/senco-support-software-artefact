<?php

namespace App\Traits;

use App\Models\Attachment;

trait HasAttachments
{
    // cascade delete attachments when the model is deleted
    protected static function bootHasAttachments()
    {
        static::deleting(function ($model) {
            $model->attachments()->get()->each->delete();
        });
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    /**
     * Save multiple files as attachments for this model.
     *
     * @param array|\Illuminate\Http\UploadedFile|null $files
     * @return void
     */
    public function saveAttachments($files)
    {
        if (!$files) {
            return;
        }

        $files = is_array($files) ? $files : [$files];

        foreach ($files as $file) {
            $path = $file->store('attachments');
            $this->attachments()->create([
                'filename' => $file->getClientOriginalName(),
                'file_path' => $path,
                'mime_type' => $file->getClientMimeType(),
                'size_bytes' => $file->getSize(),
            ]);
        }
    }
}
