<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family={{ $fontFamily }}:wght@400;700&display=swap');

        body {
            font-family: "{{ $fontName }}", "DejaVu Sans", sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            margin-bottom: 30px;
            border-bottom: 1px solid #4a5568;
            padding-bottom: 10px;
            border-top: 7px solid #4a5568;
            padding-top: 12px;
        }
        .header .title {
            color: #2d3748;
            font-size: 24px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .header .sub_title {
            font-size: 12px;
            font-weight: normal;
        }
        .section {
            border: 1px solid #edf2f7;
            padding: 20px;
            margin-bottom: 25px;
            border-radius: 4px;
        }
        .section_title {
            background-color: #f7fafc;
            padding: 8px 12px;
            font-size: 14px;
            border-left: 4px solid #4a5568;
            margin-bottom: 15px;
            color: #2d3748;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #edf2f7;
        }
        th, td {
            padding: 8px 12px;
            text-align: left;
            border-bottom: 1px solid #edf2f7;
        }
        th {
            background-color: #f8fafc;
            color: #4a5568;
        }
        .inline th{
            border-right: 1px solid #CCC;
            width: 30%;
        }
        table:not(.inline) th, table:not(.inline) td {
            border-right: 1px solid #edf2f7;
        }
        .data-table th {
            background-color: #edf2f7;
            width: auto;
        }
        .label_cards div{
            background-color: #eee;
            padding: 5px 12px;
            margin-top: 4px;
            margin-right: 1px;
            display: inline-block;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #a0aec0;
            border-top: 1px solid #edf2f7;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <table class="header">
        <tr>
            <td class="title">
                {{ $pupil->first_name }} {{ $pupil->last_name }}
                <div class="sub_title">({{ $pupil->pupil_number }})</div>
            </td>
            <td style="text-align: right;">
                <div>{{ __('Pupil Profile Summary') }}</div>
                <div>{{ __('Date: :date', ['date' => date('d/m/Y')]) }}</div>
            </td>
        </tr>
    </table>

    <div class="section">
        <div class="section_title">{{ __('Personal Details') }}</div>
        <table class="inline">
            <tr>
                <th>{{ __('Full Name') }}</th>
                <td>{{ $pupil->first_name }} {{ $pupil->last_name }}</td>
            </tr>
            <tr>
                <th>{{ __('Pupil Number') }}</th>
                <td>{{ $pupil->pupil_number }}</td>
            </tr>
            <tr>
                <th>{{ __('Date of Birth') }}</th>
                <td>{{ $pupil->dob?->format('d/m/Y') ?? __('N/A') }}</td>
            </tr>
            <tr>
                <th>{{ __('Gender') }}</th>
                <td>{{ $pupil->gender ?? __('N/A') }}</td>
            </tr>
            <tr>
                <th>{{ __('After School Job') }}</th>
                <td>{{ $pupil->after_school_job ?? __('N/A') }}</td>
            </tr>
            <tr>
                <th>{{ __('Next of Kin') }}</th>
                <td>{{ $pupil->primaryFamilyMember ? $pupil->primaryFamilyMember->first_name . ' ' . $pupil->primaryFamilyMember->last_name : __('N/A') }}</td>
            </tr>
        </table>
    </div>
    
    <div class="section">
        <div class="section_title">{{ __('Contact Information') }}</div>
        <table class="inline">
            <tr>
                <th>{{ __('Phone') }}</th>
                <td>{{ $pupil->phone ?? __('N/A') }}</td>
            </tr>
            <tr>
                <th>{{ __('Email') }}</th>
                <td>{{ $pupil->email ?? __('N/A') }}</td>
            </tr>
            <tr>
                <th>{{ __('Address') }}</th>
                <td>{!! collect([$pupil->address_line_1, $pupil->address_line_2])->filter()->implode(', <br>') ?: __('N/A') !!}</td>
            </tr>
            <tr>
                <th>{{ __('Town/City') }}</th>
                <td>{{ $pupil->locality ?? __('N/A') }}</td>
            </tr>
            <tr>
                <th>{{ __('Postcode') }}</th>
                <td>{{ $pupil->postcode ?? __('N/A') }}</td>
            </tr>
            <tr>
                <th>{{ __('Country') }}</th>
                <td>{{ $pupil->country ?? __('N/A') }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section_title">{{ __('Safeguarding & Needs') }}</div>
        <table class="inline">
            <tr>
                <th>{{ __('Smoking History?') }}</th>
                <td>{{ $pupil->smoking_history ? 'Yes' : 'No' }}</td>
            </tr>
            <tr>
                <th>{{ __('Drug Abuse History?') }}</th>
                <td>{{ $pupil->drug_abuse_history ? 'Yes' : 'No' }}</td>
            </tr>
            <tr>
                <th>{{ __('Special Needs?') }}</th>
                <td>
                    @if($pupil->has_special_needs)
                        {!! $pupil->special_needs_details ? nl2br(e($pupil->special_needs_details)) : __('Yes') !!}
                    @else
                        {{ __('N/A') }}
                    @endif
                </td>
            </tr>
            <tr>
                <th>{{ __('Special School History') }}</th>
                <td>
                    @if($pupil->attended_special_school)
                        {!! $pupil->special_school_details ? nl2br(e($pupil->special_school_details)) : __('Yes') !!}
                    @else
                        {{ __('N/A') }}
                    @endif
                </td>
            </tr>
            <tr>
                <th>{{ __('Social Services Involvement') }}</th>
                <td>{{ $pupil->social_services_involvement ? __('Yes') : __('No') }}</td>
            </tr>
            <tr>
                <th>{{ __('Social Worker') }}</th>
                <td>
                    @if($pupil->socialServicesProfessional)
                        {{ collect([
                            $pupil->socialServicesProfessional->title,
                            $pupil->socialServicesProfessional->first_name,
                            $pupil->socialServicesProfessional->last_name,
                        ])->filter()->implode(' ') }}
                    @else
                        {{ __('N/A') }}
                    @endif
                </td>
            </tr>
            <tr>
                <th>{{ __('Visiting Probation Officer Required') }}</th>
                <td>{{ $pupil->probation_officer_required ? __('Yes') : __('No') }}</td>
            </tr>
            <tr>
                <th>{{ __('Probation Officer') }}</th>
                <td>
                    @if($pupil->probationOfficerProfessional)
                        {{ collect([
                            $pupil->probationOfficerProfessional->title,
                            $pupil->probationOfficerProfessional->first_name,
                            $pupil->probationOfficerProfessional->last_name,
                        ])->filter()->implode(' ') }}
                    @else
                        {{ __('N/A') }}
                    @endif
                </td>
            </tr>            
        </table>
    </div>

    <div class="section">
        <div class="section_title">{{ __('Parental Description') }}</div>
        <div style="padding: 0 12px;">
            {!! $pupil->parental_description ? nl2br(e($pupil->parental_description)) : __('N/A') !!}
        </div>
    </div>

    <div class="section">
        <div class="section_title">{{ __('Treatment Plan') }}</div>
        <div style="padding: 0 12px;">
            {!! $pupil->treatment_plan ? nl2br(e($pupil->treatment_plan)) : __('N/A') !!}
        </div>
    </div>

    <div class="section">
        <div class="section_title">{{ __('Administrative Information') }}</div>
        <table class="inline">
            <tr>
                <th>{{ __('Joined Date') }}</th>
                <td>{{ $pupil->joined_date?->format('d/m/Y') ?? __('N/A') }}</td>
            </tr>
            <tr>
                <th>{{ __('Initial Tutor Group') }}</th>
                <td>{{ $pupil->initial_tutor_group ?? __('N/A') }}</td>
            </tr>
            <tr>
                <th>{{ __('Current Year Group') }}</th>
                <td>{{ $pupil->current_year_group ?? __('N/A') }}</td>
            </tr>
            <tr>
                <th>{{ __('Current Tutor Group') }}</th>
                <td>{{ $pupil->current_tutor_group ?? __('N/A') }}</td>
            </tr>
            <tr>
                <th>{{ __('Onboarded by') }}</th>
                <td>{{ $pupil->onboardedBy->first_name }} {{ $pupil->onboardedBy->last_name }}</td>
            </tr>
            <tr>
                <th>{{ __('Last Edited') }}</th>
                <td>{{ $pupil->updated_at->format('d/m/Y') }}, {{ $pupil->updated_at->format('H:i') }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section_title">{{ __('Data Summary') }}</div>
        <table class="inline">
            <tr>
                <th>{{ __('Medications') }}</th>
                <td>
                    <div class="label_cards">
                        @forelse ($pupil->medications as $medication)
                            <div>{{ $medication->name }}</div>
                        @empty
                            {{ __('N/A') }}
                        @endforelse
                    </div>
                </td>
            </tr>
            <tr>
                <th>{{ __('Diagnoses') }}</th>
                <td>
                    <div class="label_cards">
                        @forelse ($pupil->diagnoses as $diagnosis)
                            <div>{{ $diagnosis->name }}</div>
                        @empty
                            {{ __('N/A') }}
                        @endforelse
                    </div>
                </td>
            </tr>
            <tr>
                <th>{{ __('Subjects') }}</th>
                <td>
                    <div class="label_cards">
                        @forelse ($pupil->diets as $diet)
                            <div>{{ $diet->subject->name }}</div>
                        @empty
                            {{ __('N/A') }}
                        @endforelse
                    </div>
                </td>
            </tr>
        </table>
    </div>

    @if($involvements->isNotEmpty())
    <div class="section">
        <div class="section_title">{{ __('Related Professionals') }}</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>{{ __('Name') }}</th>
                    <th>{{ __('Role') }}</th>
                    <th>{{ __('Involvement') }}</th>
                    <th>{{ __('Contact') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($involvements as $entry)
                    <tr>
                        <td>{{ $entry['professional']->title }} {{ $entry['professional']->first_name }} {{ $entry['professional']->last_name }}</td>
                        <td>{{ $entry['professional']->role }}</td>
                        <td>
                            {{ implode(', ', $entry['involvements']) }}
                        </td>
                        <td>
                            @if($entry['professional']->phone) {{ $entry['professional']->phone }} <br> @endif
                            @if($entry['professional']->email) {{ $entry['professional']->email }} @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="footer">
        {{ __('Generated by EduSen on :datetime by :name', [
            'datetime' => now()->format('d/m/Y H:i'),
            'name' => Auth::user()->first_name.' '.Auth::user()->last_name,
        ]) }}
        <br>
        {{ __('Confidential data, not to be shared without consent. Handle with care.') }}
    </div>
</body>
</html>