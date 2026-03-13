<?php

namespace App\Models;

use App\Traits\HasAttachments;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Record extends Model
{
    use HasFactory, HasAttachments;

    protected $fillable = [
        'pupil_id',
        'record_type_id',
        'professional_id',
        'title',
        'date',
        'reference_number',
        'description',
        'outcome',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function pupil()
    {
        return $this->belongsTo(Pupil::class);
    }

    public function recordType()
    {
        return $this->belongsTo(RecordType::class);
    }

    public function professional()
    {
        return $this->belongsTo(Professional::class);
    }
}
