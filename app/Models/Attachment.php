<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    use HasFactory;

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
