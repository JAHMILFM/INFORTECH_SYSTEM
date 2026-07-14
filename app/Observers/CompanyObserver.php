<?php

namespace App\Observers;

use App\Models\Company;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class CompanyObserver
{
    private function log($action, Company $company)
    {
        AuditLog::create([
            'user_id'    => Auth::id(),
            'action'     => $action,
            'company_id' => $action === 'DELETED' ? null : $company->id,
            'service_record_id' => null,
            'old_data'   => $action !== 'CREATED' ? $company->getOriginal() : null,
            'new_data'   => $action !== 'DELETED' ? $company->getAttributes() : null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public function created(Company $company): void { $this->log('CREATED', $company); }
    public function updated(Company $company): void { $this->log('UPDATED', $company); }
    public function deleted(Company $company): void { $this->log('DELETED', $company); }
}
