<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'pupil_id',
        'school_name',
        'school_type',
        'class_type',
        'years_attended',
        'transition_reason',
    ];

    public function pupil()
    {
        return $this->belongsTo(Pupil::class);
    }
}
