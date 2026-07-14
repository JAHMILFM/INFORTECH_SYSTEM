<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MigrateCategoriesToFolders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'infortech:migrate-categories-to-folders';

    protected $description = 'Migrates document categories from metadata into actual Folder records';

    public function handle()
    {
        $documents = \App\Models\Document::all();
        $count = 0;

        foreach ($documents as $doc) {
            $category = $doc->metadata['category'] ?? 'General';
            
            // Buscar o crear la carpeta para esta empresa
            $folder = \App\Models\Folder::firstOrCreate([
                'company_id' => $doc->company_id,
                'name' => $category,
                'parent_id' => null
            ]);

            // Asignar el documento a la carpeta
            $doc->folder_id = $folder->id;
            
            // Limpiar la categoría de la metadata para evitar redundancia
            $meta = $doc->metadata;
            unset($meta['category']);
            $doc->metadata = $meta;
            
            $doc->save();
            $count++;
        }

        $this->info("Migrados $count documentos a sus respectivas carpetas.");
    }
}
