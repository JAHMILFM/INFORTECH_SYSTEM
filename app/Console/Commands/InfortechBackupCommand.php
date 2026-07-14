<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class InfortechBackupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'infortech:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crea una copia de seguridad segura de la base de datos de Infortech';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando backup de la base de datos...');

        $dbPath = database_path('database.sqlite');
        
        if (!File::exists($dbPath)) {
            $this->error('No se encontró la base de datos SQLite.');
            return Command::FAILURE;
        }

        $backupDir = storage_path('app/backups');
        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $date = now()->format('Y-m-d_H-i-s');
        $backupFilename = "infortech_backup_{$date}.sqlite";
        $backupPath = $backupDir . '/' . $backupFilename;

        // Copiar el archivo
        File::copy($dbPath, $backupPath);

        $this->info("Backup creado exitosamente: {$backupPath}");

        return Command::SUCCESS;
    }
}
