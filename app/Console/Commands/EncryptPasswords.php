<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class EncryptPasswords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'security:encrypt-passwords';
    protected $description = 'Escanea todos los ServiceRecords y encripta las contraseñas en texto plano usando AES-256';

    public function handle()
    {
        $this->info('Iniciando migración criptográfica...');
        
        $records = \App\Models\ServiceRecord::all();
        $encryptedCount = 0;
        $skippedCount = 0;

        // Desactivamos temporalmente el observer de ServiceRecord para no llenar los logs de auditoría por esto
        \App\Models\ServiceRecord::flushEventListeners();

        foreach ($records as $record) {
            $data = $record->data; // Esto pasará por el Mutator "get" que intentará desencriptar.
            // Si el texto original era plano, el Mutator "get" falla la desencriptación silenciosamente y nos da el plano.
            
            if (is_array($data) && !empty($data['password'])) {
                // Forzamos guardar de nuevo. El Mutator "set" detectará que no está encriptado y lo encriptará.
                // Usamos DB::table para no disparar eventos ni mutators raros si preferimos controlar el JSON directo.
                // Pero como ya hicimos el Mutator seguro, podemos simplemente re-asignar.
                $record->data = $data;
                $record->saveQuietly(); 
                $encryptedCount++;
            } else {
                $skippedCount++;
            }
        }

        $this->info("Migración completada. Encriptados/Verificados: {$encryptedCount}. Omitidos (Sin password): {$skippedCount}");
    }
}
