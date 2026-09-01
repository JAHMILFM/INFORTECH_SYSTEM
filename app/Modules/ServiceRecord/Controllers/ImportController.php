<?php

namespace App\Modules\ServiceRecord\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\ServiceRecord;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Crypt;

class ImportController extends Controller
{
    public function show(Company $company)
    {
        $this->requireWriteAccess();
        return view('companies.import', compact('company'));
    }

    public function store(Request $request, Company $company)
    {
        $this->requireWriteAccess();

        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            $file = $request->file('file');
            $spreadsheet = IOFactory::load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();

            $emailsFound = 0;
            $updated = 0;

            // Simple heuristic: Find which column has the most emails
            $colCounts = [];
            foreach ($worksheet->getRowIterator(1, 20) as $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(FALSE);
                foreach ($cellIterator as $idx => $cell) {
                    $val = trim((string)$cell->getValue());
                    if (filter_var($val, FILTER_VALIDATE_EMAIL)) {
                        $colCounts[$idx] = ($colCounts[$idx] ?? 0) + 1;
                    }
                }
            }

            if (empty($colCounts)) {
                return back()->with('error', 'No se encontraron correos electrónicos válidos en el archivo.');
            }

            // The column with the most emails is our target
            arsort($colCounts);
            $emailColIndex = array_key_first($colCounts);

            // First look for a header called 'CONTRASEÑA' or 'PASSWORD'
            $passwordColIndex = null;
            foreach ($worksheet->getRowIterator(1, 10) as $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(FALSE);
                foreach ($cellIterator as $idx => $cell) {
                    $val = strtoupper(trim((string)$cell->getValue()));
                    if (str_contains($val, 'CONTRASEÑA') || str_contains($val, 'PASSWORD') || str_contains($val, 'CLAVE')) {
                        $passwordColIndex = $idx;
                        break 2;
                    }
                }
            }

            // Fallback: just use the column to the right of email
            if ($passwordColIndex === null) {
                $passwordColIndex = ++$emailColIndex;
                $emailColIndex = array_key_first($colCounts); // restore since increment modifies it
            }

            // Pre-cargar todos los correos existentes para evitar N+1 consultas (Index Seek masivo en RAM)
            $existingRecords = ServiceRecord::where('company_id', $company->id)
                ->where('type', 'email')
                ->get()
                ->keyBy(function ($item) {
                    return strtolower($item->data['email'] ?? '');
                });

            \Illuminate\Support\Facades\DB::transaction(function () use ($worksheet, $company, $emailColIndex, $passwordColIndex, &$emailsFound, &$updated, $existingRecords) {
                foreach ($worksheet->getRowIterator() as $row) {
                    $cellIterator = $row->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(FALSE);
                    $rowData = [];
                    foreach ($cellIterator as $idx => $cell) {
                        $rowData[$idx] = $cell->getValue();
                    }

                    $email = isset($rowData[$emailColIndex]) ? trim((string)$rowData[$emailColIndex]) : null;
                    $password = isset($rowData[$passwordColIndex]) ? trim((string)$rowData[$passwordColIndex]) : null;

                    if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $emailKey = strtolower($email);
                        $record = $existingRecords->get($emailKey);

                        $newData = $record ? $record->data : [];
                        $newData['email'] = $email;
                        $newData['status'] = 'Activo';

                        if (!empty($password) && !filter_var($password, FILTER_VALIDATE_URL) && !str_contains($password, 'mail.')) {
                            $newData['password'] = Crypt::encryptString($password);
                        } elseif ($record) {
                            $oldData = $record->data;
                            if (isset($oldData['password'])) {
                                $newData['password'] = $oldData['password'];
                            }
                        }

                        if ($record) {
                            $record->update(['data' => $newData]);
                            $updated++;
                        } else {
                            ServiceRecord::create([
                                'company_id' => $company->id,
                                'type' => 'email',
                                'data' => $newData,
                            ]);
                            $emailsFound++;
                        }
                    }
                }
            });

            return redirect()->route('companies.show', $company->id)
                ->with('success', "Importación completada: {$emailsFound} nuevos correos importados, {$updated} actualizados.");

        } catch (\Exception $e) {
            return back()->with('error', 'Error al procesar el Excel: ' . $e->getMessage());
        }
    }
}
