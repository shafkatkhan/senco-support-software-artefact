<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = [
        'name',
        'code',
    ];

    public function majors()
    {
        return $this->belongsToMany(Major::class, 'major_subjects');
    }

    public function accommodations()
    {
        return $this->belongsToMany(Accommodation::class, 'subject_accommodations');
    }
    
    public function proficiencies()
    {
        return $this->belongsToMany(Proficiency::class, 'subject_proficiencies');
    }
}
