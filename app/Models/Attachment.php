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

    public function getSourceNameAttribute()
    {
        if ($this->attachable) {
            $type = class_basename($this->attachable_type);
            $model = $this->attachable;
            return match ($type) {
                'Medication' => 'Medication: ' . $model->name,
                'Diagnosis' => 'Diagnosis: ' . $model->name,
                'Event' => 'Event: ' . $model->title,
                'Meeting' => 'Meeting: ' . $model->title,
                'FamilyMember' => 'Family Member: ' . $model->first_name . ' ' . $model->last_name,
                'SchoolHistory' => 'School History: ' . $model->school_name,
                'Record' => 'Record: ' . ($model->title ?? $model->recordType->name . ' Record'),
                default => 'Pupil Profile',
            };
        }
        return 'Unknown Source';
    }
}
