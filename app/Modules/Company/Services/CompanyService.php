<?php

namespace App\Modules\Company\Services;

use App\Models\Company;

class CompanyService
{
    /**
     * Registra una nueva empresa.
     */
    public function createCompany(array $data): Company
    {
        return Company::create($data);
    }

    /**
     * Actualiza los datos de una empresa.
     */
    public function updateCompany(Company $company, array $data): bool
    {
        return $company->update($data);
    }

    /**
     * Elimina una empresa.
     */
    public function deleteCompany(Company $company): ?bool
    {
        return $company->delete();
    }
}
