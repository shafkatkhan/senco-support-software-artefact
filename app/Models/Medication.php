<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasAttachments;

class Medication extends Model
{
    use HasFactory, HasAttachments;

    protected $fillable = [
        'pupil_id',
        'name',
        'dosage',
        'frequency',
        'time_of_day',
        'administration_method',
        'start_date',
        'end_date',
        'expiry_date',
        'storage_instructions',
        'self_administer',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'expiry_date' => 'date',
        'self_administer' => 'boolean',
    ];

    public function pupil()
    {
        return $this->belongsTo(Pupil::class);
    }
}
