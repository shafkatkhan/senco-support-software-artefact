<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;

abstract class BaseExport implements WithEvents, WithStyles, WithCustomCsvSettings
{
    /**
     * Settings for CSV exports to fix Unicode squares.
     */
    public function getCsvSettings(): array
    {
        return [
            'use_bom' => true,
        ];
    }

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
     * Register events for auto-sizing columns
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

                // NOTE: cannot use setWrapText(true) here, as it causes non-Latin characters (Arabic, Chinese, etc.) to render as squares
                // $sheet->getStyle($range)->getAlignment()->setWrapText(true);
                
                $sheet->getStyle($range)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);

                // flip the spreadsheet for RTL languages
                if (is_rtl()) {
                    $sheet->setRightToLeft(true);
                }

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
