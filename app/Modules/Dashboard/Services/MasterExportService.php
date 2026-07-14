<?php

namespace App\Modules\Dashboard\Services;

use App\Models\ServiceRecord;
use Illuminate\Http\Response;

class MasterExportService
{
    /**
     * Genera la exportación maestra en formato CSV.
     * SRP: Aísla la lógica de exportación del controlador.
     */
    public function exportCSV()
    {
        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=infortech_master_export_" . date('Y-m-d') . ".csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            
            // BOM for Excel UTF-8 support
            fputs($file, "\xEF\xBB\xBF");

            // Header row
            fputcsv($file, ['ID Servicio', 'Empresa', 'Dominio', 'Tipo Servicio', 'Datos (JSON)', 'Fecha Creacion'], ';');

            foreach (ServiceRecord::with('company')->cursor() as $service) {
                // Prevención Data Leak: No exportar hashes de contraseñas
                $exportData = $service->data;
                if (isset($exportData['password'])) {
                    $exportData['password'] = '*** ENMASCARADO ***';
                }

                fputcsv($file, [
                    $service->id,
                    $service->company->name ?? 'N/A',
                    $service->company->domain ?? 'N/A',
                    strtoupper($service->type),
                    json_encode($exportData, JSON_UNESCAPED_UNICODE),
                    $service->created_at->format('Y-m-d H:i:s')
                ], ';');
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
