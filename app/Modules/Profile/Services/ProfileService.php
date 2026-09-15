<?php

namespace App\Modules\Profile\Services;

use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Hash;
use Exception;

class ProfileService
{
    /**
     * Actualiza el perfil profesional y personal del usuario autenticado.
     */
    public function updateProfile(User $user, array $data): bool
    {
        $oldData = [
            'name'        => $user->name,
            'email'       => $user->email,
            'job_title'   => $user->job_title,
            'phone'       => $user->phone,
            'document_id' => $user->document_id,
        ];

        $updated = $user->update([
            'name'        => $data['name'],
            'email'       => $data['email'],
            'job_title'   => $data['job_title'] ?? null,
            'phone'       => $data['phone'] ?? null,
            'document_id' => $data['document_id'] ?? null,
        ]);

        if ($updated) {
            AuditLog::create([
                'user_id'    => $user->id,
                'action'     => 'UPDATE_PROFILE',
                'old_data'   => $oldData,
                'new_data'   => [
                    'name'        => $user->name,
                    'email'       => $user->email,
                    'job_title'   => $user->job_title,
                    'phone'       => $user->phone,
                    'document_id' => $user->document_id,
                ],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }

        return $updated;
    }

    /**
     * Registra o actualiza la firma digital oficial del usuario.
     */
    public function updateSignature(User $user, string $signatureData): bool
    {
        $updated = $user->update([
            'signature_data'       => $signatureData,
            'signature_updated_at' => now(),
        ]);

        if ($updated) {
            AuditLog::create([
                'user_id'    => $user->id,
                'action'     => 'UPDATE_DIGITAL_SIGNATURE',
                'new_data'   => [
                    'signature_updated_at' => now()->toIso8601String(),
                ],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }

        return $updated;
    }

    /**
     * Elimina la firma digital oficial del usuario.
     */
    public function deleteSignature(User $user): bool
    {
        $updated = $user->update([
            'signature_data'       => null,
            'signature_updated_at' => null,
        ]);

        if ($updated) {
            AuditLog::create([
                'user_id'    => $user->id,
                'action'     => 'DELETE_DIGITAL_SIGNATURE',
                'new_data'   => ['status' => 'signature_removed'],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }

        return $updated;
    }

    /**
     * Actualiza la contraseña del usuario.
     */
    public function changePassword(User $user, string $newPassword): bool
    {
        $updated = $user->update([
            'password' => Hash::make($newPassword),
        ]);

        if ($updated) {
            AuditLog::create([
                'user_id'    => $user->id,
                'action'     => 'CHANGE_PASSWORD',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }

        return $updated;
    }
}
