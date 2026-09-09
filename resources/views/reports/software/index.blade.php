@extends('layouts.app')

@section('page-title', 'Catálogo de Programas')

@section('breadcrumb')
    <i class="bi bi-chevron-right mx-1"></i>
    <a href="{{ route('reports.index') }}" style="color: var(--text-muted); text-decoration: none;">Reportes</a>
    <i class="bi bi-chevron-right mx-1"></i>
    <span>Catálogo de Programas</span>
@endsection

@section('content')
<div class="row page-titles mx-0 mb-4 align-items-center">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4 class="mb-1" style="color: var(--text);">Catálogo de Programas y Software</h4>
            <p class="mb-0 text-muted">Administra los programas seleccionables en los reportes de formateo.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex gap-2">
        <button class="btn btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#addSoftwareModal">
            <i class="bi bi-plus-circle-fill"></i>
            <span>Agregar Programa</span>
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

<div class="row g-4">
    @foreach($software as $category => $items)
    <div class="col-md-6">
        <div class="card h-100" style="background: var(--surface); border: 1px solid var(--border); border-radius: 14px;">
            <div class="card-header py-3 px-4 d-flex justify-content-between align-items-center" style="border-bottom: 1px solid var(--border);">
                <h5 class="card-title mb-0" style="color: var(--primary);">
                    <i class="bi bi-folder-fill me-2"></i> {{ $category ?: 'General' }}
                </h5>
                <span class="badge bg-secondary fs-11">{{ $items->count() }} programas</span>
            </div>
            <div class="card-body p-3">
                <div class="d-flex flex-column gap-2">
                    @foreach($items as $prog)
                    <div class="p-2 rounded d-flex align-items-center justify-content-between" style="background: rgba(255,255,255,0.02); border: 1px solid var(--border);">
                        <div>
                            <span class="fw-semibold fs-13" style="color: var(--text);">{{ $prog->name }}</span>
                            @if($prog->requires_detail)
                                <span class="badge bg-warning text-dark fs-10 ms-1">Detallable</span>
                            @endif
                            @if(!$prog->is_active)
                                <span class="badge bg-danger fs-10 ms-1">Inactivo</span>
                            @endif
                        </div>
                        <div class="btn-group btn-group-sm">
                            <form action="{{ route('software-catalog.destroy', $prog->id) }}" method="POST" onsubmit="return confirm('¿Eliminar {{ $prog->name }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm py-0 px-2">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- Modal Agregar Programa -->
<div class="modal fade" id="addSoftwareModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: var(--surface); border: 1px solid var(--border); border-radius: 14px; color: var(--text);">
            <form action="{{ route('software-catalog.store') }}" method="POST">
                @csrf
                <div class="modal-header" style="border-bottom: 1px solid var(--border);">
                    <h5 class="modal-title"><i class="bi bi-app-indicator me-2" style="color: var(--primary);"></i> Agregar Programa al Catálogo</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nombre del Programa <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="Ej. Microsoft 365, AutoCAD, Visual Studio" required style="background: var(--surface); color: var(--text); border-color: var(--border);">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Categoría <span class="text-danger">*</span></label>
                        <input type="text" name="category" class="form-control" placeholder="Ofimática, Colaboración, CAD / Diseño, Utilidades" required list="categoryList" style="background: var(--surface); color: var(--text); border-color: var(--border);">
                        <datalist id="categoryList">
                            <option value="Ofimática">
                            <option value="Colaboración">
                            <option value="CAD / Diseño">
                            <option value="Soporte Remoto">
                            <option value="Navegación">
                            <option value="Utilidades">
                            <option value="Seguridad">
                        </datalist>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="requires_detail" value="1" id="reqDetail">
                        <label class="form-check-label fs-13" for="reqDetail">
                            Requiere especificar productos / extensiones (como en Autodesk o Adobe)
                        </label>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid var(--border);">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Programa</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
