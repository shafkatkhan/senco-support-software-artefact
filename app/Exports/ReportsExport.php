<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ReportsExport extends BaseExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $pupils;

    public function __construct(Collection $pupils)
    {
        $this->pupils = $pupils;
    }

    public function collection()
    {
        return $this->pupils;
    }

    public function headings(): array
    {
        return [
            __('Pupil Number'),
            __('First Name'),
            __('Last Name'),
            __('Major'),
            __('Date of Birth'),
            __('Gender'),
            __('Joined Date'),
            __('Address Line 1'),
            __('Address Line 2'),
            __('Town/City'),
            __('Postcode'),
            __('Country'),
            __('Phone'),
            __('Email'),
            __('Auto Progression'),
            __('Smoking History?'),
            __('Drug Abuse History?'),
            __('After School Job'),
            __('Special Needs?'),
            __('Special Needs Details'),
            __('Attended Special School?'),
            __('Special School Details'),
            __('Parental Description'),
            __('Social Services Involvement'),
            __('Social Worker'),
            __('Visiting Probation Officer Required?'),
            __('Probation Officer'),
            __('Treatment Plan'),
            __('Next of Kin Name'),
            __('Next of Kin Relation'),
            __('Next of Kin Phone'),
            __('Next of Kin Email'),
            __('Year Group'),
            __('Tutor Group'),
            __('Diagnoses'),
            __('Medications'),
            __('Subjects'),
            __('Accommodations'),
        ];
    }

    public function map($pupil): array
    {
        $latest = $pupil->latestProgression;
        $next_kin = $pupil->primaryFamilyMember;

        $diagnosesList = $pupil->diagnoses->pluck('name')->implode("; \n\n");
        $medicationsList = $pupil->medications->pluck('name')->implode("; \n\n");
        $subjectsList = $pupil->diets->map(fn ($d) => $d->subject?->name)->filter()->unique()->implode("; \n\n");

        $accommodationsList = $pupil->diets->flatMap(function ($diet) {
            $subjectName = $diet->subject?->name ?? __('N/A');
            return $diet->accommodations->map(function ($acc) use ($subjectName) {
                return __(':name (:status for :subject)', [
                    'name' => $acc->name,
                    'status' => __($acc->pivot->status),
                    'subject' => $subjectName,
                ]);
            });
        })->unique()->implode("; \n\n");

        $sw = $pupil->socialServicesProfessional;
        $social_worker = $sw ? collect([
            $sw->first_name . ' ' . $sw->last_name,
            $sw->phone,
            $sw->email
        ])->filter()->implode(",\n") : __('N/A');
        
        $po = $pupil->probationOfficerProfessional;
        $probation_officer = $po ? collect([
            $po->first_name . ' ' . $po->last_name,
            $po->phone,
            $po->email
        ])->filter()->implode(",\n") : __('N/A');

        return [
            $pupil->pupil_number,
            $pupil->first_name,
            $pupil->last_name,
            $pupil->major ? $pupil->major->name : __('N/A'),
            $pupil->dob ? $pupil->dob->format('d/m/Y') : '',
            __($pupil->gender),
            $pupil->joined_date ? $pupil->joined_date->format('d/m/Y') : '',
            $pupil->address_line_1,
            $pupil->address_line_2,
            $pupil->locality,
            $pupil->postcode,
            $pupil->country,
            $pupil->phone ?? __('N/A'),
            $pupil->email ?? __('N/A'),
            $pupil->auto_progression ? __('Yes') : __('No'),
            $pupil->smoking_history ? __('Yes') : __('No'),
            $pupil->drug_abuse_history ? __('Yes') : __('No'),
            $pupil->after_school_job ?? __('N/A'),
            $pupil->has_special_needs ? __('Yes') : __('No'),
            $pupil->special_needs_details ?? __('N/A'),
            $pupil->attended_special_school ? __('Yes') : __('No'),
            $pupil->special_school_details ?? __('N/A'),
            $pupil->parental_description ?? __('N/A'),
            $pupil->social_services_involvement ? __('Yes') : __('No'),
            $social_worker,
            $pupil->probation_officer_required ? __('Yes') : __('No'),
            $probation_officer,
            $pupil->treatment_plan ?? __('N/A'),
            $next_kin ? ($next_kin->first_name . ' ' . $next_kin->last_name) : __('N/A'),
            $next_kin ? __($next_kin->relation) : __('N/A'),
            $next_kin ? $next_kin->phone : __('N/A'),
            $next_kin ? $next_kin->email : __('N/A'),
            $latest ? $latest->year_group : __('N/A'),
            $latest ? $latest->tutor_group : __('N/A'),
            $diagnosesList ?: __('N/A'),
            $medicationsList ?: __('N/A'),
            $subjectsList ?: __('N/A'),
            $accommodationsList ?: __('N/A'),
        ];
    }
}
