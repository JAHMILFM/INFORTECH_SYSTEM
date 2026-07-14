<?php

namespace App\Modules\User\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Exception;

class UserService
{
    /**
     * Crea un nuevo usuario.
     */
    public function createUser(array $data): User
    {
        // Prevención Escalada de Privilegios: Solo SuperAdmin puede crear SuperAdmin
        if ($data['role'] === 'SuperAdmin' && auth()->user() && auth()->user()->role !== 'SuperAdmin') {
            throw new Exception('Acción bloqueada: No tienes privilegios para crear un usuario SuperAdmin.');
        }

        return User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => $data['role'],
        ]);
    }

    /**
     * Actualiza un usuario con validaciones de seguridad anti-bloqueo.
     */
    public function updateUser(User $user, array $data): bool
    {
        // Prevención Escalada de Privilegios: Solo SuperAdmin puede asignar el rol SuperAdmin
        if (isset($data['role']) && $data['role'] === 'SuperAdmin' && auth()->user() && auth()->user()->role !== 'SuperAdmin') {
            throw new Exception('Acción bloqueada: No tienes privilegios para asignar el rol de SuperAdmin.');
        }

        // Regla de Seguridad: Prevenir que el único SuperAdmin cambie su propio rol
        if (isset($data['role']) && $data['role'] !== 'SuperAdmin' && $user->role === 'SuperAdmin') {
            $superAdminCount = User::where('role', 'SuperAdmin')->count();
            if ($superAdminCount <= 1) {
                throw new Exception('Acción bloqueada: Eres el único SuperAdmin en el sistema. No puedes degradar tu propio rol.');
            }
        }

        $updateData = [
            'name'  => $data['name'],
            'email' => $data['email'],
            'role'  => $data['role'],
        ];

        if (isset($data['password']) && $data['password'] !== '') {
            $updateData['password'] = Hash::make($data['password']);
        }

        return $user->update($updateData);
    }

    /**
     * Elimina un usuario con validaciones de seguridad.
     */
    public function deleteUser(User $user): bool
    {
        if ($user->id === auth()->id()) {
            throw new Exception('Acción bloqueada: No puedes eliminarte a ti mismo.');
        }

        return $user->delete();
    }
}
