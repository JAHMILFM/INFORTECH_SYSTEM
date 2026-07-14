<?php

namespace App\Modules\Search\Services;

use App\Models\Company;
use App\Models\ServiceRecord;
use Illuminate\Support\Collection;

class SearchService
{
    /**
     * Realiza búsquedas seguras y optimizadas.
     * Principio SOLID: Responsabilidad Única (SRP). Centraliza la lógica de búsqueda.
     */
    public function searchGlobal(string $query): array
    {
        $companyResults = collect();
        $serviceResults = collect();

        if ($query !== '') {
            $companyResults = Company::where('name', 'like', "%{$query}%")
                ->orWhere('domain', 'like', "%{$query}%")
                ->take(30) // Límite para escalabilidad
                ->get();

            // Búsqueda en el JSON de ServiceRecord.
            // Para máxima compatibilidad con SQLite y evitar problemas de case-sensitivity,
            // buscamos en la columna 'data' cruda como texto.
            $serviceResults = ServiceRecord::with('company')
                ->where('data', 'like', "%{$query}%")
                ->take(30)
                ->get();
        }

        return [
            'companies' => $companyResults,
            'services'  => $serviceResults,
        ];
    }
}
