<?php

namespace App\Modules\ServiceRecord\Services\ImportStrategies;

use App\Models\Company;
use App\Models\ServiceRecord;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GenericImportService implements ImportStrategyInterface
{
    public function import(array $rows, Company $company, string $serviceType): array
    {
        $imported = 0;
        $skipped  = 0;
        
        DB::transaction(function () use ($rows, $company, $serviceType, &$imported, &$skipped) {
            $now = Carbon::now();
            $inserts = [];
            $headers = null;

            foreach ($rows as $i => $row) {
                $vals = array_values($row);
                
                // Skip empty rows completely before setting headers
                if (empty(array_filter($vals))) { 
                    $skipped++; 
                    continue; 
                }
                
                if ($headers === null) {
                    $headers = array_map('strtolower', array_map('trim', array_filter($vals)));
                    continue;
                }
                
                $data = [];
                foreach ($headers as $j => $h) {
                    if ($h && isset($vals[$j])) {
                        $data[$h] = $vals[$j];
                    }
                }
                
                if ($i === 1 || $i === 2) {
                    var_dump("ROW $i DATA:", $data);
                }
                
                if (!empty($data)) {
                    $inserts[] = [
                        'company_id' => $company->id,
                        'type'       => $serviceType,
                        'data'       => json_encode($data, JSON_UNESCAPED_UNICODE),
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                    $imported++;
                }
            }

            if (!empty($inserts)) {
                // Bulk Insert chunking 500 at a time to prevent packet too large errors
                $chunks = array_chunk($inserts, 500);
                foreach ($chunks as $chunk) {
                    ServiceRecord::insert($chunk);
                }
            }
        });

        return ['imported' => $imported, 'skipped' => $skipped];
    }
}
