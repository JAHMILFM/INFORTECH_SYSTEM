@extends('layouts.app')

@section('page-title', 'Catálogo de Software y Perfiles')

@section('breadcrumb')
    <i class="bi bi-chevron-right mx-1"></i>
    <a href="{{ route('reports.index') }}" style="color:var(--text-muted);text-decoration:none;">Reportes</a>
    <i class="bi bi-chevron-right mx-1"></i>
    <span>Catálogo de Software</span>
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
.base-card{border:1px solid var(--border);background:rgba(255,255,255,.02);border-radius:14px;padding:16px;position:relative;transition:all .2s;}
.base-card:hover{border-color:var(--primary);background:rgba(99,102,241,.03);}
.soft-chip{display:inline-flex;align-items:center;gap:4px;padding:3px 9px;border-radius:12px;font-size:11px;background:rgba(255,255,255,.06);color:var(--text);border:1px solid var(--border);}
</style>

{{-- HEADER --}}
<div class="row page-titles mx-0 mb-4 align-items-center">
    <div class="col-sm-6 p-md-0">
        <h4 class="mb-1 fw-bold" style="color:var(--text);">
            <i class="bi bi-grid-3x3-gap-fill me-2" style="color:var(--primary);"></i>Catálogo de Software y Perfiles
        </h4>
        <p class="mb-0 text-muted" style="font-size:13px;">Administra el software disponible y configura perfiles de instalación rápida.</p>
    </div>
    <div class="col-sm-6 p-md-0 d-flex justify-content-sm-end gap-2 mt-2 mt-sm-0 flex-wrap">
        <button class="btn btn-outline-primary fw-semibold d-flex align-items-center gap-2"
                onclick="openNewBaseline()">
            <i class="bi bi-layers-half"></i>Nuevo Perfil (Baseline)
        </button>
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

{{-- NAVEGACIÓN POR PESTAÑAS --}}
<ul class="nav nav-pills mb-4 gap-2" id="catalogTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active fw-bold px-4 py-2" id="software-tab" data-bs-toggle="pill" data-bs-target="#tab-software" type="button" role="tab">
            <i class="bi bi-app-indicator me-2"></i>Programas por Categoría ({{ $software->flatten()->count() }})
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link fw-bold px-4 py-2" id="baselines-tab" data-bs-toggle="pill" data-bs-target="#tab-baselines" type="button" role="tab">
            <i class="bi bi-layers-fill me-2"></i>Perfiles Rápidos (Baselines) ({{ $baselines->count() }})
        </button>
    </li>
</ul>

<div class="tab-content" id="catalogTabsContent">
    {{-- PESTAÑA 1: PROGRAMAS INDIVIDUALES --}}
    <div class="tab-pane fade show active" id="tab-software" role="tabpanel">
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

        {{-- GRID PROGRAMAS --}}
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
    </div>

    {{-- PESTAÑA 2: PERFILES RÁPIDOS (BASELINES) --}}
    <div class="tab-pane fade" id="tab-baselines" role="tabpanel">
        <div class="alert alert-info border-0 shadow-sm d-flex align-items-center gap-3 mb-4" style="background:rgba(99,102,241,.1);color:var(--text);">
            <i class="bi bi-info-circle-fill text-primary fs-4"></i>
            <div style="font-size:13px;">
                <b>¿Qué son las plantillas de perfil (Software Baselines)?</b><br>
                Son paquetes preconfigurados de software agrupados por rol de trabajo. Al crear un nuevo reporte técnico de servicio, el técnico puede aplicar cualquiera de estos perfiles para seleccionar automáticamente todos sus programas en <b>1 solo clic</b>.
            </div>
        </div>

        <div class="row g-4">
        @forelse($baselines as $base)
        <div class="col-md-6 col-lg-6">
            <div class="base-card h-100 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-primary rounded-pill p-2 fs-6">
                                <i class="bi {{ $base->icon ?: 'bi-briefcase-fill' }}"></i>
                            </span>
                            <div>
                                <h6 class="fw-bold mb-0" style="color:var(--text);">
                                    {{ $base->name }}
                                    @if($base->is_default)
                                    <span class="badge bg-success ms-1" style="font-size:10px;">Predeterminado</span>
                                    @endif
                                </h6>
                                <small class="text-muted" style="font-size:11px;">{{ $base->description }}</small>
                            </div>
                        </div>
                        <div class="d-flex gap-1">
                            <button class="xbtn x-ed" title="Editar Perfil"
                                    data-id="{{ $base->id }}"
                                    data-name="{{ $base->name }}"
                                    data-desc="{{ $base->description }}"
                                    data-icon="{{ $base->icon }}"
                                    data-default="{{ $base->is_default ? '1' : '0' }}"
                                    data-ids='@json($base->software_ids ?? [])'
                                    onclick="openEditBaseline(this)">
                                <i class="bi bi-pencil-fill"></i>
                            </button>
                            <button class="xbtn x-rm" title="Eliminar Perfil" onclick="confirmDelBaseline({{ $base->id }}, '{{ $base->name }}')">
                                <i class="bi bi-trash-fill"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mt-3">
                        <small class="text-muted d-block mb-2 fw-semibold" style="font-size:11px;">
                            Programas incluidos ({{ count($base->software_ids ?? []) }}):
                        </small>
                        <div class="d-flex flex-wrap gap-1">
                            @php
                                $itemsInBase = $software->flatten()->whereIn('id', $base->software_ids ?? []);
                            @endphp
                            @forelse($itemsInBase as $item)
                            <span class="soft-chip">
                                <i class="bi bi-check2 text-success"></i>{{ $item->name }}
                            </span>
                            @empty
                            <span class="text-muted fst-italic" style="font-size:12px;">Ningún programa asignado aún</span>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <i class="bi bi-layers text-muted display-4"></i>
            <p class="text-muted mt-2">No hay perfiles configurados. Crea el primero arriba.</p>
        </div>
        @endforelse
        </div>
    </div>
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

<form id="fDelBase" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
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
                        <input type="text" name="category" id="nCatInput" class="form-control" placeholder="Ej: Antivirus, Seguridad..." required>
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

{{-- Modal Crear / Editar Baseline (Perfil) --}}
<div class="modal fade" id="mBaseline" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form id="fBaseline" method="POST">
                @csrf
                <div id="bMethodField"></div>
                <div class="modal-header">
                    <h5 class="modal-header-title fw-bold" id="bTitle" style="color:var(--text);">
                        <i class="bi bi-layers-fill me-2" style="color:var(--primary);"></i>Gestión de Perfil
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Nombre del Perfil <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="bName" class="form-control" placeholder="Ej: Administrativo, Diseño, Desarrollo" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Icono Bootstrap</label>
                            <select name="icon" id="bIcon" class="form-select" style="background:var(--surface);color:var(--text);border-color:var(--border);">
                                <option value="bi-briefcase-fill">💼 Maletín (Oficina / Admin)</option>
                                <option value="bi-palette-fill">🎨 Paleta (Diseño / Multimedia)</option>
                                <option value="bi-code-slash">💻 Código (Desarrollo / TI)</option>
                                <option value="bi-shield-check">🛡️ Escudo (Básico / Antivirus)</option>
                                <option value="bi-calculator-fill">📊 Calculadora (Finanzas)</option>
                                <option value="bi-gear-fill">⚙️ Engranaje (Soporte)</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold" style="font-size:13px;">Descripción breve</label>
                            <input type="text" name="description" id="bDesc" class="form-control" placeholder="Ej: Paquete estándar para puestos de facturación y secretaría">
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="form-label fw-bold d-block mb-2" style="font-size:13px;">
                            Selecciona los programas incluidos en este perfil:
                        </label>
                        <div class="row g-2" style="max-height:280px;overflow-y:auto;padding-right:5px;">
                            @foreach($software as $cat => $progs)
                            <div class="col-12">
                                <small class="fw-bold text-primary text-uppercase" style="font-size:11px;letter-spacing:0.5px;">{{ $cat }}</small>
                            </div>
                            @foreach($progs as $p)
                            <div class="col-md-6 col-lg-4">
                                <div class="form-check form-check-inline me-0 w-100 p-2 border rounded" style="background:rgba(255,255,255,.015);border-color:var(--border)!important;">
                                    <input class="form-check-input base-soft-chk" type="checkbox" name="software_ids[]" value="{{ $p->id }}" id="b_soft_{{ $p->id }}">
                                    <label class="form-check-label ms-1 fw-semibold text-truncate" style="font-size:12px;color:var(--text);cursor:pointer;" for="b_soft_{{ $p->id }}">
                                        {{ $p->name }}
                                    </label>
                                </div>
                            </div>
                            @endforeach
                            @endforeach
                        </div>
                    </div>

                    <div class="form-check form-switch mt-3">
                        <input class="form-check-input" type="checkbox" name="is_default" value="1" id="bDefault">
                        <label class="form-check-label fw-semibold" for="bDefault" style="font-size:13px;">Establecer como Perfil Predeterminado</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-semibold"><i class="bi bi-check-lg me-1"></i>Guardar Perfil</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
var BASE = '{{ url("software-catalog") }}';
var BASE_BASELINE = '{{ url("software-baselines") }}';

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

// --- BASELINES JS ---

function openNewBaseline() {
    var f = document.getElementById('fBaseline');
    f.action = BASE_BASELINE;
    document.getElementById('bMethodField').innerHTML = '';
    document.getElementById('bTitle').innerHTML = '<i class="bi bi-layers-fill me-2 text-primary"></i>Nuevo Perfil de Software';
    document.getElementById('bName').value = '';
    document.getElementById('bDesc').value = '';
    document.getElementById('bIcon').value = 'bi-briefcase-fill';
    document.getElementById('bDefault').checked = false;

    document.querySelectorAll('.base-soft-chk').forEach(function(c) { c.checked = false; });
    showModal('mBaseline');
}

function openEditBaseline(btn) {
    var f = document.getElementById('fBaseline');
    var id = btn.dataset.id;
    f.action = BASE_BASELINE + '/' + id;
    document.getElementById('bMethodField').innerHTML = '@method("PUT")';
    document.getElementById('bTitle').innerHTML = '<i class="bi bi-pencil-square me-2 text-primary"></i>Editar Perfil';
    document.getElementById('bName').value = btn.dataset.name;
    document.getElementById('bDesc').value = btn.dataset.desc;
    document.getElementById('bIcon').value = btn.dataset.icon || 'bi-briefcase-fill';
    document.getElementById('bDefault').checked = btn.dataset.default === '1';

    var selectedIds = [];
    try {
        selectedIds = JSON.parse(btn.dataset.ids || '[]');
    } catch(e){}

    document.querySelectorAll('.base-soft-chk').forEach(function(c) {
        c.checked = selectedIds.includes(parseInt(c.value));
    });

    showModal('mBaseline');
}

function confirmDelBaseline(id, name) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            background:'var(--surface)', color:'var(--text)',
            title: 'Eliminar Perfil',
            text: '¿Eliminar la plantilla de perfil "' + name + '"?',
            icon: 'warning', showCancelButton: true,
            confirmButtonText: 'Eliminar', cancelButtonText: 'Cancelar', confirmButtonColor: '#ef4444'
        }).then(function(r) {
            if (r.isConfirmed) {
                var f = document.getElementById('fDelBase');
                f.action = BASE_BASELINE + '/' + id;
                f.submit();
            }
        });
    } else {
        if (confirm('¿Eliminar la plantilla de perfil "' + name + '"?')) {
            var f = document.getElementById('fDelBase');
            f.action = BASE_BASELINE + '/' + id;
            f.submit();
        }
    }
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