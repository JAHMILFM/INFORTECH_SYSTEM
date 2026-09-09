@extends('layouts.app')

@section('page-title', 'Detalle de Reporte ' . $report->code)

@section('breadcrumb')
    <i class="bi bi-chevron-right mx-1"></i>
    <a href="{{ route('reports.index') }}" style="color: var(--text-muted); text-decoration: none;">Reportes Técnicos</a>
    <i class="bi bi-chevron-right mx-1"></i>
    <span>{{ $report->code }}</span>
@endsection

@section('content')
<div class="row page-titles mx-0 mb-4 align-items-center">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <div class="d-flex align-items-center gap-3">
                <h4 class="mb-0" style="color: var(--text);">Reporte {{ $report->code }}</h4>
                @if($report->status === 'confirmed')
                    <span class="badge px-3 py-2" style="background: rgba(5,150,105,0.2); color: #34d399; border: 1px solid rgba(5,150,105,0.3); border-radius: 8px;">
                        <i class="bi bi-check-circle-fill me-1"></i> Confirmado
                    </span>
                @else
                    <span class="badge px-3 py-2" style="background: rgba(245,158,11,0.2); color: #fbbf24; border: 1px solid rgba(245,158,11,0.3); border-radius: 8px;">
                        <i class="bi bi-clock-history me-1"></i> Borrador
                    </span>
                @endif
            </div>
            <p class="mb-0 text-muted mt-1">Formato {{ $report->reportType->format_code ?? 'FOR-TI-001' }} — Versión {{ $report->reportType->version ?? '02' }}</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex gap-2">
        <a href="{{ route('reports.print', $report->id) }}" target="_blank" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm">
            <i class="bi bi-printer-fill"></i>
            <span>Imprimir / Exportar PDF</span>
        </a>
        @if($report->status === 'draft')
            <form action="{{ route('reports.confirm', $report->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Confirmar este reporte como oficial?')">
                @csrf
                <button type="submit" class="btn btn-success d-flex align-items-center gap-2 shadow-sm">
                    <i class="bi bi-check-lg"></i>
                    <span>Confirmar Reporte</span>
                </button>
            </form>
        @endif
        <a href="{{ route('reports.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert">
    <i class="bi bi-check-circle-fill fs-5"></i>
    <div>{{ session('success') }}</div>
    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row g-4">
    <!-- Columna Izquierda: Información del Cliente y Equipo -->
    <div class="col-lg-6">
        <!-- Tarjeta Cliente -->
        <div class="card mb-4" style="background: var(--surface); border: 1px solid var(--border); border-radius: 14px;">
            <div class="card-header py-3 px-4 d-flex align-items-center justify-content-between" style="border-bottom: 1px solid var(--border);">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary fs-11">Sección A</span>
                    <h5 class="card-title mb-0" style="color: var(--text);">Datos de la Empresa Cliente</h5>
                </div>
                <a href="{{ route('companies.show', $report->company_id) }}" class="fs-12 text-primary text-decoration-none">
                    <i class="bi bi-box-arrow-up-right me-1"></i> Ver Ficha
                </a>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <small class="text-muted d-block fs-12">Razón Social</small>
                        <span class="fw-bold fs-15" style="color: var(--text);">{{ $report->company->name ?? 'N/D' }}</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block fs-12">RUC</small>
                        <span class="fw-semibold" style="color: var(--text);">{{ $report->company->tax_id ?? 'N/D' }}</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block fs-12">Sede / Local</small>
                        <span style="color: var(--text);">{{ $report->data['client_branch'] ?? 'Principal' }}</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block fs-12">Área / Departamento</small>
                        <span style="color: var(--text);">{{ $report->data['client_area'] ?? 'No especificada' }}</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block fs-12">Persona de Contacto</small>
                        <span style="color: var(--text);">{{ $report->data['client_contact'] ?? 'No especificado' }}</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block fs-12">Teléfono / Correo</small>
                        <span style="color: var(--text);">{{ $report->data['client_phone'] ?? $report->data['client_email'] ?? 'No especificado' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tarjeta Equipo -->
        <div class="card mb-4" style="background: var(--surface); border: 1px solid var(--border); border-radius: 14px;">
            <div class="card-header py-3 px-4 d-flex align-items-center justify-content-between" style="border-bottom: 1px solid var(--border);">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary fs-11">Sección B</span>
                    <h5 class="card-title mb-0" style="color: var(--text);">Datos del Equipo y Servicio</h5>
                </div>
                @if($report->equipment)
                    <a href="{{ route('equipment.show', $report->equipment->id) }}" class="fs-12 text-primary text-decoration-none">
                        <i class="bi bi-clock-history me-1"></i> Hoja de Vida
                    </a>
                @endif
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <small class="text-muted d-block fs-12">Tipo de Equipo</small>
                        <span class="fw-bold text-uppercase" style="color: var(--text);">
                            <i class="bi bi-laptop text-primary me-1"></i>
                            {{ $report->equipment->type ?? 'Laptop' }}
                        </span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block fs-12">Número de Serie (S/N)</small>
                        <span class="badge bg-dark px-2 py-1 fs-13 font-monospace">{{ $report->equipment->serial_number ?? 'S/N' }}</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block fs-12">Marca / Modelo</small>
                        <span class="fw-semibold" style="color: var(--text);">{{ $report->equipment->brand ?? 'N/D' }} {{ $report->equipment->model ?? '' }}</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block fs-12">Sistema Operativo</small>
                        <span style="color: var(--text);">{{ $report->data['os_installed'] ?? $report->equipment->os ?? 'Windows' }}</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block fs-12">Fecha del Servicio</small>
                        <span class="fw-bold" style="color: var(--primary);">
                            <i class="bi bi-calendar-check me-1"></i>
                            {{ \Carbon\Carbon::parse($report->service_date)->format('d/m/Y') }}
                        </span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block fs-12">Técnico Responsable</small>
                        <span style="color: var(--text);">{{ $report->technician_name ?? 'Infortech' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tarjeta Usuario y Accesos -->
        <div class="card mb-4" style="background: var(--surface); border: 1px solid var(--border); border-radius: 14px;">
            <div class="card-header py-3 px-4 d-flex align-items-center gap-2" style="border-bottom: 1px solid var(--border);">
                <span class="badge bg-primary fs-11">Sección C</span>
                <h5 class="card-title mb-0" style="color: var(--text);">Usuario y Accesos al Equipo</h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-sm-4">
                        <small class="text-muted d-block fs-12">Usuario Final</small>
                        <span class="fw-semibold" style="color: var(--text);">{{ $report->data['user_name'] ?? 'No asignado' }}</span>
                    </div>
                    <div class="col-sm-4">
                        <small class="text-muted d-block fs-12">Login / Usuario Acceso</small>
                        <code class="fs-13">{{ $report->data['user_login'] ?? 'N/A' }}</code>
                    </div>
                    <div class="col-sm-4">
                        <small class="text-muted d-block fs-12">Hostname</small>
                        <span style="color: var(--text);">{{ $report->data['hostname'] ?? 'N/A' }}</span>
                    </div>
                    <div class="col-12 pt-2 border-top" style="border-color: var(--border) !important;">
                        <div class="d-flex align-items-center justify-content-between p-3 rounded" style="background: rgba(255,255,255,0.02); border: 1px solid var(--border);">
                            <div>
                                <small class="text-muted d-block fs-12">Credenciales Configuradas</small>
                                <span class="badge {{ ($report->data['credentials_configured'] ?? false) ? 'bg-success' : 'bg-secondary' }} px-3 py-1">
                                    {{ ($report->data['credentials_configured'] ?? false) ? 'Sí (Protegidas con Clave)' : 'No (Acceso Libre)' }}
                                </span>
                            </div>
                            @if(!empty($report->data['encrypted_access_password']))
                                <div>
                                    <button type="button" class="btn btn-outline-warning btn-sm" onclick="toggleSecretPassword()">
                                        <i class="bi bi-key-fill me-1"></i> <span id="pwd_btn_label">Ver Contraseña (Cifrada)</span>
                                    </button>
                                    <span id="revealed_password" class="ms-2 fw-bold font-monospace text-warning fs-14" style="display: none;">
                                        {{ $report->decrypted_password }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Columna Derecha: Programas, Configuración y Firmas -->
    <div class="col-lg-6">
        <!-- Tarjeta Programas Instalados -->
        <div class="card mb-4" style="background: var(--surface); border: 1px solid var(--border); border-radius: 14px;">
            <div class="card-header py-3 px-4 d-flex align-items-center justify-content-between" style="border-bottom: 1px solid var(--border);">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary fs-11">Sección D</span>
                    <h5 class="card-title mb-0" style="color: var(--text);">Programas Instalados</h5>
                </div>
                <span class="badge bg-secondary fs-12">{{ $report->software->count() }} programas</span>
            </div>
            <div class="card-body p-4">
                <div class="d-flex flex-wrap gap-2">
                    @forelse($report->software as $soft)
                        <div class="p-2 rounded d-flex align-items-center gap-2" style="background: rgba(255,255,255,0.03); border: 1px solid var(--border);">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <div>
                                <strong class="fs-13" style="color: var(--text);">{{ $soft->effective_name }}</strong>
                                @if($soft->detail)
                                    <div class="fs-11 text-muted">{{ $soft->detail }}</div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <span class="text-muted">No se registraron programas para este reporte.</span>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Tarjeta Tareas de Configuración -->
        <div class="card mb-4" style="background: var(--surface); border: 1px solid var(--border); border-radius: 14px;">
            <div class="card-header py-3 px-4 d-flex align-items-center gap-2" style="border-bottom: 1px solid var(--border);">
                <span class="badge bg-primary fs-11">Sección D.2</span>
                <h5 class="card-title mb-0" style="color: var(--text);">Tareas de Configuración Realizadas</h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="p-3 rounded text-center" style="background: rgba(255,255,255,0.02); border: 1px solid var(--border);">
                            <small class="text-muted d-block fs-12">Backup Realizado</small>
                            <span class="badge {{ ($report->data['backup_done'] ?? false) ? 'bg-success' : 'bg-secondary' }} mt-1">
                                {{ ($report->data['backup_done'] ?? false) ? 'Sí' : 'No' }}
                            </span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 rounded text-center" style="background: rgba(255,255,255,0.02); border: 1px solid var(--border);">
                            <small class="text-muted d-block fs-12">Antivirus</small>
                            <span class="badge {{ ($report->data['antivirus_installed'] ?? false) ? 'bg-success' : 'bg-secondary' }} mt-1">
                                {{ ($report->data['antivirus_installed'] ?? false) ? ($report->data['antivirus_name'] ?? 'Instalado') : 'No instalado' }}
                            </span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 rounded text-center" style="background: rgba(255,255,255,0.02); border: 1px solid var(--border);">
                            <small class="text-muted d-block fs-12">Licencia Activada</small>
                            <span class="badge {{ ($report->data['license_activated'] ?? false) ? 'bg-success' : 'bg-secondary' }} mt-1">
                                {{ ($report->data['license_activated'] ?? false) ? 'Sí (Activa)' : 'No' }}
                            </span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 rounded text-center" style="background: rgba(255,255,255,0.02); border: 1px solid var(--border);">
                            <small class="text-muted d-block fs-12">Drivers Actualizados</small>
                            <span class="badge {{ ($report->data['drivers_installed'] ?? false) ? 'bg-success' : 'bg-secondary' }} mt-1">
                                {{ ($report->data['drivers_installed'] ?? false) ? 'Sí (Completos)' : 'No' }}
                            </span>
                        </div>
                    </div>
                </div>

                @if($report->notes)
                    <div class="mt-4 p-3 rounded" style="background: rgba(229,107,12,0.05); border-left: 3px solid var(--primary);">
                        <strong class="fs-13 d-block mb-1" style="color: var(--primary);">
                            <i class="bi bi-chat-left-text me-1"></i> Observaciones y Recomendaciones:
                        </strong>
                        <div class="fs-13" style="color: var(--text);">{{ $report->notes }}</div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Tarjeta Firmas Digitales -->
        <div class="card mb-4" style="background: var(--surface); border: 1px solid var(--border); border-radius: 14px;">
            <div class="card-header py-3 px-4 d-flex align-items-center gap-2" style="border-bottom: 1px solid var(--border);">
                <span class="badge bg-primary fs-11">Sección E</span>
                <h5 class="card-title mb-0" style="color: var(--text);">Firmas Digitales de Conformidad</h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-4">
                    <!-- Firma Entrega -->
                    <div class="col-sm-6 text-center">
                        <div class="p-3 rounded" style="background: rgba(255,255,255,0.02); border: 1px solid var(--border);">
                            <div class="fs-12 fw-bold text-uppercase mb-2" style="color: var(--primary);">
                                Entregado por (INOFERTEC)
                            </div>
                            <div class="signature-box bg-white rounded p-2 mb-2 d-flex align-items-center justify-content-center" style="height: 110px; border: 1px solid #cbd5e1;">
                                @if($report->deliverySignature && $report->deliverySignature->signature_data)
                                    <img src="{{ $report->deliverySignature->signature_data }}" alt="Firma Técnico" style="max-height: 90px; max-width: 100%;">
                                @else
                                    <span class="text-muted fs-12">Sin firma digital</span>
                                @endif
                            </div>
                            <div class="fw-bold fs-13" style="color: var(--text);">{{ $report->technician_name }}</div>
                            <small class="text-muted">Técnico Especialista</small>
                        </div>
                    </div>

                    <!-- Firma Recepción -->
                    <div class="col-sm-6 text-center">
                        <div class="p-3 rounded" style="background: rgba(255,255,255,0.02); border: 1px solid var(--border);">
                            <div class="fs-12 fw-bold text-uppercase mb-2" style="color: var(--success);">
                                Recibido por (Cliente)
                            </div>
                            <div class="signature-box bg-white rounded p-2 mb-2 d-flex align-items-center justify-content-center" style="height: 110px; border: 1px solid #cbd5e1;">
                                @if($report->receptionSignature && $report->receptionSignature->signature_data)
                                    <img src="{{ $report->receptionSignature->signature_data }}" alt="Firma Receptor" style="max-height: 90px; max-width: 100%;">
                                @else
                                    <span class="text-muted fs-12">Sin firma digital</span>
                                @endif
                            </div>
                            <div class="fw-bold fs-13" style="color: var(--text);">{{ $report->receptionSignature->signer_name ?? $report->data['receiver_name'] ?? 'Cliente' }}</div>
                            <small class="text-muted">{{ $report->receptionSignature->signer_role ?? $report->data['receiver_role'] ?? 'Recepción' }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleSecretPassword() {
        const span = document.getElementById('revealed_password');
        const btn = document.getElementById('pwd_btn_label');
        if (span.style.display === 'none') {
            span.style.display = 'inline-block';
            btn.textContent = 'Ocultar Contraseña';
        } else {
            span.style.display = 'none';
            btn.textContent = 'Ver Contraseña (Cifrada)';
        }
    }
</script>
@endsection
