<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class Report extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'report_type_id',
        'code',
        'company_id',
        'equipment_id',
        'technician_id',
        'technician_name',
        'service_date',
        'status',
        'data',
        'notes',
    ];

    protected $casts = [
        'data' => 'array',
        'service_date' => 'date',
    ];

    public function reportType()
    {
        return $this->belongsTo(ReportType::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    public function software()
    {
        return $this->hasMany(ReportSoftware::class);
    }

    public function signatures()
    {
        return $this->hasMany(ReportSignature::class);
    }

    public function deliverySignature()
    {
        return $this->hasOne(ReportSignature::class)->where('role', 'delivery');
    }

    public function receptionSignature()
    {
        return $this->hasOne(ReportSignature::class)->where('role', 'reception');
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    /**
     * Retorna la clave descifrada únicamente para usuarios autorizados en vistas protegidas.
     * NUNCA se muestra en documentos impresos o exportados a clientes.
     */
    public function getDecryptedPasswordAttribute(): ?string
    {
        $encrypted = $this->data['encrypted_access_password'] ?? null;
        if (!$encrypted) return null;

        try {
            return Crypt::decryptString($encrypted);
        } catch (DecryptException $e) {
            return null;
        }
    }
}
