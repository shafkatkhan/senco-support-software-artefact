<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;

abstract class BaseExport implements WithEvents, WithStyles
{
    /**
     * Style the header row
     * 
     * @param  Worksheet  $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'underline' => true]],
        ];
    }

    /**
     * Register events for auto-sizing columns and wrapping text
     * 
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestColumn = $sheet->getHighestDataColumn();
                $highestRow = $sheet->getHighestDataRow();
                $range = 'A1:' . $highestColumn . $highestRow;
                
                // wrap text and align to top globally
                $sheet->getStyle($range)->getAlignment()->setWrapText(true);
                $sheet->getStyle($range)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);

                // auto-sizing to a maximum: requires forced calculation of the widths first, then cap any that exceed the limit of 50
                $sheet->calculateColumnWidths();

                $highestColumn++; 
                for ($col = 'A'; $col !== $highestColumn; $col++) {
                    $dim = $sheet->getColumnDimension($col);
                    if ($dim->getAutoSize() && $dim->getWidth() > 50) {
                        $dim->setAutoSize(false);
                        $dim->setWidth(50);
                    }
                }
            },
        ];
    }
}
