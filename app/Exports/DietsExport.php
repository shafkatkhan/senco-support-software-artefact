<?php

namespace App\Exports;

use App\Models\Pupil;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class DietsExport extends BaseExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $pupil;

    public function __construct(Pupil $pupil)
    {
        $this->pupil = $pupil;
    }

    public function collection()
    {
        return $this->pupil->diets()->with(['subject', 'proficiency', 'accommodations'])->get();
    }

    public function headings(): array
    {
        return [
            __('Subject'),
            __('Proficiency'),
            __('Accommodations'),
            __('Created At'),
            __('Last Updated'),
        ];
    }

    public function map($diet): array
    {
        $accommodationsText = $diet->accommodations->map(function ($acc) {
            $line = $acc->name . ' (' . __($acc->pivot->status) . ')';
            if ($acc->pivot->details) {
                $line .= ":\n" . $acc->pivot->details;
            }
            return $line;
        })->implode("\n\n");

        return [
            $diet->subject ? $diet->subject->name : '',
            $diet->proficiency ? $diet->proficiency->name : '',
            $accommodationsText,
            $diet->created_at ? $diet->created_at->format('d/m/Y, H:i') : '',
            $diet->updated_at ? $diet->updated_at->format('d/m/Y, H:i') : '',
        ];
    }
}
