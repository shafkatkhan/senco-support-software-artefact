<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttachmentTranscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'attachment_id',
        'transcript',
    ];

    public function attachment()
    {
        return $this->belongsTo(Attachment::class);
    }
}
