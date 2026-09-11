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

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert">
    <i class="bi bi-exclamation-circle-fill fs-5"></i>
    <div>{{ session('error') }}</div>
    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- Barra de búsqueda y estadísticas --}}
<div class="card mb-4" style="background: var(--surface); border: 1px solid var(--border); border-radius: 14px;">
    <div class="card-body p-3 d-flex flex-wrap gap-3 align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2" style="flex: 1; min-width: 200px;">
            <i class="bi bi-search text-muted"></i>
            <input
                type="text"
                id="searchInput"
                class="form-control border-0 shadow-none p-0"
                placeholder="Buscar programa por nombre o categoría..."
                style="background: transparent; color: var(--text); outline: none;"
                autocomplete="off"
            >
        </div>
        <div class="d-flex gap-3 text-muted fs-13">
            <span><i class="bi bi-app-indicator me-1"></i> <strong id="totalCount" style="color:var(--text);">{{ $software->flatten()->count() }}</strong> programas</span>
            <span><i class="bi bi-folder me-1"></i> <strong style="color:var(--text);">{{ $software->count() }}</strong> categorías</span>
            <span><i class="bi bi-check-circle me-1" style="color:#22c55e;"></i> <strong style="color:var(--text);">{{ $software->flatten()->where('is_active', true)->count() }}</strong> activos</span>
        </div>
    </div>
</div>

{{-- Grid de tarjetas por categoría --}}
<div class="row g-4" id="catalogGrid">
    @foreach($software as $category => $items)
    <div class="col-md-6 category-col" data-category="{{ strtolower($category) }}">
        <div class="card h-100" style="background: var(--surface); border: 1px solid var(--border); border-radius: 14px;">
            <div class="card-header py-3 px-4 d-flex justify-content-between align-items-center" style="border-bottom: 1px solid var(--border);">
                <h5 class="card-title mb-0" style="color: var(--primary);">
                    <i class="bi bi-folder-fill me-2"></i> {{ $category ?: 'General' }}
                </h5>
                <span class="badge bg-secondary fs-11 category-count">{{ $items->count() }} programas</span>
            </div>
            <div class="card-body p-3">
                <div class="d-flex flex-column gap-2">
                    @foreach($items as $prog)
                    <div
                        class="prog-row p-3 rounded d-flex align-items-center justify-content-between"
                        data-name="{{ strtolower($prog->name) }}"
                        style="background: rgba(255,255,255,0.02); border: 1px solid var(--border); transition: opacity 0.2s; {{ !$prog->is_active ? 'opacity:0.45;' : '' }}"
                    >
                        <div class="d-flex flex-column gap-1">
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <span class="fw-semibold fs-13" style="color: var(--text);">{{ $prog->name }}</span>
                                @if($prog->requires_detail)
                                    <span class="badge bg-warning text-dark fs-10">Detallable</span>
                                @endif
                                @if(!$prog->is_active)
                                    <span class="badge bg-danger fs-10">Inactivo</span>
                                @endif
                            </div>
                            @if($prog->sort_order > 0)
                            <span class="fs-11 text-muted"><i class="bi bi-sort-numeric-down me-1"></i>Orden: {{ $prog->sort_order }}</span>
                            @endif
                        </div>

                        <div class="d-flex align-items-center gap-1">
                            {{-- Toggle Activo/Inactivo --}}
                            <form action="{{ route('software-catalog.update', $prog->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="name" value="{{ $prog->name }}">
                                <input type="hidden" name="category" value="{{ $prog->category }}">
                                <input type="hidden" name="sort_order" value="{{ $prog->sort_order }}">
                                @if($prog->requires_detail)<input type="hidden" name="requires_detail" value="1">@endif
                                @if(!$prog->is_active)<input type="hidden" name="is_active" value="1">@endif
                                <button type="submit"
                                    class="btn btn-sm py-1 px-2 {{ $prog->is_active ? 'btn-outline-success' : 'btn-outline-secondary' }}"
                                    title="{{ $prog->is_active ? 'Desactivar' : 'Activar' }}">
                                    <i class="bi {{ $prog->is_active ? 'bi-toggle-on fs-5' : 'bi-toggle-off fs-5' }}"></i>
                                </button>
                            </form>

                            {{-- Botón Editar --}}
                            <button type="button"
                                class="btn btn-outline-primary btn-sm py-1 px-2"
                                title="Editar programa"
                                onclick="openEditModal({{ $prog->id }}, {{ json_encode($prog->name) }}, {{ json_encode($prog->category) }}, {{ $prog->requires_detail ? 'true' : 'false' }}, {{ $prog->is_active ? 'true' : 'false' }}, {{ $prog->sort_order }})">
                                <i class="bi bi-pencil-fill"></i>
                            </button>

                            {{-- Botón Eliminar --}}
                            <form action="{{ route('software-catalog.destroy', $prog->id) }}" method="POST"
                                onsubmit="return confirm('¿Eliminar ' + {{ json_encode($prog->name) }} + '? Esta acción no se puede deshacer.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm py-1 px-2" title="Eliminar">
                                    <i class="bi bi-trash-fill"></i>
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

{{-- Mensaje sin resultados --}}
<div id="noResults" class="d-none text-center py-5">
    <i class="bi bi-search fs-1 text-muted d-block mb-3"></i>
    <p class="text-muted fs-14">No se encontraron programas que coincidan con "<span id="searchTerm" class="fw-semibold"></span>"</p>
</div>


{{-- ==================== MODAL: AGREGAR PROGRAMA ==================== --}}
<div class="modal fade" id="addSoftwareModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: var(--surface); border: 1px solid var(--border); border-radius: 14px; color: var(--text);">
            <form action="{{ route('software-catalog.store') }}" method="POST">
                @csrf
                <div class="modal-header" style="border-bottom: 1px solid var(--border);">
                    <h5 class="modal-title"><i class="bi bi-plus-circle-fill me-2" style="color: var(--primary);"></i> Agregar Programa al Catálogo</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nombre del Programa <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="Ej. Microsoft 365, AutoCAD, Visual Studio" required
                            style="background: var(--surface); color: var(--text); border-color: var(--border);">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Categoría <span class="text-danger">*</span></label>
                        <input type="text" name="category" class="form-control" placeholder="Ofimática, Colaboración, CAD / Diseño..." required list="categoryListAdd"
                            style="background: var(--surface); color: var(--text); border-color: var(--border);">
                        <datalist id="categoryListAdd">
                            @foreach($software->keys() as $cat)
                            <option value="{{ $cat }}">
                            @endforeach
                        </datalist>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Orden de aparición</label>
                        <input type="number" name="sort_order" class="form-control" value="0" min="0"
                            style="background: var(--surface); color: var(--text); border-color: var(--border);">
                        <div class="form-text text-muted">Número menor aparece primero dentro de la categoría.</div>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="requires_detail" value="1" id="reqDetailAdd">
                        <label class="form-check-label fs-13" for="reqDetailAdd">
                            Requiere especificar productos / extensiones (como Autodesk o Adobe)
                        </label>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid var(--border);">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i>Guardar Programa</button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- ==================== MODAL: EDITAR PROGRAMA ==================== --}}
<div class="modal fade" id="editSoftwareModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: var(--surface); border: 1px solid var(--border); border-radius: 14px; color: var(--text);">
            <form id="editSoftwareForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header" style="border-bottom: 1px solid var(--border);">
                    <h5 class="modal-title"><i class="bi bi-pencil-fill me-2" style="color: var(--primary);"></i> Editar Programa</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nombre del Programa <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="editName" class="form-control" required
                            style="background: var(--surface); color: var(--text); border-color: var(--border);">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Categoría <span class="text-danger">*</span></label>
                        <input type="text" name="category" id="editCategory" class="form-control" required list="categoryListEdit"
                            style="background: var(--surface); color: var(--text); border-color: var(--border);">
                        <datalist id="categoryListEdit">
                            @foreach($software->keys() as $cat)
                            <option value="{{ $cat }}">
                            @endforeach
                        </datalist>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Orden de aparición</label>
                        <input type="number" name="sort_order" id="editSortOrder" class="form-control" min="0"
                            style="background: var(--surface); color: var(--text); border-color: var(--border);">
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="requires_detail" value="1" id="editReqDetail">
                        <label class="form-check-label fs-13" for="editReqDetail">
                            Requiere especificar productos / extensiones
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="editIsActive">
                        <label class="form-check-label fs-13" for="editIsActive">
                            Programa activo (visible al crear reportes)
                        </label>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid var(--border);">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>


@push('scripts')
<script>
// ─── Abrir modal de edición prellenado ───────────────────────────────────────
function openEditModal(id, name, category, requiresDetail, isActive, sortOrder) {
    const form = document.getElementById('editSoftwareForm');
    form.action = '{{ url("software-catalog") }}/' + id;

    document.getElementById('editName').value        = name;
    document.getElementById('editCategory').value    = category;
    document.getElementById('editSortOrder').value   = sortOrder;
    document.getElementById('editReqDetail').checked = requiresDetail;
    document.getElementById('editIsActive').checked  = isActive;

    new bootstrap.Modal(document.getElementById('editSoftwareModal')).show();
}

// ─── Búsqueda en tiempo real ─────────────────────────────────────────────────
document.getElementById('searchInput').addEventListener('input', function () {
    const query     = this.value.trim().toLowerCase();
    const rows      = document.querySelectorAll('.prog-row');
    const cols      = document.querySelectorAll('.category-col');
    const noResults = document.getElementById('noResults');
    let visible     = 0;

    document.getElementById('searchTerm').textContent = query;

    rows.forEach(row => {
        const nameMatch = (row.dataset.name || '').includes(query);
        const catMatch  = (row.closest('.category-col')?.dataset.category || '').includes(query);
        const show      = !query || nameMatch || catMatch;
        row.style.display = show ? '' : 'none';
        if (show) visible++;
    });

    cols.forEach(col => {
        const hasVisible = col.querySelectorAll('.prog-row:not([style*="display: none"])').length > 0;
        col.style.display = hasVisible ? '' : 'none';
    });

    document.getElementById('totalCount').textContent = visible;
    noResults.classList.toggle('d-none', visible > 0 || !query);
});
</script>
@endpush

@endsection
