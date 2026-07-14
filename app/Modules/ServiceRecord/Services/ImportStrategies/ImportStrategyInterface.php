<?php

namespace App\Modules\ServiceRecord\Services\ImportStrategies;

use App\Models\Company;

interface ImportStrategyInterface
{
    /**
     * Procesa las filas parseadas de un archivo y las inserta en la base de datos de forma optimizada.
     *
     * @param array $rows Las filas extraídas del archivo (array asociativo o numérico según el caso).
     * @param Company $company La empresa a la que pertenecen los registros.
     * @param string $serviceType El tipo de registro (ej. email, vps).
     * @return array Un arreglo con el número de importados y omitidos: ['imported' => X, 'skipped' => Y]
     */
    public function import(array $rows, Company $company, string $serviceType): array;
}
