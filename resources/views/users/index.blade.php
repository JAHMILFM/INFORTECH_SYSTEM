@extends('layouts.app')

@section('page-title', 'Gestión de Usuarios y Accesos')

@section('breadcrumb')
    <span class="mx-2 text-muted">/</span>
    <span class="text-muted">Usuarios</span>
@endsection

@section('content')
<div class="row page-titles mx-0 align-items-center mb-4">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4 class="fw-bold mb-1" style="color: var(--text);">Gestión de Usuarios y Accesos</h4>
            <p class="mb-0 text-muted fs-14">Administra credenciales, cargos profesionales y estados de firma digital del personal técnico.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addUserModal" style="background: linear-gradient(135deg, var(--primary), var(--primary-d)); border: none;">
            <i class="bi bi-person-plus-fill me-1"></i> Nuevo Usuario
        </button>
    </div>
</div>

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4">
    <strong><i class="bi bi-exclamation-triangle-fill me-2"></i> Errores:</strong>
    <ul class="mb-0 mt-2 ps-3 fs-14">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" style="background: rgba(5, 150, 105, 0.15); color: #34d399; border-left: 4px solid #059669 !important;">
    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="background: var(--surface); border: 1px solid var(--border) !important; border-radius: 16px;">
            <div class="card-header py-3 px-4 border-0 d-flex align-items-center justify-content-between" style="background: transparent;">
                <h5 class="card-title mb-0 fw-bold" style="color: var(--text);">Personal Técnico y Accesos</h5>
                <span class="fs-12 text-muted">Total: {{ $users->total() }} usuarios</span>
            </div>
            <div class="card-body px-4 pb-4 pt-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="color: var(--text);">
                        <thead>
                            <tr class="text-muted fs-12 text-uppercase" style="border-bottom: 1px solid var(--border);">
                                <th>Usuario / Cargo</th>
                                <th>Email / Teléfono</th>
                                <th>Rol de Seguridad</th>
                                <th>Firma Digital</th>
                                <th>Fecha Alta</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                            <tr style="border-bottom: 1px solid var(--border);">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3 text-white fw-bold shadow-sm" style="width:42px;height:42px; background: linear-gradient(135deg, var(--primary), #1f3a6f); font-size: 15px;">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <strong class="d-block" style="color: var(--text);">{{ $user->name }}</strong>
                                            <small class="text-muted fs-12">{{ $user->job_title ?: 'Sin cargo especificado' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div><i class="bi bi-envelope me-1 text-muted"></i> {{ $user->email }}</div>
                                    <small class="text-muted fs-11">
                                        <i class="bi bi-telephone me-1"></i> {{ $user->phone ?: 'Sin teléfono' }}
                                    </small>
                                </td>
                                <td>
                                    @if($user->role === 'SuperAdmin')
                                        <span class="badge bg-danger text-uppercase px-2 py-1 fs-11"><i class="bi bi-shield-fill me-1"></i> {{ $user->role }}</span>
                                    @elseif($user->role === 'Soporte')
                                        <span class="badge bg-info text-dark text-uppercase px-2 py-1 fs-11"><i class="bi bi-wrench-adjustable me-1"></i> {{ $user->role }}</span>
                                    @else
                                        <span class="badge bg-secondary text-uppercase px-2 py-1 fs-11">{{ $user->role }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->hasSignature())
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fs-11" title="Firma Oficial Registrada">
                                            <i class="bi bi-patch-check-fill me-1"></i> Firma Activa
                                        </span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-muted border border-secondary-subtle px-2 py-1 fs-11">
                                            <i class="bi bi-dash-circle me-1"></i> Sin Firma
                                        </span>
                                    @endif
                                </td>
                                <td class="fs-13 text-muted">{{ $user->created_at->format('d/m/Y') }}</td>
                                <td class="text-end">
                                    <div class="d-flex gap-2 justify-content-end">
                                        <button class="btn btn-outline-info btn-sm shadow-sm" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editUserModal"
                                                data-id="{{ $user->id }}"
                                                data-name="{{ $user->name }}"
                                                data-email="{{ $user->email }}"
                                                data-role="{{ $user->role }}"
                                                data-job-title="{{ $user->job_title }}"
                                                data-phone="{{ $user->phone }}"
                                                title="Editar Usuario">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        @if(auth()->id() !== $user->id)
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar el acceso a este usuario?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm shadow-sm" title="Revocar Acceso">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-people fs-1 mb-2 d-block text-muted"></i>
                                    <h5>No hay usuarios registrados</h5>
                                    <p class="mb-0 fs-13">Agrega el primer usuario haciendo clic en "Nuevo Usuario".</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $users->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Agregar Usuario --}}
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: var(--surface); color: var(--text); border: 1px solid var(--border); border-radius: 14px;">
            <div class="modal-header py-3 px-4" style="background: linear-gradient(135deg, var(--primary), var(--primary-d)); border:none;">
                <h5 class="modal-title text-white fw-bold"><i class="bi bi-person-plus-fill me-2"></i> Nuevo Usuario</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('users.store') }}" method="POST" onsubmit="this.querySelector('button[type=submit]').disabled=true; this.querySelector('button[type=submit]').innerHTML='<i class=\'bi bi-arrow-repeat spin me-1\'></i> Guardando...';">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold fs-13">Nombre Completo <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required placeholder="Ej. Juan Pérez" style="background: var(--surface2); color: var(--text); border-color: var(--border);">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold fs-13">Email Corporativo <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" required placeholder="usuario@infortech.pe" style="background: var(--surface2); color: var(--text); border-color: var(--border);">
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-7">
                            <label class="form-label fw-semibold fs-13">Cargo / Especialidad</label>
                            <input type="text" name="job_title" class="form-control" placeholder="Ej. Técnico Especialista TI" style="background: var(--surface2); color: var(--text); border-color: var(--border);">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-semibold fs-13">Teléfono</label>
                            <input type="text" name="phone" class="form-control" placeholder="987654321" style="background: var(--surface2); color: var(--text); border-color: var(--border);">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold fs-13">Contraseña Segura <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" name="password" id="userPassword" class="form-control" required autocomplete="new-password" placeholder="Mínimo 8 caracteres" style="background: var(--surface2); color: var(--text); border-color: var(--border);">
                            <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('userPassword').type = document.getElementById('userPassword').type === 'password' ? 'text' : 'password'" title="Ver/Ocultar" style="border-color: var(--border);"><i class="bi bi-eye"></i></button>
                            <button type="button" class="btn btn-outline-primary" onclick="generateUserPwd('userPassword')" title="Generar Contraseña Segura" style="border-color: var(--border);"><i class="bi bi-magic"></i></button>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-semibold fs-13">Nivel de Acceso (Rol) <span class="text-danger">*</span></label>
                        <select name="role" class="form-control" required style="background: var(--surface2); color: var(--text); border-color: var(--border);">
                            <option value="Soporte" selected>Soporte (Gestión técnica y reportes)</option>
                            <option value="SuperAdmin">SuperAdmin (Acceso total e irrestricto)</option>
                            <option value="Ventas">Ventas (Solo lectura)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer py-3 px-4" style="border-top: 1px solid var(--border);">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, var(--primary), var(--primary-d)); border:none;">Crear Usuario</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Editar Usuario --}}
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: var(--surface); color: var(--text); border: 1px solid var(--border); border-radius: 14px;">
            <div class="modal-header py-3 px-4" style="background: linear-gradient(135deg, #1f3a6f, #2c5282); border:none;">
                <h5 class="modal-title text-white fw-bold"><i class="bi bi-pencil-square me-2"></i> Editar Usuario</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editUserForm" method="POST" onsubmit="this.querySelector('button[type=submit]').disabled=true; this.querySelector('button[type=submit]').innerHTML='<i class=\'bi bi-arrow-repeat spin me-1\'></i> Actualizando...';">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold fs-13">Nombre Completo <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="edit_user_name" class="form-control" required style="background: var(--surface2); color: var(--text); border-color: var(--border);">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold fs-13">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="edit_user_email" class="form-control" required style="background: var(--surface2); color: var(--text); border-color: var(--border);">
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-7">
                            <label class="form-label fw-semibold fs-13">Cargo / Especialidad</label>
                            <input type="text" name="job_title" id="edit_user_job_title" class="form-control" style="background: var(--surface2); color: var(--text); border-color: var(--border);">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-semibold fs-13">Teléfono</label>
                            <input type="text" name="phone" id="edit_user_phone" class="form-control" style="background: var(--surface2); color: var(--text); border-color: var(--border);">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold fs-13">Nueva Contraseña (Opcional)</label>
                        <input type="password" name="password" class="form-control" placeholder="Dejar en blanco para conservar actual" style="background: var(--surface2); color: var(--text); border-color: var(--border);">
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-semibold fs-13">Nivel de Acceso (Rol) <span class="text-danger">*</span></label>
                        <select name="role" id="edit_user_role" class="form-control" required style="background: var(--surface2); color: var(--text); border-color: var(--border);">
                            <option value="Soporte">Soporte</option>
                            <option value="SuperAdmin">SuperAdmin</option>
                            <option value="Ventas">Ventas</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer py-3 px-4" style="border-top: 1px solid var(--border);">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #1f3a6f, #2c5282); border:none;">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const editModal = document.getElementById('editUserModal');
    if (editModal) {
        editModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const name = button.getAttribute('data-name');
            const email = button.getAttribute('data-email');
            const role = button.getAttribute('data-role');
            const jobTitle = button.getAttribute('data-job-title') || '';
            const phone = button.getAttribute('data-phone') || '';
            
            editModal.querySelector('form').action = `{{ url('users') }}/${id}`;
            editModal.querySelector('input[name="name"]').value = name;
            editModal.querySelector('input[name="email"]').value = email;
            editModal.querySelector('select[name="role"]').value = role;
            editModal.querySelector('input[name="job_title"]').value = jobTitle;
            editModal.querySelector('input[name="phone"]').value = phone;
        });
    }
});

function generateUserPwd(inputId) {
    const chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^*";
    let pwd = "";
    const randomValues = new Uint32Array(12);
    window.crypto.getRandomValues(randomValues);
    for (let i = 0; i < 12; i++) {
        pwd += chars.charAt(Math.floor((randomValues[i] / 4294967296.0) * chars.length));
    }
    const input = document.getElementById(inputId);
    input.value = pwd;
    input.type = 'text';
}
</script>
@endsection
