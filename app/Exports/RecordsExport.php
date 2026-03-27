<?php

namespace App\Exports;

use App\Models\Pupil;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class RecordsExport extends BaseExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $pupil;

    public function __construct(Pupil $pupil)
    {
        $this->pupil = $pupil;
    }

    public function collection()
    {
        return $this->pupil->records()->with(['recordType', 'professional'])->orderBy('date', 'desc')->orderBy('id', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            __('Title'),
            __('Type'),
            __('Date'),
            __('Reference No.'),
            __('Professional'),
            __('Description'),
            __('Outcome / Next Steps'),
            __('Created At'),
            __('Last Updated'),
        ];
    }

    public function map($record): array
    {
        $professionalName = '';
        if ($record->professional) {
            $professionalName = trim("{$record->professional->title} {$record->professional->first_name} {$record->professional->last_name}");
        }

        return [
            $record->title,
            $record->recordType ? $record->recordType->name : '',
            $record->date ? $record->date->format('d/m/Y') : '',
            $record->reference_number,
            $professionalName,
            $record->description,
            $record->outcome,
            $record->created_at ? $record->created_at->format('d/m/Y, H:i') : '',
            $record->updated_at ? $record->updated_at->format('d/m/Y, H:i') : '',
        ];
    }
}
