<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Accommodation extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    public function pupils()
    {
        return $this->belongsToMany(Pupil::class, 'pupil_accommodations');
    }
}
