<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    use HasFactory;

    protected $fillable = [
        'pupil_id',
        'meeting_type_id',
        'date',
        'title',
        'participants',
        'discussion',
        'recommendations',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function pupil()
    {
        return $this->belongsTo(Pupil::class);
    }

    public function meetingType()
    {
        return $this->belongsTo(MeetingType::class);
    }
}
