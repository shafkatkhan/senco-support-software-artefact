<?php

namespace App\Exports;

use App\Models\Pupil;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SchoolHistoriesExport extends BaseExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $pupil;

    public function __construct(Pupil $pupil)
    {
        $this->pupil = $pupil;
    }

    public function collection()
    {
        return $this->pupil->schoolHistories;
    }

    public function headings(): array
    {
        return [
            __('Name of School'),
            __('School Type'),
            __('Class Type'),
            __('Years Attended'),
            __('Reason for Transition'),
            __('Created At'),
            __('Last Updated'),
        ];
    }

    public function map($history): array
    {
        return [
            $history->school_name,
            $history->school_type,
            $history->class_type,
            $history->years_attended ? number_format((float)$history->years_attended, 1) : '',
            $history->transition_reason,
            $history->created_at ? $history->created_at->format('d/m/Y, H:i') : '',
            $history->updated_at ? $history->updated_at->format('d/m/Y, H:i') : '',
        ];
    }
}
