@extends('layouts.app')

@section('page-title', 'Catálogo de Programas')

@section('breadcrumb')
    <i class="bi bi-chevron-right mx-1"></i>
    <a href="{{ route('reports.index') }}" style="color:var(--text-muted);text-decoration:none;">Reportes</a>
    <i class="bi bi-chevron-right mx-1"></i>
    <span>Catálogo de Programas</span>
@endsection

@section('content')

<style>
.cat-card{border-radius:16px;border:1px solid var(--border);background:var(--surface);transition:box-shadow .25s,transform .2s;overflow:hidden;}
.cat-card:hover{box-shadow:0 8px 32px rgba(0,0,0,.22);transform:translateY(-2px);}
.cat-header{padding:14px 18px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;background:rgba(255,255,255,.02);}
.prog-item{display:flex;align-items:center;justify-content:space-between;padding:11px 14px;border-radius:10px;border:1px solid var(--border);background:rgba(255,255,255,.02);transition:background .15s,box-shadow .15s;gap:10px;}
.prog-item:hover{background:rgba(255,255,255,.06);box-shadow:0 2px 8px rgba(0,0,0,.1);}
.prog-item.is-inactive{opacity:.45;}
.prog-actions{display:flex;gap:5px;align-items:center;flex-shrink:0;}
.ic-btn{width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center;border-radius:8px;font-size:14px;cursor:pointer;transition:all .15s;padding:0;background:transparent;border:1.5px solid;}
.ic-btn:hover{transform:translateY(-1px);filter:brightness(1.2);}
.ic-toggle-on {color:#4ade80;border-color:#4ade80;}
.ic-toggle-off{color:#6b7280;border-color:#6b7280;}
.ic-edit{color:#818cf8;border-color:#818cf8;}
.ic-del{color:#f87171;border-color:#f87171;}
.ic-cat-del{color:#f87171;border-color:transparent;background:rgba(248,113,113,.08);}
.search-wrap{flex:1;min-width:220px;display:flex;align-items:center;gap:10px;background:var(--surface);border:1px solid var(--border);border-radius:12px;padding:10px 16px;}
.search-wrap input{background:transparent;border:none;outline:none;color:var(--text);flex:1;font-size:14px;}
.chip{display:inline-flex;align-items:center;gap:5px;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:700;}
.add-btn-inside{width:100%;border:1.5px dashed var(--border);border-radius:10px;padding:9px;color:var(--text-muted);background:transparent;font-size:12px;cursor:pointer;transition:all .15s;margin-top:6px;letter-spacing:.2px;}
.add-btn-inside:hover{border-color:var(--primary);color:var(--primary);background:rgba(99,102,241,.07);}
.modal-content{background:var(--surface);border:1px solid var(--border);border-radius:16px;color:var(--text);}
.modal-header,.modal-footer{border-color:var(--border)!important;}
.form-control,.form-select{background:var(--surface)!important;color:var(--text)!important;border-color:var(--border)!important;}
.form-control:focus,.form-select:focus{border-color:var(--primary)!important;box-shadow:0 0 0 3px rgba(99,102,241,.18)!important;}
.prog-name-text{font-size:13px;font-weight:600;color:var(--text);}
.b-det{background:rgba(234,179,8,.18);color:#fbbf24;font-size:10px;padding:2px 7px;border-radius:20px;font-weight:700;}
.b-off{background:rgba(239,68,68,.15);color:#f87171;font-size:10px;padding:2px 7px;border-radius:20px;font-weight:700;}
.cat-icon{width:36px;height:36px;border-radius:10px;display:inline-flex;align-items:center;justify-content:center;flex-shrink:0;background:rgba(99,102,241,.14);}
</style>

{{-- ── HEADER ──────────────────────────────────────────────────── --}}
<div class="row page-titles mx-0 mb-4 align-items-center">
    <div class="col-sm-6 p-md-0">
        <h4 class="mb-1 fw-bold" style="color:var(--text);">
            <i class="bi bi-grid-3x3-gap-fill me-2" style="color:var(--primary);"></i>Catálogo de Software
        </h4>
        <p class="mb-0 text-muted" style="font-size:13px;">Gestiona programas y categorías disponibles en los reportes de formateo.</p>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex gap-2">
        <button class="btn btn-outline-secondary d-flex align-items-center gap-2 fw-semibold"
            onclick="document.getElementById('newCatName').value=''; new bootstrap.Modal(document.getElementById('modalCat')).show()">
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
        <input type="text" id="searchInput" placeholder="Buscar programa o categoría…" autocomplete="off">
        <span id="clearBtn" style="cursor:pointer;color:var(--text-muted);display:none;" onclick="clearSrch()">
            <i class="bi bi-x-circle-fill"></i>
        </span>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <span class="chip" style="background:rgba(99,102,241,.13);color:#818cf8;">
            <i class="bi bi-app-indicator"></i><span id="totalCount">{{ $software->flatten()->count() }}</span> programas
        </span>
        <span class="chip" style="background:rgba(34,197,94,.13);color:#4ade80;">
            <i class="bi bi-check-circle-fill"></i>{{ $software->flatten()->where('is_active',true)->count() }} activos
        </span>
        <span class="chip" style="background:rgba(148,163,184,.1);color:#94a3b8;">
            <i class="bi bi-folder-fill"></i>{{ $software->count() }} categorías
        </span>
    </div>
</div>

{{-- ── GRID ─────────────────────────────────────────────────────── --}}
<div class="row g-4" id="catalogGrid">
    @forelse($software as $category => $items)
    <div class="col-md-6 col-xl-4 cat-col" data-category="{{ strtolower($category) }}">
        <div class="cat-card h-100">
            <div class="cat-header">
                <div class="d-flex align-items-center gap-2">
                    <span class="cat-icon">
                        <i class="bi bi-folder-fill" style="color:var(--primary);font-size:16px;"></i>
                    </span>
                    <div>
                        <div class="fw-bold" style="font-size:14px;color:var(--text);line-height:1.2;">{{ $category ?: 'General' }}</div>
                        <div style="font-size:11px;color:var(--text-muted);">{{ $items->count() }} programa(s)</div>
                    </div>
                </div>
                <button class="ic-btn ic-cat-del" title="Eliminar categoría"
                    onclick="delCat({{ json_encode($category ?: 'General') }}, {{ $items->count() }})">
                    <i class="bi bi-folder-x fs-5"></i>
                </button>
            </div>

            <div class="p-3 d-flex flex-column gap-2">
                @foreach($items as $prog)
                <div class="prog-item {{ !$prog->is_active ? 'is-inactive' : '' }}"
                     id="pi-{{ $prog->id }}"
                     data-name="{{ strtolower($prog->name) }}">
                    <div style="min-width:0;flex:1;">
                        <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                            <span class="prog-name-text">{{ $prog->name }}</span>
                            @if($prog->requires_detail)<span class="b-det"><i class="bi bi-tag-fill me-1"></i>Detallable</span>@endif
                            @if(!$prog->is_active)<span class="b-off"><i class="bi bi-slash-circle me-1"></i>Inactivo</span>@endif
                        </div>
                        @if($prog->sort_order > 0)
                        <span style="font-size:11px;color:var(--text-muted);"><i class="bi bi-sort-numeric-down me-1"></i>Orden {{ $prog->sort_order }}</span>
                        @endif
                    </div>
                    <div class="prog-actions">
                        <button class="ic-btn {{ $prog->is_active ? 'ic-toggle-on' : 'ic-toggle-off' }}"
                            title="{{ $prog->is_active ? 'Deshabilitar' : 'Habilitar' }}"
                            onclick="toggleProg({{ $prog->id }},{{ $prog->is_active?'true':'false' }},{{ json_encode($prog->name) }},{{ json_encode($prog->category) }},{{ $prog->sort_order }},{{ $prog->requires_detail?'true':'false' }})">
                            <i class="bi {{ $prog->is_active ? 'bi-toggle-on' : 'bi-toggle-off' }}" style="font-size:17px;"></i>
                        </button>
                        <button class="ic-btn ic-edit" title="Editar"
                            onclick="openEditModal({{ $prog->id }},{{ json_encode($prog->name) }},{{ json_encode($prog->category) }},{{ $prog->requires_detail?'true':'false' }},{{ $prog->is_active?'true':'false' }},{{ $prog->sort_order }})">
                            <i class="bi bi-pencil-fill"></i>
                        </button>
                        <button class="ic-btn ic-del" title="Eliminar"
                            onclick="delProg({{ $prog->id }},{{ json_encode($prog->name) }})">
                            <i class="bi bi-trash-fill"></i>
                        </button>
                    </div>
                </div>
                @endforeach

                <button class="add-btn-inside" onclick="openAddModal({{ json_encode($category ?: 'General') }})">
                    <i class="bi bi-plus-circle me-1"></i>Agregar en "{{ $category ?: 'General' }}"
                </button>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 text-center py-5">
        <i class="bi bi-folder-x d-block mb-3" style="font-size:48px;color:var(--text-muted);opacity:.4;"></i>
        <p style="color:var(--text-muted);">No hay categorías.
            <a href="#" style="color:var(--primary);"
               onclick="document.getElementById('newCatName').value=''; new bootstrap.Modal(document.getElementById('modalCat')).show(); return false;">
               Crea la primera.
            </a>
        </p>
    </div>
    @endforelse
</div>

<div id="noResults" style="display:none;text-align:center;padding:60px 20px;">
    <i class="bi bi-search d-block mb-3" style="font-size:48px;color:var(--text-muted);opacity:.35;"></i>
    <p style="color:var(--text-muted);">Sin resultados para "<strong id="searchTerm"></strong>"</p>
    <button class="btn btn-sm btn-outline-secondary mt-1" onclick="clearSrch()">Limpiar</button>
</div>

{{-- ── FORMS OCULTOS ─────────────────────────────────────────────── --}}
<form id="fToggle" method="POST" style="display:none;">
    @csrf @method('PUT')
    <input type="hidden" name="name"            id="tN">
    <input type="hidden" name="category"        id="tC">
    <input type="hidden" name="sort_order"      id="tS">
    <input type="hidden" name="requires_detail" id="tR">
    <input type="hidden" name="is_active"       id="tA">
</form>
<form id="fDel" method="POST" style="display:none;">@csrf @method('DELETE')</form>
<form id="fDelCat" method="POST" style="display:none;">
    @csrf @method('DELETE')
    <input type="hidden" name="delete_category" value="1">
    <input type="hidden" name="category_name"   id="dcN">
</form>

{{-- ── MODAL AGREGAR ─────────────────────────────────────────────── --}}
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
                        <label class="form-label fw-semibold" style="font-size:13px;">Nombre <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="aN" class="form-control" required placeholder="Ej. Microsoft 365, AutoCAD…">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">Categoría <span class="text-danger">*</span></label>
                        <input type="text" name="category" id="aC" class="form-control" required list="catLA" placeholder="Escribe o selecciona…">
                        <datalist id="catLA">@foreach($software->keys() as $k)<option value="{{ $k }}">@endforeach</datalist>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">Orden</label>
                        <input type="number" name="sort_order" class="form-control" value="0" min="0">
                        <small style="color:var(--text-muted);">Número menor aparece primero.</small>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="requires_detail" value="1" id="aR">
                        <label class="form-check-label" style="font-size:13px;" for="aR">Requiere especificar versión / extensiones</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-semibold"><i class="bi bi-plus-circle me-1"></i>Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ── MODAL EDITAR ──────────────────────────────────────────────── --}}
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
                        <label class="form-label fw-semibold" style="font-size:13px;">Nombre <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="eN" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">Categoría <span class="text-danger">*</span></label>
                        <input type="text" name="category" id="eC" class="form-control" required list="catLE">
                        <datalist id="catLE">@foreach($software->keys() as $k)<option value="{{ $k }}">@endforeach</datalist>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">Orden</label>
                        <input type="number" name="sort_order" id="eS" class="form-control" min="0">
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="requires_detail" value="1" id="eR">
                        <label class="form-check-label" style="font-size:13px;" for="eR">Requiere especificar versión / extensiones</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="eA">
                        <label class="form-check-label" style="font-size:13px;" for="eA">Programa activo (visible en reportes)</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-semibold"><i class="bi bi-save me-1"></i>Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ── MODAL NUEVA CATEGORÍA ─────────────────────────────────────── --}}
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
                    <label class="form-label fw-semibold" style="font-size:13px;">Nombre <span class="text-danger">*</span></label>
                    <input type="text" name="category" id="newCatName" class="form-control" placeholder="Ej. Seguridad, Diseño…" required>
                    <small class="mt-2 d-block" style="color:var(--text-muted);">Se creará con un programa temporal reemplazable.</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success fw-semibold"><i class="bi bi-folder-plus me-1"></i>Crear</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
(function(){
const swCfg = {
    background:'var(--surface)', color:'var(--text)',
    confirmButtonColor:'#6366f1', cancelButtonColor:'#374151'
};

// ── Abrir modal agregar ──────────────────────────────────────
window.openAddModal = function(cat) {
    document.getElementById('aN').value = '';
    document.getElementById('aC').value = cat || '';
    document.getElementById('aR').checked = false;
    new bootstrap.Modal(document.getElementById('modalAdd')).show();
};

// ── Abrir modal editar ───────────────────────────────────────
window.openEditModal = function(id, name, cat, reqDet, isActive, sort) {
    document.getElementById('fEdit').action = '{{ url("software-catalog") }}/' + id;
    document.getElementById('eN').value   = name;
    document.getElementById('eC').value   = cat;
    document.getElementById('eS').value   = sort;
    document.getElementById('eR').checked = reqDet;
    document.getElementById('eA').checked = isActive;
    new bootstrap.Modal(document.getElementById('modalEdit')).show();
};

// ── Toggle habilitar / deshabilitar ─────────────────────────
window.toggleProg = function(id, isActive, name, cat, sort, reqDet) {
    Swal.fire({
        ...swCfg,
        title: isActive ? '⛔ Deshabilitar programa' : '✅ Habilitar programa',
        html: '¿Deseas <b>' + (isActive?'deshabilitar':'habilitar') + '</b> a <b>' + name + '</b>?',
        icon: isActive ? 'warning' : 'question',
        showCancelButton: true,
        confirmButtonText: isActive ? 'Deshabilitar' : 'Habilitar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: isActive ? '#ef4444' : '#22c55e'
    }).then(function(r) {
        if (!r.isConfirmed) return;
        const f = document.getElementById('fToggle');
        f.action = '{{ url("software-catalog") }}/' + id;
        document.getElementById('tN').value = name;
        document.getElementById('tC').value = cat;
        document.getElementById('tS').value = sort;
        document.getElementById('tR').value = reqDet ? '1' : '';
        document.getElementById('tA').value = isActive ? '' : '1';
        f.submit();
    });
};

// ── Eliminar programa ────────────────────────────────────────
window.delProg = function(id, name) {
    Swal.fire({
        ...swCfg,
        title: '🗑️ Eliminar programa',
        html: '¿Eliminar <b>' + name + '</b>?<br><small style="color:#f87171;">Esta acción es irreversible.</small>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Eliminar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#ef4444'
    }).then(function(r) {
        if (!r.isConfirmed) return;
        const f = document.getElementById('fDel');
        f.action = '{{ url("software-catalog") }}/' + id;
        f.submit();
    });
};

// ── Eliminar categoría ───────────────────────────────────────
window.delCat = function(cat, count) {
    Swal.fire({
        ...swCfg,
        title: '📁 Eliminar categoría',
        html: '¿Eliminar <b>' + cat + '</b> y sus <b>' + count + ' programa(s)</b>?<br>' +
              '<span style="color:#f87171;font-size:13px;">⚠️ Acción irreversible.</span>',
        icon: 'error',
        showCancelButton: true,
        confirmButtonText: 'Eliminar todo',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#ef4444',
        input: 'checkbox',
        inputValue: 0,
        inputPlaceholder: 'Entiendo que es irreversible',
        preConfirm: function(v){ if(!v) Swal.showValidationMessage('Debes marcar la confirmación'); }
    }).then(function(r) {
        if (!r.isConfirmed) return;
        document.getElementById('dcN').value = cat;
        document.getElementById('fDelCat').action = '{{ url("software-catalog") }}/0';
        document.getElementById('fDelCat').submit();
    });
};

// ── Búsqueda ─────────────────────────────────────────────────
document.getElementById('searchInput').addEventListener('input', function(){
    const q = this.value.trim().toLowerCase();
    document.getElementById('clearBtn').style.display = q ? 'inline' : 'none';
    document.getElementById('searchTerm').textContent  = q;
    const rows = document.querySelectorAll('.prog-item');
    const cols = document.querySelectorAll('.cat-col');
    let vis = 0;
    rows.forEach(function(r){
        const ok = !q || r.dataset.name.includes(q) || (r.closest('.cat-col')||{dataset:{category:''}}).dataset.category.includes(q);
        r.style.display = ok ? '' : 'none';
        if(ok) vis++;
    });
    cols.forEach(function(c){
        c.style.display = Array.from(c.querySelectorAll('.prog-item')).some(function(r){return r.style.display!=='none';}) ? '' : 'none';
    });
    document.getElementById('totalCount').textContent = vis;
    document.getElementById('noResults').style.display = (!vis && q) ? 'block' : 'none';
    document.getElementById('catalogGrid').style.display = (!vis && q) ? 'none' : '';
});

window.clearSrch = function(){
    document.getElementById('searchInput').value='';
    document.getElementById('searchInput').dispatchEvent(new Event('input'));
};

// ── Notificaciones flash ─────────────────────────────────────
@if(session('success'))
Swal.fire({...swCfg,icon:'success',title:'¡Listo!',text:@json(session('success')),
    timer:2800,showConfirmButton:false,toast:true,position:'top-end'});
@endif
@if(session('error'))
Swal.fire({...swCfg,icon:'error',title:'Error',text:@json(session('error')),
    timer:3500,showConfirmButton:false,toast:true,position:'top-end'});
@endif

})();
</script>
@endsection