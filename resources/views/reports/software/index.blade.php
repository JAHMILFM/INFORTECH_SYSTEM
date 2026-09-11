@extends('layouts.app')

@section('page-title', 'Catálogo de Programas')

@section('breadcrumb')
    <i class="bi bi-chevron-right mx-1"></i>
    <a href="{{ route('reports.index') }}" style="color: var(--text-muted); text-decoration: none;">Reportes</a>
    <i class="bi bi-chevron-right mx-1"></i>
    <span>Catálogo de Programas</span>
@endsection

@push('styles')
<style>
    .cat-card { border-radius: 16px; border: 1px solid var(--border); background: var(--surface); transition: box-shadow .2s; }
    .cat-card:hover { box-shadow: 0 4px 24px rgba(0,0,0,.18); }
    .cat-header { border-radius: 16px 16px 0 0; border-bottom: 1px solid var(--border); padding: 14px 18px; display: flex; align-items: center; justify-content: space-between; }
    .prog-item { display: flex; align-items: center; justify-content: space-between; padding: 10px 14px; border-radius: 10px; border: 1px solid var(--border); background: rgba(255,255,255,0.025); transition: background .15s, opacity .2s; }
    .prog-item:hover { background: rgba(255,255,255,0.05); }
    .prog-item.inactive { opacity: .45; }
    .prog-actions { display: flex; gap: 5px; align-items: center; flex-shrink: 0; }
    .btn-icon { width: 30px; height: 30px; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; border: 1px solid; font-size: 13px; cursor: pointer; transition: all .15s; padding: 0; }
    .btn-icon:hover { transform: translateY(-1px); }
    .search-bar { background: var(--surface); border: 1px solid var(--border); border-radius: 12px; padding: 10px 16px; display: flex; align-items: center; gap: 10px; }
    .search-bar input { background: transparent; border: none; outline: none; color: var(--text); flex: 1; font-size: 14px; }
    .stat-pill { display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
    .add-prog-btn { width: 100%; border: 1.5px dashed var(--border); border-radius: 10px; padding: 8px; color: var(--text-muted); background: transparent; font-size: 13px; cursor: pointer; transition: all .15s; margin-top: 6px; }
    .add-prog-btn:hover { border-color: var(--primary); color: var(--primary); background: rgba(99,102,241,.06); }
    .modal-content { background: var(--surface); border: 1px solid var(--border); border-radius: 16px; color: var(--text); }
    .modal-header { border-bottom: 1px solid var(--border); }
    .modal-footer { border-top: 1px solid var(--border); }
    .form-control, .form-select { background: var(--surface) !important; color: var(--text) !important; border-color: var(--border) !important; }
    .form-control:focus, .form-select:focus { border-color: var(--primary) !important; box-shadow: 0 0 0 3px rgba(99,102,241,.15) !important; }
    .no-results { text-align: center; padding: 60px 20px; display: none; }
    .no-results i { font-size: 48px; opacity: .35; display: block; margin-bottom: 12px; }
    .category-header-icon { width: 34px; height: 34px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; font-size: 16px; margin-right: 10px; }
</style>
@endpush

@section('content')

{{-- ─── HEADER ─────────────────────────────────────────────────────────────── --}}
<div class="row page-titles mx-0 mb-4 align-items-center">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4 class="mb-1" style="color: var(--text);">Catálogo de Programas y Software</h4>
            <p class="mb-0 text-muted">Administra los programas y categorías disponibles en los reportes.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex gap-2">
        <button class="btn btn-outline-secondary d-flex align-items-center gap-2" onclick="openAddCategoryModal()">
            <i class="bi bi-folder-plus"></i>
            <span>Nueva Categoría</span>
        </button>
        <button class="btn btn-primary d-flex align-items-center gap-2" onclick="openAddProgramModal(null)">
            <i class="bi bi-plus-circle-fill"></i>
            <span>Agregar Programa</span>
        </button>
    </div>
</div>

{{-- ─── BARRA STATS + BÚSQUEDA ──────────────────────────────────────────────── --}}
<div class="d-flex flex-wrap gap-3 align-items-center mb-4">
    <div class="search-bar" style="flex:1; min-width:220px;">
        <i class="bi bi-search text-muted"></i>
        <input type="text" id="searchInput" placeholder="Buscar programa o categoría..." autocomplete="off">
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <span class="stat-pill" style="background:rgba(99,102,241,.12); color:#818cf8;">
            <i class="bi bi-app-indicator"></i>
            <span id="totalCount">{{ $software->flatten()->count() }}</span> programas
        </span>
        <span class="stat-pill" style="background:rgba(34,197,94,.12); color:#4ade80;">
            <i class="bi bi-check-circle"></i>
            <span id="activeCount">{{ $software->flatten()->where('is_active', true)->count() }}</span> activos
        </span>
        <span class="stat-pill" style="background:rgba(148,163,184,.1); color:#94a3b8;">
            <i class="bi bi-folder"></i>
            <span>{{ $software->count() }}</span> categorías
        </span>
    </div>
</div>

{{-- ─── GRID DE CATEGORÍAS ──────────────────────────────────────────────────── --}}
<div class="row g-4" id="catalogGrid">
    @forelse($software as $category => $items)
    <div class="col-md-6 category-col" data-category="{{ strtolower($category) }}">
        <div class="cat-card">
            {{-- Header de categoría --}}
            <div class="cat-header">
                <div class="d-flex align-items-center">
                    <span class="category-header-icon" style="background:rgba(99,102,241,.15);">
                        <i class="bi bi-folder-fill" style="color:var(--primary);"></i>
                    </span>
                    <div>
                        <span class="fw-bold fs-14" style="color:var(--text);">{{ $category ?: 'General' }}</span>
                        <span class="ms-2 badge bg-secondary fs-10 category-count">{{ $items->count() }}</span>
                    </div>
                </div>
                {{-- Botón eliminar categoría --}}
                <button class="btn-icon btn-outline-danger" style="color:#f87171; border-color:#f87171;"
                    title="Eliminar categoría completa"
                    onclick="deleteCategory({{ json_encode($category ?: 'General') }})">
                    <i class="bi bi-folder-x"></i>
                </button>
            </div>

            {{-- Lista de programas --}}
            <div class="p-3 d-flex flex-column gap-2">
                @foreach($items as $prog)
                <div class="prog-item {{ !$prog->is_active ? 'inactive' : '' }}" data-name="{{ strtolower($prog->name) }}" id="prog-{{ $prog->id }}">
                    <div class="d-flex flex-column gap-1 me-2" style="min-width:0;">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="fw-semibold fs-13" style="color:var(--text); white-space:nowrap;">{{ $prog->name }}</span>
                            @if($prog->requires_detail)
                                <span class="badge fs-10" style="background:rgba(234,179,8,.2); color:#fbbf24;">
                                    <i class="bi bi-tag-fill me-1"></i>Detallable
                                </span>
                            @endif
                            @if(!$prog->is_active)
                                <span class="badge fs-10" style="background:rgba(239,68,68,.15); color:#f87171;">
                                    <i class="bi bi-slash-circle me-1"></i>Inactivo
                                </span>
                            @endif
                        </div>
                        @if($prog->sort_order > 0)
                        <span class="fs-11" style="color:var(--text-muted);">
                            <i class="bi bi-sort-numeric-down me-1"></i>Orden {{ $prog->sort_order }}
                        </span>
                        @endif
                    </div>

                    <div class="prog-actions">
                        {{-- Toggle Habilitar/Deshabilitar --}}
                        <button class="btn-icon {{ $prog->is_active ? 'btn-outline-success' : 'btn-outline-secondary' }}"
                            style="color:{{ $prog->is_active ? '#4ade80' : '#94a3b8' }}; border-color:{{ $prog->is_active ? '#4ade80' : '#94a3b8' }};"
                            title="{{ $prog->is_active ? 'Deshabilitar' : 'Habilitar' }}"
                            onclick="toggleProgram({{ $prog->id }}, {{ $prog->is_active ? 'true' : 'false' }}, {{ json_encode($prog->name) }})">
                            <i class="bi {{ $prog->is_active ? 'bi-toggle-on' : 'bi-toggle-off' }}" style="font-size:16px;"></i>
                        </button>

                        {{-- Editar --}}
                        <button class="btn-icon"
                            style="color:#818cf8; border-color:#818cf8;"
                            title="Editar programa"
                            onclick="openEditModal({{ $prog->id }}, {{ json_encode($prog->name) }}, {{ json_encode($prog->category) }}, {{ $prog->requires_detail ? 'true' : 'false' }}, {{ $prog->is_active ? 'true' : 'false' }}, {{ $prog->sort_order }})">
                            <i class="bi bi-pencil-fill"></i>
                        </button>

                        {{-- Eliminar --}}
                        <button class="btn-icon"
                            style="color:#f87171; border-color:#f87171;"
                            title="Eliminar programa"
                            onclick="deleteProgram({{ $prog->id }}, {{ json_encode($prog->name) }})">
                            <i class="bi bi-trash-fill"></i>
                        </button>
                    </div>
                </div>
                @endforeach

                {{-- Botón agregar programa dentro de la categoría --}}
                <button class="add-prog-btn" onclick="openAddProgramModal({{ json_encode($category ?: 'General') }})">
                    <i class="bi bi-plus-circle me-1"></i> Agregar programa a "{{ $category ?: 'General' }}"
                </button>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 text-center py-5">
        <i class="bi bi-folder-x fs-1 text-muted d-block mb-3"></i>
        <p class="text-muted">No hay categorías ni programas. <a href="#" onclick="openAddCategoryModal()">Crea la primera categoría</a>.</p>
    </div>
    @endforelse
</div>

{{-- Sin resultados en búsqueda --}}
<div class="no-results" id="noResults">
    <i class="bi bi-search"></i>
    <p class="text-muted">Sin resultados para "<strong id="searchTerm"></strong>"</p>
</div>


{{-- ══════════════════════════════════════════════════════════════
     FORMS OCULTOS PARA ACCIONES
═══════════════════════════════════════════════════════════════ --}}

{{-- Form toggle --}}
<form id="formToggle" method="POST" style="display:none;">
    @csrf
    @method('PUT')
    <input type="hidden" name="name"           id="toggleName">
    <input type="hidden" name="category"       id="toggleCategory">
    <input type="hidden" name="sort_order"     id="toggleSortOrder">
    <input type="hidden" name="requires_detail" id="toggleRequiresDetail">
    <input type="hidden" name="is_active"      id="toggleIsActive">
</form>

{{-- Form eliminar programa --}}
<form id="formDelete" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>

{{-- Form eliminar categoría (múltiple) --}}
<form id="formDeleteCategory" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
    <input type="hidden" name="delete_category" value="1">
    <input type="hidden" name="category_name" id="deleteCategoryName">
</form>


{{-- ══════════════════════════════════════════════════════════════
     MODAL: AGREGAR PROGRAMA
═══════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="addProgramModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('software-catalog.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-plus-circle-fill me-2" style="color:var(--primary);"></i>Agregar Programa
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold fs-13">Nombre <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="addProgName" class="form-control"
                            placeholder="Ej. Microsoft 365, AutoCAD..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold fs-13">Categoría <span class="text-danger">*</span></label>
                        <input type="text" name="category" id="addProgCategory" class="form-control"
                            placeholder="Escribe o selecciona una categoría" required list="categoryListAdd">
                        <datalist id="categoryListAdd">
                            @foreach($software->keys() as $cat)
                            <option value="{{ $cat }}">
                            @endforeach
                        </datalist>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold fs-13">Orden de aparición</label>
                        <input type="number" name="sort_order" class="form-control" value="0" min="0">
                        <div class="form-text" style="color:var(--text-muted);">Número menor aparece primero.</div>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="requires_detail" value="1" id="addReqDetail">
                        <label class="form-check-label fs-13" for="addReqDetail">
                            Requiere especificar versión / productos (ej. Autodesk, Adobe)
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- ══════════════════════════════════════════════════════════════
     MODAL: EDITAR PROGRAMA
═══════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="editProgramModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="editProgramForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-pencil-fill me-2" style="color:#818cf8;"></i>Editar Programa
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold fs-13">Nombre <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="editName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold fs-13">Categoría <span class="text-danger">*</span></label>
                        <input type="text" name="category" id="editCategory" class="form-control" required list="categoryListEdit">
                        <datalist id="categoryListEdit">
                            @foreach($software->keys() as $cat)
                            <option value="{{ $cat }}">
                            @endforeach
                        </datalist>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold fs-13">Orden de aparición</label>
                        <input type="number" name="sort_order" id="editSortOrder" class="form-control" min="0">
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="requires_detail" value="1" id="editReqDetail">
                        <label class="form-check-label fs-13" for="editReqDetail">
                            Requiere especificar versión / productos
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="editIsActive">
                        <label class="form-check-label fs-13" for="editIsActive">
                            Programa activo (visible al crear reportes)
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i>Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- ══════════════════════════════════════════════════════════════
     MODAL: NUEVA CATEGORÍA
═══════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <form action="{{ route('software-catalog.store') }}" method="POST">
                @csrf
                <input type="hidden" name="is_category_placeholder" value="1">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-folder-plus me-2" style="color:#4ade80;"></i>Nueva Categoría
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <label class="form-label fw-semibold fs-13">Nombre de la categoría <span class="text-danger">*</span></label>
                    <input type="text" name="category" id="newCategoryName" class="form-control"
                        placeholder="Ej. Seguridad, Diseño, CAD..." required>
                    <input type="hidden" name="name" value="_placeholder_">
                    <div class="form-text mt-2" style="color:var(--text-muted);">
                        Se creará la categoría con un programa temporal que podrás eliminar o reemplazar.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-folder-plus me-1"></i>Crear Categoría
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


@push('scripts')
<script>
// ─── Config SweetAlert2 theme ─────────────────────────────────────────────────
const swalTheme = {
    background: 'var(--surface)',
    color: 'var(--text)',
    confirmButtonColor: '#6366f1',
    cancelButtonColor: '#374151',
    customClass: { popup: 'shadow-lg' }
};

// ─── Abrir modal Agregar Programa (con categoría prellenada opcional) ──────────
function openAddProgramModal(category = null) {
    if (category) {
        document.getElementById('addProgCategory').value = category;
    } else {
        document.getElementById('addProgCategory').value = '';
    }
    document.getElementById('addProgName').value = '';
    document.getElementById('addReqDetail').checked = false;
    new bootstrap.Modal(document.getElementById('addProgramModal')).show();
}

// ─── Abrir modal Nueva Categoría ──────────────────────────────────────────────
function openAddCategoryModal() {
    document.getElementById('newCategoryName').value = '';
    new bootstrap.Modal(document.getElementById('addCategoryModal')).show();
}

// ─── Abrir modal Editar Programa ──────────────────────────────────────────────
function openEditModal(id, name, category, requiresDetail, isActive, sortOrder) {
    const form = document.getElementById('editProgramForm');
    form.action = '{{ url("software-catalog") }}/' + id;
    document.getElementById('editName').value        = name;
    document.getElementById('editCategory').value    = category;
    document.getElementById('editSortOrder').value   = sortOrder;
    document.getElementById('editReqDetail').checked = requiresDetail;
    document.getElementById('editIsActive').checked  = isActive;
    new bootstrap.Modal(document.getElementById('editProgramModal')).show();
}

// ─── Toggle Habilitar / Deshabilitar ─────────────────────────────────────────
function toggleProgram(id, isCurrentlyActive, name) {
    const action = isCurrentlyActive ? 'deshabilitar' : 'habilitar';
    const icon   = isCurrentlyActive ? '⛔' : '✅';

    Swal.fire({
        ...swalTheme,
        title: `${icon} ${isCurrentlyActive ? 'Deshabilitar' : 'Habilitar'} programa`,
        html: `¿Deseas <strong>${action}</strong> el programa <strong>${name}</strong>?`,
        icon: isCurrentlyActive ? 'warning' : 'question',
        showCancelButton: true,
        confirmButtonText: isCurrentlyActive ? 'Sí, deshabilitar' : 'Sí, habilitar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: isCurrentlyActive ? '#ef4444' : '#22c55e',
    }).then(result => {
        if (!result.isConfirmed) return;

        // Buscar el elemento del programa para leer sus datos
        const row = document.getElementById('prog-' + id);
        const nameVal     = row.querySelector('[data-name]') ? row.dataset.name : name;

        // Llenar el form oculto
        const form = document.getElementById('formToggle');
        form.action = '{{ url("software-catalog") }}/' + id;

        // Necesitamos los datos del programa del DOM
        const buttons = row.querySelectorAll('button[onclick*="toggleProgram"]');

        // Usar datos que ya tenemos
        document.getElementById('toggleName').value           = name;
        document.getElementById('toggleSortOrder').value      = 0;
        document.getElementById('toggleRequiresDetail').value = '';
        document.getElementById('toggleIsActive').value       = isCurrentlyActive ? '' : '1';

        // Obtener categoría desde la tarjeta padre
        const catCol = row.closest('.category-col');
        document.getElementById('toggleCategory').value = catCol ? catCol.dataset.category : '';

        // Manejar requires_detail
        const reqBadge = row.querySelector('.badge[style*="fbbf24"]');
        if (reqBadge) document.getElementById('toggleRequiresDetail').value = '1';

        form.submit();
    });
}

// ─── Eliminar Programa ────────────────────────────────────────────────────────
function deleteProgram(id, name) {
    Swal.fire({
        ...swalTheme,
        title: '🗑️ Eliminar programa',
        html: `¿Estás seguro de eliminar <strong>${name}</strong>?<br><small style="color:var(--text-muted);">Esta acción no se puede deshacer.</small>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#ef4444',
    }).then(result => {
        if (!result.isConfirmed) return;
        const form = document.getElementById('formDelete');
        form.action = '{{ url("software-catalog") }}/' + id;
        form.submit();
    });
}

// ─── Eliminar Categoría completa ──────────────────────────────────────────────
function deleteCategory(categoryName) {
    Swal.fire({
        ...swalTheme,
        title: '📁 Eliminar categoría',
        html: `¿Eliminar la categoría <strong>${categoryName}</strong> y <strong>todos sus programas</strong>?<br>
               <small style="color:#f87171;">⚠️ Esta acción eliminará todos los programas de esta categoría.</small>`,
        icon: 'error',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar todo',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#ef4444',
        input: 'checkbox',
        inputValue: 0,
        inputPlaceholder: 'Entiendo que esta acción es irreversible',
        preConfirm: (val) => {
            if (!val) {
                Swal.showValidationMessage('Debes confirmar que entiendes la acción.');
            }
        }
    }).then(result => {
        if (!result.isConfirmed) return;
        document.getElementById('deleteCategoryName').value = categoryName;
        document.getElementById('formDeleteCategory').submit();
    });
}

// ─── Búsqueda en tiempo real ──────────────────────────────────────────────────
document.getElementById('searchInput').addEventListener('input', function () {
    const q       = this.value.trim().toLowerCase();
    const rows    = document.querySelectorAll('.prog-item');
    const cols    = document.querySelectorAll('.category-col');
    let   visible = 0;

    document.getElementById('searchTerm').textContent = q;

    rows.forEach(row => {
        const nameMatch = (row.dataset.name || '').includes(q);
        const catMatch  = (row.closest('.category-col')?.dataset.category || '').includes(q);
        const show = !q || nameMatch || catMatch;
        row.style.display = show ? '' : 'none';
        if (show) visible++;
    });

    cols.forEach(col => {
        const hasVisible = [...col.querySelectorAll('.prog-item')].some(r => r.style.display !== 'none');
        col.style.display = hasVisible ? '' : 'none';
    });

    document.getElementById('totalCount').textContent = visible;
    document.getElementById('noResults').style.display = (visible === 0 && q) ? 'block' : 'none';
});

// ─── SweetAlert para mensajes flash ──────────────────────────────────────────
@if(session('success'))
Swal.fire({ ...swalTheme, icon: 'success', title: '¡Listo!', text: '{{ session("success") }}', timer: 2800, showConfirmButton: false, toast: true, position: 'top-end' });
@endif
@if(session('error'))
Swal.fire({ ...swalTheme, icon: 'error', title: 'Error', text: '{{ session("error") }}', timer: 3500, showConfirmButton: false, toast: true, position: 'top-end' });
@endif
</script>
@endpush

@endsection
