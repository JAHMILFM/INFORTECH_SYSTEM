<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceRecord extends Model
{
    protected $fillable = ['company_id', 'type', 'data'];

    protected $casts = []; // Quitamos data=>array porque lo manejaremos con el mutator

    protected function data(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: function ($value) {
                $data = json_decode((string)$value, true) ?: [];
                if (isset($data['password']) && $data['password'] !== '') {
                    try {
                        $data['password'] = \Illuminate\Support\Facades\Crypt::decryptString($data['password']);
                    } catch (\Exception $e) {
                        // Si falla, es porque está en texto plano (aún no migrado)
                    }
                }
                return $data;
            },
            set: function ($value) {
                $data = is_string($value) ? json_decode($value, true) : $value;
                
                // Poka-Yoke de Seguridad: Garantizar que la contraseña SIEMPRE se encripte al guardar
                if (isset($data['password']) && $data['password'] !== '') {
                    try {
                        // Intentamos desencriptar. Si falla, es porque es texto plano.
                        \Illuminate\Support\Facades\Crypt::decryptString($data['password']);
                    } catch (\Exception $e) {
                        // Es texto plano, la encriptamos.
                        $data['password'] = \Illuminate\Support\Facades\Crypt::encryptString($data['password']);
                    }
                }
                
                return json_encode($data);
            },
        );
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    // Tipos válidos con su config de campos
    public static function typeConfig(): array
    {
        return [
            'email' => [
                'label'  => 'Email',
                'icon'   => 'fa-envelope',
                'color'  => 'linear-gradient(135deg,#5e72e4,#825ee4)',
                'fields' => [
                    ['key' => 'address',     'label' => 'Dirección de correo', 'type' => 'email',    'required' => true],
                    ['key' => 'name',        'label' => 'Nombre completo',     'type' => 'text',     'required' => true],
                    ['key' => 'password',    'label' => 'Contraseña',          'type' => 'text',     'required' => false],
                    ['key' => 'quota',       'label' => 'Cuota (MB)',          'type' => 'number',   'required' => false],
                    ['key' => 'status',      'label' => 'Estado',              'type' => 'select',   'required' => true,
                     'options' => ['Activo', 'Inactivo', 'Suspendido']],
                    ['key' => 'observacion', 'label' => 'Observación',         'type' => 'text',     'required' => false],
                ],
                'columns' => ['address' => 'Correo', 'name' => 'Nombre', 'password' => 'Contraseña', 'status' => 'Estado', 'observacion' => 'Observación'],
            ],
            'account' => [
                'label'  => 'Accounts',
                'icon'   => 'fa-users',
                'color'  => 'linear-gradient(135deg,#11cdef,#1171ef)',
                'fields' => [
                    ['key' => 'username',     'label' => 'Usuario',              'type' => 'text',   'required' => true],
                    ['key' => 'email',        'label' => 'Email Admin',          'type' => 'email',  'required' => true],
                    ['key' => 'password',     'label' => 'Contraseña',           'type' => 'text',   'required' => false],
                    ['key' => 'host',         'label' => 'Host / Servidor',      'type' => 'text',   'required' => false],
                    ['key' => 'url',          'label' => 'URL Panel Admin Zimbra','type' => 'url',    'required' => false],
                    ['key' => 'webmail_link', 'label' => 'URL Webmail',          'type' => 'url',    'required' => false],
                    ['key' => 'role',         'label' => 'Rol',                  'type' => 'select', 'required' => true,
                     'options' => ['Administrador', 'Usuario', 'Solo lectura']],
                    ['key' => 'status',       'label' => 'Estado',               'type' => 'select', 'required' => true,
                     'options' => ['Activo', 'Inactivo']],
                    ['key' => 'nota',         'label' => 'Nota',                 'type' => 'text',   'required' => false],
                ],
                'columns' => ['email' => 'Email / Usuario', 'password' => 'Contraseña', 'url' => 'Panel URL', 'role' => 'Rol'],
            ],
            'email_alias' => [
                'label'  => 'Email Aliases',
                'icon'   => 'fa-at',
                'color'  => 'linear-gradient(135deg,#2dce89,#2dcecc)',
                'fields' => [
                    ['key' => 'alias',   'label' => 'Alias (dirección)',  'type' => 'email', 'required' => true],
                    ['key' => 'target',  'label' => 'Redirige a',         'type' => 'email', 'required' => true],
                    ['key' => 'status',  'label' => 'Estado',             'type' => 'select','required' => true,
                     'options' => ['Activo', 'Inactivo']],
                ],
                'columns' => ['alias' => 'Alias', 'target' => 'Redirige a', 'status' => 'Estado'],
            ],
            'distribution_list' => [
                'label'  => 'Distribution Lists',
                'icon'   => 'fa-list-ul',
                'color'  => 'linear-gradient(135deg,#fb6340,#fbb140)',
                'fields' => [
                    ['key' => 'name',    'label' => 'Nombre del grupo',  'type' => 'text',     'required' => true],
                    ['key' => 'address', 'label' => 'Dirección del grupo','type' => 'email',    'required' => true],
                    ['key' => 'members', 'label' => 'Miembros (separados por coma)', 'type' => 'textarea', 'required' => false],
                    ['key' => 'status',  'label' => 'Estado',            'type' => 'select',   'required' => true,
                     'options' => ['Activo', 'Inactivo']],
                ],
                'columns' => ['name' => 'Grupo', 'address' => 'Dirección', 'status' => 'Estado'],
            ],
            'domain_alias' => [
                'label'  => 'Domain Aliases',
                'icon'   => 'fa-globe',
                'color'  => 'linear-gradient(135deg,#825ee4,#e45ebb)',
                'fields' => [
                    ['key' => 'domain',  'label' => 'Dominio alias',   'type' => 'text',   'required' => true],
                    ['key' => 'status',  'label' => 'Estado',          'type' => 'select', 'required' => true,
                     'options' => ['Activo', 'Inactivo']],
                ],
                'columns' => ['domain' => 'Dominio', 'status' => 'Estado'],
            ],
            'other_credentials' => [
                'label'  => 'Otras Plataformas',
                'icon'   => 'fa-key',
                'color'  => 'linear-gradient(135deg,#f5365c,#f56036)',
                'fields' => [
                    ['key' => 'plataforma', 'label' => 'Plataforma (Ej. Nextcloud, CPanel)', 'type' => 'text', 'required' => true],
                    ['key' => 'url',        'label' => 'URL de Acceso',                      'type' => 'url',  'required' => false],
                    ['key' => 'username',   'label' => 'Usuario / Email',                    'type' => 'text', 'required' => true],
                    ['key' => 'password',   'label' => 'Contraseña',                         'type' => 'password', 'required' => true],
                    ['key' => 'nota',       'label' => 'Nota / Observación',                 'type' => 'text', 'required' => false],
                    ['key' => 'status',     'label' => 'Estado',                             'type' => 'select', 'required' => true,
                     'options' => ['Activo', 'Inactivo']],
                ],
                'columns' => ['plataforma' => 'Plataforma', 'username' => 'Usuario', 'password' => 'Contraseña', 'url' => 'URL', 'status' => 'Estado'],
            ],
        ];
    }
}
