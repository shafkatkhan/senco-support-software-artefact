<?php

namespace App\Exports;

use App\Models\Pupil;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class FamilyMembersExport extends BaseExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $pupil;

    public function __construct(Pupil $pupil)
    {
        $this->pupil = $pupil;
    }

    public function collection()
    {
        return $this->pupil->familyMembers;
    }

    public function headings(): array
    {
        return [
            __('First Name'),
            __('Last Name'),
            __('Relation'),
            __('Date of Birth'),
            __('Phone'),
            __('Email'),
            __('Address Line 1'),
            __('Address Line 2'),
            __('Town/City'),
            __('Postcode'),
            __('Country'),
            __('Marital Status'),
            __('Highest Education'),
            __('Financial Status'),
            __('Occupation'),
            __('State Support/Benefits'),
            __('Next of Kin?'),
            __('Created At'),
            __('Last Updated'),
        ];
    }

    public function map($familyMember): array
    {
        $isNextOfKin = $familyMember->pupil->primary_family_member_id == $familyMember->id;

        return [
            $familyMember->first_name,
            $familyMember->last_name,
            $familyMember->relation,
            $familyMember->dob ? $familyMember->dob->format('d/m/Y') : '',
            $familyMember->phone,
            $familyMember->email,
            $familyMember->address_line_1,
            $familyMember->address_line_2,
            $familyMember->locality,
            $familyMember->postcode,
            $familyMember->country,
            $familyMember->marital_status,
            $familyMember->highest_education,
            $familyMember->financial_status,
            $familyMember->occupation,
            $familyMember->state_support,
            $isNextOfKin ? __('Yes') : __('No'),
            $familyMember->created_at ? $familyMember->created_at->format('d/m/Y, H:i') : '',
            $familyMember->updated_at ? $familyMember->updated_at->format('d/m/Y, H:i') : '',
        ];
    }
}
