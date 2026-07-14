<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait BelongsToTenant
{
    /**
     * Aplica el filtro automáticamente a todas las consultas de los modelos que usen este trait.
     */
    protected static function bootBelongsToTenant(): void
    {
        static::addGlobalScope('tenant', function (Builder $builder) {
            // Solo aplica si hay un usuario autenticado
            if (auth()->check()) {
                // Filtra por el company_id del usuario logueado en la tabla correspondiente
                $builder->where($builder->getQuery()->from . '.company_id', auth()->user()->company_id);
            }
        });
    }
}
