<?php

namespace App\Http\Controllers;

abstract class Controller
{
    /**
     * Verifica si el usuario actual tiene permisos de escritura (Soporte o SuperAdmin).
     */
    protected function requireWriteAccess()
    {
        $user = auth()->user();
        if (!$user || !in_array($user->role, ['SuperAdmin', 'Soporte'])) {
            $roleName = $user ? $user->role : 'Visitante Anónimo';
            abort(403, 'Acceso Denegado: Tu rol actual (' . $roleName . ') es de solo lectura y no permite modificar datos.');
        }
    }
}
