@extends('layouts.app')

@section('page-title', 'Hoja de Vida: ' . $equipment->display_name)

@section('breadcrumb')
    <i class="bi bi-chevron-right mx-1"></i>
    <a href="{{ route('reports.index') }}" style="color: var(--text-muted); text-decoration: none;">Reportes</a>
    <i class="bi bi-chevron-right mx-1"></i>
    <a href="{{ route('equipment.index') }}" style="color: var(--text-muted); text-decoration: none;">Equipos</a>
    <i class="bi bi-chevron-right mx-1"></i>
    <span>{{ $equipment->serial_number }}</span>
@endsection

@section('content')
<div class="row page-titles mx-0 mb-4 align-items-center">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4 class="mb-1" style="color: var(--text);">Hoja de Vida del Equipo</h4>
            <p class="mb-0 text-muted">Historial completo de formateos, servicios técnicos y mantenimientos realizados.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex gap-2">
        <a href="{{ route('reports.create', ['company_id' => $equipment->company_id]) }}" class="btn btn-primary d-flex align-items-center gap-2">
            <i class="bi bi-plus-circle-fill"></i>
            <span>Nuevo Reporte para este Equipo</span>
        </a>
        <a href="{{ route('equipment.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Ficha Técnica del Equipo -->
    <div class="col-lg-4">
        <div class="card h-100" style="background: var(--surface); border: 1px solid var(--border); border-radius: 14px;">
            <div class="card-header py-3 px-4" style="border-bottom: 1px solid var(--border);">
                <h5 class="card-title mb-0" style="color: var(--text);">
                    <i class="bi bi-info-circle me-2" style="color: var(--primary);"></i>
                    Especificaciones
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="text-center pb-3 mb-3 border-bottom" style="border-color: var(--border) !important;">
                    <div class="display-icon mb-2">
                        @if($equipment->type === 'laptop')
                            <i class="bi bi-laptop fs-1 text-primary"></i>
                        @elseif($equipment->type === 'desktop')
                            <i class="bi bi-display fs-1 text-info"></i>
                        @else
                            <i class="bi bi-server fs-1 text-warning"></i>
                        @endif
                    </div>
                    <h5 class="fw-bold mb-1" style="color: var(--text);">{{ $equipment->brand }} {{ $equipment->model }}</h5>
                    <span class="badge bg-dark font-monospace fs-13 px-3 py-1">S/N: {{ $equipment->serial_number }}</span>
                </div>

                <div class="d-flex flex-column gap-3">
                    <div>
                        <small class="text-muted d-block fs-12">Empresa Propietaria</small>
                        <a href="{{ route('companies.show', $equipment->company_id) }}" class="fw-semibold text-primary text-decoration-none">
                            {{ $equipment->company->name ?? 'N/D' }}
                        </a>
                    </div>
                    <div>
                        <small class="text-muted d-block fs-12">Tipo de Dispositivo</small>
                        <span class="fw-semibold text-uppercase" style="color: var(--text);">{{ $equipment->type }}</span>
                    </div>
                    <div>
                        <small class="text-muted d-block fs-12">Hostname Registrado</small>
                        <span style="color: var(--text);">{{ $equipment->hostname ?? 'No asignado' }}</span>
                    </div>
                    <div>
                        <small class="text-muted d-block fs-12">Sistema Operativo</small>
                        <span style="color: var(--text);">{{ $equipment->os ?? 'No asignado' }}</span>
                    </div>
                    <div>
                        <small class="text-muted d-block fs-12">Fecha de Alta</small>
                        <span style="color: var(--text);">{{ $equipment->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Línea de Tiempo de Servicios Realizados -->
    <div class="col-lg-8">
        <div class="card h-100" style="background: var(--surface); border: 1px solid var(--border); border-radius: 14px;">
            <div class="card-header py-3 px-4 d-flex justify-content-between align-items-center" style="border-bottom: 1px solid var(--border);">
                <h5 class="card-title mb-0" style="color: var(--text);">
                    <i class="bi bi-clock-history me-2" style="color: var(--primary);"></i>
                    Cronograma de Servicios Realizados
                </h5>
                <span class="badge bg-secondary">{{ $equipment->reports->count() }} intervenciones</span>
            </div>
            <div class="card-body p-4">
                @forelse($equipment->reports as $rep)
                    <div class="p-3 mb-3 rounded d-flex align-items-center justify-content-between" style="background: rgba(255,255,255,0.02); border: 1px solid var(--border);">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <a href="{{ route('reports.show', $rep->id) }}" class="fw-bold fs-15 text-decoration-none" style="color: var(--primary);">
                                    <i class="bi bi-file-earmark-check me-1"></i> {{ $rep->code }}
                                </a>
                                @if($rep->status === 'confirmed')
                                    <span class="badge bg-success fs-11">Confirmado</span>
                                @else
                                    <span class="badge bg-warning fs-11">Borrador</span>
                                @endif
                            </div>
                            <div class="fs-13 text-muted">
                                <i class="bi bi-calendar3 me-1"></i> {{ \Carbon\Carbon::parse($rep->service_date)->format('d/m/Y') }}
                                &bull; <i class="bi bi-person me-1"></i> Técnico: {{ $rep->technician_name }}
                            </div>
                            @if($rep->notes)
                                <div class="fs-12 text-muted mt-2 fst-italic">"{{ Str::limit($rep->notes, 120) }}"</div>
                            @endif
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('reports.show', $rep->id) }}" class="btn btn-outline-secondary btn-sm" title="Ver Detalle">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('reports.print', $rep->id) }}" target="_blank" class="btn btn-outline-primary btn-sm" title="Imprimir / PDF">
                                <i class="bi bi-printer"></i>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-calendar-x fs-1 d-block mb-3" style="opacity: 0.5;"></i>
                        No hay reportes de servicio registrados para este equipo todavía.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
