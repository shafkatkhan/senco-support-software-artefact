<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Attachment extends Model
{
    use HasFactory;

    // cascade delete attachments from storage when the attachment is deleted
    protected static function booted()
    {
        static::deleting(function ($attachment) {
            if ($attachment->file_path) {
                Storage::delete($attachment->file_path);
            }
        });
    }

    protected $fillable = [
        'attachable_type',
        'attachable_id',
        'filename',
        'file_path',
        'mime_type',
        'size_bytes',
    ];
    
    public function attachable()
    {
        return $this->morphTo();
    }

    public function transcription()
    {
        return $this->hasOne(AttachmentTranscription::class);
    }
}
