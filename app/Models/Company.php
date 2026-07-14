<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Company extends Model
{
    use HasFactory;
    
    protected $fillable = ['name', 'tax_id', 'domain', 'contact_email', 'is_active'];

    public function serviceRecords()
    {
        return $this->hasMany(ServiceRecord::class);
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($company) {
            // Desvincular logs de auditoría para evitar restricción de clave foránea
            $company->auditLogs()->update(['company_id' => null, 'service_record_id' => null]);
            
            // Eliminar registros de servicio (disparando sus propios eventos de borrado si los tienen)
            $company->serviceRecords()->delete();
        });
    }
}
