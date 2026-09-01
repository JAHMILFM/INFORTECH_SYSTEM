<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceRecord extends Model
{
    use SoftDeletes;
    
    protected $fillable = ['company_id', 'type', 'data', 'status', 'expiration_date'];

    protected $casts = []; // Quitamos data=>array porque lo manejaremos con el mutator

    protected function data(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: function ($value) {
                $data = json_decode((string)$value, true) ?: [];
                return $data;
            },
            set: function ($value) {
                $data = is_string($value) ? json_decode($value, true) : $value;
                
                // Poka-Yoke de Seguridad: Garantizar que CUALQUIER contraseña se encripte al guardar
                if (is_array($data)) {
                    foreach ($data as $key => $val) {
                        if (preg_match('/pass(word)?|key/i', $key) && !empty($val)) {
                            try {
                                \Illuminate\Support\Facades\Crypt::decryptString($val);
                            } catch (\Exception $e) {
                                $data[$key] = \Illuminate\Support\Facades\Crypt::encryptString($val);
                            }
                        }
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
                'label'  => 'Cuentas Zimbra',
                'icon'   => 'fa-envelope',
                'color'  => 'linear-gradient(135deg,#5e72e4,#825ee4)',
                'fields' => [
                    ['key' => 'address',     'label' => 'Dirección de correo',     'type' => 'email', 'required' => true],
                    ['key' => 'name',        'label' => 'Usuario',                 'type' => 'text',  'required' => false],
                    ['key' => 'password',    'label' => 'Contraseña',              'type' => 'text',  'required' => false],
                    ['key' => 'quota',       'label' => 'Cuota (Ej: 5000 MB o Ilimitado)', 'type' => 'text', 'required' => false],
                    ['key' => 'imap_server', 'label' => 'Servidor IMAP',           'type' => 'text',  'required' => false],
                    ['key' => 'smtp_server', 'label' => 'Servidor SMTP',           'type' => 'text',  'required' => false],
                    ['key' => 'status',      'label' => 'Estado',                  'type' => 'select','required' => true,
                     'options' => ['Activo', 'Inactivo', 'Suspendido']],
                    ['key' => 'observacion', 'label' => 'Observación',             'type' => 'text',  'required' => false],
                ],
                'columns' => ['address' => 'Correo', 'name' => 'Usuario', 'password' => 'Contraseña', 'imap_server' => 'IMAP', 'smtp_server' => 'SMTP', 'status' => 'Estado', 'observacion' => 'Observación', 'quota' => 'Cuota'],
            ],
            'domain_alias' => [
                'label'  => 'Dominios',
                'icon'   => 'fa-globe',
                'color'  => 'linear-gradient(135deg,#825ee4,#e45ebb)',
                'fields' => [
                    ['key' => 'domain',     'label' => 'Dominio alias',   'type' => 'text',   'required' => true],
                    ['key' => 'proveedor',  'label' => 'Proveedor',       'type' => 'text',   'required' => false],
                    ['key' => 'vencimiento','label' => 'Fecha de vencimiento', 'type' => 'date', 'required' => false],
                    ['key' => 'costo',      'label' => 'Costo',           'type' => 'number', 'required' => false],
                    ['key' => 'usuario',    'label' => 'Usuario',         'type' => 'text',   'required' => false],
                    ['key' => 'password',   'label' => 'Contraseña',      'type' => 'password', 'required' => false],
                    ['key' => 'link',       'label' => 'Link',            'type' => 'url',    'required' => false],
                    ['key' => 'status',     'label' => 'Estado',          'type' => 'select', 'required' => true,
                     'options' => ['Activo', 'Inactivo']],
                ],
                'columns' => ['domain' => 'Dominio', 'proveedor' => 'Proveedor', 'vencimiento' => 'Vencimiento', 'costo' => 'Costo', 'usuario' => 'Usuario', 'password' => 'Contraseña', 'link' => 'Link', 'status' => 'Estado'],
            ],
            'cpanel' => [
                'label'  => 'CPanel / Hosting',
                'icon'   => 'fa-server',
                'color'  => 'linear-gradient(135deg,#f5365c,#f56036)',
                'fields' => [
                    ['key' => 'url',        'label' => 'URL de Acceso',       'type' => 'url',      'required' => true],
                    ['key' => 'username',   'label' => 'Usuario',             'type' => 'text',     'required' => true],
                    ['key' => 'password',   'label' => 'Contraseña',          'type' => 'password', 'required' => true],
                    ['key' => 'dominio',    'label' => 'Dominio Principal',   'type' => 'text',     'required' => false],
                    ['key' => 'nota',       'label' => 'Nota / Observación',  'type' => 'text',     'required' => false],
                    ['key' => 'status',     'label' => 'Estado',              'type' => 'select',   'required' => true, 'options' => ['Activo', 'Inactivo']],
                ],
                'columns' => ['url' => 'URL', 'username' => 'Usuario', 'password' => 'Contraseña', 'dominio' => 'Dominio Principal'],
            ],
            'local_machines' => [
                'label'  => 'Equipos',
                'icon'   => 'fa-desktop',
                'color'  => 'linear-gradient(135deg,#2dce89,#2dcecc)',
                'fields' => [
                    ['key' => 'pc_name',    'label' => 'Nombre de la PC',     'type' => 'text',     'required' => true],
                    ['key' => 'anydesk',    'label' => 'ID AnyDesk/TeamViewer','type' => 'text',    'required' => false],
                    ['key' => 'username',   'label' => 'Usuario Windows/Mac', 'type' => 'text',     'required' => false],
                    ['key' => 'password',   'label' => 'Contraseña Máquina',  'type' => 'password', 'required' => false],
                    ['key' => 'admin_user', 'label' => 'Usuario Administrador', 'type' => 'text',   'required' => false],
                    ['key' => 'admin_pass', 'label' => 'Contraseña Administrador', 'type' => 'password', 'required' => false],
                    ['key' => 'encargado',  'label' => 'Usuario Encargado',   'type' => 'text',     'required' => false],
                    ['key' => 'nota',       'label' => 'Nota / Observación',  'type' => 'text',     'required' => false],
                    ['key' => 'status',     'label' => 'Estado',              'type' => 'select',   'required' => true, 'options' => ['Activo', 'Inactivo']],
                ],
                'columns' => ['pc_name' => 'PC', 'anydesk' => 'AnyDesk', 'username' => 'Usuario', 'password' => 'Contraseña', 'admin_user' => 'Usr. Admin', 'admin_pass' => 'Pass Admin', 'encargado' => 'Encargado'],
            ],

            'inventario' => [
                'label'  => 'Inventario',
                'icon'   => 'fa-archive',
                'color'  => 'linear-gradient(135deg,#ff9966,#ff5e62)',
                'fields' => [
                    ['key' => 'numero', 'label' => 'Número', 'type' => 'text', 'required' => true],
                    ['key' => 'responsable', 'label' => 'Responsable', 'type' => 'text', 'required' => false],
                    ['key' => 'usuario', 'label' => 'Usuario', 'type' => 'text', 'required' => false],
                    ['key' => 'areas', 'label' => 'Áreas', 'type' => 'text', 'required' => false],
                    ['key' => 'sede', 'label' => 'Sede', 'type' => 'text', 'required' => false],
                    ['key' => 'categoria', 'label' => 'Categoría', 'type' => 'text', 'required' => false],
                    ['key' => 'serie', 'label' => 'Serie', 'type' => 'text', 'required' => false],
                    ['key' => 'marca', 'label' => 'Marca', 'type' => 'text', 'required' => false],
                    ['key' => 'estabilizador', 'label' => 'Estabilizador', 'type' => 'select', 'required' => false, 'options' => ['Sí', 'No']],
                    ['key' => 'cable_de_red', 'label' => 'Cable de red', 'type' => 'select', 'required' => false, 'options' => ['Sí', 'No']],
                    ['key' => 'adaptador', 'label' => 'Adaptador', 'type' => 'select', 'required' => false, 'options' => ['Sí', 'No']],
                    ['key' => 'perifericos', 'label' => 'Periféricos', 'type' => 'textarea', 'required' => false],
                    ['key' => 'procesador', 'label' => 'Procesador', 'type' => 'text', 'required' => false],
                    ['key' => 'sistema_operativo', 'label' => 'Sistema Operativo', 'type' => 'text', 'required' => false],
                    ['key' => 'ram', 'label' => 'RAM', 'type' => 'text', 'required' => false],
                    ['key' => 'disco', 'label' => 'Disco', 'type' => 'text', 'required' => false],
                    ['key' => 'tipo_de_hdd', 'label' => 'Tipo de HDD', 'type' => 'text', 'required' => false],
                    ['key' => 'estado', 'label' => 'Estado', 'type' => 'select', 'required' => false, 'options' => ['Operativo', 'Inoperativo', 'En Reparación', 'Baja']],
                    ['key' => 'monitor', 'label' => 'Monitor', 'type' => 'text', 'required' => false],
                    ['key' => 'tarjeta_de_video', 'label' => 'Tarjeta de Video', 'type' => 'text', 'required' => false],
                    ['key' => 'hostname', 'label' => 'Hostname', 'type' => 'text', 'required' => false],
                    ['key' => 'ip', 'label' => 'IP', 'type' => 'text', 'required' => false],
                    ['key' => 'status', 'label' => 'Status Gral.', 'type' => 'select', 'required' => true, 'options' => ['Activo', 'Inactivo']],
                ],
                'columns' => [
                    'numero' => 'N°', 'responsable' => 'Responsable', 'usuario' => 'Usuario', 
                    'areas' => 'Áreas', 'sede' => 'Sede', 'categoria' => 'Categoría', 
                    'serie' => 'Serie', 'marca' => 'Marca', 'estabilizador' => 'Estabilizador', 
                    'cable_de_red' => 'Cable Red', 'adaptador' => 'Adaptador', 
                    'perifericos' => 'Periféricos', 'procesador' => 'Procesador', 
                    'sistema_operativo' => 'S.O.', 'ram' => 'RAM', 'disco' => 'Disco', 
                    'tipo_de_hdd' => 'Tipo HDD', 'estado' => 'Estado Equipo', 'monitor' => 'Monitor', 
                    'tarjeta_de_video' => 'T. Video', 'hostname' => 'Hostname', 'ip' => 'IP',
                    'status' => 'Activo'
                ],
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
            'standard_email' => [
                'label'  => 'Email',
                'icon'   => 'fa-envelope-o',
                'color'  => 'linear-gradient(135deg,#11cdef,#1171ef)',
                'fields' => [
                    ['key' => 'address',     'label' => 'Dirección de correo',     'type' => 'email',    'required' => true],
                    ['key' => 'name',        'label' => 'Usuario',                 'type' => 'text',     'required' => false],
                    ['key' => 'password',    'label' => 'Contraseña',              'type' => 'password', 'required' => false],
                    ['key' => 'quota',       'label' => 'Cuota (Ej: 5000 MB o Ilimitado)', 'type' => 'text', 'required' => false],
                    ['key' => 'imap_server', 'label' => 'Servidor IMAP',           'type' => 'text',     'required' => false],
                    ['key' => 'smtp_server', 'label' => 'Servidor SMTP',           'type' => 'text',     'required' => false],
                    ['key' => 'pop3_server', 'label' => 'Servidor POP3',           'type' => 'text',     'required' => false],
                    ['key' => 'status',      'label' => 'Estado',                  'type' => 'select',   'required' => true,
                     'options' => ['Activo', 'Inactivo', 'Suspendido']],
                    ['key' => 'observacion', 'label' => 'Observación',             'type' => 'text',     'required' => false],
                ],
                'columns' => ['address' => 'Correo', 'name' => 'Usuario', 'password' => 'Contraseña', 'imap_server' => 'IMAP', 'smtp_server' => 'SMTP', 'pop3_server' => 'POP3', 'status' => 'Estado', 'observacion' => 'Observación', 'quota' => 'Cuota'],
            ],
            'onedrive_user' => [
                'label'  => 'Cuentas OneDrive',
                'icon'   => 'fa-cloud',
                'color'  => 'linear-gradient(135deg,#11cdef,#1171ef)',
                'fields' => [
                    ['key' => 'name',       'label' => 'Nombre Completo',     'type' => 'text',     'required' => true],
                    ['key' => 'username',   'label' => 'Usuario',             'type' => 'text',     'required' => true],
                    ['key' => 'password',   'label' => 'Contraseña',          'type' => 'password', 'required' => false],
                    ['key' => 'permissions','label' => 'Áreas / Permisos',    'type' => 'textarea', 'required' => false],
                    ['key' => 'status',     'label' => 'Estado',              'type' => 'select',   'required' => true, 'options' => ['Activo', 'Inactivo']],
                ],
                'columns' => ['name' => 'Nombre', 'username' => 'Usuario', 'password' => 'Contraseña', 'permissions' => 'Áreas'],
            ],
            'nextcloud_user' => [
                'label'  => 'Cuentas Nextcloud',
                'icon'   => 'fa-cloud',
                'color'  => 'linear-gradient(135deg,#11cdef,#1171ef)',
                'fields' => [
                    ['key' => 'name',       'label' => 'Nombre Completo',     'type' => 'text',     'required' => true],
                    ['key' => 'username',   'label' => 'Usuario',             'type' => 'text',     'required' => true],
                    ['key' => 'password',   'label' => 'Contraseña',          'type' => 'password', 'required' => false],
                    ['key' => 'permissions','label' => 'Áreas / Permisos',    'type' => 'textarea', 'required' => false],
                    ['key' => 'status',     'label' => 'Estado',              'type' => 'select',   'required' => true, 'options' => ['Activo', 'Inactivo']],
                ],
                'columns' => ['name' => 'Nombre', 'username' => 'Usuario', 'password' => 'Contraseña', 'permissions' => 'Áreas'],
            ],
            'antivirus_license' => [
                'label'  => 'Licencias Antivirus',
                'icon'   => 'fa-shield',
                'color'  => 'linear-gradient(135deg,#fb6340,#fbb140)',
                'fields' => [
                    ['key' => 'product',    'label' => 'Producto (Ej. ESET)', 'type' => 'text',     'required' => true],
                    ['key' => 'license_key','label' => 'Clave de Licencia',   'type' => 'password', 'required' => true],
                    ['key' => 'devices',    'label' => 'Cantidad Equipos',    'type' => 'number',   'required' => false],
                    ['key' => 'expiration', 'label' => 'Fecha Expiración',    'type' => 'date',     'required' => false],
                    ['key' => 'assigned_to','label' => 'Asignado a',          'type' => 'text',     'required' => false],
                    ['key' => 'status',     'label' => 'Estado',              'type' => 'select',   'required' => true, 'options' => ['Activo', 'Expirado', 'Inactivo']],
                ],
                'columns' => ['product' => 'Producto', 'license_key' => 'Clave', 'devices' => 'Equipos', 'expiration' => 'Vence', 'assigned_to' => 'Asignado a', 'status' => 'Estado'],
            ],
            'autodesk_license' => [
                'label'  => 'Licencias Autodesk',
                'icon'   => 'fa-pencil-square-o',
                'color'  => 'linear-gradient(135deg,#5e72e4,#825ee4)',
                'fields' => [
                    ['key' => 'email',      'label' => 'Correo Asociado',     'type' => 'email',    'required' => true],
                    ['key' => 'password',   'label' => 'Contraseña',          'type' => 'password', 'required' => false],
                    ['key' => 'product',    'label' => 'Producto (Ej. AutoCAD)','type' => 'text',   'required' => true],
                    ['key' => 'assigned_to','label' => 'Usuario Asignado',    'type' => 'text',     'required' => false],
                    ['key' => 'expiration', 'label' => 'Fecha Expiración',    'type' => 'date',     'required' => false],
                    ['key' => 'status',     'label' => 'Estado',              'type' => 'select',   'required' => true, 'options' => ['Activo', 'Expirado', 'Inactivo']],
                ],
                'columns' => ['email' => 'Correo', 'password' => 'Contraseña', 'product' => 'Producto', 'assigned_to' => 'Asignado a', 'expiration' => 'Vence', 'status' => 'Estado'],
            ],
        ];
    }
}
