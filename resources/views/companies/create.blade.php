@extends('layouts.app')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Registrar Empresa</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-6 col-lg-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Datos del Cliente</h4>
            </div>
            <div class="card-body">
                <div class="basic-form">
                    <form action="{{ route('companies.store') }}" method="POST" onsubmit="if(!this.checkValidity()){ alert('Por favor complete todos los campos obligatorios (Nombre y RUC).'); return false; }">
                        @csrf
                        <div class="form-group">
                            <label>Nombre de la Empresa <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="form-group mt-3">
                            <label>RUC / Tax ID <span class="text-danger">*</span></label>
                            <input type="text" name="tax_id" class="form-control" required placeholder="Ej. 20123456789">
                        </div>
                        <div class="form-group">
                            <label>Dominio Web (Opcional)</label>
                            <input type="text" name="domain" class="form-control" placeholder="Ej. coca-cola.com">
                        </div>
                        <div class="form-group">
                            <label>Email de Contacto (Opcional)</label>
                            <input type="email" name="contact_email" class="form-control" placeholder="contacto@empresa.com">
                        </div>
                        <div class="form-group mt-3">
                            <label class="form-label fw-bold">Servicios Disponibles para la Empresa</label>
                            <div class="row">
                                @foreach(\App\Models\ServiceRecord::typeConfig() as $type => $config)
                                <div class="col-md-6 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="allowed_services[]" value="{{ $type }}" id="svc_create_{{ $type }}" checked>
                                        <label class="form-check-label" for="svc_create_{{ $type }}">
                                            <i class="fa {{ $config['icon'] }} me-1"></i> {{ $config['label'] }}
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <small class="text-muted">Si desmarcas un servicio, se ocultará del panel de esta empresa.</small>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3">Guardar Empresa</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
