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
                    <form action="{{ route('companies.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label>Nombre de la Empresa</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Dominio Web (Opcional)</label>
                            <input type="text" name="domain" class="form-control" placeholder="Ej. coca-cola.com">
                        </div>
                        <div class="form-group">
                            <label>Email de Contacto (Opcional)</label>
                            <input type="email" name="contact_email" class="form-control" placeholder="contacto@empresa.com">
                        </div>
                        <button type="submit" class="btn btn-primary mt-3">Guardar Empresa</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
