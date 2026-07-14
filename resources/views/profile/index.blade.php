@extends('layouts.app')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4><i class="fa fa-user-circle-o me-2 text-primary"></i> Mi Perfil</h4>
            <p class="mb-0">Gestiona tu información personal y seguridad.</p>
        </div>
    </div>
</div>

{{-- Alertas --}}
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
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fa fa-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row">
    <div class="col-xl-6 col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-light">
                <h4 class="card-title text-primary mb-0"><i class="fa fa-id-card-o me-2"></i> Actualizar Datos</h4>
            </div>
            <div class="card-body">
                <div class="basic-form">
                    <form action="{{ route('profile.update') }}" method="POST" onsubmit="this.querySelector('button[type=submit]').disabled=true; this.querySelector('button[type=submit]').innerHTML='<i class=\'fa fa-spinner fa-spin me-2\'></i> Guardando...';">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark">Nombre Completo <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fa fa-user text-muted"></i></span>
                                <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark">Email <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fa fa-envelope text-muted"></i></span>
                                <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 shadow-sm" style="background: linear-gradient(135deg, #5e72e4, #825ee4); border:none;">
                            <i class="fa fa-save me-1"></i> Guardar Cambios
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-6 col-lg-6">
        <div class="card shadow-sm border-0" style="border-left: 4px solid #f5365c !important;">
            <div class="card-header bg-light">
                <h4 class="card-title text-danger mb-0"><i class="fa fa-lock me-2"></i> Cambiar Contraseña</h4>
            </div>
            <div class="card-body">
                <div class="basic-form">
                    <form action="{{ route('profile.password') }}" method="POST" onsubmit="this.querySelector('button[type=submit]').disabled=true; this.querySelector('button[type=submit]').innerHTML='<i class=\'fa fa-spinner fa-spin me-2\'></i> Actualizando...';">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark">Contraseña Actual <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fa fa-key text-muted"></i></span>
                                <input type="password" name="current_password" class="form-control" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark">Nueva Contraseña <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fa fa-shield text-muted"></i></span>
                                <input type="password" name="new_password" class="form-control" required>
                            </div>
                            <small class="text-muted">Debe contener al menos 8 caracteres.</small>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark">Confirmar Nueva Contraseña <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fa fa-check-circle text-muted"></i></span>
                                <input type="password" name="new_password_confirmation" class="form-control" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-danger w-100 shadow-sm" style="background: linear-gradient(135deg, #f5365c, #f56036); border:none;">
                            <i class="fa fa-refresh me-1"></i> Actualizar Contraseña
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
