<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: "Segoe UI", Arial, sans-serif;
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
                <div>Pupil Profile Summary</div>
                <div>Date: {{ date('d/m/Y') }}</div>
            </td>
        </tr>
    </table>

    <div class="section">
        <div class="section_title">Personal Details</div>
        <table class="inline">
            <tr>
                <th>Full Name</th>
                <td>{{ $pupil->first_name }} {{ $pupil->last_name }}</td>
            </tr>
            <tr>
                <th>Pupil Number</th>
                <td>{{ $pupil->pupil_number }}</td>
            </tr>
            <tr>
                <th>Date of Birth</th>
                <td>{{ $pupil->dob?->format('d/m/Y') ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Gender</th>
                <td>{{ $pupil->gender ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>After School Job</th>
                <td>{{ $pupil->after_school_job ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Next of Kin</th>
                <td>{{ $pupil->primaryFamilyMember ? $pupil->primaryFamilyMember->first_name . ' ' . $pupil->primaryFamilyMember->last_name : 'N/A' }}</td>
            </tr>
        </table>
    </div>
    
    <div class="section">
        <div class="section_title">Contact Information</div>
        <table class="inline">
            <tr>
                <th>Phone</th>
                <td>{{ $pupil->phone ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Email</th>
                <td>{{ $pupil->email ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Address</th>
                <td>{!! collect([$pupil->address_line_1, $pupil->address_line_2])->filter()->implode(', <br>') ?: 'N/A' !!}</td>
            </tr>
            <tr>
                <th>Town/City</th>
                <td>{{ $pupil->locality ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Postcode</th>
                <td>{{ $pupil->postcode ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Country</th>
                <td>{{ $pupil->country ?? 'N/A' }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section_title">Safeguarding & Needs</div>
        <table class="inline">
            <tr>
                <th>Smoking History</th>
                <td>{{ $pupil->smoking_history ? 'Yes' : 'No' }}</td>
            </tr>
            <tr>
                <th>Drug Abuse History</th>
                <td>{{ $pupil->drug_abuse_history ? 'Yes' : 'No' }}</td>
            </tr>
            <tr>
                <th>Special Needs</th>
                <td>
                    @if($pupil->has_special_needs)
                        {!! $pupil->special_needs_details ? nl2br(e($pupil->special_needs_details)) : 'Yes' !!}
                    @else
                        N/A
                    @endif
                </td>
            </tr>
            <tr>
                <th>Special School History</th>
                <td>
                    @if($pupil->attended_special_school)
                        {!! $pupil->special_school_details ? nl2br(e($pupil->special_school_details)) : 'Yes' !!}
                    @else
                        N/A
                    @endif
                </td>
            </tr>
            <tr>
                <th>Social Services Involvement</th>
                <td>{{ $pupil->social_services_involvement ? 'Yes' : 'No' }}</td>
            </tr>
            <tr>
                <th>Social Worker</th>
                <td>
                    @if($pupil->socialServicesProfessional)
                        {{ collect([
                            $pupil->socialServicesProfessional->title,
                            $pupil->socialServicesProfessional->first_name,
                            $pupil->socialServicesProfessional->last_name,
                        ])->filter()->implode(' ') }}
                    @else
                        N/A
                    @endif
                </td>
            </tr>
            <tr>
                <th>Visiting Probation Officer Required</th>
                <td>{{ $pupil->probation_officer_required ? 'Yes' : 'No' }}</td>
            </tr>
            <tr>
                <th>Probation Officer</th>
                <td>
                    @if($pupil->probationOfficerProfessional)
                        {{ collect([
                            $pupil->probationOfficerProfessional->title,
                            $pupil->probationOfficerProfessional->first_name,
                            $pupil->probationOfficerProfessional->last_name,
                        ])->filter()->implode(' ') }}
                    @else
                        N/A
                    @endif
                </td>
            </tr>            
        </table>
    </div>

    <div class="section">
        <div class="section_title">Parental Description</div>
        <div style="padding: 0 12px;">
            {!! $pupil->parental_description ? nl2br(e($pupil->parental_description)) : 'N/A' !!}
        </div>
    </div>

    <div class="section">
        <div class="section_title">Administrative Information</div>
        <table class="inline">
            <tr>
                <th>Joined Date</th>
                <td>{{ $pupil->joined_date?->format('d/m/Y') ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Initial Tutor Group</th>
                <td>{{ $pupil->initial_tutor_group ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Onboarded by</th>
                <td>{{ $pupil->onboardedBy->first_name }} {{ $pupil->onboardedBy->last_name }}</td>
            </tr>
            <tr>
                <th>Last Edited</th>
                <td>{{ $pupil->updated_at->format('d/m/Y') }}, {{ $pupil->updated_at->format('H:i') }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section_title">Data Summary</div>
        <table class="inline">
            <tr>
                <th>Medications</th>
                <td>
                    <div class="label_cards">
                        @forelse ($pupil->medications as $medication)
                            <div>{{ $medication->name }}</div>
                        @empty
                            N/A
                        @endforelse
                    </div>
                </td>
            </tr>
            <tr>
                <th>Diagnoses</th>
                <td>
                    <div class="label_cards">
                        @forelse ($pupil->diagnoses as $diagnosis)
                            <div>{{ $diagnosis->name }}</div>
                        @empty
                            N/A
                        @endforelse
                    </div>
                </td>
            </tr>
            <tr>
                <th>Subjects</th>
                <td>
                    <div class="label_cards">
                        @forelse ($pupil->diets as $diet)
                            <div>{{ $diet->subject->name }}</div>
                        @empty
                            N/A
                        @endforelse
                    </div>
                </td>
            </tr>
        </table>
    </div>

    @if($involvements->isNotEmpty())
    <div class="section">
        <div class="section_title">Related Professionals</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Role</th>
                    <th>Involvement</th>
                    <th>Contact</th>
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
        Generated by MySENCOSupportSoftware on {{ now()->format('d/m/Y H:i') }} by {{ Auth::user()->first_name }} {{ Auth::user()->last_name }} <br> Confidential data, not to be shared without consent. Handle with care.
    </div>
</body>
</html>