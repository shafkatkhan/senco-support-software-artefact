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
}
