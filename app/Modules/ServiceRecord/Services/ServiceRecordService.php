<?php

namespace App\Modules\ServiceRecord\Services;

use App\Models\Company;
use App\Models\ServiceRecord;
use Illuminate\Support\Facades\Crypt;

class ServiceRecordService
{
    /**
     * Construye las reglas de validación dinámicas basadas en la configuración del tipo.
     */
    public function buildValidationRules(array $config): array
    {
        $rules = [];
        foreach ($config['fields'] as $field) {
            $baseRules = [];
            
            // Requisito
            $baseRules[] = ($field['required'] ?? false) ? 'required' : 'nullable';
            
            // Mapeo Dinámico de Tipos de Datos (Integridad)
            $type = $field['type'] ?? 'text';
            if ($type === 'email') {
                $baseRules[] = 'email';
            } elseif ($type === 'number') {
                $baseRules[] = 'numeric';
            } elseif ($type === 'url') {
                $baseRules[] = 'url';
            } else {
                $baseRules[] = 'string';
            }
            
            $rules[$field['key']] = implode('|', $baseRules);
        }
        return $rules;
    }

    /**
     * Une el prefijo y el dominio de un correo electrónico si corresponde.
     */
    public function applyPokaYokeEmail(string $type, array &$inputData, Company $company): void
    {
        if ($type === 'email' && !empty($inputData['address_prefix'])) {
            $prefix = $inputData['address_prefix'];
            // Poka-Yoke Bug Fix: Si el usuario escribe el @dominio por error, lo limpiamos
            if (str_contains($prefix, '@')) {
                $prefix = explode('@', $prefix)[0];
            }
            
            $domain = $inputData['address_domain'] ?? $company->domain ?? 'dominio.com';
            // Limpiar cualquier @ que el usuario haya puesto en el dominio
            $domain = ltrim($domain, '@');
            
            $inputData['address'] = $prefix . '@' . $domain;
        }
    }

    /**
     * Procesa la creación de un nuevo registro de servicio.
     */
    public function createRecord(Company $company, string $type, array $validatedData): ServiceRecord
    {
        if (isset($validatedData['password']) && $validatedData['password'] !== '') {
            $validatedData['password'] = Crypt::encryptString($validatedData['password']);
        }

        return ServiceRecord::create([
            'company_id' => $company->id,
            'type'       => $type,
            'data'       => $validatedData,
        ]);
    }

    /**
     * Procesa la actualización de un registro de servicio existente.
     */
    public function updateRecord(ServiceRecord $record, array $validatedData): bool
    {
        // Filtro para evitar borrar contraseñas existentes si no se provee una nueva
        if (array_key_exists('password', $validatedData) && $validatedData['password'] === null) {
            unset($validatedData['password']);
        }
        
        $newData = array_merge($record->data, $validatedData);

        if (isset($validatedData['password']) && $validatedData['password'] !== '') {
            $newData['password'] = Crypt::encryptString($validatedData['password']);
        }

        return $record->update(['data' => $newData]);
    }

    /**
     * Alterna rápidamente el estado del registro.
     */
    public function toggleStatus(ServiceRecord $record, string $status): bool
    {
        if (in_array($status, ['Activo', 'Suspendido', 'Bloqueada', 'Inactivo'])) {
            $newData = $record->data;
            $newData['status'] = $status;
            return $record->update(['data' => $newData]);
        }
        return false;
    }

    /**
     * Elimina un registro de servicio.
     */
    public function deleteRecord(ServiceRecord $record): ?bool
    {
        return $record->delete();
    }
}
