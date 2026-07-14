@extends('layouts.app')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>
                <span style="display:inline-flex;align-items:center;justify-content:center;width:34px;height:34px;border-radius:8px;background:{{ $config['color'] }};margin-right:10px;vertical-align:middle;">
                    <i class="fa {{ $config['icon'] }}" style="color:#fff;font-size:15px;"></i>
                </span>
                {{ $config['label'] }} — {{ $company->name }}
            </h4>
            <p class="mb-0">
                <i class="fa fa-globe me-1 text-primary"></i>{{ $company->domain ?? 'Sin dominio' }}
            </p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex align-items-center gap-2 flex-wrap">
        <a href="{{ route('companies.show', $company->id) }}" class="btn btn-secondary btn-sm">
            <i class="fa fa-arrow-left me-1"></i> Volver
        </a>
        @if($records->count() > 0)
        <a href="{{ route('companies.services.export', [$company->id, $type]) }}" class="btn btn-success btn-sm text-white">
            <i class="fa fa-file-excel-o me-1"></i> Exportar Excel
        </a>
        @endif
        @if($type === 'email')
        <a href="{{ route('companies.import.show', $company->id) }}" class="btn btn-success btn-sm text-white">
            <i class="fa fa-file-excel-o me-1"></i> Importar Excel
        </a>
        @endif
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addRecordModal">
            <i class="fa fa-plus me-1"></i> Agregar
        </button>
    </div>
</div>

{{-- Alertas --}}
@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <strong><i class="fa fa-exclamation-triangle me-2"></i> Error de Validación:</strong>
    <ul class="mb-0 mt-1">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fa fa-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h4 class="card-title mb-0">
                    Registros de {{ $config['label'] }}
                    <span class="badge badge-primary ms-2">{{ $records->count() }}</span>
                </h4>
                {{-- Resumen rápido --}}
                @php
                    $activos     = $records->filter(fn($r) => ($r->data['status'] ?? '') === 'Activo')->count();
                    $suspendidos = $records->filter(fn($r) => ($r->data['status'] ?? '') === 'Suspendido')->count();
                    $conObs      = $records->filter(fn($r) => !empty($r->data['observacion']))->count();
                @endphp
                @if($records->count() > 0)
                <div class="d-flex gap-2 flex-wrap">
                    <span class="badge bg-success px-3 py-2"><i class="fa fa-check-circle me-1"></i>{{ $activos }} Activos</span>
                    @if($suspendidos > 0)
                    <span class="badge bg-warning px-3 py-2"><i class="fa fa-ban me-1"></i>{{ $suspendidos }} Suspendidos</span>
                    @endif
                    @if($conObs > 0)
                    <span class="badge bg-danger px-3 py-2"><i class="fa fa-exclamation-circle me-1"></i>{{ $conObs }} con observaciones</span>
                    @endif
                </div>
                @endif
            </div>
            <div class="card-body">
                {{-- Buscador rápido --}}
                @if($records->count() > 5)
                <div class="mb-3">
                    <div class="input-group" style="max-width:380px;">
                        <span class="input-group-text bg-white"><i class="fa fa-search text-muted"></i></span>
                        <input type="text" id="quickSearch" class="form-control" placeholder="Buscar en esta tabla...">
                    </div>
                </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle" id="serviceTable">
                        <thead>
                            <tr style="background:{{ $config['color'] }};color:#fff;">
                                <th style="width:40px;">#</th>
                                @foreach($config['columns'] as $key => $label)
                                    <th>{{ $label }}</th>
                                @endforeach
                                <th style="width:110px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            @forelse($records as $i => $record)
                            <tr class="table-row" id="row_{{ $record->id }}">
                                <td class="text-center text-muted">{{ ($records->currentPage() - 1) * $records->perPage() + $i + 1 }}</td>
                                @foreach($config['columns'] as $key => $label)
                                    <td>
                                        @php $val = $record->data[$key] ?? '—'; @endphp
                                        @if($key === 'status')
                                            <div class="form-check form-switch d-flex align-items-center gap-2">
                                                <input class="form-check-input" type="checkbox" role="switch" 
                                                    id="statusToggle_{{ $record->id }}"
                                                    onchange="toggleStatus({{ $record->id }}, '{{ $company->id }}', '{{ $type }}', this.checked)"
                                                    {{ $val === 'Activo' ? 'checked' : '' }}
                                                    style="cursor:pointer; width:35px; height:18px;">
                                                <label class="form-check-label mb-0" for="statusToggle_{{ $record->id }}" id="statusLabel_{{ $record->id }}">
                                                    @php
                                                        $badgeClass = 'bg-secondary';
                                                        $icon = '';
                                                        if (in_array($val, ['Activo', 'Aprobado'])) {
                                                            $badgeClass = 'bg-success';
                                                            $icon = '<i class="fa fa-circle me-1" style="font-size:8px;"></i>';
                                                        } elseif (in_array($val, ['Inactivo', 'Suspendido', 'Bloqueada'])) {
                                                            $badgeClass = 'bg-danger';
                                                            $icon = '<i class="fa fa-ban me-1"></i>';
                                                        }
                                                    @endphp
                                                    <span class="badge {{ $badgeClass }}">{!! $icon !!}{{ $val }}</span>
                                                </label>
                                            </div>
                                        @elseif($key === 'password')
                                            @if($val && $val !== '—')
                                                <div class="d-flex align-items-center gap-1">
                                                    <span class="pass-mask" style="font-family:monospace;letter-spacing:2px;">••••••••</span>
                                                    <span class="pass-text d-none" style="font-family:monospace;font-size:12px;background:#f0f0f0;padding:1px 5px;border-radius:4px;">{{ $val }}</span>
                                                    <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-1 toggle-pass"><i class="fa fa-eye"></i></button>
                                                    <button type="button" class="btn btn-xs btn-outline-primary py-0 px-1 btn-copy-clipboard"
                                                        data-clipboard="{{ $val }}" title="Copiar"><i class="fa fa-copy"></i></button>
                                                </div>
                                            @else <span class="text-muted">—</span> @endif
                                        @elseif($key === 'observacion')
                                            @if($val && $val !== '—')
                                                <span class="badge bg-warning text-dark"><i class="fa fa-exclamation-circle me-1"></i>{{ $val }}</span>
                                            @else <span class="text-muted">—</span> @endif
                                        @elseif($key === 'url')
                                            @if($val && $val !== '—')
                                                @php
                                                    $safeUrl = Str::startsWith($val, ['http://', 'https://']) ? $val : 'https://' . ltrim($val, '/');
                                                    if(Str::startsWith(strtolower($val), 'javascript:')) { $safeUrl = '#'; }
                                                @endphp
                                                <a href="{{ $safeUrl }}" target="_blank" style="font-size:12px;word-break:break-all;">{{ $val }}</a>
                                            @else <span class="text-muted">—</span> @endif
                                        @elseif(in_array($key, ['address','email','alias','target']))
                                            <div class="d-flex align-items-center gap-1">
                                                <code style="background:#f0f0f0;padding:2px 6px;border-radius:4px;font-size:12px;">{{ $val }}</code>
                                                <button type="button" class="btn btn-xs btn-outline-primary py-0 px-1 btn-copy-clipboard"
                                                    data-clipboard="{{ $val }}" title="Copiar"><i class="fa fa-copy"></i></button>
                                            </div>
                                        @else
                                            <span style="font-size:13px;">{{ $val }}</span>
                                        @endif
                                    </td>
                                @endforeach
                                <td>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-info btn-sm shadow text-white" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editRecordModal"
                                                data-id="{{ $record->id }}"
                                                data-payload="{{ json_encode($record->data) }}"
                                                title="Editar">
                                            <i class="fa fa-pencil"></i>
                                        </button>
                                        @if(auth()->user()->role === 'SuperAdmin')
                                        <form action="{{ route('companies.services.destroy', [$company->id, $type, $record->id]) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este registro?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm shadow" title="Eliminar">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ count($config['columns']) + 2 }}" class="text-center py-5">
                                    <i class="fa {{ $config['icon'] }} fa-3x text-muted mb-3 d-block"></i>
                                    <p class="text-muted mb-0">No hay registros de {{ $config['label'] }} aún.</p>
                                    <button class="btn btn-primary btn-sm mt-3" data-bs-toggle="modal" data-bs-target="#addRecordModal">
                                        <i class="fa fa-plus me-1"></i> Agregar el primero
                                    </button>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($records->hasPages())
                <div class="mt-3">
                    {{ $records->links('pagination::bootstrap-5') }}
                </div>
                @endif
            </div>
    </div>
</div>

{{-- ===== MODAL: AGREGAR ===== --}}
<div class="modal fade" id="addRecordModal" tabindex="-1">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background:{{ $config['color'] }};">
                <h5 class="modal-title text-white">
                    <i class="fa {{ $config['icon'] }} me-2"></i> Agregar {{ $config['label'] }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('companies.services.store', [$company->id, $type]) }}" method="POST" onsubmit="this.querySelector('button[type=submit]').disabled=true; this.querySelector('button[type=submit]').innerHTML='<i class=\'fa fa-spinner fa-spin me-1\'></i> Guardando...';">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        @foreach($config['fields'] as $field)
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">
                                {{ $field['label'] }}
                                @if($field['required']) <span class="text-danger">*</span> @endif
                            </label>
                            
                            @if($field['type'] === 'select')
                                <select name="{{ $field['key'] }}" class="form-control" {{ $field['required'] ? 'required' : '' }}>
                                    <option value="">-- Seleccionar --</option>
                                    @foreach($field['options'] as $opt)
                                        <option value="{{ $opt }}">{{ $opt }}</option>
                                    @endforeach
                                </select>
                            @elseif($field['type'] === 'textarea')
                                <textarea name="{{ $field['key'] }}" class="form-control" rows="3"
                                    placeholder="{{ $field['label'] }}"
                                    {{ $field['required'] ? 'required' : '' }}></textarea>
                            @else
                                {{-- Manejo especial de Contraseña (Generador BPM) --}}
                                @if($field['key'] === 'password')
                                    <div class="input-group">
                                        <input type="text" name="{{ $field['key'] }}" id="add_pwd_{{ $field['key'] }}"
                                            class="form-control" placeholder="Escriba o genere clave"
                                            {{ $field['required'] ? 'required' : '' }}>
                                        <button class="btn btn-outline-secondary" type="button" onclick="generatePwd('add_pwd_{{ $field['key'] }}')" title="Generar Contraseña Segura">
                                            <i class="fa fa-magic"></i>
                                        </button>
                                        <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('add_pwd_{{ $field['key'] }}')" title="Copiar">
                                            <i class="fa fa-copy"></i>
                                        </button>
                                    </div>
                                
                                {{-- Manejo especial de Correo (Poka-Yoke) --}}
                                @elseif($type === 'email' && $field['key'] === 'address' && !empty($company->domain))
                                    <div class="input-group">
                                        <input type="text" name="{{ $field['key'] }}_prefix" id="add_{{ $field['key'] }}_prefix"
                                            class="form-control" placeholder="ej. gerencia"
                                            {{ $field['required'] ? 'required' : '' }}>
                                        <span class="input-group-text bg-light text-muted">@</span>
                                        <input type="text" name="{{ $field['key'] }}_domain" class="form-control" 
                                            value="{{ $company->domain }}" placeholder="dominio.com" required>
                                    </div>
                                    <small class="text-muted" style="font-size: 0.75rem;">Puedes cambiar el dominio si este correo usa uno distinto.</small>
                                
                                {{-- Inputs estándar --}}
                                @else
                                    <input type="{{ $field['type'] }}" name="{{ $field['key'] }}"
                                        class="form-control" placeholder="{{ $field['label'] }}"
                                        {{ $field['required'] ? 'required' : '' }}>
                                @endif
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-save me-1"></i> Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===== MODAL: EDITAR ===== --}}
<div class="modal fade" id="editRecordModal" tabindex="-1">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background:{{ $config['color'] }};">
                <h5 class="modal-title text-white">
                    <i class="fa fa-pencil me-2"></i> Editar {{ $config['label'] }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editForm" method="POST" onsubmit="this.querySelector('button[type=submit]').disabled=true; this.querySelector('button[type=submit]').innerHTML='<i class=\'fa fa-spinner fa-spin me-1\'></i> Actualizando...';">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row" id="editFields">
                        @foreach($config['fields'] as $field)
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">
                                {{ $field['label'] }}
                                @if($field['required']) <span class="text-danger">*</span> @endif
                            </label>
                            @if($field['type'] === 'select')
                                <select name="{{ $field['key'] }}" id="edit_{{ $field['key'] }}" class="form-control" {{ $field['required'] ? 'required' : '' }}>
                                    <option value="">-- Seleccionar --</option>
                                    @foreach($field['options'] as $opt)
                                        <option value="{{ $opt }}">{{ $opt }}</option>
                                    @endforeach
                                </select>
                            @elseif($field['type'] === 'textarea')
                                <textarea name="{{ $field['key'] }}" id="edit_{{ $field['key'] }}" class="form-control" rows="3"
                                    placeholder="{{ $field['label'] }}"
                                    {{ $field['required'] ? 'required' : '' }}></textarea>
                            @else
                                {{-- Manejo especial de Contraseña (Generador BPM) --}}
                                @if($field['key'] === 'password')
                                    <div class="input-group">
                                        <input type="text" name="{{ $field['key'] }}" id="edit_{{ $field['key'] }}"
                                            class="form-control" placeholder="Escriba o genere clave"
                                            {{ $field['required'] ? 'required' : '' }}>
                                        <button class="btn btn-outline-secondary" type="button" onclick="generatePwd('edit_{{ $field['key'] }}')" title="Generar Contraseña Segura">
                                            <i class="fa fa-magic"></i>
                                        </button>
                                        <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('edit_{{ $field['key'] }}')" title="Copiar">
                                            <i class="fa fa-copy"></i>
                                        </button>
                                    </div>
                                    
                                {{-- Manejo especial de Correo (Poka-Yoke) --}}
                                @elseif($type === 'email' && $field['key'] === 'address' && !empty($company->domain))
                                    <div class="input-group">
                                        <input type="text" name="{{ $field['key'] }}_prefix" id="edit_{{ $field['key'] }}_prefix"
                                            class="form-control" placeholder="ej. gerencia"
                                            {{ $field['required'] ? 'required' : '' }}>
                                        <span class="input-group-text bg-light text-muted">@</span>
                                        <input type="text" name="{{ $field['key'] }}_domain" id="edit_{{ $field['key'] }}_domain" 
                                            class="form-control" value="{{ $company->domain }}" required>
                                    </div>
                                    <small class="text-muted" style="font-size: 0.75rem;">Puedes editar el dominio si es distinto al principal.</small>
                                
                                {{-- Inputs estándar --}}
                                @else
                                    <input type="{{ $field['type'] === 'email' ? 'text' : $field['type'] }}"
                                        name="{{ $field['key'] }}" id="edit_{{ $field['key'] }}"
                                        class="form-control" placeholder="{{ $field['label'] }}"
                                        {{ $field['required'] ? 'required' : '' }}>
                                @endif
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-save me-1"></i> Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // ---- Buscador rápido ----
    const searchInput = document.getElementById('quickSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const q = this.value.toLowerCase();
            document.querySelectorAll('#tableBody .table-row').forEach(function (row) {
                row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
            });
        });
    }

    // ---- Modal Editar: prellenar campos y Poka-Yoke ----
    const editModal = document.getElementById('editRecordModal');
    if (editModal) {
        editModal.addEventListener('show.bs.modal', function (event) {
            const btn      = event.relatedTarget;
            const recordId = btn.getAttribute('data-record-id') || btn.getAttribute('data-id');
            const data     = JSON.parse(btn.getAttribute('data-record') || btn.getAttribute('data-payload') || '{}');
            const baseUrl  = '{{ url("companies/'.$company->id.'/services/'.$type.'") }}/' + recordId;
            document.getElementById('editForm').action = baseUrl;

            // Prellenar cada campo
            Object.keys(data).forEach(function(key) {
                const el = document.getElementById('edit_' + key);
                if (el) {
                    if (el.tagName === 'SELECT') {
                        // Si la opción no existe (ej. estado "Cerrado" importado), la agregamos dinámicamente
                        let optionExists = Array.from(el.options).some(opt => opt.value === data[key]);
                        if (!optionExists && data[key]) {
                            el.add(new Option(data[key], data[key]));
                        }
                    }
                    el.value = data[key] || '';
                }
                
                // Especial para Poka-Yoke de email
                const prefixInput = document.getElementById('edit_' + key + '_prefix');
                const domainInput = document.getElementById('edit_' + key + '_domain');
                if(prefixInput && data[key]) {
                    const parts = data[key].split('@');
                    prefixInput.value = parts[0] || '';
                    if (domainInput && parts.length > 1) {
                        domainInput.value = parts[1] || '';
                    }
                }
            });
        });
    }
    
    // ---- Toggle contraseñas ----
    document.querySelectorAll('.toggle-pass').forEach(btn => {
        btn.addEventListener('click', function() {
            const container = this.closest('div');
            const mask = container.querySelector('.pass-mask');
            const text = container.querySelector('.pass-text');
            const icon = this.querySelector('i');
            
            if(mask.classList.contains('d-none')) {
                mask.classList.remove('d-none');
                text.classList.add('d-none');
                icon.className = 'fa fa-eye';
            } else {
                mask.classList.add('d-none');
                text.classList.remove('d-none');
                icon.className = 'fa fa-eye-slash';
            }
        });
    });

    // ---- Prevenir XSS: Botones de copia seguros ----
    document.querySelectorAll('.btn-copy-clipboard').forEach(btn => {
        btn.addEventListener('click', function() {
            const val = this.getAttribute('data-clipboard');
            navigator.clipboard.writeText(val).then(() => {
                if (typeof toastr !== 'undefined') toastr.success('Copiado al portapapeles');
                else alert('Copiado al portapapeles');
            });
        });
    });
});

// BPM: Auto-resaltado desde Búsqueda
window.addEventListener('load', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const highlightId = urlParams.get('highlight');
    if (highlightId) {
        const row = document.getElementById('row_' + highlightId);
        if (row) {
            row.style.transition = 'background-color 2s';
            row.style.backgroundColor = '#fff3cd'; // color warning de bootstrap
            row.scrollIntoView({ behavior: 'smooth', block: 'center' });
            setTimeout(() => { row.style.backgroundColor = ''; }, 3000);
        }
    }
});

// BPM: Generador de Contraseña (Libre de Modulo Bias)
function generatePwd(inputId) {
    const chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^*";
    let pwd = "";
    const randomValues = new Uint32Array(12);
    window.crypto.getRandomValues(randomValues);
    for (let i = 0; i < 12; i++) {
        // Se remueve el Modulo Bias dividiendo entre el máximo valor posible + 1 (4294967296)
        pwd += chars.charAt(Math.floor((randomValues[i] / 4294967296.0) * chars.length));
    }
    document.getElementById(inputId).value = pwd;
    if (typeof toastr !== 'undefined') {
        toastr.info('Contraseña segura generada');
    }
}

// BPM: Copiar del Input
function copyToClipboard(inputId) {
    const input = document.getElementById(inputId);
    navigator.clipboard.writeText(input.value).then(() => {
        if (typeof toastr !== 'undefined') {
            toastr.success('Copiado al portapapeles');
        } else {
            alert('Copiado al portapapeles');
        }
    });
}

// BPM: Toggle Rápido de Estado
function toggleStatus(recordId, companyId, type, isChecked) {
    const newStatus = isChecked ? 'Activo' : 'Suspendido';
    
    fetch(`{{ url('') }}/companies/${companyId}/services/${type}/${recordId}/toggle-status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ status: newStatus })
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            // Actualizar Label Visual
            const label = document.getElementById('statusLabel_' + recordId);
            if(isChecked) {
                label.innerHTML = '<span class="badge bg-success"><i class="fa fa-circle me-1" style="font-size:8px;"></i>Activo</span>';
            } else {
                label.innerHTML = '<span class="badge bg-danger"><i class="fa fa-ban me-1"></i>Suspendido</span>';
            }
            if (typeof toastr !== 'undefined') toastr.success('Estado actualizado correctamente (BPM)');
        } else {
            if (typeof toastr !== 'undefined') toastr.error('Error al actualizar el estado');
            else alert('Error al actualizar el estado');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (typeof toastr !== 'undefined') toastr.error('Error de conexión');
    });
}
</script>
@endsection
