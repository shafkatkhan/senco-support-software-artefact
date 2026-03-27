<?php

namespace App\Traits;

use App\Models\Pupil;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

trait ExportsPupilData
{
    /**
     * Generate the standardised filename for pupil exports.
     */
    protected function getPupilExportFilename(Pupil $pupil, $suffix, $extension)
    {
        $prefix = str_replace(' ', '_', $pupil->pupil_number . '_' . $pupil->first_name . '_' . $pupil->last_name);
        return "{$prefix}_{$suffix}.{$extension}";
    }

    /**
     * Handle the Excel/CSV download for a pupil.
     */
    protected function downloadPupilExport(Pupil $pupil, $exportClass, $suffix, $format)
    {
        Gate::authorize('export-pupil-data');

        $extension = strtolower($format) == 'csv' ? 'csv' : 'xlsx';
        $filename = $this->getPupilExportFilename($pupil, $suffix, $extension);
        
        return Excel::download(new $exportClass($pupil), $filename);
    }

    /**
     * Route action for exporting pupil-related tables as spreadsheets.
     * The export class and suffix are inferred dynamically from the executing Controller's name.
     */
    public function exportSpreadsheet(Pupil $pupil, $format)
    {
        $baseName = str_replace('Controller', '', class_basename($this));
        $suffix = Str::plural($baseName);
        $exportClass = "\\App\\Exports\\{$suffix}Export";

        return $this->downloadPupilExport($pupil, $exportClass, $suffix, $format);
    }
}
