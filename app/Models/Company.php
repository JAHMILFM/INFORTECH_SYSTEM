<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = ['name', 'tax_id', 'domain', 'contact_email', 'is_active', 'status', 'onboarding_stage', 'account_manager_id', 'allowed_services'];


    public function serviceRecords()
    {
        return $this->hasMany(ServiceRecord::class);
    }

    protected $casts = [
        'allowed_services' => 'array',
    ];

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    public function accountManager()
    {
        return $this->belongsTo(User::class, 'account_manager_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($company) {
            // Eliminar registros de servicio individualmente para disparar los eventos de auditoría y aplicar cascade soft-delete
            $company->serviceRecords->each->delete();
        });
    }
}
