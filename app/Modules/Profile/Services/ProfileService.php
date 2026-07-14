<?php

namespace App\Modules\Profile\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Exception;

class ProfileService
{
    /**
     * Actualiza el perfil del usuario autenticado.
     */
    public function updateProfile(User $user, array $data): bool
    {
        return $user->update($data);
    }

    /**
     * Actualiza la contraseña del usuario.
     */
    public function changePassword(User $user, string $newPassword): bool
    {
        return $user->update([
            'password' => Hash::make($newPassword),
        ]);
    }
}
