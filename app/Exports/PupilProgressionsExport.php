<?php

namespace App\Exports;

use App\Models\Pupil;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PupilProgressionsExport extends BaseExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $pupil;

    public function __construct(Pupil $pupil)
    {
        $this->pupil = $pupil;
    }

    public function collection()
    {
        return $this->pupil->progressions()->orderBy('academic_year', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            __('Academic Year'),
            __('Year Group'),
            __('Tutor Group'),
            __('Type'),
            __('Created At'),
            __('Last Updated'),
        ];
    }

    public function map($progression): array
    {
        return [
            $progression->academic_year,
            __('Year :year', ['year' => $progression->year_group]),
            $progression->tutor_group,
            __(ucfirst($progression->type)),
            $progression->created_at ? $progression->created_at->format('d/m/Y, H:i') : '',
            $progression->updated_at ? $progression->updated_at->format('d/m/Y, H:i') : '',
        ];
    }
}
