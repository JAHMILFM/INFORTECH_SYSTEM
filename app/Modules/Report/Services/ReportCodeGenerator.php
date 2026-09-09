<?php

namespace App\Modules\Report\Services;

use App\Models\Report;
use App\Models\ReportType;

class ReportCodeGenerator
{
    /**
     * Genera el siguiente código correlativo único para un tipo de reporte.
     * Ejemplo: FOR-TI-001-2026-0001
     */
    public function generate(ReportType $reportType, ?int $year = null): string
    {
        $year = $year ?: (int) date('Y');
        $formatCode = $reportType->format_code ?: 'FOR-TI-001';
        $prefix = "{$formatCode}-{$year}-";

        // Obtener el último correlativo generado para este prefijo en el año
        $lastReport = Report::where('code', 'like', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = 1;
        if ($lastReport && preg_match('/-(\d+)$/', $lastReport->code, $matches)) {
            $nextNumber = (int) $matches[1] + 1;
        }

        return $prefix . str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
    }
}
