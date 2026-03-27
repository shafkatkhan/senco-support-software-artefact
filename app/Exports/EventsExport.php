<?php

namespace App\Exports;

use App\Models\Pupil;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class EventsExport extends BaseExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $pupil;

    public function __construct(Pupil $pupil)
    {
        $this->pupil = $pupil;
    }

    public function collection()
    {
        return $this->pupil->events()->orderBy('date', 'desc')->orderBy('id', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            __('Title'),
            __('Date'),
            __('Reference No.'),
            __('Description'),
            __('Outcome / Next Steps'),
            __('Created At'),
            __('Last Edited'),
        ];
    }

    public function map($event): array
    {
        return [
            $event->title,
            $event->date ? $event->date->format('d/m/Y') : '',
            $event->reference_number,
            $event->description,
            $event->outcome,
            $event->created_at ? $event->created_at->format('d/m/Y, H:i') : '',
            $event->updated_at ? $event->updated_at->format('d/m/Y, H:i') : '',
        ];
    }
}
