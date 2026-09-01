<?php

namespace App\Observers;

use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class UserObserver
{
    private function log($action, User $targetUser)
    {
        $oldData = $action !== 'CREATED' ? $targetUser->getOriginal() : null;
        $newData = $action !== 'DELETED' ? $targetUser->getAttributes() : null;

        // Limpiar contraseñas de los datos de auditoría
        if (is_array($oldData) && array_key_exists('password', $oldData)) {
            $oldData['password'] = '********';
        }
        if (is_array($newData) && array_key_exists('password', $newData)) {
            $newData['password'] = '********';
        }

        AuditLog::create([
            'user_id'         => Auth::check() ? Auth::id() : null, // El autor de la acción
            'action'          => $action,
            'company_id'      => null,
            'service_record_id' => null,
            'target_user_id'  => $targetUser->id, // El usuario que fue modificado
            'old_data'        => $oldData,
            'new_data'        => $newData,
            'ip_address'      => request()->ip(),
            'user_agent'      => request()->userAgent(),
        ]);
    }

    public function created(User $user): void { $this->log('CREATED', $user); }
    public function updated(User $user): void { $this->log('UPDATED', $user); }
    public function deleted(User $user): void { $this->log('DELETED', $user); }
}
