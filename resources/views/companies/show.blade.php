@extends('layouts.app')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Panel: {{ $company->name }}</h4>
            <p class="mb-0">
                <i class="fa fa-globe me-1 text-primary"></i>{{ $company->domain ?? 'Sin dominio' }}
                @if($company->contact_email)
                    &nbsp;&nbsp;<i class="fa fa-envelope me-1 text-primary"></i>{{ $company->contact_email }}
                @endif
            </p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex align-items-center gap-2">
        <a href="{{ route('companies.import.show', $company->id) }}" class="btn btn-success btn-sm">
            <i class="fa fa-file-excel-o me-1"></i> Importar Excel
        </a>
        @if(auth()->user()->role === 'SuperAdmin')
        <a href="{{ route('audit.index', ['company_id' => $company->id]) }}" class="btn btn-warning btn-sm shadow-sm text-dark">
            <i class="fa fa-history me-1"></i> Ver Historial (BPM)
        </a>
        @endif
        <a href="{{ route('companies.index') }}" class="btn btn-secondary btn-sm">
            <i class="fa fa-arrow-left me-1"></i> Volver a Clientes
        </a>
    </div>
</div>

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

    $nextcloudRecord = \App\Models\ServiceRecord::where('company_id', $company->id)
        ->where('type', 'account')
        ->where('data->role', 'Administrador')
        ->where('data->plataforma', 'Nextcloud')
        ->first();
    $nextcloudData = $nextcloudRecord ? $nextcloudRecord->data : [];
@endphp

@if($adminData || $webmailLink)
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
                        <code style="font-size:12px;background:#fff3e0;padding:1px 5px;border-radius:4px;">{{ $adminData['email'] }}</code>
                        <button onclick="navigator.clipboard.writeText('{{ addslashes($adminData['email']) }}').then(() => { if(typeof toastr !== 'undefined') toastr.success('Email copiado'); })"
                                class="btn btn-xs btn-outline-secondary ms-1 py-0 px-1" title="Copiar">
                            <i class="fa fa-copy"></i>
                        </button>
                    </div>
                    <div>
                        <small class="text-muted">Contraseña:</small><br>
                        <span class="admin-pass-mask" style="font-family:monospace;letter-spacing:2px;font-size:13px;">••••••••</span>
                        <span class="admin-pass-text d-none" style="font-family:monospace;font-size:12px;background:#fff3e0;padding:1px 5px;border-radius:4px;"></span>
                        <button class="btn btn-xs btn-outline-secondary ms-1 py-0 px-1 toggle-admin-pass" title="Mostrar">
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
@endif

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
                        <code style="font-size:12px;background:#fff3e0;padding:1px 5px;border-radius:4px;">{{ $nextcloudData['username'] }}</code>
                        <button onclick="navigator.clipboard.writeText('{{ addslashes($nextcloudData['username']) }}').then(() => { if(typeof toastr !== 'undefined') toastr.success('Usuario copiado'); })"
                                class="btn btn-xs btn-outline-secondary ms-1 py-0 px-1" title="Copiar">
                            <i class="fa fa-copy"></i>
                        </button>
                    </div>
                    <div>
                        <small class="text-muted">Contraseña:</small><br>
                        <span class="nextcloud-pass-mask" style="font-family:monospace;letter-spacing:2px;font-size:13px;">••••••••</span>
                        <span class="nextcloud-pass-text d-none" style="font-family:monospace;font-size:12px;background:#fff3e0;padding:1px 5px;border-radius:4px;"></span>
                        <button class="btn btn-xs btn-outline-secondary ms-1 py-0 px-1 toggle-nextcloud-pass" title="Mostrar">
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

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="fa fa-server me-2 text-primary"></i>
                        Servicios Disponibles — {{ $company->name }}
                    </h4>
                    @if($company->domain)
                        <span class="badge badge-primary fs-14 px-3 py-2 ms-3">
                            <i class="fa fa-globe me-1"></i> {{ $company->domain }}
                        </span>
                    @endif
                </div>
                <div>
                    <a href="{{ route('companies.import.show', $company->id) }}" class="btn btn-success btn-sm text-white shadow-sm">
                        <i class="fa fa-file-excel-o me-1"></i> Importar
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle" style="min-width: 700px;">
                        <thead>
                            <tr style="background: linear-gradient(135deg, #5e72e4, #825ee4); color: #fff;">
                                <th style="width: 40px;">#</th>
                                <th><i class="fa fa-cogs me-2"></i>Servicio</th>
                                <th><i class="fa fa-info-circle me-2"></i>Descripción</th>
                                <th><i class="fa fa-database me-2"></i>Registros</th>
                                <th style="width: 160px;"><i class="fa fa-external-link me-2"></i>Gestionar</th>
                            </tr>
                        </thead>
                        <tbody>

                            @php
                                $services = [
                                    ['type' => 'email',             'label' => 'Email',              'desc' => 'Buzones de correo corporativos de la empresa.',                             'icon' => 'fa-envelope',  'color' => 'linear-gradient(135deg,#5e72e4,#825ee4)', 'btn' => 'btn-primary'],
                                    ['type' => 'account',           'label' => 'Accounts',           'desc' => 'Cuentas de usuario y administración del dominio.',                          'icon' => 'fa-users',     'color' => 'linear-gradient(135deg,#11cdef,#1171ef)', 'btn' => 'btn-info'],
                                    ['type' => 'email_alias',       'label' => 'Email Aliases',      'desc' => 'Alias de correo que redirigen a buzones reales.',                           'icon' => 'fa-at',        'color' => 'linear-gradient(135deg,#2dce89,#2dcecc)', 'btn' => 'btn-success'],
                                    ['type' => 'distribution_list', 'label' => 'Distribution Lists', 'desc' => 'Grupos de correo para envío a múltiples destinatarios.',                    'icon' => 'fa-list-ul',   'color' => 'linear-gradient(135deg,#fb6340,#fbb140)', 'btn' => 'btn-warning'],
                                    ['type' => 'domain_alias',      'label' => 'Domain Aliases',     'desc' => 'Dominios alternativos que apuntan al dominio principal.',                   'icon' => 'fa-globe',     'color' => 'linear-gradient(135deg,#825ee4,#e45ebb)', 'btn' => 'btn-purple'],
                                    ['type' => 'other_credentials', 'label' => 'Otras Plataformas',  'desc' => 'Credenciales para Nextcloud, CPanel, ERP, etc.',                            'icon' => 'fa-key',       'color' => 'linear-gradient(135deg,#f5365c,#f56036)', 'btn' => 'btn-danger'],
                                    ['type' => 'webmail',           'label' => 'Webmail',            'desc' => 'Acceso al correo desde el navegador (webmail.' . ($company->domain ?? '...') . ').', 'icon' => 'fa-chrome',    'color' => 'linear-gradient(135deg,#f5365c,#f56036)', 'btn' => 'btn-danger'],
                                ];
                                $counts = \App\Models\ServiceRecord::where('company_id', $company->id)
                                    ->selectRaw('type, count(*) as total')
                                    ->groupBy('type')
                                    ->pluck('total', 'type');
                            @endphp

                            @foreach($services as $i => $svc)
                            <tr>
                                <td class="text-center"><span class="badge badge-primary">{{ $i + 1 }}</span></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="me-3" style="width:42px;height:42px;background:{{ $svc['color'] }};border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                            <i class="fa {{ $svc['icon'] }}" style="color:#fff;font-size:18px;"></i>
                                        </div>
                                        <div>
                                            <strong class="d-block" style="font-size:15px;">{{ $svc['label'] }}</strong>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-muted" style="font-size:13px;">{{ $svc['desc'] }}</td>
                                <td class="text-center">
                                    @if($svc['type'] === 'webmail')
                                        <span class="text-muted">—</span>
                                    @else
                                        <span class="badge badge-light text-dark border" style="font-size:13px;">
                                            {{ $counts[$svc['type']] ?? 0 }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($svc['type'] === 'webmail')
                                        @if($company->domain)
                                            <a href="https://webmail.{{ $company->domain }}" target="_blank" class="btn {{ $svc['btn'] }} btn-sm w-100">
                                                <i class="fa fa-external-link me-1"></i> Abrir
                                            </a>
                                        @else
                                            <span class="btn btn-secondary btn-sm w-100 disabled">Sin dominio</span>
                                        @endif
                                    @else
                                        <a href="{{ route('companies.services.index', [$company->id, $svc['type']]) }}"
                                           class="btn {{ $svc['btn'] }} btn-sm w-100 {{ $svc['btn'] === 'btn-warning' ? 'text-white' : '' }}">
                                            <i class="fa fa-arrow-right me-1"></i> Gestionar
                                        </a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>

                @if(!$company->domain)
                <div class="alert alert-warning mt-3" role="alert">
                    <i class="fa fa-exclamation-triangle me-2"></i>
                    <strong>Atención:</strong> Esta empresa no tiene un dominio configurado.
                </div>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.toggle-admin-pass').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const card = btn.closest('.card-body');
            const mask = card.querySelector('.admin-pass-mask');
            const text = card.querySelector('.admin-pass-text');
            const icon = btn.querySelector('i');
            
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
                    return fetch(`{{ route('companies.zimbra.reveal', $company->id) }}`, {
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

    document.querySelectorAll('.toggle-nextcloud-pass').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const card = btn.closest('.card-body');
            const mask = card.querySelector('.nextcloud-pass-mask');
            const text = card.querySelector('.nextcloud-pass-text');
            const icon = btn.querySelector('i');
            
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
                    return fetch(`{{ route('companies.nextcloud.reveal', $company->id) }}`, {
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
});
</script>
