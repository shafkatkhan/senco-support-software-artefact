<?php

namespace App\Exports;

use App\Models\Pupil;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class MeetingsExport extends BaseExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $pupil;

    public function __construct(Pupil $pupil)
    {
        $this->pupil = $pupil;
    }

    public function collection()
    {
        return $this->pupil->meetings()->with('meetingType')->orderBy('date', 'desc')->orderBy('id', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            __('Title'),
            __('Type'),
            __('Date'),
            __('Participants'),
            __('Discussion Notes'),
            __('Recommendations'),
            __('Created At'),
            __('Last Updated'),
        ];
    }

    public function map($meeting): array
    {
        return [
            $meeting->title,
            $meeting->meetingType ? $meeting->meetingType->name : '',
            $meeting->date ? $meeting->date->format('d/m/Y') : '',
            $meeting->participants,
            $meeting->discussion,
            $meeting->recommendations,
            $meeting->created_at ? $meeting->created_at->format('d/m/Y, H:i') : '',
            $meeting->updated_at ? $meeting->updated_at->format('d/m/Y, H:i') : '',
        ];
    }
}
