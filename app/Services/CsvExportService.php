<?php

namespace App\Services;

use Illuminate\Support\Str;
use App\Models\Company;
use Illuminate\Database\Eloquent\Builder;

class CsvExportService
{
    /**
     * Exporta registros a CSV de forma eficiente usando cursor() para evitar N+1 y colapsos de RAM.
     * Principio SOLID: Responsabilidad Única (SRP). Solo se encarga de formatear y exportar.
     */
    public function export(Company $company, string $type, Builder $query, array $columns)
    {
        $filename = Str::slug($company->name) . '_' . $type . '_export.csv';
        
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($query, $columns) {
            $file = fopen('php://output', 'w');
            
            // BOM para compatibilidad UTF-8 con Excel
            fputs($file, "\xEF\xBB\xBF");
            fputcsv($file, array_values($columns));

            // Uso de cursor() para procesar 1 registro a la vez, liberando RAM
            foreach ($query->cursor() as $record) {
                $row = [];
                $data = $record->data;
                // Prevención Data Leak: Enmascarar contraseña exportada
                if (isset($data['password'])) {
                    $data['password'] = '*** ENMASCARADO ***';
                }

                foreach (array_keys($columns) as $key) {
                    $row[] = $data[$key] ?? '';
                }
                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
