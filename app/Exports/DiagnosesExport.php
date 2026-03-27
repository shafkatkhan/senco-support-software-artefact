<?php

namespace App\Exports;

use App\Models\Pupil;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class DiagnosesExport extends BaseExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $pupil;

    public function __construct(Pupil $pupil)
    {
        $this->pupil = $pupil;
    }

    public function collection()
    {
        return $this->pupil->diagnoses()->with('professional')->get();
    }

    public function headings(): array
    {
        return [
            __('Name'),
            __('Date'),
            __('Description'),
            __('Recommendations'),
            __('Carried Out By'),
            __('Created At'),
            __('Last Updated'),
        ];
    }

    public function map($diagnosis): array
    {
        $professionalName = '';
        if ($diagnosis->professional) {
            $professionalName = trim("{$diagnosis->professional->title} {$diagnosis->professional->first_name} {$diagnosis->professional->last_name}");
        }

        return [
            $diagnosis->name,
            $diagnosis->date ? $diagnosis->date->format('d/m/Y') : '',
            $diagnosis->description,
            $diagnosis->recommendations,
            $professionalName,
            $diagnosis->created_at ? $diagnosis->created_at->format('d/m/Y, H:i') : '',
            $diagnosis->updated_at ? $diagnosis->updated_at->format('d/m/Y, H:i') : '',
        ];
    }
}
