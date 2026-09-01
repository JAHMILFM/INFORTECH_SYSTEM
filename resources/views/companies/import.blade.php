@extends('layouts.app')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Importar Correos a {{ $company->name }} 📥</h4>
            <p class="mb-0">Carga masiva desde archivo Excel.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <a href="{{ route('companies.show', $company->id) }}" class="btn btn-secondary">
            <i class="fa fa-arrow-left me-1"></i> Volver al Panel
        </a>
    </div>
</div>

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show">
    <i class="fa fa-times-circle me-2"></i>{{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h4 class="card-title text-primary"><i class="fa fa-file-excel-o me-2"></i> Subir Archivo</h4>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fa fa-info-circle me-2"></i> <strong>Importante:</strong> El sistema es inteligente y detectará automáticamente la columna de los correos y las contraseñas.
                    <ul>
                        <li>Archivos soportados: <code>.xlsx</code>, <code>.xls</code></li>
                        <li>Las filas que no contengan un correo válido serán ignoradas automáticamente.</li>
                        <li>Si el correo ya existe en la base de datos, solo se actualizará su contraseña (Upsert).</li>
                    </ul>
                </div>
                
                <form id="importForm" action="{{ route('companies.import.store', $company->id) }}" method="POST" enctype="multipart/form-data" 
                      onsubmit="this.querySelector('button[type=submit]').disabled=true; this.querySelector('button[type=submit]').innerHTML='<i class=\'fa fa-spinner fa-spin me-2\'></i> Importando y Procesando...';">
                    @csrf
                    
                    <div class="mb-4 mt-4 text-center p-5 border rounded" style="background-color: #f8f9fa; border: 2px dashed #e56b0c !important;">
                        <i class="fa fa-cloud-upload fa-4x text-primary mb-3"></i>
                        <h5 class="fw-bold">Arrastra tu archivo aquí o haz clic para seleccionar</h5>
                        <p class="text-muted">Tamaño máximo: 10MB</p>
                        <input class="form-control mt-3 mx-auto" type="file" name="file" id="formFile" accept=".xlsx, .xls, .csv" required style="max-width: 350px;">
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg shadow-sm" style="background: linear-gradient(135deg, #e56b0c 0%, #1f3a6f 100%); border:none;">
                            <i class="fa fa-upload me-2"></i> Iniciar Importación
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
