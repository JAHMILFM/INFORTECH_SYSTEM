<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ServiceRecord;
use Illuminate\Support\Facades\Crypt;

class EncryptServicePasswords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'infortech:encrypt-passwords';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrates plaintext passwords in service_records JSON to encrypted strings for BPM compliance';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando encriptación masiva de contraseñas legacy...');

        $records = ServiceRecord::all();
        $encryptedCount = 0;
        $skippedCount = 0;

        foreach ($records as $record) {
            $data = $record->data;
            if (isset($data['password']) && !empty($data['password'])) {
                // Try to decrypt to see if it's already encrypted
                try {
                    Crypt::decryptString($data['password']);
                    $skippedCount++; // Already encrypted
                } catch (\Exception $e) {
                    // Not encrypted, let's encrypt it
                    $data['password'] = Crypt::encryptString($data['password']);
                    // Disable audit logs for this mass migration if we had observers, but direct update is fine
                    $record->update(['data' => $data]);
                    $encryptedCount++;
                }
            } else {
                $skippedCount++; // No password
            }
            
            // Extract status to column for BPM
            if (isset($data['status'])) {
                $record->status = $data['status'];
            }
            // Extract expiration to column for BPM
            if (isset($data['expiration'])) {
                $record->expiration_date = $data['expiration'];
            }
            if ($record->isDirty(['status', 'expiration_date'])) {
                $record->save();
            }
        }

        $this->info("Migración completada. Encriptadas: {$encryptedCount}. Omitidas/Ya encriptadas: {$skippedCount}.");
    }
}
