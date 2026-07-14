@extends('layouts.app')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>¡Hola, {{ auth()->user()->name ?? 'Admin' }}! 👋</h4>
            <p class="mb-0">Bienvenido al panel de control de Infortech.</p>
        </div>
    </div>
</div>

<div class="row">
    {{-- Total Empresas --}}
    <div class="col-xl-3 col-lg-6 col-sm-6">
        <div class="widget-stat card" style="background:linear-gradient(135deg,#5e72e4,#825ee4);">
            <div class="card-body p-4">
                <div class="media ai-icon">
                    <span class="me-3 bgl-light text-white" style="background:rgba(255,255,255,0.2);">
                        <i class="flaticon-381-heart"></i>
                    </span>
                    <div class="media-body text-white text-end">
                        <p class="mb-1 text-white">Total Clientes</p>
                        <h3 class="text-white">{{ $companiesCount }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Total Correos --}}
    <div class="col-xl-3 col-lg-6 col-sm-6">
        <div class="widget-stat card" style="background:linear-gradient(135deg,#11cdef,#1171ef);">
            <div class="card-body p-4">
                <div class="media ai-icon">
                    <span class="me-3 bgl-light text-white" style="background:rgba(255,255,255,0.2);">
                        <i class="fa fa-envelope-o"></i>
                    </span>
                    <div class="media-body text-white text-end">
                        <p class="mb-1 text-white">Correos Totales</p>
                        <h3 class="text-white">{{ $totalEmails }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Correos Activos --}}
    <div class="col-xl-3 col-lg-6 col-sm-6">
        <div class="widget-stat card" style="background:linear-gradient(135deg,#2dce89,#2dcecc);">
            <div class="card-body p-4">
                <div class="media ai-icon">
                    <span class="me-3 bgl-light text-white" style="background:rgba(255,255,255,0.2);">
                        <i class="fa fa-check-circle-o"></i>
                    </span>
                    <div class="media-body text-white text-end">
                        <p class="mb-1 text-white">Correos Activos</p>
                        <h3 class="text-white">{{ $activeEmails }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Correos Suspendidos --}}
    <div class="col-xl-3 col-lg-6 col-sm-6">
        <a href="{{ route('search', ['q' => 'Suspendido']) }}" class="text-decoration-none">
            <div class="widget-stat card" style="background:linear-gradient(135deg,#f5365c,#f56036); cursor: pointer; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'">
                <div class="card-body p-4">
                    <div class="media ai-icon">
                        <span class="me-3 bgl-light text-white" style="background:rgba(255,255,255,0.2);">
                            <i class="fa fa-ban"></i>
                        </span>
                        <div class="media-body text-white text-end">
                            <p class="mb-1 text-white">Correos Suspendidos</p>
                            <h3 class="text-white">{{ $suspendedEmails }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

<div class="row">
    {{-- Últimos clientes agregados --}}
    <div class="col-xl-8 col-lg-8">
        <div class="card">
            <div class="card-header border-0 pb-0">
                <h4 class="card-title">Últimos Clientes Agregados</h4>
                <a href="{{ route('companies.index') }}" class="btn btn-primary btn-sm">Ver Todos</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-responsive-sm mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>Nombre de Empresa</th>
                                <th>Dominio</th>
                                <th>Fecha de Registro</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentCompanies as $comp)
                            <tr>
                                <td><h6 class="mb-0">{{ $comp->name }}</h6></td>
                                <td>{{ $comp->domain ?? 'N/A' }}</td>
                                <td><span class="badge badge-light">{{ $comp->created_at->format('d M Y') }}</span></td>
                                <td><a href="{{ route('companies.show', $comp->id) }}" class="btn btn-outline-primary btn-sm">Panel</a></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4">No hay empresas registradas aún.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Resumen Adicional --}}
    <div class="col-xl-4 col-lg-4">
        <div class="card">
            <div class="card-header border-0 pb-0">
                <h4 class="card-title">Resumen General</h4>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-4 align-items-center">
                    <div class="d-flex align-items-center">
                        <span class="bgl-primary me-3" style="width:40px;height:40px;display:flex;align-items:center;justify-content:center;border-radius:10px;">
                            <i class="fa fa-list text-primary"></i>
                        </span>
                        <div>
                            <p class="mb-0 fs-14">Total Servicios Extra</p>
                            <h6 class="mb-0">{{ $totalServices - $totalEmails }}</h6>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-between mb-4 align-items-center">
                    <div class="d-flex align-items-center">
                        <span class="bgl-success me-3" style="width:40px;height:40px;display:flex;align-items:center;justify-content:center;border-radius:10px;">
                            <i class="fa fa-server text-success"></i>
                        </span>
                        <div>
                            <p class="mb-0 fs-14">Dominios Activos</p>
                            <h6 class="mb-0">{{ \App\Models\Company::whereNotNull('domain')->count() }}</h6>
                        </div>
                    </div>
                </div>
                <p class="mb-0 text-muted fs-12 text-center mt-4">Sistema CRM Infortech v1.0</p>
            </div>
        </div>
    </div>
</div>
@endsection
