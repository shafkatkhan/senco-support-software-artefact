<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TreatmentPlanUpdate extends Model
{
    protected $fillable = [
        'pupil_id',
        'user_id',
        'date',
        'description',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function pupil()
    {
        return $this->belongsTo(Pupil::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
