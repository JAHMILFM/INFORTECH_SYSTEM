@extends('layouts.app')

@section('page-title', 'Reportes Técnicos')

@section('breadcrumb')
    <i class="bi bi-chevron-right mx-1"></i>
    <span>Reportes Técnicos</span>
@endsection

@section('content')
<div class="row page-titles mx-0 mb-4 align-items-center">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4 class="mb-1" style="color: var(--text);">Módulo de Reportes Técnicos</h4>
            <p class="mb-0 text-muted">Gestión y control de reportes de formateo y servicios de infraestructura.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex gap-2">
        <a href="{{ route('reports.create') }}" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm">
            <i class="bi bi-plus-circle-fill"></i>
            <span>Nuevo Reporte FOR-TI-001</span>
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

@if(session('info'))
<div class="alert alert-info alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert">
    <i class="bi bi-info-circle-fill fs-5"></i>
    <div>{{ session('info') }}</div>
    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
</div>
@endif

<!-- Tarjetas de Métricas -->
<div class="row mb-4 g-3">
    <div class="col-xl-3 col-sm-6">
        <div class="card card-stat h-100 p-3" style="background: var(--surface); border: 1px solid var(--border); border-radius: 14px;">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon-wrap" style="width: 50px; height: 50px; border-radius: 12px; background: rgba(229,107,12,0.15); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 24px;">
                    <i class="bi bi-folder2-open"></i>
                </div>
                <div>
                    <div class="fs-12 text-muted text-uppercase fw-semibold letter-spacing-1">Total Reportes</div>
                    <div class="fs-24 fw-bold mt-1" style="color: var(--text);">{{ $stats['total'] }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="card card-stat h-100 p-3" style="background: var(--surface); border: 1px solid var(--border); border-radius: 14px;">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon-wrap" style="width: 50px; height: 50px; border-radius: 12px; background: rgba(5,150,105,0.15); color: var(--success); display: flex; align-items: center; justify-content: center; font-size: 24px;">
                    <i class="bi bi-patch-check-fill"></i>
                </div>
                <div>
                    <div class="fs-12 text-muted text-uppercase fw-semibold letter-spacing-1">Confirmados</div>
                    <div class="fs-24 fw-bold mt-1" style="color: var(--success);">{{ $stats['confirmed'] }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="card card-stat h-100 p-3" style="background: var(--surface); border: 1px solid var(--border); border-radius: 14px;">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon-wrap" style="width: 50px; height: 50px; border-radius: 12px; background: rgba(245,158,11,0.15); color: var(--warning); display: flex; align-items: center; justify-content: center; font-size: 24px;">
                    <i class="bi bi-hourglass-split"></i>
                </div>
                <div>
                    <div class="fs-12 text-muted text-uppercase fw-semibold letter-spacing-1">En Borrador</div>
                    <div class="fs-24 fw-bold mt-1" style="color: var(--warning);">{{ $stats['draft'] }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="card card-stat h-100 p-3" style="background: var(--surface); border: 1px solid var(--border); border-radius: 14px;">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon-wrap" style="width: 50px; height: 50px; border-radius: 12px; background: rgba(31,58,111,0.25); color: #60a5fa; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                    <i class="bi bi-calendar-check-fill"></i>
                </div>
                <div>
                    <div class="fs-12 text-muted text-uppercase fw-semibold letter-spacing-1">Atendidos Este Mes</div>
                    <div class="fs-24 fw-bold mt-1" style="color: #60a5fa;">{{ $stats['this_month'] }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filtros de Búsqueda -->
<div class="card mb-4" style="background: var(--surface); border: 1px solid var(--border); border-radius: 14px;">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('reports.index') }}" class="row g-2 align-items-center">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0 text-muted" style="border-color: var(--border);">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="search" class="form-control border-start-0" 
                           placeholder="Buscar por código, serie, marca o técnico..."
                           value="{{ request('search') }}" style="background: transparent; color: var(--text); border-color: var(--border);">
                </div>
            </div>
            <div class="col-md-3">
                <select name="company_id" class="form-select" style="background: var(--surface); color: var(--text); border-color: var(--border);">
                    <option value="">-- Todas las Empresas --</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>
                            {{ $company->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select" style="background: var(--surface); color: var(--text); border-color: var(--border);">
                    <option value="">-- Todos los Estados --</option>
                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmado</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Borrador</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-secondary flex-grow-1">
                    <i class="bi bi-funnel me-1"></i> Filtrar
                </button>
                @if(request()->anyFilled(['search', 'company_id', 'status']))
                    <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary" title="Limpiar filtros">
                        <i class="bi bi-x-circle"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Tabla de Reportes -->
<div class="card" style="background: var(--surface); border: 1px solid var(--border); border-radius: 14px;">
    <div class="card-header d-flex justify-content-between align-items-center py-3 px-4" style="border-bottom: 1px solid var(--border);">
        <h5 class="card-title mb-0" style="color: var(--text);">
            <i class="bi bi-list-columns-reverse me-2" style="color: var(--primary);"></i>
            Historial de Reportes FOR-TI-001
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="color: var(--text);">
                <thead style="background: rgba(255,255,255,0.02); border-bottom: 1px solid var(--border);">
                    <tr>
                        <th class="ps-4">Código</th>
                        <th>Fecha Servicio</th>
                        <th>Empresa Cliente</th>
                        <th>Equipo / Serie</th>
                        <th>Técnico</th>
                        <th class="text-center">Estado</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $report)
                    <tr style="border-bottom: 1px solid var(--border);">
                        <td class="ps-4">
                            <a href="{{ route('reports.show', $report->id) }}" class="fw-bold" style="color: var(--primary); text-decoration: none;">
                                <i class="bi bi-file-earmark-text me-1"></i>
                                {{ $report->code }}
                            </a>
                            <div class="fs-11 text-muted">{{ $report->reportType->name ?? 'Formateo' }} (v{{ $report->reportType->version ?? '02' }})</div>
                        </td>
                        <td>
                            <div>{{ \Carbon\Carbon::parse($report->service_date)->format('d/m/Y') }}</div>
                            <small class="text-muted">{{ \Carbon\Carbon::parse($report->service_date)->diffForHumans() }}</small>
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $report->company->name ?? 'Empresa no asignada' }}</div>
                            <small class="text-muted">RUC: {{ $report->company->tax_id ?? 'S/R' }}</small>
                        </td>
                        <td>
                            @if($report->equipment)
                                <a href="{{ route('equipment.show', $report->equipment->id) }}" class="text-reset text-decoration-none">
                                    <div class="d-flex align-items-center gap-2">
                                        @if($report->equipment->type === 'laptop')
                                            <i class="bi bi-laptop fs-5 text-primary"></i>
                                        @elseif($report->equipment->type === 'desktop')
                                            <i class="bi bi-display fs-5 text-info"></i>
                                        @else
                                            <i class="bi bi-server fs-5 text-warning"></i>
                                        @endif
                                        <div>
                                            <span class="fw-semibold">{{ $report->equipment->brand }} {{ $report->equipment->model }}</span>
                                            <div class="fs-12 text-muted">S/N: <code>{{ $report->equipment->serial_number }}</code></div>
                                        </div>
                                    </div>
                                </a>
                            @else
                                <span class="text-muted">No especificado</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-sm rounded-circle d-flex align-items-center justify-content-center text-white" 
                                     style="width: 28px; height: 28px; background: var(--surface2); font-size: 11px;">
                                    {{ strtoupper(substr($report->technician_name ?? 'T', 0, 1)) }}
                                </div>
                                <span class="fs-13">{{ $report->technician_name ?? 'Técnico Infortech' }}</span>
                            </div>
                        </td>
                        <td class="text-center">
                            @if($report->status === 'confirmed')
                                <span class="badge px-3 py-2" style="background: rgba(5,150,105,0.2); color: #34d399; border: 1px solid rgba(5,150,105,0.3); border-radius: 8px;">
                                    <i class="bi bi-check-circle-fill me-1"></i> Confirmado
                                </span>
                            @else
                                <span class="badge px-3 py-2" style="background: rgba(245,158,11,0.2); color: #fbbf24; border: 1px solid rgba(245,158,11,0.3); border-radius: 8px;">
                                    <i class="bi bi-clock-history me-1"></i> Borrador
                                </span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('reports.show', $report->id) }}" class="btn btn-outline-secondary" title="Ver Detalle">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('reports.print', $report->id) }}" target="_blank" class="btn btn-outline-primary" title="Imprimir / Exportar PDF">
                                    <i class="bi bi-printer"></i>
                                </a>
                                @if($report->status === 'draft')
                                    <form action="{{ route('reports.confirm', $report->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Deseas confirmar este reporte? Ya no se podrá editar como borrador.')">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-success" title="Confirmar Reporte">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                    </form>
                                @endif
                                @if(auth()->user()->role === 'SuperAdmin')
                                    <form action="{{ route('reports.destroy', $report->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este reporte definitivamente?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="text-muted">
                                <i class="bi bi-folder-x fs-1 d-block mb-3" style="opacity: 0.5;"></i>
                                <p class="mb-2">No se encontraron reportes técnicos registrados.</p>
                                <a href="{{ route('reports.create') }}" class="btn btn-primary btn-sm mt-1">
                                    <i class="bi bi-plus-circle me-1"></i> Crear primer reporte FOR-TI-001
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($reports->hasPages())
        <div class="p-3 border-top" style="border-color: var(--border) !important;">
            {{ $reports->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
