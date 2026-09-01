@extends('layouts.app')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>
                <span style="display:inline-flex;align-items:center;justify-content:center;width:34px;height:34px;border-radius:8px;background:{{ $config['color'] }};margin-right:10px;vertical-align:middle;">
                    <i class="fa {{ $config['icon'] }}" style="color:#fff;font-size:15px;"></i>
                </span>
                {{ $config['label'] }} — {{ $company->name }}
            </h4>
            <p class="mb-0">
                <i class="fa fa-globe me-1 text-primary"></i>{{ $company->domain ?? 'Sin dominio' }}
            </p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex align-items-center gap-2 flex-wrap">
        <a href="{{ route('companies.show', $company->id) }}" class="btn btn-secondary btn-sm">
            <i class="fa fa-arrow-left me-1"></i> Volver
        </a>
        @if($records->count() > 0)
        <a href="{{ route('companies.services.export', [$company->id, $type]) }}" class="btn btn-dark btn-sm">
            <i class="fa fa-download me-1"></i> Exportar CSV
        </a>
        @endif
        @if($type === 'email')
        <a href="{{ route('companies.import.show', $company->id) }}" class="btn btn-success btn-sm text-white">
            <i class="fa fa-file-excel-o me-1"></i> Importar Excel
        </a>
        @endif
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addRecordModal">
            <i class="fa fa-plus me-1"></i> Agregar
        </button>
    </div>
</div>

{{-- Alertas --}}
@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <strong><i class="fa fa-exclamation-triangle me-2"></i> Error de Validación:</strong>
    <ul class="mb-0 mt-1">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fa fa-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(in_array($type, ['email', 'account', 'zimbra_admin']))
    @php
        $zimbraRecord = \App\Models\ServiceRecord::where('company_id', $company->id)
            ->where('type', 'account')
            ->where('data->role', 'Administrador')
            ->where(function($q) {
                $q->whereNull('data->plataforma')->orWhere('data->plataforma', 'Zimbra');
            })
            ->first();
        $adminData = $zimbraRecord ? $zimbraRecord->data : [];
        
        $webmailLink = $adminData['webmail_link'] ?? ($company->domain ? 'https://mail.'.$company->domain : null);
        $host        = $adminData['host'] ?? ($company->domain ? 'mail.'.$company->domain : null);
    @endphp

    <div class="d-flex justify-content-between align-items-end mb-2 mt-4">
        <h5 class="mb-0 text-muted fw-bold text-uppercase" style="font-size: 0.85rem; letter-spacing: 1px;">
            <i class="fa fa-server me-1"></i> Configuración Global Zimbra
        </h5>
        <button class="btn btn-sm btn-outline-primary" style="border-radius: 20px;" data-bs-toggle="modal" data-bs-target="#zimbraConfigModal">
            <i class="fa fa-cog me-1"></i> Configurar Panel
        </button>
    </div>

    {{-- MODAL CONFIGURACIÓN ZIMBRA --}}
    <div class="modal fade" id="zimbraConfigModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #11cdef, #1171ef);">
                    <h5 class="modal-title text-white"><i class="fa fa-cog me-2"></i> Configuración Global Zimbra</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('companies.updateZimbraConfig', $company->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Host / Servidor</label>
                            <input type="text" name="zimbra_host" class="form-control" placeholder="mail.ejemplo.com" value="{{ $host }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">URL Webmail</label>
                            <input type="url" name="zimbra_webmail" class="form-control" placeholder="https://mail.ejemplo.com" value="{{ $webmailLink }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">URL Panel Admin</label>
                            <input type="url" name="zimbra_panel" class="form-control" placeholder="https://mail.ejemplo.com:7071" value="{{ $adminData['url'] ?? '' }}">
                        </div>
                        <hr>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Usuario Administrador (Email)</label>
                            <input type="email" name="zimbra_username" class="form-control" placeholder="admin@ejemplo.com" value="{{ $adminData['email'] ?? '' }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Contraseña Admin</label>
                            <input type="text" name="zimbra_password" class="form-control" placeholder="Dejar en blanco para no cambiar">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary"><i class="fa fa-save me-1"></i> Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        {{-- Host & Webmail --}}
        <div class="col-xl-3 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm h-100" style="border-left:4px solid #5e72e4 !important;">
                <div class="card-body py-3 px-4">
                    <div class="d-flex align-items-center mb-1">
                        <div style="width:36px;height:36px;background:linear-gradient(135deg,#5e72e4,#825ee4);border-radius:8px;display:flex;align-items:center;justify-content:center;margin-right:10px;flex-shrink:0;">
                            <i class="fa fa-server" style="color:#fff;font-size:15px;"></i>
                        </div>
                        <small class="text-muted fw-semibold text-uppercase" style="font-size:11px;letter-spacing:1px;">Host / Servidor</small>
                    </div>
                    <p class="mb-0 fw-bold" style="font-family:monospace;font-size:14px;">{{ $host ?? '—' }}</p>
                </div>
            </div>
        </div>

        {{-- Webmail link --}}
        <div class="col-xl-3 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm h-100" style="border-left:4px solid #2dce89 !important;">
                <div class="card-body py-3 px-4">
                    <div class="d-flex align-items-center mb-1">
                        <div style="width:36px;height:36px;background:linear-gradient(135deg,#2dce89,#2dcecc);border-radius:8px;display:flex;align-items:center;justify-content:center;margin-right:10px;flex-shrink:0;">
                            <i class="fa fa-globe" style="color:#fff;font-size:15px;"></i>
                        </div>
                        <small class="text-muted fw-semibold text-uppercase" style="font-size:11px;letter-spacing:1px;">Webmail</small>
                    </div>
                    @if($webmailLink)
                        <a href="{{ $webmailLink }}" target="_blank" class="fw-bold text-success" style="font-size:13px;word-break:break-all;">{{ $webmailLink }}</a>
                    @else
                        <p class="mb-0 text-muted">—</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Admin Panel Zimbra --}}
        <div class="col-xl-3 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm h-100" style="border-left:4px solid #f5365c !important;">
                <div class="card-body py-3 px-4">
                    <div class="d-flex align-items-center mb-1">
                        <div style="width:36px;height:36px;background:linear-gradient(135deg,#f5365c,#f56036);border-radius:8px;display:flex;align-items:center;justify-content:center;margin-right:10px;flex-shrink:0;">
                            <i class="fa fa-shield" style="color:#fff;font-size:15px;"></i>
                        </div>
                        <small class="text-muted fw-semibold text-uppercase" style="font-size:11px;letter-spacing:1px;">Panel Admin Zimbra</small>
                    </div>
                    @if(!empty($adminData['url']))
                        <a href="{{ $adminData['url'] }}" target="_blank" class="fw-bold text-danger" style="font-size:12px;word-break:break-all;">{{ $adminData['url'] }}</a>
                    @else
                        <p class="mb-0 text-muted">—</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Admin Credentials --}}
        <div class="col-xl-3 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm h-100" style="border-left:4px solid #fb6340 !important;">
                <div class="card-body py-3 px-4">
                    <div class="d-flex align-items-center mb-2">
                        <div style="width:36px;height:36px;background:linear-gradient(135deg,#fb6340,#fbb140);border-radius:8px;display:flex;align-items:center;justify-content:center;margin-right:10px;flex-shrink:0;">
                            <i class="fa fa-key" style="color:#fff;font-size:15px;"></i>
                        </div>
                        <small class="text-muted fw-semibold text-uppercase" style="font-size:11px;letter-spacing:1px;">Credenciales Admin</small>
                    </div>
                    @if(!empty($adminData['email']))
                        <div class="mb-1">
                            <small class="text-muted">Usuario:</small><br>
                            <code style="font-size:12px;background:#fff3e0;color:#212529 !important;font-weight:bold;padding:1px 5px;border-radius:4px;">{{ $adminData['email'] }}</code>
                            <button onclick="navigator.clipboard.writeText('{{ addslashes($adminData['email']) }}').then(() => { if(typeof toastr !== 'undefined') toastr.success('Email copiado'); })"
                                    class="btn btn-xs btn-outline-secondary ms-1 py-0 px-1" title="Copiar">
                                <i class="fa fa-copy"></i>
                            </button>
                        </div>
                        <div>
                            <small class="text-muted">Contraseña:</small><br>
                            <span class="admin-pass-mask" style="font-family:monospace;letter-spacing:2px;font-size:13px;">••••••••</span>
                            <span class="admin-pass-text d-none" style="font-family:monospace;font-size:12px;background:#fff3e0;color:#212529 !important;font-weight:bold;padding:1px 5px;border-radius:4px;"></span>
                            <button class="btn btn-xs btn-outline-secondary ms-1 py-0 px-1 toggle-admin-pass-global" title="Mostrar" data-company-id="{{ $company->id }}">
                                <i class="fa fa-eye"></i>
                            </button>
                            <button onclick="let pwd = this.closest('div').querySelector('.admin-pass-text').innerText; if(pwd) { navigator.clipboard.writeText(pwd).then(() => { if(typeof toastr !== 'undefined') toastr.success('Contraseña copiada'); }); } else { if(typeof toastr !== 'undefined') toastr.error('Desbloquea la contraseña primero'); }"
                                    class="btn btn-xs btn-outline-primary ms-1 py-0 px-1" title="Copiar">
                                <i class="fa fa-copy"></i>
                            </button>
                        </div>
                    @else
                        <p class="mb-0 text-muted">No configurado</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endif

@if(in_array($type, ['nextcloud_user', 'nextcloud']))
    @php
        $nextcloudRecord = \App\Models\ServiceRecord::where('company_id', $company->id)
            ->where('type', 'account')
            ->where('data->role', 'Administrador')
            ->where('data->plataforma', 'Nextcloud')
            ->first();
        $nextcloudData = $nextcloudRecord ? $nextcloudRecord->data : [];
    @endphp

    <div class="d-flex justify-content-between align-items-end mb-2 mt-4">
        <h5 class="mb-0 text-muted fw-bold text-uppercase" style="font-size: 0.85rem; letter-spacing: 1px;">
            <i class="fa fa-cloud me-1"></i> Configuración Global Nextcloud
        </h5>
        <button class="btn btn-sm btn-outline-info" style="border-radius: 20px;" data-bs-toggle="modal" data-bs-target="#nextcloudConfigModal">
            <i class="fa fa-cog me-1"></i> Configurar Nextcloud
        </button>
    </div>

    {{-- MODAL CONFIGURACIÓN NEXTCLOUD --}}
    <div class="modal fade" id="nextcloudConfigModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #11cdef, #1171ef);">
                    <h5 class="modal-title text-white"><i class="fa fa-cloud me-2"></i> Configuración Global Nextcloud</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('companies.updateNextcloudConfig', $company->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">URL Nextcloud</label>
                            <input type="url" name="nextcloud_url" class="form-control" placeholder="https://cloud.ejemplo.com" value="{{ $nextcloudData['url'] ?? '' }}">
                        </div>
                        <hr>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Usuario Administrador</label>
                            <input type="text" name="nextcloud_username" class="form-control" placeholder="admin" value="{{ $nextcloudData['username'] ?? '' }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Contraseña Admin</label>
                            <input type="text" name="nextcloud_password" class="form-control" placeholder="Dejar en blanco para no cambiar">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary"><i class="fa fa-save me-1"></i> Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        {{-- URL Nextcloud --}}
        <div class="col-xl-6 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm h-100" style="border-left:4px solid #11cdef !important;">
                <div class="card-body py-3 px-4">
                    <div class="d-flex align-items-center mb-1">
                        <div style="width:36px;height:36px;background:linear-gradient(135deg,#11cdef,#1171ef);border-radius:8px;display:flex;align-items:center;justify-content:center;margin-right:10px;flex-shrink:0;">
                            <i class="fa fa-cloud" style="color:#fff;font-size:15px;"></i>
                        </div>
                        <small class="text-muted fw-semibold text-uppercase" style="font-size:11px;letter-spacing:1px;">Acceso Nextcloud</small>
                    </div>
                    @if(!empty($nextcloudData['url']))
                        <a href="{{ $nextcloudData['url'] }}" target="_blank" class="fw-bold text-info" style="font-size:13px;word-break:break-all;">{{ $nextcloudData['url'] }}</a>
                    @else
                        <p class="mb-0 text-muted">—</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Admin Credentials Nextcloud --}}
        <div class="col-xl-6 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm h-100" style="border-left:4px solid #fb6340 !important;">
                <div class="card-body py-3 px-4">
                    <div class="d-flex align-items-center mb-2">
                        <div style="width:36px;height:36px;background:linear-gradient(135deg,#fb6340,#fbb140);border-radius:8px;display:flex;align-items:center;justify-content:center;margin-right:10px;flex-shrink:0;">
                            <i class="fa fa-key" style="color:#fff;font-size:15px;"></i>
                        </div>
                        <small class="text-muted fw-semibold text-uppercase" style="font-size:11px;letter-spacing:1px;">Credenciales Admin Nextcloud</small>
                    </div>
                    @if(!empty($nextcloudData['username']))
                        <div class="mb-1">
                            <small class="text-muted">Usuario:</small><br>
                            <code style="font-size:12px;background:#fff3e0;color:#212529 !important;font-weight:bold;padding:1px 5px;border-radius:4px;">{{ $nextcloudData['username'] }}</code>
                            <button onclick="navigator.clipboard.writeText('{{ addslashes($nextcloudData['username']) }}').then(() => { if(typeof toastr !== 'undefined') toastr.success('Usuario copiado'); })"
                                    class="btn btn-xs btn-outline-secondary ms-1 py-0 px-1" title="Copiar">
                                <i class="fa fa-copy"></i>
                            </button>
                        </div>
                        <div>
                            <small class="text-muted">Contraseña:</small><br>
                            <span class="nextcloud-pass-mask" style="font-family:monospace;letter-spacing:2px;font-size:13px;">••••••••</span>
                            <span class="nextcloud-pass-text d-none" style="font-family:monospace;font-size:12px;background:#fff3e0;color:#212529 !important;font-weight:bold;padding:1px 5px;border-radius:4px;"></span>
                            <button class="btn btn-xs btn-outline-secondary ms-1 py-0 px-1 toggle-nextcloud-pass-global" title="Mostrar" data-company-id="{{ $company->id }}">
                                <i class="fa fa-eye"></i>
                            </button>
                            <button onclick="let pwd = this.closest('div').querySelector('.nextcloud-pass-text').innerText; if(pwd) { navigator.clipboard.writeText(pwd).then(() => { if(typeof toastr !== 'undefined') toastr.success('Contraseña copiada'); }); } else { if(typeof toastr !== 'undefined') toastr.error('Desbloquea la contraseña primero'); }"
                                    class="btn btn-xs btn-outline-primary ms-1 py-0 px-1" title="Copiar">
                                <i class="fa fa-copy"></i>
                            </button>
                        </div>
                    @else
                        <p class="mb-0 text-muted">No configurado</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endif

@if(!in_array($type, ['email', 'account', 'zimbra_admin', 'nextcloud_user', 'nextcloud']))
    @php
        $adminRecord = \App\Models\ServiceRecord::where('company_id', $company->id)
            ->where('type', 'admin_' . $type)
            ->first();
        $adminData = $adminRecord ? $adminRecord->data : [];
    @endphp

    <div class="d-flex justify-content-between align-items-end mb-2 mt-4">
        <h5 class="mb-0 text-muted fw-bold text-uppercase" style="font-size: 0.85rem; letter-spacing: 1px;">
            <i class="fa fa-key me-1"></i> Credenciales de Administrador del Servicio
        </h5>
        <button class="btn btn-sm btn-outline-primary" style="border-radius: 20px;" data-bs-toggle="modal" data-bs-target="#serviceAdminConfigModal">
            <i class="fa fa-cog me-1"></i> Configurar Administrador
        </button>
    </div>

    {{-- MODAL CONFIGURACIÓN ADMINISTRADOR GENERICO --}}
    <div class="modal fade" id="serviceAdminConfigModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background: {{ $config['color'] ?? 'linear-gradient(135deg, #11cdef, #1171ef)' }};">
                    <h5 class="modal-title text-white"><i class="fa fa-key me-2"></i> Configurar Admin — {{ $config['label'] }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('companies.services.updateAdminConfig', [$company->id, $type]) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">URL de Acceso / Panel</label>
                            <input type="url" name="admin_url" class="form-control" placeholder="https://..." value="{{ $adminData['url'] ?? '' }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Usuario Administrador</label>
                            <input type="text" name="admin_username" class="form-control" placeholder="admin" value="{{ $adminData['username'] ?? '' }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Contraseña Administrador</label>
                            <input type="password" name="admin_password" class="form-control" placeholder="Dejar en blanco para no cambiar">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Notas / Observaciones</label>
                            <textarea name="admin_notes" class="form-control" rows="3" placeholder="Detalles de acceso, IPs, etc.">{{ $adminData['notes'] ?? '' }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary"><i class="fa fa-save me-1"></i> Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        {{-- URL Acceso --}}
        <div class="col-xl-4 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm h-100" style="border-left:4px solid #5e72e4 !important;">
                <div class="card-body py-3 px-4">
                    <div class="d-flex align-items-center mb-1">
                        <div style="width:36px;height:36px;background:linear-gradient(135deg,#5e72e4,#825ee4);border-radius:8px;display:flex;align-items:center;justify-content:center;margin-right:10px;flex-shrink:0;">
                            <i class="fa fa-link" style="color:#fff;font-size:15px;"></i>
                        </div>
                        <small class="text-muted fw-semibold text-uppercase" style="font-size:11px;letter-spacing:1px;">URL de Acceso</small>
                    </div>
                    @if(!empty($adminData['url']))
                        <a href="{{ $adminData['url'] }}" target="_blank" class="fw-bold text-primary" style="font-size:13px;word-break:break-all;">{{ $adminData['url'] }}</a>
                    @else
                        <p class="mb-0 text-muted">—</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Credenciales --}}
        <div class="col-xl-4 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm h-100" style="border-left:4px solid #fb6340 !important;">
                <div class="card-body py-3 px-4">
                    <div class="d-flex align-items-center mb-2">
                        <div style="width:36px;height:36px;background:linear-gradient(135deg,#fb6340,#fbb140);border-radius:8px;display:flex;align-items:center;justify-content:center;margin-right:10px;flex-shrink:0;">
                            <i class="fa fa-user-secret" style="color:#fff;font-size:15px;"></i>
                        </div>
                        <small class="text-muted fw-semibold text-uppercase" style="font-size:11px;letter-spacing:1px;">Credenciales Admin</small>
                    </div>
                    @if(!empty($adminData['username']))
                        <div class="mb-1">
                            <small class="text-muted">Usuario:</small>
                            <code style="font-size:12px;background:#fff3e0;color:#212529 !important;font-weight:bold;padding:1px 5px;border-radius:4px;">{{ $adminData['username'] }}</code>
                            <button onclick="navigator.clipboard.writeText('{{ addslashes($adminData['username']) }}').then(() => { if(typeof toastr !== 'undefined') toastr.success('Usuario copiado'); })"
                                    class="btn btn-xs btn-outline-secondary ms-1 py-0 px-1" title="Copiar">
                                <i class="fa fa-copy"></i>
                            </button>
                        </div>
                        <div>
                            <small class="text-muted">Contraseña:</small>
                            <span class="generic-pass-mask" style="font-family:monospace;letter-spacing:2px;font-size:13px;">••••••••</span>
                            <span class="generic-pass-text d-none" style="font-family:monospace;font-size:12px;background:#fff3e0;color:#212529 !important;font-weight:bold;padding:1px 5px;border-radius:4px;"></span>
                            <button class="btn btn-xs btn-outline-secondary ms-1 py-0 px-1 toggle-generic-pass" title="Mostrar" data-company-id="{{ $company->id }}" data-type="{{ $type }}">
                                <i class="fa fa-eye"></i>
                            </button>
                            <button onclick="let pwd = this.closest('div').querySelector('.generic-pass-text').innerText; if(pwd) { navigator.clipboard.writeText(pwd).then(() => { if(typeof toastr !== 'undefined') toastr.success('Contraseña copiada'); }); } else { if(typeof toastr !== 'undefined') toastr.error('Desbloquea la contraseña primero'); }"
                                    class="btn btn-xs btn-outline-primary ms-1 py-0 px-1" title="Copiar">
                                <i class="fa fa-copy"></i>
                            </button>
                        </div>
                    @else
                        <p class="mb-0 text-muted">No configurado</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Notas --}}
        <div class="col-xl-4 col-sm-12 mb-3">
            <div class="card border-0 shadow-sm h-100" style="border-left:4px solid #2dce89 !important;">
                <div class="card-body py-3 px-4">
                    <div class="d-flex align-items-center mb-1">
                        <div style="width:36px;height:36px;background:linear-gradient(135deg,#2dce89,#2dcecc);border-radius:8px;display:flex;align-items:center;justify-content:center;margin-right:10px;flex-shrink:0;">
                            <i class="fa fa-sticky-note" style="color:#fff;font-size:15px;"></i>
                        </div>
                        <small class="text-muted fw-semibold text-uppercase" style="font-size:11px;letter-spacing:1px;">Notas</small>
                    </div>
                    <p class="mb-0 text-dark" style="font-size:12px; white-space: pre-line;">{{ $adminData['notes'] ?? '—' }}</p>
                </div>
            </div>
        </div>
    </div>
@endif

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h4 class="card-title mb-0">
                    Registros de {{ $config['label'] }}
                    <span class="badge badge-primary ms-2">{{ $records->count() }}</span>
                </h4>
                {{-- Resumen rápido --}}
                @php
                    $activos     = $records->filter(fn($r) => ($r->data['status'] ?? '') === 'Activo')->count();
                    $suspendidos = $records->filter(fn($r) => ($r->data['status'] ?? '') === 'Suspendido')->count();
                    $conObs      = $records->filter(fn($r) => !empty($r->data['observacion']))->count();
                @endphp
                @if($records->count() > 0)
                <div class="d-flex gap-2 flex-wrap">
                    <span class="badge bg-success px-3 py-2"><i class="fa fa-check-circle me-1"></i>{{ $activos }} Activos</span>
                    @if($suspendidos > 0)
                    <span class="badge bg-warning px-3 py-2"><i class="fa fa-ban me-1"></i>{{ $suspendidos }} Suspendidos</span>
                    @endif
                    @if($conObs > 0)
                    <span class="badge bg-danger px-3 py-2"><i class="fa fa-exclamation-circle me-1"></i>{{ $conObs }} con observaciones</span>
                    @endif
                </div>
                @endif
            </div>
            <div class="card-body">
                {{-- Buscador rápido --}}
                @if($records->count() > 5)
                <div class="mb-3">
                    <div class="input-group" style="max-width:380px;">
                        <span class="input-group-text bg-white"><i class="fa fa-search text-muted"></i></span>
                        <input type="text" id="quickSearch" class="form-control" placeholder="Buscar en esta tabla...">
                    </div>
                </div>
                @endif

                @php
                    $colCount = count($config['columns']);
                    $tableMinWidth = $colCount > 8 ? ($colCount * 130) . 'px' : 'auto';
                @endphp
                <div class="table-responsive" style="overflow-x:auto; scrollbar-width: thin; scrollbar-color: #adb5bd #f8f9fa; max-height:75vh; overflow-y:auto;">
                    <table class="table table-hover table-bordered align-middle" id="serviceTable" style="min-width:{{ $tableMinWidth }};font-size:12px;">
                        <thead class="sticky-top" style="z-index:2;">
                            <tr style="background:{{ $config['color'] }};color:#fff;">
                                @if($type !== 'inventario')
                                <th style="width:40px;background:inherit;">N°</th>
                                @endif
                                @foreach($config['columns'] as $key => $label)
                                    <th style="white-space:nowrap;background:inherit;">{{ $label }}</th>
                                @endforeach
                                <th style="width:90px;white-space:nowrap;position:sticky;right:0;background:inherit;z-index:3;box-shadow:-2px 0 4px rgba(0,0,0,0.1);">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            @forelse($records as $i => $record)
                            <tr class="table-row" id="row_{{ $record->id }}">
                                @if($type !== 'inventario')
                                <td class="text-center text-muted">{{ ($records->currentPage() - 1) * $records->perPage() + $i + 1 }}</td>
                                @endif
                                @foreach($config['columns'] as $key => $label)
                                    <td style="white-space:nowrap;">
                                        @php $val = $record->data[$key] ?? '—'; @endphp
                                        @if($key === 'status')
                                            <div class="form-check form-switch d-flex align-items-center gap-2">
                                                <input class="form-check-input" type="checkbox" role="switch" 
                                                    id="statusToggle_{{ $record->id }}"
                                                    onchange="toggleStatus({{ $record->id }}, '{{ $company->id }}', '{{ $type }}', this.checked)"
                                                    {{ $val === 'Activo' ? 'checked' : '' }}
                                                    style="cursor:pointer; width:35px; height:18px;">
                                                <label class="form-check-label mb-0" for="statusToggle_{{ $record->id }}" id="statusLabel_{{ $record->id }}">
                                                    @php
                                                        $badgeClass = 'bg-secondary';
                                                        $icon = '';
                                                        if (in_array($val, ['Activo', 'Aprobado'])) {
                                                            $badgeClass = 'bg-success';
                                                            $icon = '<i class="fa fa-circle me-1" style="font-size:8px;"></i>';
                                                        } elseif (in_array($val, ['Inactivo', 'Suspendido', 'Bloqueada'])) {
                                                            $badgeClass = 'bg-danger';
                                                            $icon = '<i class="fa fa-ban me-1"></i>';
                                                        }
                                                    @endphp
                                                    <span class="badge {{ $badgeClass }}">{!! $icon !!}{{ $val }}</span>
                                                </label>
                                            </div>
                                        @elseif(in_array($key, ['password', 'admin_pass']))
                                            @if($val && $val !== '—')
                                                <div class="d-flex align-items-center gap-1">
                                                    <span class="pass-mask" style="font-family:monospace;letter-spacing:2px;">••••••••</span>
                                                    <span class="pass-text d-none" style="font-family:monospace;font-size:12px;background:#f0f0f0;padding:1px 5px;border-radius:4px;"></span>
                                                    <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-1 toggle-pass"
                                                        data-reveal-url="{{ route('companies.services.reveal', [$company->id, $type, $record->id]) }}" 
                                                        data-field="{{ $key }}"
                                                        title="Mostrar"><i class="fa fa-eye"></i></button>
                                                    <button type="button" class="btn btn-xs btn-outline-primary py-0 px-1 btn-copy-clipboard"
                                                        onclick="if(!this.getAttribute('data-clipboard')) { if(typeof toastr !== 'undefined') toastr.warning('Debes desbloquear la contraseña con el ojito primero'); else alert('Debes desbloquear la contraseña primero'); return false; }"
                                                        title="Copiar"><i class="fa fa-copy"></i></button>
                                                </div>
                                            @else <span class="text-muted">—</span> @endif
                                        @elseif($key === 'estado')
                                            @if($val && $val !== '—')
                                                @php
                                                    $estadoBadge = match($val) {
                                                        'Operativo'     => 'bg-success',
                                                        'Inoperativo'   => 'bg-danger',
                                                        'En Reparación' => 'bg-warning text-dark',
                                                        'Baja'          => 'bg-secondary',
                                                        default         => 'bg-secondary'
                                                    };
                                                @endphp
                                                <span class="badge {{ $estadoBadge }}">{{ $val }}</span>
                                            @else <span class="text-muted">—</span> @endif
                                        @elseif($key === 'observacion')
                                            @if($val && $val !== '—')
                                                @php $obsDisplay = Str::limit((string)$val, 35, '...'); @endphp
                                                <span class="badge bg-warning text-dark" style="max-width:200px;overflow:hidden;text-overflow:ellipsis;display:inline-block;vertical-align:middle;white-space:nowrap;" title="{{ e($val) }}">
                                                    <i class="fa fa-exclamation-circle me-1"></i>{{ $obsDisplay }}
                                                </span>
                                            @else <span class="text-muted">—</span> @endif
                                        @elseif($key === 'url')
                                            @if($val && $val !== '—')
                                                @php
                                                    $safeUrl = Str::startsWith($val, ['http://', 'https://']) ? $val : 'https://' . ltrim($val, '/');
                                                    if(Str::startsWith(strtolower($val), 'javascript:')) { $safeUrl = '#'; }
                                                    $urlDisplay = Str::limit($val, 45, '...');
                                                @endphp
                                                <a href="{{ $safeUrl }}" target="_blank" style="font-size:12px;display:inline-block;max-width:260px;overflow:hidden;text-overflow:ellipsis;vertical-align:middle;" title="{{ e($val) }}">{{ $urlDisplay }}</a>
                                            @else <span class="text-muted">—</span> @endif
                                        @elseif(in_array($key, ['address','email','alias','target']))
                                            <div class="d-flex align-items-center gap-1" style="max-width:280px;">
                                                <i class="fa fa-envelope text-muted me-1" style="font-size:12px;flex-shrink:0;"></i>
                                                <code style="background:#f0f0f0;padding:2px 6px;border-radius:4px;font-size:12px;max-width:200px;overflow:hidden;text-overflow:ellipsis;display:inline-block;vertical-align:middle;" title="{{ e($val) }}">{{ $val }}</code>
                                                <button type="button" class="btn btn-xs btn-outline-primary py-0 px-1 btn-copy-clipboard flex-shrink-0"
                                                    data-clipboard="{{ $val }}" title="Copiar"><i class="fa fa-copy"></i></button>
                                            </div>
                                        @elseif($key === 'permissions')
                                            @if($val && $val !== '—')
                                                <div class="d-flex flex-wrap gap-1">
                                                    @foreach(explode("\n", $val) as $perm)
                                                        @if(trim($perm))
                                                            @php 
                                                                $parts = explode(':', $perm);
                                                                $area = trim($parts[0] ?? '');
                                                                $rol = trim($parts[1] ?? '');
                                                                
                                                                $bg = 'bg-primary';
                                                                $desc = 'Solo Lectura';
                                                                if (strtolower($rol) === 'adm') {
                                                                    $bg = 'bg-danger';
                                                                    $desc = 'Edición y Lectura';
                                                                } elseif (strtolower($rol) === 'listo') {
                                                                    $bg = 'bg-info';
                                                                    $desc = 'Solo Lectura';
                                                                }
                                                            @endphp
                                                            <div class="d-inline-flex align-items-center mb-1 me-1">
                                                                <span class="badge {{ $bg }}" style="font-size:11px; border-top-right-radius: 0; border-bottom-right-radius: 0;">
                                                                    {{ $area }} 
                                                                    <span class="badge bg-white text-dark ms-1" style="font-size:9px;">{{ $rol }}</span>
                                                                </span>
                                                                <span class="badge bg-secondary text-white" style="font-size:10px; border-top-left-radius: 0; border-bottom-left-radius: 0; border-left: 1px solid rgba(255,255,255,0.2);">
                                                                    {{ $desc }}
                                                                </span>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        @elseif(in_array($key, ['imap_server', 'smtp_server', 'pop3_server']))
                                            <code style="font-size:11px;">{{ $val && $val !== '—' ? $val : '—' }}</code>
                                        @elseif(in_array($key, ['estabilizador', 'cable_de_red', 'adaptador']))
                                            @if($val && $val !== '—')
                                                @if($val === 'Sí')
                                                    <span class="badge bg-success"><i class="fa fa-check me-1"></i>Sí</span>
                                                @else
                                                    <span class="badge bg-secondary"><i class="fa fa-times me-1"></i>No</span>
                                                @endif
                                            @else <span class="text-muted">—</span> @endif
                                        @else
                                            @php
                                                $displayVal = Str::limit(strip_tags((string)$val), 40, '...');
                                            @endphp
                                            @if(strlen((string)$val) > 40)
                                                <span style="font-size:12px;cursor:help;" title="{{ e($val) }}">{{ $displayVal }}</span>
                                            @else
                                                <span style="font-size:12px;">{{ $val }}</span>
                                            @endif
                                        @endif
                                    </td>
                                @endforeach
                                <td style="position:sticky;right:0;background:#fff;z-index:1;box-shadow:-2px 0 4px rgba(0,0,0,0.07);">
                                    <div class="d-flex gap-1">
                                        <button class="btn btn-info btn-sm shadow text-white" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editRecordModal"
                                                data-id="{{ $record->id }}"
                                                data-payload="{{ json_encode($record->data) }}"
                                                data-update-url="{{ route('companies.services.update', [$company->id, $type, $record->id]) }}"
                                                title="Editar">
                                            <i class="fa fa-pencil"></i>
                                        </button>
                                        @if(auth()->user()->role === 'SuperAdmin')
                                        <form action="{{ route('companies.services.destroy', [$company->id, $type, $record->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este registro? Esta acción no se puede deshacer.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm shadow" title="Eliminar">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ count($config['columns']) + ($type !== 'inventario' ? 2 : 1) }}" class="text-center py-5">
                                    <i class="fa {{ $config['icon'] }} fa-3x text-muted mb-3 d-block"></i>
                                    <p class="text-muted mb-0">No hay registros de {{ $config['label'] }} aún.</p>
                                    <button class="btn btn-primary btn-sm mt-3" data-bs-toggle="modal" data-bs-target="#addRecordModal">
                                        <i class="fa fa-plus me-1"></i> Agregar el primero
                                    </button>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($records->hasPages())
                <div class="mt-3">
                    {{ $records->links('pagination::bootstrap-5') }}
                </div>
                @endif
            </div>
    </div>
</div>

{{-- ===== MODAL: AGREGAR ===== --}}
<div class="modal fade" id="addRecordModal" tabindex="-1">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background:{{ $config['color'] }};">
                <h5 class="modal-title text-white">
                    <i class="fa {{ $config['icon'] }} me-2"></i> Agregar {{ $config['label'] }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('companies.services.store', [$company->id, $type]) }}" method="POST" onsubmit="this.querySelector('button[type=submit]').disabled=true; this.querySelector('button[type=submit]').innerHTML='<i class=\'fa fa-spinner fa-spin me-1\'></i> Guardando...';">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        @foreach($config['fields'] as $field)
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">
                                {{ $field['label'] }}
                                @if($field['required']) <span class="text-danger">*</span> @endif
                            </label>
                            
                            @if($field['type'] === 'select')
                                <select name="{{ $field['key'] }}" class="form-control" {{ $field['required'] ? 'required' : '' }}>
                                    <option value="">-- Seleccionar --</option>
                                    @foreach($field['options'] as $opt)
                                        <option value="{{ $opt }}">{{ $opt }}</option>
                                    @endforeach
                                </select>
                            @elseif($field['type'] === 'textarea')
                                @if(isset($type) && $type === 'nextcloud_user' && $field['key'] === 'permissions')
                                    <textarea name="{{ $field['key'] }}" id="add_permissions" class="d-none" {{ $field['required'] ? 'required' : '' }}></textarea>
                                    <div class="permissions-builder border p-2 rounded bg-light">
                                        <div id="add_permissions_container" class="mb-2 d-flex flex-column gap-2"></div>
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addPermissionRow('add')">
                                            <i class="fa fa-plus me-1"></i> Añadir Área
                                        </button>
                                    </div>
                                @else
                                    <textarea name="{{ $field['key'] }}" class="form-control" rows="3"
                                        placeholder="{{ $field['label'] }}"
                                        {{ $field['required'] ? 'required' : '' }}></textarea>
                                @endif
                            @else
                                {{-- Manejo especial de Contraseña (Generador BPM) --}}
                                @if($field['key'] === 'password')
                                    <div class="input-group">
                                        <input type="text" name="{{ $field['key'] }}" id="add_pwd_{{ $field['key'] }}"
                                            class="form-control" placeholder="Escriba o genere clave"
                                            {{ $field['required'] ? 'required' : '' }}>
                                        <button class="btn btn-outline-secondary" type="button" onclick="generatePwd('add_pwd_{{ $field['key'] }}')" title="Generar Contraseña Segura">
                                            <i class="fa fa-magic"></i>
                                        </button>
                                        <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('add_pwd_{{ $field['key'] }}')" title="Copiar">
                                            <i class="fa fa-copy"></i>
                                        </button>
                                    </div>
                                
                                {{-- Manejo especial de Correo (Poka-Yoke) --}}
                                @elseif($type === 'email' && $field['key'] === 'address' && !empty($company->domain))
                                    <div class="input-group">
                                        <input type="text" name="{{ $field['key'] }}_prefix" id="add_{{ $field['key'] }}_prefix"
                                            class="form-control" placeholder="ej. gerencia"
                                            {{ $field['required'] ? 'required' : '' }}>
                                        <span class="input-group-text bg-light text-muted">@</span>
                                        <input type="text" name="{{ $field['key'] }}_domain" class="form-control" 
                                            value="{{ $company->domain }}" placeholder="dominio.com" required>
                                    </div>
                                    <small class="text-muted" style="font-size: 0.75rem;">Puedes cambiar el dominio si este correo usa uno distinto.</small>
                                
                                {{-- Inputs estándar --}}
                                @else
                                    <input type="{{ $field['type'] }}" name="{{ $field['key'] }}"
                                        class="form-control" placeholder="{{ $field['label'] }}"
                                        {{ $field['required'] ? 'required' : '' }}>
                                @endif
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-save me-1"></i> Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===== MODAL: EDITAR ===== --}}
<div class="modal fade" id="editRecordModal" tabindex="-1">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background:{{ $config['color'] }};">
                <h5 class="modal-title text-white">
                    <i class="fa fa-pencil me-2"></i> Editar {{ $config['label'] }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editForm" method="POST" onsubmit="this.querySelector('button[type=submit]').disabled=true; this.querySelector('button[type=submit]').innerHTML='<i class=\'fa fa-spinner fa-spin me-1\'></i> Actualizando...';">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row" id="editFields">
                        @foreach($config['fields'] as $field)
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">
                                {{ $field['label'] }}
                                @if($field['required']) <span class="text-danger">*</span> @endif
                            </label>
                            @if($field['type'] === 'select')
                                <select name="{{ $field['key'] }}" id="edit_{{ $field['key'] }}" class="form-control" {{ $field['required'] ? 'required' : '' }}>
                                    <option value="">-- Seleccionar --</option>
                                    @foreach($field['options'] as $opt)
                                        <option value="{{ $opt }}">{{ $opt }}</option>
                                    @endforeach
                                </select>
                            @elseif($field['type'] === 'textarea')
                                @if(isset($type) && $type === 'nextcloud_user' && $field['key'] === 'permissions')
                                    <textarea name="{{ $field['key'] }}" id="edit_permissions" class="d-none" {{ $field['required'] ? 'required' : '' }}></textarea>
                                    <div class="permissions-builder border p-2 rounded bg-light">
                                        <div id="edit_permissions_container" class="mb-2 d-flex flex-column gap-2"></div>
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addPermissionRow('edit')">
                                            <i class="fa fa-plus me-1"></i> Añadir Área
                                        </button>
                                    </div>
                                @else
                                    <textarea name="{{ $field['key'] }}" id="edit_{{ $field['key'] }}" class="form-control" rows="3"
                                        placeholder="{{ $field['label'] }}"
                                        {{ $field['required'] ? 'required' : '' }}></textarea>
                                @endif
                            @else
                                {{-- Manejo especial de Contraseña (Generador BPM) --}}
                                @if($field['key'] === 'password')
                                    <div class="input-group">
                                        <input type="text" name="{{ $field['key'] }}" id="edit_{{ $field['key'] }}"
                                            class="form-control" placeholder="Escriba o genere clave"
                                            {{ $field['required'] ? 'required' : '' }}>
                                        <button class="btn btn-outline-secondary" type="button" onclick="generatePwd('edit_{{ $field['key'] }}')" title="Generar Contraseña Segura">
                                            <i class="fa fa-magic"></i>
                                        </button>
                                        <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('edit_{{ $field['key'] }}')" title="Copiar">
                                            <i class="fa fa-copy"></i>
                                        </button>
                                    </div>
                                    
                                {{-- Manejo especial de Correo (Poka-Yoke) --}}
                                @elseif($type === 'email' && $field['key'] === 'address' && !empty($company->domain))
                                    <div class="input-group">
                                        <input type="text" name="{{ $field['key'] }}_prefix" id="edit_{{ $field['key'] }}_prefix"
                                            class="form-control" placeholder="ej. gerencia"
                                            {{ $field['required'] ? 'required' : '' }}>
                                        <span class="input-group-text bg-light text-muted">@</span>
                                        <input type="text" name="{{ $field['key'] }}_domain" id="edit_{{ $field['key'] }}_domain" 
                                            class="form-control" value="{{ $company->domain }}" required>
                                    </div>
                                    <small class="text-muted" style="font-size: 0.75rem;">Puedes editar el dominio si es distinto al principal.</small>
                                
                                {{-- Inputs estándar --}}
                                @else
                                    <input type="{{ $field['type'] === 'email' ? 'text' : $field['type'] }}"
                                        name="{{ $field['key'] }}" id="edit_{{ $field['key'] }}"
                                        class="form-control" placeholder="{{ $field['label'] }}"
                                        {{ $field['required'] ? 'required' : '' }}>
                                @endif
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-save me-1"></i> Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // ---- Buscador rápido ----
    const searchInput = document.getElementById('quickSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const q = this.value.toLowerCase();
            document.querySelectorAll('#tableBody .table-row').forEach(function (row) {
                row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
            });
        });
    }

    // ---- Modal Editar: prellenar campos y Poka-Yoke ----
    const editModal = document.getElementById('editRecordModal');
    if (editModal) {
        editModal.addEventListener('show.bs.modal', function (event) {
            const btn      = event.relatedTarget;
            const recordId = btn.getAttribute('data-record-id') || btn.getAttribute('data-id');
            const data     = JSON.parse(btn.getAttribute('data-record') || btn.getAttribute('data-payload') || '{}');
            const updateUrl = btn.getAttribute('data-update-url');
            if (updateUrl) {
                document.getElementById('editForm').action = updateUrl;
            } else {
                const baseUrl  = '{{ url("companies/'.$company->id.'/services/'.$type.'") }}/' + recordId;
                document.getElementById('editForm').action = baseUrl;
            }

            // Prellenar cada campo
            Object.keys(data).forEach(function(key) {
                if (key === 'password') return; // Poka-Yoke: Jamás prellenar el hash de la contraseña
                const el = document.getElementById('edit_' + key);
                if (el) {
                    if (el.tagName === 'SELECT') {
                        // Si la opción no existe (ej. estado "Cerrado" importado), la agregamos dinámicamente
                        let optionExists = Array.from(el.options).some(opt => opt.value === data[key]);
                        if (!optionExists && data[key]) {
                            el.add(new Option(data[key], data[key]));
                        }
                    }
                    el.value = data[key] || '';
                }
                
                // Especial para Poka-Yoke de email
                const prefixInput = document.getElementById('edit_' + key + '_prefix');
                const domainInput = document.getElementById('edit_' + key + '_domain');
                if(prefixInput && data[key]) {
                    const parts = data[key].split('@');
                    prefixInput.value = parts[0] || '';
                    if (domainInput && parts.length > 1) {
                        domainInput.value = parts[1] || '';
                    }
                }
            });
        });
    }
    
    // ---- Toggle contraseñas ----
    document.querySelectorAll('.toggle-pass').forEach(btn => {
        btn.addEventListener('click', function() {
            const container = this.closest('div');
            const mask = container.querySelector('.pass-mask');
            const text = container.querySelector('.pass-text');
            const icon = this.querySelector('i');
            const copyBtn = container.querySelector('.btn-copy-clipboard');
            const revealUrl = this.getAttribute('data-reveal-url');
            const fieldName = this.getAttribute('data-field') || 'password';
            
            if(mask.classList.contains('d-none')) {
                // Hide it
                mask.classList.remove('d-none');
                text.classList.add('d-none');
                copyBtn.classList.add('d-none');
                icon.className = 'fa fa-eye';
            } else {
                // Reveal it via AJAX
                icon.className = 'fa fa-spinner fa-spin';
                const pwd = prompt('Por favor, ingresa tu contraseña de acceso:');
                if(!pwd) {
                    icon.className = 'fa fa-eye';
                    return;
                }

                fetch(revealUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ password: pwd, field: fieldName })
                })
                .then(res => res.json())
                .then(data => {
                    if(data.error) {
                        alert(data.error);
                        icon.className = 'fa fa-eye';
                    } else if(data.password) {
                        text.textContent = data.password;
                        copyBtn.setAttribute('data-clipboard', data.password);
                        mask.classList.add('d-none');
                        text.classList.remove('d-none');
                        copyBtn.classList.remove('d-none');
                        icon.className = 'fa fa-eye-slash';
                    }
                })
                .catch(err => {
                    alert('Error de conexión');
                    icon.className = 'fa fa-eye';
                });
            }
        });
    });

    // ---- Prevenir XSS: Botones de copia seguros ----
    document.querySelectorAll('.btn-copy-clipboard').forEach(btn => {
        btn.addEventListener('click', function() {
            const val = this.getAttribute('data-clipboard');
            if (!val) return; // Prevent copying null
            navigator.clipboard.writeText(val).then(() => {
                if (typeof toastr !== 'undefined') toastr.success('Copiado al portapapeles');
                else alert('Copiado al portapapeles');
            });
        });
    });
});

// BPM: Auto-resaltado desde Búsqueda
window.addEventListener('load', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const highlightId = urlParams.get('highlight');
    if (highlightId) {
        const row = document.getElementById('row_' + highlightId);
        if (row) {
            row.style.transition = 'background-color 2s';
            row.style.backgroundColor = '#fff3cd'; // color warning de bootstrap
            row.scrollIntoView({ behavior: 'smooth', block: 'center' });
            setTimeout(() => { row.style.backgroundColor = ''; }, 3000);
        }
    }
});

// BPM: Generador de Contraseña (Libre de Modulo Bias)
function generatePwd(inputId) {
    const chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^*";
    let pwd = "";
    const randomValues = new Uint32Array(12);
    window.crypto.getRandomValues(randomValues);
    for (let i = 0; i < 12; i++) {
        // Se remueve el Modulo Bias dividiendo entre el máximo valor posible + 1 (4294967296)
        pwd += chars.charAt(Math.floor((randomValues[i] / 4294967296.0) * chars.length));
    }
    document.getElementById(inputId).value = pwd;
    if (typeof toastr !== 'undefined') {
        toastr.info('Contraseña segura generada');
    }
}

// BPM: Copiar del Input
function copyToClipboard(inputId) {
    const input = document.getElementById(inputId);
    navigator.clipboard.writeText(input.value).then(() => {
        if (typeof toastr !== 'undefined') {
            toastr.success('Copiado al portapapeles');
        } else {
            alert('Copiado al portapapeles');
        }
    });
}

// BPM: Toggle Rápido de Estado
function toggleStatus(recordId, companyId, type, isChecked) {
    const newStatus = isChecked ? 'Activo' : 'Suspendido';
    
    fetch(`{{ url('') }}/companies/${companyId}/services/${type}/${recordId}/toggle-status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ status: newStatus })
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            // Actualizar Label Visual
            const label = document.getElementById('statusLabel_' + recordId);
            if(isChecked) {
                label.innerHTML = '<span class="badge bg-success"><i class="fa fa-circle me-1" style="font-size:8px;"></i>Activo</span>';
            } else {
                label.innerHTML = '<span class="badge bg-danger"><i class="fa fa-ban me-1"></i>Suspendido</span>';
            }
            if (typeof toastr !== 'undefined') toastr.success('Estado actualizado correctamente (BPM)');
        } else {
            if (typeof toastr !== 'undefined') toastr.error('Error al actualizar el estado');
            else alert('Error al actualizar el estado');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (typeof toastr !== 'undefined') toastr.error('Error de conexión');
    });
}
// ---- Permissions Builder Logic ----
function addPermissionRow(type, areaName = '', role = 'ADM') {
    const container = document.getElementById(type + '_permissions_container');
    if (!container) return;
    
    const row = document.createElement('div');
    row.className = 'd-flex gap-2 align-items-center permission-row';
    
    row.innerHTML = `
        <input type="text" class="form-control form-control-sm permission-area" placeholder="Nombre del Área" value="${areaName}" oninput="syncPermissions('${type}')" required>
        <select class="form-select form-select-sm permission-role" style="width:160px;" onchange="syncPermissions('${type}')">
            <option value="ADM" ${role.toUpperCase() === 'ADM' ? 'selected' : ''}>Edición y Lectura (ADM)</option>
            <option value="LISTO" ${role.toUpperCase() === 'LISTO' ? 'selected' : ''}>Solo Lectura (LISTO)</option>
        </select>
        <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.parentElement.remove(); syncPermissions('${type}')"><i class="fa fa-trash"></i></button>
    `;
    
    container.appendChild(row);
    syncPermissions(type);
}

function syncPermissions(type) {
    const container = document.getElementById(type + '_permissions_container');
    const textarea = document.getElementById(type + '_permissions');
    if (!container || !textarea) return;
    
    const rows = container.querySelectorAll('.permission-row');
    let lines = [];
    rows.forEach(row => {
        const area = row.querySelector('.permission-area').value.trim();
        const role = row.querySelector('.permission-role').value;
        if (area) lines.push(area + ': ' + role);
    });
    textarea.value = lines.join('\\n');
}

// Interceptar modal de Editar para construir el UI
const editModalEvent = document.getElementById('editRecordModal');
if (editModalEvent) {
    editModalEvent.addEventListener('show.bs.modal', function(e) {
        setTimeout(() => {
            const textarea = document.getElementById('edit_permissions');
            const container = document.getElementById('edit_permissions_container');
            if (textarea && container) {
                container.innerHTML = ''; // reset
                const val = textarea.value.trim();
                if (val) {
                    const lines = val.split('\\n');
                    lines.forEach(line => {
                        const parts = line.split(':');
                        if (parts.length >= 2) {
                            addPermissionRow('edit', parts[0].trim(), parts[1].trim());
                        }
                    });
                }
            }
        }, 100);
    });
    
    editModalEvent.addEventListener('hidden.bs.modal', function(e) {
        const container = document.getElementById('edit_permissions_container');
        if (container) container.innerHTML = '';
    });
}

const addModalEvent = document.getElementById('addRecordModal');
if (addModalEvent) {
    addModalEvent.addEventListener('hidden.bs.modal', function(e) {
        const container = document.getElementById('add_permissions_container');
        if (container) container.innerHTML = '';
        const textarea = document.getElementById('add_permissions');
        if (textarea) textarea.value = '';
    });
}

// Global Credentials Toggle
document.querySelectorAll('.toggle-admin-pass-global').forEach(function (btn) {
    btn.addEventListener('click', function () {
        const card = btn.closest('.card-body');
        const mask = card.querySelector('.admin-pass-mask');
        const text = card.querySelector('.admin-pass-text');
        const icon = btn.querySelector('i');
        const companyId = btn.getAttribute('data-company-id');
        
        if (!text.classList.contains('d-none')) {
            text.classList.add('d-none');
            mask.classList.remove('d-none');
            icon.classList.replace('fa-eye-slash', 'fa-eye');
            text.innerHTML = '';
            return;
        }

        Swal.fire({
            title: 'Autenticación Requerida',
            text: 'Ingresa tu contraseña de Infortech para ver esta credencial',
            input: 'password',
            inputAttributes: {
                autocapitalize: 'off',
                autocorrect: 'off'
            },
            showCancelButton: true,
            confirmButtonText: 'Ver Credencial',
            cancelButtonText: 'Cancelar',
            showLoaderOnConfirm: true,
            preConfirm: (password) => {
                return fetch(`{{ url('/companies') }}/${companyId}/zimbra/reveal`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ password: password })
                })
                .then(response => {
                    if (!response.ok) throw new Error('Contraseña incorrecta');
                    return response.json();
                })
                .catch(error => { Swal.showValidationMessage(`Falló: ${error.message}`); })
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                text.innerHTML = result.value.password;
                text.classList.remove('d-none');
                mask.classList.add('d-none');
                icon.classList.replace('fa-eye', 'fa-eye-slash');
                Swal.fire({ icon: 'success', title: 'Acceso Concedido', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
            }
        });
    });
});

document.querySelectorAll('.toggle-nextcloud-pass-global').forEach(function (btn) {
    btn.addEventListener('click', function () {
        const card = btn.closest('.card-body');
        const mask = card.querySelector('.nextcloud-pass-mask');
        const text = card.querySelector('.nextcloud-pass-text');
        const icon = btn.querySelector('i');
        const companyId = btn.getAttribute('data-company-id');
        
        if (!text.classList.contains('d-none')) {
            text.classList.add('d-none');
            mask.classList.remove('d-none');
            icon.classList.replace('fa-eye-slash', 'fa-eye');
            text.innerHTML = '';
            return;
        }

        Swal.fire({
            title: 'Autenticación Requerida',
            text: 'Ingresa tu contraseña de Infortech para ver esta credencial',
            input: 'password',
            inputAttributes: {
                autocapitalize: 'off',
                autocorrect: 'off'
            },
            showCancelButton: true,
            confirmButtonText: 'Ver Credencial',
            cancelButtonText: 'Cancelar',
            showLoaderOnConfirm: true,
            preConfirm: (password) => {
                return fetch(`{{ url('/companies') }}/${companyId}/nextcloud/reveal`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ password: password })
                })
                .then(response => {
                    if (!response.ok) throw new Error('Contraseña incorrecta');
                    return response.json();
                })
                .catch(error => { Swal.showValidationMessage(`Falló: ${error.message}`); })
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                text.innerHTML = result.value.password;
                text.classList.remove('d-none');
                mask.classList.add('d-none');
                icon.classList.replace('fa-eye', 'fa-eye-slash');
                Swal.fire({ icon: 'success', title: 'Acceso Concedido', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
            }
        });
    });
});

document.querySelectorAll('.toggle-generic-pass').forEach(function (btn) {
    btn.addEventListener('click', function () {
        const card = btn.closest('.card-body');
        const mask = card.querySelector('.generic-pass-mask');
        const text = card.querySelector('.generic-pass-text');
        const icon = btn.querySelector('i');
        const companyId = btn.getAttribute('data-company-id');
        const type = btn.getAttribute('data-type');
        
        if (!text.classList.contains('d-none')) {
            text.classList.add('d-none');
            mask.classList.remove('d-none');
            icon.classList.replace('fa-eye-slash', 'fa-eye');
            text.innerHTML = '';
            return;
        }

        Swal.fire({
            title: 'Autenticación Requerida',
            text: 'Ingresa tu contraseña de Infortech para ver esta credencial',
            input: 'password',
            inputAttributes: {
                autocapitalize: 'off',
                autocorrect: 'off'
            },
            showCancelButton: true,
            confirmButtonText: 'Ver Credencial',
            cancelButtonText: 'Cancelar',
            showLoaderOnConfirm: true,
            preConfirm: (password) => {
                return fetch(`{{ url('/companies') }}/${companyId}/services/${type}/admin-password/reveal`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ password: password })
                })
                .then(response => {
                    if (!response.ok) throw new Error('Contraseña incorrecta');
                    return response.json();
                })
                .catch(error => { Swal.showValidationMessage(`Falló: ${error.message}`); })
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                text.innerHTML = result.value.password;
                text.classList.remove('d-none');
                mask.classList.add('d-none');
                icon.classList.replace('fa-eye', 'fa-eye-slash');
                Swal.fire({ icon: 'success', title: 'Acceso Concedido', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
            }
        });
    });
});
</script>
@endsection
