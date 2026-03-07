<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proficiency extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'subject_proficiencies');
    }

    public function diets()
    {
        return $this->hasMany(Diet::class);
    }
}
