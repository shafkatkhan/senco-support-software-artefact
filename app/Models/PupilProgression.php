<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PupilProgression extends Model
{
    protected $fillable = [
        'pupil_id',
        'academic_year',
        'year_group',
        'tutor_group',
        'type',
    ];

    public function pupil()
    {
        return $this->belongsTo(Pupil::class);
    }
}
