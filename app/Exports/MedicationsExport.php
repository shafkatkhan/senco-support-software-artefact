<?php

namespace App\Exports;

use App\Models\Pupil;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class MedicationsExport extends BaseExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $pupil;

    public function __construct(Pupil $pupil)
    {
        $this->pupil = $pupil;
    }

    public function collection()
    {
        return $this->pupil->medications;
    }

    public function headings(): array
    {
        return [
            __('Name'),
            __('Dosage'),
            __('Frequency'),
            __('Time of Day'),
            __('Method of Administration'),
            __('Start Date'),
            __('End Date'),
            __('Expiry Date'),
            __('Storage Instructions'),
            __('Self Administer?'),
            __('Created At'),
            __('Last Updated'),
        ];
    }

    public function map($medication): array
    {
        return [
            $medication->name,
            $medication->dosage,
            $medication->frequency,
            $medication->time_of_day,
            $medication->administration_method,
            $medication->start_date ? $medication->start_date->format('d/m/Y') : '',
            $medication->end_date ? $medication->end_date->format('d/m/Y') : '',
            $medication->expiry_date ? $medication->expiry_date->format('d/m/Y') : '',
            $medication->storage_instructions,
            $medication->self_administer ? 'Yes' : 'No',
            $medication->created_at ? $medication->created_at->format('d/m/Y, H:i') : '',
            $medication->updated_at ? $medication->updated_at->format('d/m/Y, H:i') : '',
        ];
    }
}
