<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PupilFamilyMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'pupil_id',
        'first_name',
        'last_name',
        'dob',
        'relation',
    ];

    protected $casts = [
        'dob' => 'date',
    ];

    public function pupil()
    {
        return $this->belongsTo(Pupil::class);
    }
}
