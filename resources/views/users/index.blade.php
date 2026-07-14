@extends('layouts.app')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Gestión de Usuarios y Accesos</h4>
            <p class="mb-0">Administra quién tiene acceso al sistema Infortech.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <button class="btn btn-primary shadow" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="fa fa-user-plus me-1"></i> Nuevo Usuario
        </button>
    </div>
</div>

@if($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
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
            <div class="card-header border-0 pb-0">
                <h4 class="card-title">Usuarios Registrados</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Rol de Seguridad</th>
                                <th>Fecha Creación</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-3" style="width:40px;height:40px;">
                                            <i class="fa fa-user text-primary"></i>
                                        </div>
                                        <strong>{{ $user->name }}</strong>
                                    </div>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @if($user->role === 'SuperAdmin')
                                        <span class="badge bg-danger text-uppercase"><i class="fa fa-shield me-1"></i> {{ $user->role }}</span>
                                    @elseif($user->role === 'Soporte')
                                        <span class="badge bg-info text-uppercase"><i class="fa fa-wrench me-1"></i> {{ $user->role }}</span>
                                    @else
                                        <span class="badge bg-secondary text-uppercase">{{ $user->role }}</span>
                                    @endif
                                </td>
                                <td>{{ $user->created_at->format('d M, Y') }}</td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-info btn-sm shadow text-white" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editUserModal"
                                                data-id="{{ $user->id }}"
                                                data-name="{{ $user->name }}"
                                                data-email="{{ $user->email }}"
                                                data-role="{{ $user->role }}"
                                                title="Editar Usuario">
                                            <i class="fa fa-pencil"></i>
                                        </button>
                                        @if(auth()->id() !== $user->id)
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar el acceso a este usuario?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm shadow" title="Revocar Acceso">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="fa fa-users fa-3x mb-3 text-light"></i>
                                    <h5>No hay usuarios registrados</h5>
                                    <p class="mb-0">Agrega el primer usuario haciendo clic en "Nuevo Usuario".</p>
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

{{-- Modal Agregar --}}
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white">Nuevo Usuario</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('users.store') }}" method="POST" onsubmit="this.querySelector('button[type=submit]').disabled=true; this.querySelector('button[type=submit]').innerHTML='<i class=\'fa fa-spinner fa-spin me-1\'></i> Guardando...';">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Email de Acceso</label>
                        <input type="email" name="email" class="form-control" required autocomplete="off">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Contraseña Segura</label>
                        <div class="input-group">
                            <input type="password" name="password" id="userPassword" class="form-control" required autocomplete="new-password">
                            <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('userPassword').type = document.getElementById('userPassword').type === 'password' ? 'text' : 'password'" title="Ver/Ocultar"><i class="fa fa-eye"></i></button>
                            <button type="button" class="btn btn-outline-primary" onclick="generateUserPwd('userPassword')" title="Generar Contraseña Segura"><i class="fa fa-magic"></i></button>
                            <button type="button" class="btn btn-outline-success" onclick="navigator.clipboard.writeText(document.getElementById('userPassword').value).then(() => { if(typeof toastr !== 'undefined') toastr.success('Contraseña copiada'); })" title="Copiar"><i class="fa fa-copy"></i></button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nivel de Acceso (Rol)</label>
                        <select name="role" class="form-control" required>
                            <option value="Soporte" selected>Soporte (Puede ver y editar contraseñas, no borrar empresas)</option>
                            <option value="SuperAdmin">SuperAdmin (Acceso total e irrestricto)</option>
                            <option value="Ventas">Ventas (Solo lectura)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Crear Accesos</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Editar --}}
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title text-white">Editar Usuario</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editUserForm" method="POST" onsubmit="this.querySelector('button[type=submit]').disabled=true; this.querySelector('button[type=submit]').innerHTML='<i class=\'fa fa-spinner fa-spin me-1\'></i> Actualizando...';">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre</label>
                        <input type="text" name="name" id="edit_user_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Email</label>
                        <input type="email" name="email" id="edit_user_email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nueva Contraseña (Opcional)</label>
                        <input type="password" name="password" class="form-control" placeholder="Dejar en blanco para no cambiar">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nivel de Acceso (Rol)</label>
                        <select name="role" id="edit_user_role" class="form-control" required>
                            <option value="Soporte">Soporte</option>
                            <option value="SuperAdmin">SuperAdmin</option>
                            <option value="Ventas">Ventas</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-info text-white">Guardar Cambios</button>
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
            
            editModal.querySelector('form').action = `{{ url('users') }}/${id}`;
            editModal.querySelector('input[name="name"]').value = name;
            editModal.querySelector('input[name="email"]').value = email;
            editModal.querySelector('select[name="role"]').value = role;
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
    input.type = 'text'; // Mostrarla al generarla
}
</script>
@endsection
