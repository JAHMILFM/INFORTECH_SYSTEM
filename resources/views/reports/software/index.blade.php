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
.cat-card{border-radius:16px;border:1px solid var(--border);background:var(--surface);overflow:hidden;transition:box-shadow .25s,transform .2s;}
.cat-card:hover{box-shadow:0 8px 28px rgba(0,0,0,.2);transform:translateY(-2px);}
.cat-header{padding:14px 18px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;}
.prog-row{display:flex;align-items:center;justify-content:space-between;gap:10px;padding:10px 14px;border-radius:10px;border:1px solid var(--border);background:rgba(255,255,255,.025);transition:background .15s;}
.prog-row:hover{background:rgba(255,255,255,.06);}
.prog-row.disabled-row{opacity:.42;}
.act-btns{display:flex;gap:5px;flex-shrink:0;}
.xbtn{display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:8px;border:1.5px solid;background:transparent;cursor:pointer;font-size:14px;transition:transform .12s,filter .12s;padding:0;}
.xbtn:hover{transform:translateY(-1px);filter:brightness(1.2);}
.x-on {color:#4ade80;border-color:#4ade80;}
.x-off{color:#6b7280;border-color:#6b7280;}
.x-ed {color:#818cf8;border-color:#818cf8;}
.x-rm {color:#f87171;border-color:#f87171;}
.x-fd {color:#f87171;border-color:rgba(248,113,113,.3);background:rgba(248,113,113,.07);}
.search-bar{display:flex;align-items:center;gap:10px;background:var(--surface);border:1px solid var(--border);border-radius:12px;padding:10px 16px;flex:1;min-width:200px;}
.search-bar input{background:transparent;border:none;outline:none;color:var(--text);flex:1;font-size:14px;}
.pill{display:inline-flex;align-items:center;gap:5px;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:700;}
.add-cat-btn{width:100%;margin-top:8px;padding:9px;border:1.5px dashed var(--border);border-radius:10px;background:transparent;color:var(--text-muted);font-size:12px;cursor:pointer;transition:all .15s;}
.add-cat-btn:hover{border-color:var(--primary);color:var(--primary);background:rgba(99,102,241,.07);}
.modal-content{background:var(--surface);border:1px solid var(--border);border-radius:16px;color:var(--text);}
.modal-header,.modal-footer{border-color:var(--border)!important;}
.form-control{background:var(--surface)!important;color:var(--text)!important;border-color:var(--border)!important;}
.form-control:focus{border-color:var(--primary)!important;box-shadow:0 0 0 3px rgba(99,102,241,.18)!important;}
.tag-det{font-size:10px;padding:2px 7px;border-radius:20px;font-weight:700;background:rgba(234,179,8,.18);color:#fbbf24;}
.tag-off{font-size:10px;padding:2px 7px;border-radius:20px;font-weight:700;background:rgba(239,68,68,.15);color:#f87171;}
</style>

{{-- HEADER --}}
<div class="row page-titles mx-0 mb-4 align-items-center">
    <div class="col-sm-6 p-md-0">
        <h4 class="mb-1 fw-bold" style="color:var(--text);">
            <i class="bi bi-grid-3x3-gap-fill me-2" style="color:var(--primary);"></i>Catálogo de Software
        </h4>
        <p class="mb-0 text-muted" style="font-size:13px;">Gestiona programas y categorías disponibles en los reportes.</p>
    </div>
    <div class="col-sm-6 p-md-0 d-flex justify-content-sm-end gap-2 mt-2 mt-sm-0">
        <button class="btn btn-outline-secondary fw-semibold d-flex align-items-center gap-2"
                onclick="showModal('mCat'); document.getElementById('nCatInput').value='';">
            <i class="bi bi-folder-plus"></i>Nueva Categoría
        </button>
        <button class="btn btn-primary fw-semibold d-flex align-items-center gap-2"
                onclick="showModal('mAdd'); document.getElementById('aName').value=''; document.getElementById('aCat').value='';">
            <i class="bi bi-plus-circle-fill"></i>Agregar Programa
        </button>
    </div>
</div>

{{-- STATS + BÚSQUEDA --}}
<div class="d-flex flex-wrap gap-3 align-items-center mb-4">
    <div class="search-bar">
        <i class="bi bi-search" style="color:var(--text-muted);"></i>
        <input type="text" id="srch" placeholder="Buscar programa o categoría..." autocomplete="off" oninput="doSearch(this.value)">
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <span class="pill" style="background:rgba(99,102,241,.13);color:#818cf8;">
            <i class="bi bi-app-indicator"></i><b id="totalCnt">{{ $software->flatten()->count() }}</b> programas
        </span>
        <span class="pill" style="background:rgba(34,197,94,.13);color:#4ade80;">
            <i class="bi bi-check-circle-fill"></i>{{ $software->flatten()->where('is_active',true)->count() }} activos
        </span>
        <span class="pill" style="background:rgba(148,163,184,.1);color:#94a3b8;">
            <i class="bi bi-folder-fill"></i>{{ $software->count() }} categorías
        </span>
    </div>
</div>

{{-- GRID --}}
<div class="row g-4" id="grid">
@forelse($software as $category => $items)
<div class="col-md-6 col-xl-4" data-cat-col="{{ strtolower($category) }}">
    <div class="cat-card h-100">
        <div class="cat-header">
            <div class="d-flex align-items-center gap-2">
                <span style="width:36px;height:36px;border-radius:10px;background:rgba(99,102,241,.14);display:inline-flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="bi bi-folder-fill" style="color:var(--primary);font-size:16px;"></i>
                </span>
                <div>
                    <div class="fw-bold" style="font-size:14px;color:var(--text);">{{ $category ?: 'General' }}</div>
                    <div style="font-size:11px;color:var(--text-muted);">{{ $items->count() }} programa(s)</div>
                </div>
            </div>
            <button class="xbtn x-fd" title="Eliminar categoría completa"
                    data-cat="{{ $category ?: 'General' }}"
                    data-cnt="{{ $items->count() }}"
                    onclick="confirmDelCat(this)">
                <i class="bi bi-folder-x" style="font-size:18px;"></i>
            </button>
        </div>

        <div class="p-3 d-flex flex-column gap-2">
        @foreach($items as $prog)
        <div class="prog-row {{ !$prog->is_active ? 'disabled-row' : '' }}"
             data-prog-id="{{ $prog->id }}"
             data-prog-name="{{ $prog->name }}"
             data-prog-cat="{{ $prog->category }}"
             data-prog-sort="{{ $prog->sort_order }}"
             data-prog-req="{{ $prog->requires_detail ? '1' : '0' }}"
             data-prog-active="{{ $prog->is_active ? '1' : '0' }}"
             data-srch-name="{{ strtolower($prog->name) }}">
            <div style="min-width:0;flex:1;">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                    <span style="font-size:13px;font-weight:600;color:var(--text);">{{ $prog->name }}</span>
                    @if($prog->requires_detail)<span class="tag-det"><i class="bi bi-tag-fill me-1"></i>Detallable</span>@endif
                    @if(!$prog->is_active)<span class="tag-off"><i class="bi bi-slash-circle me-1"></i>Inactivo</span>@endif
                </div>
                @if($prog->sort_order > 0)
                <div style="font-size:11px;color:var(--text-muted);"><i class="bi bi-sort-numeric-down me-1"></i>Orden {{ $prog->sort_order }}</div>
                @endif
            </div>

            <div class="act-btns">
                <button class="xbtn {{ $prog->is_active ? 'x-on' : 'x-off' }}"
                        title="{{ $prog->is_active ? 'Deshabilitar' : 'Habilitar' }}"
                        onclick="confirmToggle(this)">
                    <i class="bi {{ $prog->is_active ? 'bi-toggle-on' : 'bi-toggle-off' }}"></i>
                </button>
                <button class="xbtn x-ed" title="Editar programa" onclick="openEdit(this)">
                    <i class="bi bi-pencil-fill"></i>
                </button>
                <button class="xbtn x-rm" title="Eliminar programa" onclick="confirmDel(this)">
                    <i class="bi bi-trash-fill"></i>
                </button>
            </div>
        </div>
        @endforeach

        <button class="add-cat-btn" onclick="showModal('mAdd'); document.getElementById('aCat').value='{{ $category }}';">
            <i class="bi bi-plus-lg me-1"></i>Agregar a {{ $category }}
        </button>
        </div>
    </div>
</div>
@empty
<div class="col-12 text-center py-5">
    <i class="bi bi-inbox text-muted display-4"></i>
    <p class="text-muted mt-2">No hay programas en el catálogo.</p>
</div>
@endforelse
</div>

<div id="noRes" class="text-center py-5" style="display:none;">
    <i class="bi bi-search text-muted display-4"></i>
    <p class="text-muted mt-2">No hay programas que coincidan con "<span id="srchTerm"></span>".</p>
</div>

{{-- FORMS OCULTOS --}}
<form id="fToggle" method="POST" style="display:none;">
    @csrf
    @method('PUT')
    <input type="hidden" name="name" id="tgN">
    <input type="hidden" name="category" id="tgC">
    <input type="hidden" name="sort_order" id="tgS">
    <input type="hidden" name="requires_detail" id="tgR">
    <input type="hidden" name="is_active" id="tgA">
</form>

<form id="fDel" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>

<form id="fDelCat" action="{{ route('software-catalog.destroyCategory') }}" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
    <input type="hidden" name="category_name" id="dcCat">
</form>

{{-- MODALES --}}

{{-- Modal Agregar Programa --}}
<div class="modal fade" id="mAdd" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('software-catalog.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-header-title fw-bold" style="color:var(--text);">
                        <i class="bi bi-plus-circle-fill me-2" style="color:var(--primary);"></i>Agregar Programa
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">Nombre del programa <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="aName" class="form-control" placeholder="Ej: Office 2021, AutoCAD, Photoshop..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">Categoría <span class="text-danger">*</span></label>
                        <input type="text" name="category" id="aCat" class="form-control" list="catList" placeholder="Ej: Ofimática, Diseño, CAD..." required>
                        <datalist id="catList">
                            @foreach($software->keys() as $c)
                            <option value="{{ $c }}">
                            @endforeach
                        </datalist>
                    </div>
                    <div class="row g-2">
                        <div class="col-6 mb-3">
                            <label class="form-label fw-semibold" style="font-size:13px;">Orden de presentación</label>
                            <input type="number" name="sort_order" class="form-control" value="0" min="0">
                        </div>
                        <div class="col-6 mb-3 d-flex align-items-center pt-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="requires_detail" value="1" id="aReq">
                                <label class="form-check-label fw-semibold" for="aReq" style="font-size:13px;">¿Requiere detalle?</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-semibold"><i class="bi bi-check-lg me-1"></i>Guardar Programa</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Editar Programa --}}
<div class="modal fade" id="mEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="fEdit" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-header-title fw-bold" style="color:var(--text);">
                        <i class="bi bi-pencil-square me-2" style="color:var(--primary);"></i>Editar Programa
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">Nombre del programa <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="eName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">Categoría <span class="text-danger">*</span></label>
                        <input type="text" name="category" id="eCat" class="form-control" list="catListEdit" required>
                        <datalist id="catListEdit">
                            @foreach($software->keys() as $c)
                            <option value="{{ $c }}">
                            @endforeach
                        </datalist>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Orden</label>
                            <input type="number" name="sort_order" id="eSort" class="form-control" min="0">
                        </div>
                        <div class="col-6 d-flex align-items-center pt-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="requires_detail" value="1" id="eReq">
                                <label class="form-check-label fw-semibold" for="eReq" style="font-size:13px;">¿Requiere detalle?</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="eActive">
                        <label class="form-check-label fw-semibold" for="eActive" style="font-size:13px;">Programa Habilitado / Activo</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-semibold"><i class="bi bi-save me-1"></i>Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Nueva Categoría --}}
<div class="modal fade" id="mCat" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <form action="{{ route('software-catalog.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-header-title fw-bold" style="color:var(--text);">
                        <i class="bi bi-folder-plus me-2" style="color:var(--primary);"></i>Nueva Categoría
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">Nombre de la Categoría <span class="text-danger">*</span></label>
                        <input type="text" name="category" id="nCatInput" class="form-control" placeholder="Ej: Antivirus, Antivirus/Seguridad..." required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-semibold" style="font-size:13px;">Primer Programa <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="Ej: ESET NOD32, Kaspersky..." required>
                    </div>
                    <small class="text-muted" style="font-size:11px;">Toda categoría debe tener al menos 1 programa inicial.</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success fw-semibold">
                        <i class="bi bi-folder-plus me-1"></i>Crear Categoría
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
var BASE = '{{ url("software-catalog") }}';

function showModal(id) {
    var el = document.getElementById(id);
    if (!el) return;
    var m = bootstrap.Modal.getInstance(el) || new bootstrap.Modal(el);
    m.show();
}

function getRow(btn) {
    return btn.closest('[data-prog-id]');
}

function openEdit(btn) {
    var row = getRow(btn);
    var id  = row.dataset.progId;
    document.getElementById('fEdit').action  = BASE + '/' + id;
    document.getElementById('eName').value   = row.dataset.progName;
    document.getElementById('eCat').value    = row.dataset.progCat;
    document.getElementById('eSort').value   = row.dataset.progSort;
    document.getElementById('eReq').checked  = row.dataset.progReq === '1';
    document.getElementById('eActive').checked = row.dataset.progActive === '1';
    showModal('mEdit');
}

function confirmToggle(btn) {
    var row    = getRow(btn);
    var active = row.dataset.progActive === '1';
    var name   = row.dataset.progName;
    var msg    = active ? '¿Deshabilitar "' + name + '"?' : '¿Habilitar "' + name + '"?';

    if (typeof Swal !== 'undefined') {
        Swal.fire({
            background:'var(--surface)', color:'var(--text)',
            title: active ? 'Deshabilitar' : 'Habilitar',
            text: msg,
            icon: active ? 'warning' : 'question',
            showCancelButton: true,
            confirmButtonText: active ? 'Deshabilitar' : 'Habilitar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: active ? '#ef4444' : '#22c55e'
        }).then(function(r) { if (r.isConfirmed) submitToggle(row, active); });
    } else {
        if (confirm(msg)) submitToggle(row, active);
    }
}

function submitToggle(row, active) {
    var f = document.getElementById('fToggle');
    f.action = BASE + '/' + row.dataset.progId;
    document.getElementById('tgN').value = row.dataset.progName;
    document.getElementById('tgC').value = row.dataset.progCat;
    document.getElementById('tgS').value = row.dataset.progSort;
    document.getElementById('tgR').value = row.dataset.progReq;
    document.getElementById('tgA').value = active ? '0' : '1';
    f.submit();
}

function confirmDel(btn) {
    var row  = getRow(btn);
    var name = row.dataset.progName;

    if (typeof Swal !== 'undefined') {
        Swal.fire({
            background:'var(--surface)', color:'var(--text)',
            title: 'Eliminar programa',
            html: '¿Eliminar <b>' + name + '</b>?<br><small style="color:#f87171;">Esta acción es irreversible.</small>',
            icon: 'warning', showCancelButton: true,
            confirmButtonText: 'Eliminar', cancelButtonText: 'Cancelar', confirmButtonColor: '#ef4444'
        }).then(function(r) { if (r.isConfirmed) submitDel(row.dataset.progId); });
    } else {
        if (confirm('¿Eliminar "' + name + '"? Esta acción es irreversible.')) submitDel(row.dataset.progId);
    }
}

function submitDel(id) {
    var f = document.getElementById('fDel');
    f.action = BASE + '/' + id;
    f.submit();
}

function confirmDelCat(btn) {
    var cat = btn.dataset.cat;
    var cnt = btn.dataset.cnt;

    if (typeof Swal !== 'undefined') {
        Swal.fire({
            background:'var(--surface)', color:'var(--text)',
            title: 'Eliminar categoría',
            html: '¿Eliminar <b>' + cat + '</b> con <b>' + cnt + ' programa(s)</b>?<br><span style="color:#f87171;font-size:13px;">Acción Irreversible.</span>',
            icon: 'error', showCancelButton: true,
            confirmButtonText: 'Eliminar todo', cancelButtonText: 'Cancelar', confirmButtonColor: '#ef4444',
            input: 'checkbox', inputValue: 0, inputPlaceholder: 'Entiendo que es irreversible',
            preConfirm: function(v) { if (!v) Swal.showValidationMessage('Debes confirmar.'); }
        }).then(function(r) { if (r.isConfirmed) submitDelCat(cat); });
    } else {
        if (confirm('¿Eliminar la categoría "' + cat + '" y sus ' + cnt + ' programa(s)? IRREVERSIBLE.')) submitDelCat(cat);
    }
}

function submitDelCat(cat) {
    document.getElementById('dcCat').value = cat;
    document.getElementById('fDelCat').submit();
}

function doSearch(q) {
    document.getElementById('srch').value = q;
    q = q.trim().toLowerCase();
    document.getElementById('srchTerm').textContent = q;

    var rows   = document.querySelectorAll('[data-prog-id]');
    var cols   = document.querySelectorAll('[data-cat-col]');
    var vis    = 0;

    rows.forEach(function(r) {
        var nm  = (r.dataset.srchName || '').toLowerCase();
        var cat = (r.closest('[data-cat-col]') || {dataset:{catCol:''}}).dataset.catCol;
        var ok  = !q || nm.includes(q) || cat.includes(q);
        r.style.display = ok ? '' : 'none';
        if (ok) vis++;
    });

    cols.forEach(function(c) {
        var hasVis = Array.from(c.querySelectorAll('[data-prog-id]')).some(function(r){ return r.style.display !== 'none'; });
        c.style.display = hasVis ? '' : 'none';
    });

    document.getElementById('totalCnt').textContent  = vis;
    document.getElementById('noRes').style.display   = (!vis && q) ? 'block' : 'none';
    document.getElementById('grid').style.display    = (!vis && q) ? 'none'  : '';
}

window.addEventListener('load', function() {
    var showNotif = function(type, text) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                background:'var(--surface)', color:'var(--text)',
                icon: type, title: type === 'success' ? '¡Listo!' : 'Error',
                text: text, timer: 2800, showConfirmButton: false, toast: true, position: 'top-end'
            });
        }
    };
    @if(session('success')) showNotif('success', @json(session('success'))); @endif
    @if(session('error'))   showNotif('error',   @json(session('error')));   @endif
});
</script>
@endsection