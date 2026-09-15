<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'job_title',
        'phone',
        'document_id',
        'signature_data',
        'signature_updated_at',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'signature_updated_at' => 'datetime',
        ];
    }

    /**
     * Determina si el usuario tiene una firma digital registrada.
     */
    public function hasSignature(): bool
    {
        return !empty($this->signature_data);
    }

    /**
     * Obtiene el cargo del usuario o un valor por defecto profesional.
     */
    public function getJobTitleOrDefault(): string
    {
        return $this->job_title ?: 'Técnico Especialista Infortech';
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }
}
