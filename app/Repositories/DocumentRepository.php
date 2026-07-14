<?php

namespace App\Repositories;

use App\Models\Document;
use Illuminate\Database\Eloquent\Collection;

class DocumentRepository
{
    /**
     * Obtiene los documentos recientes junto a sus creadores (Optimizado).
     * Nota: El filtrado por 'company_id' ya se hace solo vía el Trait BelongsToTenant.
     */
    public function getRecentDocuments(int $limit = 50): Collection
    {
        return Document::query()
            // EAGER LOADING: Selecciona solo el id, nombre y email del usuario para ahorrar RAM
            ->with(['creator:id,name,email']) 
            ->latest()
            ->limit($limit)
            ->get();
    }
}
