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
/* ── Layout ─────────────────────────────────────────── */
.cat-card{border-radius:16px;border:1px solid var(--border);background:var(--surface);transition:box-shadow .25s,transform .25s;}
.cat-card:hover{box-shadow:0 8px 32px rgba(0,0,0,.22);transform:translateY(-2px);}
.cat-header{padding:14px 18px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;border-radius:16px 16px 0 0;}

/* ── Fila de programa ──────────────────────────────── */
.prog-item{display:flex;align-items:center;justify-content:space-between;padding:10px 14px;border-radius:10px;border:1px solid var(--border);background:rgba(255,255,255,.025);transition:background .15s,opacity .2s,box-shadow .15s;}
.prog-item:hover{background:rgba(255,255,255,.055);box-shadow:0 2px 10px rgba(0,0,0,.12);}
.prog-item.inactive{opacity:.42;}
.prog-actions{display:flex;gap:5px;align-items:center;flex-shrink:0;margin-left:10px;}

/* ── Botones icono ─────────────────────────────────── */
.btn-ic{width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center;border-radius:9px;border:1.5px solid;font-size:13px;cursor:pointer;transition:all .15s;padding:0;background:transparent;}
.btn-ic:hover{transform:translateY(-1px);filter:brightness(1.15);}
.btn-ic-toggle-on {color:#4ade80;border-color:#4ade80;}
.btn-ic-toggle-off{color:#6b7280;border-color:#6b7280;}
.btn-ic-edit      {color:#818cf8;border-color:#818cf8;}
.btn-ic-delete    {color:#f87171;border-color:#f87171;}
.btn-ic-folder-del{color:#f87171;border-color:transparent;}

/* ── Barra búsqueda ────────────────────────────────── */
.search-wrap{flex:1;min-width:220px;display:flex;align-items:center;gap:10px;background:var(--surface);border:1px solid var(--border);border-radius:12px;padding:10px 16px;}
.search-wrap input{background:transparent;border:none;outline:none;color:var(--text);flex:1;font-size:14px;}

/* ── Estadísticas ──────────────────────────────────── */
.stat-chip{display:inline-flex;align-items:center;gap:6px;padding:5px 13px;border-radius:20px;font-size:12px;font-weight:700;letter-spacing:.3px;}

/* ── Botón agregar en categoría ────────────────────── */
.add-inside-btn{width:100%;border:1.5px dashed var(--border);border-radius:10px;padding:9px;color:var(--text-muted);background:transparent;font-size:12px;cursor:pointer;transition:all .15s;margin-top:8px;letter-spacing:.3px;}
.add-inside-btn:hover{border-color:var(--primary);color:var(--primary);background:rgba(99,102,241,.07);}

/* ── Modals ────────────────────────────────────────── */
.modal-content{background:var(--surface);border:1px solid var(--border);border-radius:16px;color:var(--text);}
.modal-header{border-bottom:1px solid var(--border);}
.modal-footer{border-top:1px solid var(--border);}
.form-control,.form-select{background:var(--surface)!important;color:var(--text)!important;border-color:var(--border)!important;}
.form-control:focus,.form-select:focus{border-color:var(--primary)!important;box-shadow:0 0 0 3px rgba(99,102,241,.18)!important;}
.cat-icon-wrap{width:36px;height:36px;border-radius:10px;display:inline-flex;align-items:center;justify-content:center;flex-shrink:0;}
.prog-name{font-size:13px;font-weight:600;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:200px;}

/* ── Badge pills ───────────────────────────────────── */
.badge-detallable{background:rgba(234,179,8,.18);color:#fbbf24;font-size:10px;padding:2px 7px;border-radius:20px;font-weight:600;}
.badge-inactivo  {background:rgba(239,68,68,.15) ;color:#f87171;font-size:10px;padding:2px 7px;border-radius:20px;font-weight:600;}
</style>
@endpush

@section('content')

{{-- ── HEADER ─────────────────────────────────────────────────── --}}
<div class="row page-titles mx-0 mb-4 align-items-center">
    <div class="col-sm-6 p-md-0">
        <h4 class="mb-1" style="color:var(--text);font-weight:700;">
            <i class="bi bi-grid-3x3-gap-fill me-2" style="color:var(--primary);"></i>Catálogo de Software
        </h4>
        <p class="mb-0 text-muted fs-13">Gestiona programas y categorías disponibles en los reportes de formateo.</p>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex gap-2">
        <button class="btn btn-outline-secondary d-flex align-items-center gap-2 fw-semibold" onclick="openCategoryModal()">
            <i class="bi bi-folder-plus"></i><span>Nueva Categoría</span>
        </button>
        <button class="btn btn-primary d-flex align-items-center gap-2 fw-semibold" onclick="openAddModal(null)">
            <i class="bi bi-plus-circle-fill"></i><span>Agregar Programa</span>
        </button>
    </div>
</div>

{{-- ── STATS + BÚSQUEDA ─────────────────────────────────────────── --}}
<div class="d-flex flex-wrap gap-3 align-items-center mb-4">
    <div class="search-wrap">
        <i class="bi bi-search" style="color:var(--text-muted);"></i>
        <input type="text" id="searchInput" placeholder="Buscar por nombre o categoría…" autocomplete="off">
        <span id="searchClearBtn" style="cursor:pointer;color:var(--text-muted);display:none;" onclick="clearSearch()">
            <i class="bi bi-x-circle-fill"></i>
        </span>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <span class="stat-chip" style="background:rgba(99,102,241,.13);color:#818cf8;">
            <i class="bi bi-app-indicator"></i><span id="totalCount">{{ $software->flatten()->count() }}</span> programas
        </span>
        <span class="stat-chip" style="background:rgba(34,197,94,.13);color:#4ade80;">
            <i class="bi bi-check-circle-fill"></i><span id="activeCount">{{ $software->flatten()->where('is_active',true)->count() }}</span> activos
        </span>
        <span class="stat-chip" style="background:rgba(148,163,184,.1);color:#94a3b8;">
            <i class="bi bi-folder-fill"></i>{{ $software->count() }} categorías
        </span>
    </div>
</div>

{{-- ── GRID ─────────────────────────────────────────────────────── --}}
<div class="row g-4" id="catalogGrid">
    @forelse($software as $category => $items)
    <div class="col-md-6 col-xl-4 category-col" data-category="{{ strtolower($category) }}">
        <div class="cat-card h-100">

            {{-- Header categoría --}}
            <div class="cat-header">
                <div class="d-flex align-items-center gap-2">
                    <span class="cat-icon-wrap" style="background:rgba(99,102,241,.14);">
                        <i class="bi bi-folder-fill" style="color:var(--primary);font-size:16px;"></i>
                    </span>
                    <div>
                        <div class="fw-bold fs-14" style="color:var(--text);line-height:1.2;">{{ $category ?: 'General' }}</div>
                        <div class="fs-11" style="color:var(--text-muted);">{{ $items->count() }} programa(s)</div>
                    </div>
                </div>
                <button class="btn-ic btn-ic-folder-del" title="Eliminar categoría completa"
                    onclick="delCategory({{ json_encode($category ?: 'General') }}, {{ $items->count() }})">
                    <i class="bi bi-folder-x fs-5"></i>
                </button>
            </div>

            {{-- Lista programas --}}
            <div class="p-3 d-flex flex-column gap-2">
                @foreach($items as $prog)
                <div class="prog-item {{ !$prog->is_active ? 'inactive' : '' }}"
                     id="prog-{{ $prog->id }}"
                     data-name="{{ strtolower($prog->name) }}">

                    <div style="min-width:0;flex:1;">
                        <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                            <span class="prog-name">{{ $prog->name }}</span>
                            @if($prog->requires_detail)
                                <span class="badge-detallable"><i class="bi bi-tag-fill me-1"></i>Detallable</span>
                            @endif
                            @if(!$prog->is_active)
                                <span class="badge-inactivo"><i class="bi bi-slash-circle me-1"></i>Inactivo</span>
                            @endif
                        </div>
                        @if($prog->sort_order > 0)
                        <span class="fs-11" style="color:var(--text-muted);">
                            <i class="bi bi-sort-numeric-down me-1"></i>Orden {{ $prog->sort_order }}
                        </span>
                        @endif
                    </div>

                    <div class="prog-actions">
                        {{-- Toggle --}}
                        <button class="btn-ic {{ $prog->is_active ? 'btn-ic-toggle-on' : 'btn-ic-toggle-off' }}"
                            title="{{ $prog->is_active ? 'Deshabilitar' : 'Habilitar' }}"
                            onclick="toggleProg({{ $prog->id }},{{ $prog->is_active?'true':'false' }},{{ json_encode($prog->name) }},{{ json_encode($prog->category) }},{{ $prog->sort_order }},{{ $prog->requires_detail?'true':'false' }})">
                            <i class="bi {{ $prog->is_active ? 'bi-toggle-on' : 'bi-toggle-off' }}" style="font-size:17px;"></i>
                        </button>
                        {{-- Editar --}}
                        <button class="btn-ic btn-ic-edit" title="Editar"
                            onclick="openEditModal({{ $prog->id }},{{ json_encode($prog->name) }},{{ json_encode($prog->category) }},{{ $prog->requires_detail?'true':'false' }},{{ $prog->is_active?'true':'false' }},{{ $prog->sort_order }})">
                            <i class="bi bi-pencil-fill"></i>
                        </button>
                        {{-- Eliminar --}}
                        <button class="btn-ic btn-ic-delete" title="Eliminar"
                            onclick="delProg({{ $prog->id }},{{ json_encode($prog->name) }})">
                            <i class="bi bi-trash-fill"></i>
                        </button>
                    </div>
                </div>
                @endforeach

                {{-- Agregar dentro de categoría --}}
                <button class="add-inside-btn" onclick="openAddModal({{ json_encode($category ?: 'General') }})">
                    <i class="bi bi-plus-circle me-1"></i>
                    Agregar programa en "{{ $category ?: 'General' }}"
                </button>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 text-center py-5">
        <i class="bi bi-folder-x fs-1 d-block mb-3" style="color:var(--text-muted);"></i>
        <p style="color:var(--text-muted);">No hay categorías aún.
            <a href="#" onclick="openCategoryModal()" style="color:var(--primary);">Crea la primera.</a>
        </p>
    </div>
    @endforelse
</div>

<div id="noResults" style="display:none;text-align:center;padding:60px 20px;">
    <i class="bi bi-search fs-1 d-block mb-3" style="color:var(--text-muted);opacity:.4;"></i>
    <p style="color:var(--text-muted);">Sin resultados para "<strong id="searchTerm"></strong>"</p>
    <button class="btn btn-sm btn-outline-secondary mt-2" onclick="clearSearch()">Limpiar búsqueda</button>
</div>


{{-- ════ FORMS OCULTOS ════════════════════════════════════════════ --}}
<form id="fToggle" method="POST" style="display:none;">
    @csrf @method('PUT')
    <input type="hidden" name="name"             id="tName">
    <input type="hidden" name="category"         id="tCategory">
    <input type="hidden" name="sort_order"       id="tSort">
    <input type="hidden" name="requires_detail"  id="tReqDetail">
    <input type="hidden" name="is_active"        id="tIsActive">
</form>

<form id="fDelete" method="POST" style="display:none;">
    @csrf @method('DELETE')
</form>

<form id="fDeleteCat" method="POST" style="display:none;">
    @csrf @method('DELETE')
    <input type="hidden" name="delete_category" value="1">
    <input type="hidden" name="category_name"   id="dcName">
</form>


{{-- ════ MODAL: AGREGAR PROGRAMA ══════════════════════════════════ --}}
<div class="modal fade" id="modalAdd" tabindex="-1" aria-hidden="true">
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
                        <input type="text" name="name" id="addName" class="form-control" required
                            placeholder="Ej. Microsoft 365, AutoCAD…">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold fs-13">Categoría <span class="text-danger">*</span></label>
                        <input type="text" name="category" id="addCategory" class="form-control" required list="catListAdd"
                            placeholder="Escribe o selecciona…">
                        <datalist id="catListAdd">
                            @foreach($software->keys() as $cat)
                            <option value="{{ $cat }}">
                            @endforeach
                        </datalist>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold fs-13">Orden</label>
                        <input type="number" name="sort_order" class="form-control" value="0" min="0">
                        <div class="form-text" style="color:var(--text-muted);">Número menor aparece primero en la categoría.</div>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="requires_detail" value="1" id="addReqDetail">
                        <label class="form-check-label fs-13" for="addReqDetail">
                            Requiere especificar versión / extensiones
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-semibold">
                        <i class="bi bi-plus-circle me-1"></i>Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- ════ MODAL: EDITAR PROGRAMA ═══════════════════════════════════ --}}
<div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="fEdit" method="POST">
                @csrf @method('PUT')
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
                        <input type="text" name="category" id="editCategory" class="form-control" required list="catListEdit">
                        <datalist id="catListEdit">
                            @foreach($software->keys() as $cat)
                            <option value="{{ $cat }}">
                            @endforeach
                        </datalist>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold fs-13">Orden</label>
                        <input type="number" name="sort_order" id="editSort" class="form-control" min="0">
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="requires_detail" value="1" id="editReqDetail">
                        <label class="form-check-label fs-13" for="editReqDetail">Requiere especificar versión / extensiones</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="editIsActive">
                        <label class="form-check-label fs-13" for="editIsActive">Programa activo (visible en reportes)</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-semibold">
                        <i class="bi bi-save me-1"></i>Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- ════ MODAL: NUEVA CATEGORÍA ═══════════════════════════════════ --}}
<div class="modal fade" id="modalCat" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <form action="{{ route('software-catalog.store') }}" method="POST">
                @csrf
                <input type="hidden" name="name" value="_placeholder_">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-folder-plus me-2" style="color:#4ade80;"></i>Nueva Categoría
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <label class="form-label fw-semibold fs-13">Nombre <span class="text-danger">*</span></label>
                    <input type="text" name="category" id="newCatName" class="form-control"
                        placeholder="Ej. Seguridad, Diseño, CAD…" required>
                    <div class="form-text mt-2" style="color:var(--text-muted);">
                        Se creará con un programa temporal que podrás reemplazar.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success fw-semibold">
                        <i class="bi bi-folder-plus me-1"></i>Crear
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


@push('scripts')
<script>
// ─── SweetAlert2 theme ────────────────────────────────────────
const swCfg = {
    background: 'var(--surface)',
    color: 'var(--text)',
    confirmButtonColor: '#6366f1',
    cancelButtonColor: '#374151',
};

// ─── Modals Bootstrap ────────────────────────────────────────
const bsModal = id => new bootstrap.Modal(document.getElementById(id));

function openAddModal(cat) {
    document.getElementById('addName').value      = '';
    document.getElementById('addCategory').value  = cat || '';
    document.getElementById('addReqDetail').checked = false;
    bsModal('modalAdd').show();
}
function openEditModal(id, name, cat, reqDet, isActive, sort) {
    document.getElementById('fEdit').action      = '{{ url("software-catalog") }}/' + id;
    document.getElementById('editName').value    = name;
    document.getElementById('editCategory').value= cat;
    document.getElementById('editSort').value    = sort;
    document.getElementById('editReqDetail').checked = reqDet;
    document.getElementById('editIsActive').checked  = isActive;
    bsModal('modalEdit').show();
}
function openCategoryModal() {
    document.getElementById('newCatName').value = '';
    bsModal('modalCat').show();
}

// ─── Toggle habilitar/deshabilitar ───────────────────────────
function toggleProg(id, isActive, name, cat, sort, reqDet) {
    const action = isActive ? 'deshabilitar' : 'habilitar';
    const icon   = isActive ? '⛔' : '✅';
    Swal.fire({
        ...swCfg,
        title: `${icon} ${isActive ? 'Deshabilitar' : 'Habilitar'}`,
        html: `¿Deseas <b>${action}</b> el programa <b>${name}</b>?`,
        icon: isActive ? 'warning' : 'question',
        showCancelButton: true,
        confirmButtonText: isActive ? 'Deshabilitar' : 'Habilitar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: isActive ? '#ef4444' : '#22c55e',
    }).then(r => {
        if (!r.isConfirmed) return;
        const f = document.getElementById('fToggle');
        f.action = '{{ url("software-catalog") }}/' + id;
        document.getElementById('tName').value      = name;
        document.getElementById('tCategory').value  = cat;
        document.getElementById('tSort').value      = sort;
        document.getElementById('tReqDetail').value = reqDet ? '1' : '';
        document.getElementById('tIsActive').value  = isActive ? '' : '1';
        f.submit();
    });
}

// ─── Eliminar programa ───────────────────────────────────────
function delProg(id, name) {
    Swal.fire({
        ...swCfg,
        title: '🗑️ Eliminar programa',
        html: `¿Eliminar <b>${name}</b>?<br><small style="color:var(--text-muted);">Esta acción no se puede deshacer.</small>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Eliminar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#ef4444',
    }).then(r => {
        if (!r.isConfirmed) return;
        const f = document.getElementById('fDelete');
        f.action = '{{ url("software-catalog") }}/' + id;
        f.submit();
    });
}

// ─── Eliminar categoría completa ─────────────────────────────
function delCategory(cat, count) {
    Swal.fire({
        ...swCfg,
        title: '📁 Eliminar categoría',
        html: `¿Eliminar la categoría <b>${cat}</b> y sus <b>${count} programa(s)</b>?<br>
               <span style="color:#f87171;font-size:13px;">⚠️ Esta acción es irreversible.</span>`,
        icon: 'error',
        showCancelButton: true,
        confirmButtonText: 'Eliminar todo',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#ef4444',
        input: 'checkbox',
        inputValue: 0,
        inputPlaceholder: 'Entiendo que es irreversible',
        preConfirm: v => { if(!v) Swal.showValidationMessage('Debes marcar la confirmación'); }
    }).then(r => {
        if (!r.isConfirmed) return;
        document.getElementById('dcName').value = cat;
        document.getElementById('fDeleteCat').action = '{{ url("software-catalog") }}/0';
        document.getElementById('fDeleteCat').submit();
    });
}

// ─── Búsqueda en tiempo real ─────────────────────────────────
const searchInput = document.getElementById('searchInput');
searchInput.addEventListener('input', function(){
    const q    = this.value.trim().toLowerCase();
    const rows = document.querySelectorAll('.prog-item');
    const cols = document.querySelectorAll('.category-col');
    let vis = 0;

    document.getElementById('searchTerm').textContent = q;
    document.getElementById('searchClearBtn').style.display = q ? 'inline' : 'none';

    rows.forEach(r => {
        const ok = !q || r.dataset.name.includes(q) || (r.closest('.category-col')?.dataset.category||'').includes(q);
        r.style.display = ok ? '' : 'none';
        if(ok) vis++;
    });
    cols.forEach(c => {
        c.style.display = [...c.querySelectorAll('.prog-item')].some(r=>r.style.display!=='none') ? '' : 'none';
    });

    document.getElementById('totalCount').textContent = vis;
    document.getElementById('noResults').style.display = (!vis && q) ? 'block' : 'none';
    document.getElementById('catalogGrid').style.display = (!vis && q) ? 'none' : '';
});

function clearSearch(){
    searchInput.value='';
    searchInput.dispatchEvent(new Event('input'));
}

// ─── Notificaciones flash con SweetAlert toast ───────────────
@if(session('success'))
Swal.fire({...swCfg, icon:'success', title:'¡Listo!', text:@json(session('success')),
    timer:2800, showConfirmButton:false, toast:true, position:'top-end'});
@endif
@if(session('error'))
Swal.fire({...swCfg, icon:'error', title:'Error', text:@json(session('error')),
    timer:3500, showConfirmButton:false, toast:true, position:'top-end'});
@endif
</script>
@endpush

@endsection