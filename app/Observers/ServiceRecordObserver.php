<?php

namespace App\Observers;

use App\Models\ServiceRecord;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class ServiceRecordObserver
{
    private function log($action, ServiceRecord $serviceRecord)
    {
        // Encriptar / Ofuscar password en el log si queremos seguridad extra
        $old = $action !== 'CREATED' ? $serviceRecord->getOriginal() : null;
        $new = $action !== 'DELETED' ? $serviceRecord->getAttributes() : null;

        // Prevención Data Leak: Enmascarar contraseña en los logs de auditoría
        if ($old && isset($old['data'])) {
            $oldData = is_string($old['data']) ? json_decode($old['data'], true) : $old['data'];
            if (is_array($oldData) && isset($oldData['password'])) {
                $oldData['password'] = '*** ENMASCARADO ***';
                $old['data'] = json_encode($oldData);
            }
        }
        if ($new && isset($new['data'])) {
            $newData = is_string($new['data']) ? json_decode($new['data'], true) : $new['data'];
            if (is_array($newData) && isset($newData['password'])) {
                $newData['password'] = '*** ENMASCARADO ***';
                $new['data'] = json_encode($newData);
            }
        }

        AuditLog::create([
            'user_id'    => Auth::id(),
            'action'     => $action,
            'company_id' => $serviceRecord->company_id,
            'service_record_id' => $action === 'DELETED' ? null : $serviceRecord->id,
            'old_data'   => $old,
            'new_data'   => $new,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public function created(ServiceRecord $serviceRecord): void { $this->log('CREATED', $serviceRecord); }
    public function updated(ServiceRecord $serviceRecord): void { $this->log('UPDATED', $serviceRecord); }
    public function deleted(ServiceRecord $serviceRecord): void { $this->log('DELETED', $serviceRecord); }
}
