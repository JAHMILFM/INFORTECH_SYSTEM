@extends('layouts.app')

@section('content')

@section('breadcrumb')
    <i class="bi bi-chevron-right mx-1"></i>
    <a href="{{ route('companies.index') }}">Clientes</a>
    <i class="bi bi-chevron-right mx-1"></i>
    <span>{{ $company->name }}</span>
@endsection
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
                <div class="row g-3">

                    @php
                        $services = [
                            ['type' => 'email',             'label' => 'Cuentas Zimbra',      'desc' => 'Cuentas de usuario Zimbra (Email).',          'icon' => 'fa-envelope',       'color' => 'linear-gradient(135deg,#5e72e4,#825ee4)', 'btn' => 'btn-primary'],
                            ['type' => 'standard_email',    'label' => 'Email',                'desc' => 'Buzones de correo estándar adicionales.',     'icon' => 'fa-envelope-o',     'color' => 'linear-gradient(135deg,#11cdef,#1171ef)', 'btn' => 'btn-info'],
                            ['type' => 'domain_alias',      'label' => 'Dominios',             'desc' => 'Gestión de dominios y alias.',                'icon' => 'fa-globe',          'color' => 'linear-gradient(135deg,#825ee4,#e45ebb)', 'btn' => 'btn-primary'],
                            ['type' => 'onedrive_user',     'label' => 'Cuentas OneDrive',     'desc' => 'Gestión de cuentas y permisos OneDrive.',     'icon' => 'fa-cloud',          'color' => 'linear-gradient(135deg,#11cdef,#1171ef)', 'btn' => 'btn-info'],
                            ['type' => 'nextcloud_user',    'label' => 'Cuentas Nextcloud',    'desc' => 'Gestión de cuentas y permisos Nextcloud.',    'icon' => 'fa-cloud',          'color' => 'linear-gradient(135deg,#11cdef,#1171ef)', 'btn' => 'btn-info'],
                            ['type' => 'cpanel',            'label' => 'CPanel / Hosting',     'desc' => 'Credenciales de acceso CPanel / Hosting.',    'icon' => 'fa-server',         'color' => 'linear-gradient(135deg,#f5365c,#f56036)', 'btn' => 'btn-danger'],
                            ['type' => 'local_machines',    'label' => 'Equipos',              'desc' => 'Accesos AnyDesk, TeamViewer y PCs.',          'icon' => 'fa-desktop',        'color' => 'linear-gradient(135deg,#2dce89,#2dcecc)', 'btn' => 'btn-success'],
                            ['type' => 'inventario',        'label' => 'Inventario',           'desc' => 'Inventario de equipos y periféricos.',        'icon' => 'fa-archive',        'color' => 'linear-gradient(135deg,#ff9966,#ff5e62)', 'btn' => 'btn-warning'],
                            ['type' => 'antivirus_license', 'label' => 'Licencias Antivirus',  'desc' => 'Licencias y claves de Antivirus.',            'icon' => 'fa-shield',         'color' => 'linear-gradient(135deg,#fb6340,#fbb140)', 'btn' => 'btn-warning'],
                            ['type' => 'autodesk_license',  'label' => 'Licencias Autodesk',   'desc' => 'Licencias y cuentas de Autodesk (AutoCAD).', 'icon' => 'fa-pencil-square-o','color' => 'linear-gradient(135deg,#5e72e4,#825ee4)', 'btn' => 'btn-primary'],
                            ['type' => 'other_credentials', 'label' => 'Otras Plataformas',    'desc' => 'Credenciales adicionales no categorizadas.',  'icon' => 'fa-key',            'color' => 'linear-gradient(135deg,#f5365c,#f56036)', 'btn' => 'btn-danger'],
                        ];
                        $counts = \App\Models\ServiceRecord::where('company_id', $company->id)
                            ->selectRaw('type, count(*) as total')
                            ->groupBy('type')
                            ->pluck('total', 'type');

                        $allowed = $company->allowed_services;
                        if ($allowed !== null) {
                            $services = array_filter($services, function($svc) use ($allowed) {
                                return in_array($svc['type'], $allowed);
                            });
                        }
                    @endphp

                    @foreach($services as $svc)
                    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                        <div class="card h-100 border-0 shadow-sm" style="border-radius:14px;overflow:hidden;">
                            {{-- Tira de color superior --}}
                            <div style="height:5px;background:{{ $svc['color'] }};"></div>
                            <div class="card-body d-flex flex-column p-3">
                                {{-- Icono + Título --}}
                                <div class="d-flex align-items-center mb-2">
                                    <div class="me-3 flex-shrink-0" style="width:44px;height:44px;background:{{ $svc['color'] }};border-radius:10px;display:flex;align-items:center;justify-content:center;">
                                        <i class="fa {{ $svc['icon'] }}" style="color:#fff;font-size:20px;"></i>
                                    </div>
                                    <div class="flex-grow-1" style="min-width:0;">
                                        <strong class="d-block text-truncate" style="font-size:14px;line-height:1.3;">{{ $svc['label'] }}</strong>
                                        <small class="text-muted d-block text-truncate" style="font-size:11px;">{{ $svc['desc'] }}</small>
                                    </div>
                                </div>
                                {{-- Contador --}}
                                <div class="mt-auto pt-2 d-flex align-items-center justify-content-between">
                                    <span class="text-muted" style="font-size:11px;">
                                        <i class="fa fa-database me-1"></i>
                                        <strong>{{ $counts[$svc['type']] ?? 0 }}</strong> registros
                                    </span>
                                    @if($svc['type'] === 'webmail')
                                        @if($company->domain)
                                            <a href="https://webmail.{{ $company->domain }}" target="_blank" class="btn {{ $svc['btn'] }} btn-xs py-1 px-2" style="font-size:11px;">
                                                <i class="fa fa-external-link me-1"></i>Abrir
                                            </a>
                                        @else
                                            <span class="btn btn-secondary btn-xs py-1 px-2 disabled" style="font-size:11px;">Sin dominio</span>
                                        @endif
                                    @else
                                        <a href="{{ route('companies.services.index', [$company->id, $svc['type']]) }}"
                                           class="btn {{ $svc['btn'] }} btn-xs py-1 px-2 {{ $svc['btn'] === 'btn-warning' ? 'text-white' : '' }}" style="font-size:11px;">
                                            <i class="fa fa-arrow-right me-1"></i>Gestionar
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach

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


