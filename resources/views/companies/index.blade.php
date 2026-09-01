@extends('layouts.app')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Gestión de Empresas (Clientes)</h4>
            <p class="mb-0">Administra las empresas asociadas a Infortech.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCompanyModal">
            <i class="fa fa-plus me-1"></i> Registrar Empresa
        </button>
    </div>
</div>

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <strong><i class="fa fa-exclamation-triangle me-2"></i> Error:</strong>
    <ul class="mb-0 mt-1">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    <i class="fa fa-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Empresas Registradas</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table header-border table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>RUC</th>
                                <th>Dominio</th>
                                <th>Email Contacto</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($companies as $company)
                            <tr>
                                <td><strong>{{ $company->name }}</strong></td>
                                <td>{{ $company->tax_id }}</td>
                                <td>{{ $company->domain ?? 'N/A' }}</td>
                                <td>{{ $company->contact_email ?? 'N/A' }}</td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('companies.show', $company->id) }}" class="btn btn-primary btn-sm shadow" title="Ver Panel">
                                            <i class="fa fa-eye"></i> Panel
                                        </a>
                                        <button class="btn btn-info btn-sm shadow text-white" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editCompanyModal"
                                                data-id="{{ $company->id }}"
                                                data-name="{{ $company->name }}"
                                                data-tax_id="{{ $company->tax_id }}"
                                                data-domain="{{ $company->domain }}"
                                                data-email="{{ $company->contact_email }}"
                                                data-allowed_services="{{ json_encode($company->allowed_services) }}"
                                                title="Editar">
                                            <i class="fa fa-pencil"></i>
                                        </button>
                                        @if(auth()->user()->role === 'SuperAdmin')
                                        <button type="button" class="btn btn-danger btn-sm shadow" title="Eliminar" onclick="openHardDeleteModal({{ $company->id }}, '{{ addslashes($company->name) }}')">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                        <form id="delete-company-form-{{ $company->id }}" action="{{ route('companies.destroy', $company->id) }}" method="POST" class="d-none">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="fa fa-building-o fa-3x mb-3 text-light"></i>
                                    <h5>No hay empresas registradas</h5>
                                    <p class="mb-0">Registra tu primera empresa haciendo clic en "Registrar Empresa".</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $companies->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL AGREGAR --}}
<div class="modal fade" id="addCompanyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white">Registrar Empresa</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('companies.store') }}" method="POST" onsubmit="if(!this.checkValidity()){ alert('Por favor complete todos los campos obligatorios (Nombre y RUC).'); return false; } this.querySelector('button[type=submit]').disabled=true; this.querySelector('button[type=submit]').innerHTML='<i class=\'fa fa-spinner fa-spin me-1\'></i> Guardando...';">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre de la Empresa <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required placeholder="Ej. Infortech S.A.C">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">RUC / Tax ID <span class="text-danger">*</span></label>
                        <input type="text" name="tax_id" class="form-control" required placeholder="Ej. 20123456789">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Dominio Principal</label>
                        <input type="text" name="domain" class="form-control" placeholder="Ej. infortech.com">
                        <small class="text-muted">Se usará para autogenerar enlaces como mail.dominio.com</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Servicios Disponibles</label>
                        <div class="row">
                            @foreach(\App\Models\ServiceRecord::typeConfig() as $type => $config)
                            <div class="col-md-6 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="allowed_services[]" value="{{ $type }}" id="svc_modal_create_{{ $type }}" checked>
                                    <label class="form-check-label" for="svc_modal_create_{{ $type }}" style="font-size: 0.9em;">
                                        <i class="fa {{ $config['icon'] }} me-1" style="width: 15px; text-align: center;"></i> {{ $config['label'] }}
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL EDITAR --}}
<div class="modal fade" id="editCompanyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title text-white">Editar Empresa</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editCompanyForm" method="POST" onsubmit="if(!this.checkValidity()){ alert('Por favor complete todos los campos obligatorios (Nombre y RUC).'); return false; } this.querySelector('button[type=submit]').disabled=true; this.querySelector('button[type=submit]').innerHTML='<i class=\'fa fa-spinner fa-spin me-1\'></i> Actualizando...';">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre de la Empresa <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="edit_company_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">RUC / Tax ID <span class="text-danger">*</span></label>
                        <input type="text" name="tax_id" id="edit_company_tax_id" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Dominio Principal</label>
                        <input type="text" name="domain" id="edit_company_domain" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Email de Contacto</label>
                        <input type="email" name="contact_email" id="edit_company_email" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Servicios Disponibles</label>
                        <div class="row" id="edit_company_services_container">
                            @foreach(\App\Models\ServiceRecord::typeConfig() as $type => $config)
                            <div class="col-md-6 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input edit-service-checkbox" type="checkbox" name="allowed_services[]" value="{{ $type }}" id="svc_modal_edit_{{ $type }}">
                                    <label class="form-check-label" for="svc_modal_edit_{{ $type }}" style="font-size: 0.9em;">
                                        <i class="fa {{ $config['icon'] }} me-1" style="width: 15px; text-align: center;"></i> {{ $config['label'] }}
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="alert alert-warning">
                        <i class="fa fa-warning me-2"></i> Si cambias el dominio, los enlaces automáticos en el panel cambiarán.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-info text-white">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL HARD DELETE POKA-YOKE --}}
<div class="modal fade" id="hardDeleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-danger">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white"><i class="fa fa-exclamation-triangle me-2"></i> Zona de Peligro</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Estás a punto de eliminar la empresa <strong id="hardDeleteCompanyName" class="text-danger"></strong>.</p>
                <p class="text-muted small">Esto deshabilitará y ocultará la empresa junto con todos sus servicios, correos, accesos remotos y contraseñas (Soft Delete por seguridad).</p>
                <div class="mt-4">
                    <label class="form-label fw-bold">Escribe <span class="text-danger">ELIMINAR</span> para confirmar:</label>
                    <input type="text" id="hardDeleteConfirmInput" class="form-control text-center text-uppercase" placeholder="ELIMINAR" autocomplete="off">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="hardDeleteConfirmBtn" disabled onclick="executeHardDelete()">Confirmar Eliminación</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const editModal = document.getElementById('editCompanyModal');
        if (editModal) {
            editModal.addEventListener('show.bs.modal', function (event) {
                const btn = event.relatedTarget;
                document.getElementById('editCompanyForm').action = '{{ url("companies") }}/' + btn.getAttribute('data-id');
                document.getElementById('edit_company_name').value = btn.getAttribute('data-name');
                document.getElementById('edit_company_tax_id').value = btn.getAttribute('data-tax_id');
                document.getElementById('edit_company_domain').value = btn.getAttribute('data-domain');
                document.getElementById('edit_company_email').value = btn.getAttribute('data-email');
                
                const allowedServicesStr = btn.getAttribute('data-allowed_services');
                let allowedServices = null;
                if (allowedServicesStr && allowedServicesStr !== "null") {
                    try {
                        allowedServices = JSON.parse(allowedServicesStr);
                    } catch(e) {}
                }
                
                document.querySelectorAll('.edit-service-checkbox').forEach(cb => {
                    if (allowedServices === null) {
                        cb.checked = true;
                    } else {
                        cb.checked = allowedServices.includes(cb.value);
                    }
                });
            });
        }
        
        // Lógica Poka-Yoke Hard Delete
        const confirmInput = document.getElementById('hardDeleteConfirmInput');
        if (confirmInput) {
            confirmInput.addEventListener('input', function() {
                document.getElementById('hardDeleteConfirmBtn').disabled = (this.value.trim().toUpperCase() !== 'ELIMINAR');
            });
        }
    });

    let currentDeleteId = null;
    function openHardDeleteModal(companyId, companyName) {
        currentDeleteId = companyId;
        document.getElementById('hardDeleteCompanyName').innerText = companyName;
        document.getElementById('hardDeleteConfirmInput').value = '';
        document.getElementById('hardDeleteConfirmBtn').disabled = true;
        new bootstrap.Modal(document.getElementById('hardDeleteModal')).show();
    }

    function executeHardDelete() {
        if (currentDeleteId) {
            const btn = document.getElementById('hardDeleteConfirmBtn');
            btn.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i> Eliminando...';
            btn.disabled = true;
            document.getElementById('delete-company-form-' + currentDeleteId).submit();
        }
    }
</script>
@endsection
