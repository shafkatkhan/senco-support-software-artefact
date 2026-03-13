<?php

namespace App\Models;

use App\Traits\HasAttachments;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diagnosis extends Model
{
    use HasFactory, HasAttachments;
    
    protected $fillable = [
        'pupil_id',
        'professional_id',
        'date',
        'name',
        'description',
        'recommendations',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function pupil()
    {
        return $this->belongsTo(Pupil::class);
    }

    public function professional()
    {
        return $this->belongsTo(Professional::class);
    }
}
