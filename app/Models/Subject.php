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
}
