<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Major extends Model
{
    protected $fillable = [
        'name',
        'code',
    ];

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'major_subjects');
    }
}
