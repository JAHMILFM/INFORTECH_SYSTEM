<?php

namespace App\Services;

use Illuminate\Support\Str;
use App\Models\Company;
use Illuminate\Database\Eloquent\Builder;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ExcelExportService
{
    /**
     * Exporta registros a Excel (.xlsx) con formato visual (plantilla).
     */
    public function export(Company $company, string $type, Builder $query, array $columns)
    {
        $filename = Str::slug($company->name) . '_' . $type . '_export.xlsx';
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Configurar cabeceras
        $colIndex = 'A';
        foreach (array_values($columns) as $colName) {
            $sheet->setCellValue($colIndex . '1', strtoupper($colName));
            
            // Estilo de cabecera: fondo azul, texto blanco, negrita
            $sheet->getStyle($colIndex . '1')->applyFromArray([
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => Color::COLOR_WHITE],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF4F81BD'], // Azul elegante
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
            ]);
            
            $colIndex++;
        }

        // Llenar datos usando cursor para eficiencia
        $rowIndex = 2;
        foreach ($query->cursor() as $record) {
            $data = $record->data;
            
            if (isset($data['password'])) {
                $data['password'] = '*** ENMASCARADO ***';
            }

            $colIndex = 'A';
            foreach (array_keys($columns) as $key) {
                $sheet->setCellValue($colIndex . $rowIndex, $data[$key] ?? '');
                $colIndex++;
            }
            $rowIndex++;
        }

        // Auto-size a las columnas (excluyendo la última si es muy larga, pero lo hacemos a todas)
        $lastCol = $sheet->getHighestColumn();
        // PHP letters iteration: 'A' to 'E'
        $lastCol++; 
        for ($col = 'A'; $col !== $lastCol; $col++) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
        ];

        $callback = function() use ($writer) {
            $writer->save('php://output');
        };

        return response()->stream($callback, 200, $headers);
    }
}
