<?php

namespace App\Models;

use App\Traits\HasAttachments;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pupil extends Model
{
    use HasFactory, HasAttachments;

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
        'phone',
        'email',
        'after_school_job',
        'has_special_needs',
        'special_needs_details',
        'attended_special_school',
        'special_school_details',
        'parental_description',
        'social_services_involvement',
        'probation_officer_required',
        'onboarded_by',
        'social_services_professional_id',
        'probation_officer_professional_id',
    ];

    protected $casts = [
        'dob' => 'date',
        'joined_date' => 'date',
        'smoking_history' => 'boolean',
        'drug_abuse_history' => 'boolean',
        'has_special_needs' => 'boolean',
        'attended_special_school' => 'boolean',
        'social_services_involvement' => 'boolean',
        'probation_officer_required' => 'boolean',
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

    public function records()
    {
        return $this->hasMany(Record::class);
    }

    public function meetings()
    {
        return $this->hasMany(Meeting::class);
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function diets()
    {
        return $this->hasMany(Diet::class);
    }

    public function schoolHistories()
    {
        return $this->hasMany(SchoolHistory::class);
    }

    public function socialServicesProfessional()
    {
        return $this->belongsTo(Professional::class, 'social_services_professional_id');
    }

    public function probationOfficerProfessional()
    {
        return $this->belongsTo(Professional::class, 'probation_officer_professional_id');
    }
}
