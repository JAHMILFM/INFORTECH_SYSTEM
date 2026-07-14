@extends('layouts.app')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-12 p-md-0">
        <div class="welcome-text">
            <h4>Resultados de Búsqueda</h4>
            <p class="mb-0">Buscando: <strong>"{{ $q }}"</strong></p>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-12">
        @if(empty($q))
            <div class="alert alert-info">Por favor, ingresa un término de búsqueda en la barra superior.</div>
        @else
            {{-- Resultados de Empresas --}}
            <div class="card mb-4">
                <div class="card-header" style="background:linear-gradient(135deg,#5e72e4,#825ee4);">
                    <h4 class="card-title text-white mb-0"><i class="fa fa-building me-2"></i> Empresas Encontradas ({{ $companyResults->count() }})</h4>
                </div>
                <div class="card-body">
                    @if($companyResults->isEmpty())
                        <p class="text-muted mb-0">No se encontraron empresas con ese nombre o dominio.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Dominio</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($companyResults as $company)
                                    <tr>
                                        <td><strong>{{ $company->name }}</strong></td>
                                        <td>{{ $company->domain ?? '—' }}</td>
                                        <td>
                                            <a href="{{ route('companies.show', $company->id) }}" class="btn btn-primary btn-sm">Ir al Panel</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Resultados de Servicios (Correos, etc) --}}
            <div class="card">
                <div class="card-header" style="background:linear-gradient(135deg,#11cdef,#1171ef);">
                    <h4 class="card-title text-white mb-0"><i class="fa fa-envelope me-2"></i> Correos / Servicios Encontrados ({{ $serviceResults->count() }})</h4>
                </div>
                <div class="card-body">
                    @if($serviceResults->isEmpty())
                        <p class="text-muted mb-0">No se encontraron correos, cuentas o servicios con ese texto.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Empresa (Cliente)</th>
                                        <th>Tipo</th>
                                        <th>Datos Encontrados (Coincidencias)</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($serviceResults as $record)
                                    <tr>
                                        <td>
                                            <a href="{{ route('companies.show', $record->company->id) }}" class="fw-bold text-primary">
                                                {{ $record->company->name }}
                                            </a>
                                        </td>
                                        <td><span class="badge badge-light text-uppercase">{{ $record->type }}</span></td>
                                        <td>
                                            <ul class="list-unstyled mb-0" style="font-size:13px;">
                                                @foreach($record->data as $k => $v)
                                                    @if(stripos((string)$v, $q) !== false)
                                                        <li><strong>{{ ucfirst($k) }}:</strong> <span class="bg-warning-light px-1">{{ $v }}</span></li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        </td>
                                        <td>
                                            <a href="{{ route('companies.services.index', [$record->company->id, $record->type]) }}?highlight={{ $record->id }}" class="btn btn-info btn-sm text-white shadow-sm">
                                                <i class="fa fa-external-link me-1"></i> Ir al Registro (BPM)
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
