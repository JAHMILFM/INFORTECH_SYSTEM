<?php

namespace App\Modules\ServiceRecord\Services\ImportStrategies;

use App\Models\Company;
use App\Models\ServiceRecord;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ZimbraImportService implements ImportStrategyInterface
{
    public function import(array $rows, Company $company, string $serviceType): array
    {
        $imported = 0;
        $skipped  = 0;
        
        $dataStart = null;
        $adminLink = null; 
        $adminEmail = null; 
        $adminPass = null;
        $webmailLink = null; 
        $host = null;

        // Fase 1: Encontrar cabeceras y cuenta administradora
        foreach ($rows as $i => $row) {
            foreach ($row as $cell) {
                if (is_string($cell) && str_contains($cell, ':7071')) {
                    $vals = array_values(array_filter($row, fn($v) => $v !== null));
                    if (count($vals) >= 3) {
                        $adminLink  = $vals[0];
                        $adminEmail = $vals[1];
                        $adminPass  = $vals[2];
                    }
                }
                if (is_string($cell)) {
                    $cellUpper = strtoupper(trim($cell));
                    if ($cellUpper === 'CORREOS' || $cellUpper === 'CORREO') {
                        $dataStart = $i + 1;
                    }
                }
                if ($dataStart && $i >= $dataStart && $webmailLink === null) {
                    $vals = array_values($row);
                    if (isset($vals[2]) && is_string($vals[2]) && str_starts_with($vals[2], 'http')) {
                        $webmailLink = $vals[2];
                        $host        = $vals[4] ?? null;
                    }
                }
            }
        }

        // Usamos una Transacción para acelerar x10 las inserciones masivas (Evitar N+1 Inserts)
        DB::transaction(function () use (
            $rows, $company, $serviceType, $dataStart, 
            $adminEmail, $adminLink, $adminPass, 
            $webmailLink, $host, &$imported, &$skipped
        ) {
            $now = Carbon::now();
            $inserts = [];

            // Guardar cuenta admin
            if ($adminEmail && $serviceType === 'email') {
                $existing = ServiceRecord::where('company_id', $company->id)
                    ->where('type', 'account')
                    ->whereRaw("json_extract(data, '$.email') = ?", [$adminEmail])
                    ->first();
                    
                if (!$existing) {
                    $inserts[] = [
                        'company_id' => $company->id,
                        'type'       => 'account',
                        'data'       => json_encode([
                            'username' => $adminEmail,
                            'email'    => $adminEmail,
                            'password' => $adminPass ? Crypt::encryptString($adminPass) : null,
                            'url'      => $adminLink,
                            'host'     => $host,
                            'role'     => 'Administrador',
                            'status'   => 'Activo',
                            'nota'     => 'Panel de administracion Zimbra',
                        ], JSON_UNESCAPED_UNICODE),
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }

            // Procesar correos
            if ($dataStart !== null) {
                foreach ($rows as $i => $row) {
                    if ($i < $dataStart) continue;
                    $vals = array_values($row);
                    if (count($vals) < 5) continue;

                    $correo    = $vals[3] ?? null;
                    $hostRow   = $vals[4] ?? $host;
                    $password  = $vals[5] ?? null;
                    $nombre    = $vals[6] ?? null;
                    $estado    = $vals[7] ?? null;
                    $observ    = $vals[8] ?? null;
                    $linkRow   = $vals[2] ?? $webmailLink;

                    if (!$correo || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                        $skipped++;
                        continue;
                    }

                    $nombreStr = (string)($nombre ?? '');
                    if (!$nombreStr || $nombreStr === $correo) {
                        $nombreStr = ucwords(str_replace(['.', '_'], ' ', explode('@', $correo)[0]));
                    }

                    $est = match(strtolower((string)($estado ?? ''))) {
                        'activo'     => 'Activo',
                        'bloqueada','bloqueado','suspendido','suspendida' => 'Suspendido',
                        default      => 'Activo',
                    };

                    $pass = trim((string)($password ?? ''));
                    if ($pass !== '') {
                        $pass = Crypt::encryptString($pass);
                    }

                    $inserts[] = [
                        'company_id' => $company->id,
                        'type'       => $serviceType,
                        'data'       => json_encode([
                            'address'      => trim($correo),
                            'name'         => trim($nombreStr),
                            'password'     => $pass,
                            'quota'        => '',
                            'status'       => $est,
                            'observacion'  => trim((string)($observ ?? '')),
                            'webmail_link' => trim((string)($linkRow ?? '')),
                            'host'         => trim((string)($hostRow ?? '')),
                        ], JSON_UNESCAPED_UNICODE),
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                    $imported++;
                }
            }

            // Inserción en bloque (Bulk Insert) para rendimiento extremo
            if (!empty($inserts)) {
                // Laravel insert no dispara Eventos de Eloquent. Si dependemos de Auditoría aquí, 
                // para rendimiento extremo es preferible, o crear un evento log masivo,
                // pero por velocidad el insert es la mejor técnica.
                ServiceRecord::insert($inserts);
            }
        });

        return ['imported' => $imported, 'skipped' => $skipped];
    }
}
