<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pupil extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'dob',
        'gender',
        'primary_family_member_id',
        'address_line_1',
        'address_line_2',
        'locality',
        'postcode',
        'country',
        'joined_date',
        'initial_tutor_group',
        'smoking_history',
        'drug_abuse_history',
    ];

    protected $casts = [
        'dob' => 'date',
        'joined_date' => 'date',
        'smoking_history' => 'boolean',
        'drug_abuse_history' => 'boolean',
    ];
    public function medications()
    {
        return $this->hasMany(Medication::class);
    }

    public function onboardedBy()
    {
        return $this->belongsTo(User::class, 'onboarded_by');
    }

    public function familyMembers()
    {
        return $this->hasMany(FamilyMember::class);
    }

    public function primaryFamilyMember()
    {
        return $this->belongsTo(FamilyMember::class, 'primary_family_member_id');
    }

    public function diagnoses()
    {
        return $this->hasMany(Diagnosis::class);
    }

    public function accommodations()
    {
        return $this->belongsToMany(Accommodation::class, 'pupil_accommodations');
    }

    public function records()
    {
        return $this->hasMany(Record::class);
    }

    public function meetings()
    {
        return $this->hasMany(Meeting::class);
    }
}
