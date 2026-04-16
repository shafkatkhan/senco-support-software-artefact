<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diet extends Model
{
    use HasFactory;

    protected $fillable = [
        'pupil_id',
        'subject_id',
        'proficiency_id',
    ];

    public function pupil()
    {
        return $this->belongsTo(Pupil::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function proficiency()
    {
        return $this->belongsTo(Proficiency::class);
    }

    public function accommodations()
    {
        return $this->belongsToMany(Accommodation::class, 'diet_accommodations')
                    ->withPivot('status', 'details')
                    ->withTimestamps();
    }
}
