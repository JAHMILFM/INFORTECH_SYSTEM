<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AuditLog extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'user_id', 'action', 'company_id', 'service_record_id', 'target_user_id',
        'old_data', 'new_data', 'ip_address', 'user_agent'
    ];

    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
    ];

    protected static function booted()
    {
        static::updating(function ($model) {
            throw new \Exception('Security Violation: Audit logs cannot be modified.');
        });

        static::deleting(function ($model) {
            throw new \Exception('Security Violation: Audit logs cannot be deleted.');
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function targetUser()
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function serviceRecord()
    {
        return $this->belongsTo(ServiceRecord::class);
    }
}
