@extends('layouts.app')

@section('page-title', 'Equipos y Hoja de Vida')

@section('breadcrumb')
    <i class="bi bi-chevron-right mx-1"></i>
    <a href="{{ route('reports.index') }}" style="color: var(--text-muted); text-decoration: none;">Reportes Técnicos</a>
    <i class="bi bi-chevron-right mx-1"></i>
    <span>Equipos Registrados</span>
@endsection

@section('content')
<div class="row page-titles mx-0 mb-4 align-items-center">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4 class="mb-1" style="color: var(--text);">Catálogo de Equipos y Hoja de Vida</h4>
            <p class="mb-0 text-muted">Registro centralizado de laptops, desktops y servidores por cliente.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex gap-2">
        <button class="btn btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#addEquipmentModal">
            <i class="bi bi-plus-circle-fill"></i>
            <span>Registrar Nuevo Equipo</span>
        </button>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert">
    <i class="bi bi-check-circle-fill fs-5"></i>
    <div>{{ session('success') }}</div>
    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
</div>
@endif

<!-- Filtros de Búsqueda -->
<div class="card mb-4" style="background: var(--surface); border: 1px solid var(--border); border-radius: 14px;">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('equipment.index') }}" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0 text-muted" style="border-color: var(--border);">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="search" class="form-control border-start-0" 
                           placeholder="Buscar por serie, marca, modelo o hostname..."
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
                <select name="type" class="form-select" style="background: var(--surface); color: var(--text); border-color: var(--border);">
                    <option value="">-- Tipo --</option>
                    <option value="laptop" {{ request('type') == 'laptop' ? 'selected' : '' }}>Laptop</option>
                    <option value="desktop" {{ request('type') == 'desktop' ? 'selected' : '' }}>Desktop</option>
                    <option value="server" {{ request('type') == 'server' ? 'selected' : '' }}>Servidor</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-secondary flex-grow-1">
                    <i class="bi bi-funnel me-1"></i> Filtrar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Tabla de Equipos -->
<div class="card" style="background: var(--surface); border: 1px solid var(--border); border-radius: 14px;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="color: var(--text);">
                <thead style="background: rgba(255,255,255,0.02); border-bottom: 1px solid var(--border);">
                    <tr>
                        <th class="ps-4">Tipo</th>
                        <th>Marca y Modelo</th>
                        <th>Número de Serie</th>
                        <th>Empresa Propietaria</th>
                        <th>Hostname / S.O.</th>
                        <th>Reportes</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($equipments as $item)
                    <tr style="border-bottom: 1px solid var(--border);">
                        <td class="ps-4">
                            @if($item->type === 'laptop')
                                <span class="badge px-3 py-2 text-primary" style="background: rgba(229,107,12,0.1); border: 1px solid rgba(229,107,12,0.2);">
                                    <i class="bi bi-laptop me-1"></i> Laptop
                                </span>
                            @elseif($item->type === 'desktop')
                                <span class="badge px-3 py-2 text-info" style="background: rgba(14,165,233,0.1); border: 1px solid rgba(14,165,233,0.2);">
                                    <i class="bi bi-display me-1"></i> Desktop
                                </span>
                            @else
                                <span class="badge px-3 py-2 text-warning" style="background: rgba(245,158,11,0.1); border: 1px solid rgba(245,158,11,0.2);">
                                    <i class="bi bi-server me-1"></i> Servidor
                                </span>
                            @endif
                        </td>
                        <td>
                            <strong class="fs-14" style="color: var(--text);">{{ $item->brand }} {{ $item->model }}</strong>
                        </td>
                        <td>
                            <code class="px-2 py-1 rounded fs-13" style="background: rgba(255,255,255,0.05); color: var(--primary);">{{ $item->serial_number }}</code>
                        </td>
                        <td>
                            <span class="fw-semibold">{{ $item->company->name ?? 'N/D' }}</span>
                        </td>
                        <td>
                            <div class="fs-13">{{ $item->hostname ?? 'Sin hostname' }}</div>
                            <small class="text-muted">{{ $item->os ?? 'Sin S.O. registrado' }}</small>
                        </td>
                        <td>
                            <span class="badge bg-secondary">{{ $item->reports->count() }} servicios</span>
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('equipment.show', $item->id) }}" class="btn btn-outline-primary btn-sm" title="Ver Hoja de Vida">
                                <i class="bi bi-clock-history me-1"></i> Hoja de Vida
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-laptop fs-1 d-block mb-3" style="opacity: 0.5;"></i>
                            No hay equipos registrados.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($equipments->hasPages())
        <div class="p-3 border-top" style="border-color: var(--border) !important;">
            {{ $equipments->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Modal Registrar Nuevo Equipo -->
<div class="modal fade" id="addEquipmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: var(--surface); border: 1px solid var(--border); border-radius: 14px; color: var(--text);">
            <form action="{{ route('equipment.store') }}" method="POST">
                @csrf
                <div class="modal-header" style="border-bottom: 1px solid var(--border);">
                    <h5 class="modal-title"><i class="bi bi-laptop me-2" style="color: var(--primary);"></i> Registrar Nuevo Equipo</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Empresa Cliente <span class="text-danger">*</span></label>
                        <select name="company_id" class="form-select" required style="background: var(--surface); color: var(--text); border-color: var(--border);">
                            @foreach($companies as $comp)
                                <option value="{{ $comp->id }}">{{ $comp->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tipo de Equipo <span class="text-danger">*</span></label>
                        <select name="type" class="form-select" required style="background: var(--surface); color: var(--text); border-color: var(--border);">
                            <option value="laptop">Laptop</option>
                            <option value="desktop">Desktop</option>
                            <option value="server">Servidor</option>
                        </select>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Marca <span class="text-danger">*</span></label>
                            <input type="text" name="brand" class="form-control" placeholder="Lenovo, HP, Dell" required style="background: var(--surface); color: var(--text); border-color: var(--border);">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Modelo <span class="text-danger">*</span></label>
                            <input type="text" name="model" class="form-control" placeholder="ThinkPad E14" required style="background: var(--surface); color: var(--text); border-color: var(--border);">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Número de Serie (S/N) <span class="text-danger">*</span></label>
                        <input type="text" name="serial_number" class="form-control text-uppercase font-monospace" placeholder="PF3X889K" required style="background: var(--surface); color: var(--text); border-color: var(--border);">
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label">Hostname</label>
                            <input type="text" name="hostname" class="form-control" placeholder="CORP-LAP-01" style="background: var(--surface); color: var(--text); border-color: var(--border);">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Sistema Operativo</label>
                            <input type="text" name="os" class="form-control" placeholder="Windows 11 Pro" style="background: var(--surface); color: var(--text); border-color: var(--border);">
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid var(--border);">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Registrar Equipo</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
