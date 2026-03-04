<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'pupil_id',
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
}
