<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FamilyMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'pupil_id',
        'first_name',
        'last_name',
        'dob',
        'relation',
        'phone',
        'email',
        'address_line_1',
        'address_line_2',
        'locality',
        'postcode',
        'country',
        'marital_status',
        'highest_education',
        'financial_status',
        'occupation',
        'state_support',
    ];

    protected $casts = [
        'dob' => 'date',
    ];

    public function pupil()
    {
        return $this->belongsTo(Pupil::class);
    }
}
