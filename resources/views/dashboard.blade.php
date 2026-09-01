@extends('layouts.app')
@section('page-title', 'Dashboard')

@section('content')

<!-- Page Header -->
<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h1>¡Hola, {{ auth()->user()->name ?? 'Admin' }}! 👋</h1>
        <p>Bienvenido al panel de control de Infortech · {{ now()->format('l, d M Y') }}</p>
    </div>
    <a href="{{ route('companies.create') }}" class="btn-primary-custom d-none d-md-inline-flex align-items-center gap-2">
        <i class="bi bi-plus-lg"></i> Nuevo Cliente
    </a>
</div>

<!-- ─── STAT CARDS ─── -->
<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="stat-card stat-purple text-white h-100">
            <div class="stat-icon"><i class="bi bi-buildings-fill"></i></div>
            <div class="stat-label">Total Clientes</div>
            <div class="stat-value">{{ $companiesCount }}</div>
            <div class="mt-2" style="font-size:12px; opacity:.7;">
                <i class="bi bi-arrow-up-right"></i> Clientes registrados
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card stat-cyan text-white h-100">
            <div class="stat-icon"><i class="bi bi-envelope-fill"></i></div>
            <div class="stat-label">Correos Totales</div>
            <div class="stat-value">{{ $totalEmails }}</div>
            <div class="mt-2" style="font-size:12px; opacity:.7;">
                <i class="bi bi-info-circle"></i> Todos los servicios de correo
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card stat-green text-white h-100">
            <div class="stat-icon"><i class="bi bi-check-circle-fill"></i></div>
            <div class="stat-label">Correos Activos</div>
            <div class="stat-value">{{ $activeEmails }}</div>
            <div class="mt-2" style="font-size:12px; opacity:.7;">
                <i class="bi bi-circle-fill" style="font-size:8px;"></i> Operativos ahora
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <a href="{{ route('search', ['q' => 'Suspendido']) }}" class="text-decoration-none d-block h-100">
            <div class="stat-card stat-red text-white h-100">
                <div class="stat-icon"><i class="bi bi-slash-circle-fill"></i></div>
                <div class="stat-label">Correos Suspendidos</div>
                <div class="stat-value">{{ $suspendedEmails }}</div>
                <div class="mt-2" style="font-size:12px; opacity:.7;">
                    <i class="bi bi-arrow-right"></i> Ver detalles
                </div>
            </div>
        </a>
    </div>
</div>

<!-- ─── BOTTOM ROW ─── -->
<div class="row g-3">

    <!-- Últimos Clientes -->
    <div class="col-xl-8">
        <div class="card-dark h-100">
            <div class="d-flex align-items-center justify-content-between px-4 py-3"
                 style="border-bottom:1px solid var(--border);">
                <div>
                    <h6 class="mb-0 fw-700" style="font-size:14px;">Últimos Clientes Agregados</h6>
                    <small style="color:var(--text-muted); font-size:12px;">Registros más recientes</small>
                </div>
                <a href="{{ route('companies.index') }}" class="btn-outline-custom">
                    Ver todos <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="p-0">
                <div class="table-responsive">
                    <table class="table table-dark-custom mb-0">
                        <thead>
                            <tr>
                                <th>Empresa</th>
                                <th>Dominio</th>
                                <th>Registro</th>
                                <th style="text-align:right;">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentCompanies as $comp)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div style="width:32px;height:32px;background:linear-gradient(135deg,#e56b0c,#1f3a6f);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:var(--text);flex-shrink:0;">
                                            {{ strtoupper(substr($comp->name, 0, 1)) }}
                                        </div>
                                        <span class="fw-semibold">{{ $comp->name }}</span>
                                    </div>
                                </td>
                                <td>
                                    @if($comp->domain)
                                        <span class="badge-soft-info">{{ $comp->domain }}</span>
                                    @else
                                        <span style="color:var(--text-muted);">—</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge-soft-primary">{{ $comp->created_at->format('d M Y') }}</span>
                                </td>
                                <td style="text-align:right;">
                                    <a href="{{ route('companies.show', $comp->id) }}" class="btn-outline-custom">
                                        <i class="bi bi-eye"></i> Panel
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4">
                                    <div class="empty-state">
                                        <i class="bi bi-inbox"></i>
                                        <p>No hay empresas registradas aún.</p>
                                        <a href="{{ route('companies.create') }}" class="btn-primary-custom">
                                            <i class="bi bi-plus-lg"></i> Crear mi primer Cliente
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Resumen General -->
    <div class="col-xl-4">
        <div class="card-dark h-100">
            <div class="px-4 py-3" style="border-bottom:1px solid var(--border);">
                <h6 class="mb-0 fw-700" style="font-size:14px;">Resumen General</h6>
                <small style="color:var(--text-muted); font-size:12px;">Estadísticas del sistema</small>
            </div>
            <div class="p-4">
                <!-- Metric item -->
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div style="width:42px;height:42px;background:rgba(229,107,12,0.15);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="bi bi-list-task" style="color:var(--primary-l);font-size:18px;"></i>
                    </div>
                    <div>
                        <div style="font-size:12px;color:var(--text-muted);">Total Servicios Extra</div>
                        <div style="font-size:22px;font-weight:800;color:var(--text);">{{ $totalServices - $totalEmails }}</div>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-3 mb-4">
                    <div style="width:42px;height:42px;background:rgba(5,150,105,0.15);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="bi bi-globe" style="color:#059669;font-size:18px;"></i>
                    </div>
                    <div>
                        <div style="font-size:12px;color:var(--text-muted);">Dominios Activos</div>
                        <div style="font-size:22px;font-weight:800;color:var(--text);">{{ \App\Models\Company::whereNotNull('domain')->count() }}</div>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-3 mb-4">
                    <div style="width:42px;height:42px;background:rgba(229,107,12,0.15);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="bi bi-people-fill" style="color:#e56b0c;font-size:18px;"></i>
                    </div>
                    <div>
                        <div style="font-size:12px;color:var(--text-muted);">Usuarios del Sistema</div>
                        <div style="font-size:22px;font-weight:800;color:var(--text);">{{ \App\Models\User::count() }}</div>
                    </div>
                </div>

                <!-- Version badge & Health -->
                <div class="mt-2 pt-3" style="border-top:1px solid var(--border);">
                    <div class="d-flex align-items-center justify-content-between">
                        <span style="font-size:12px;color:var(--text-muted);">Salud de Datos (Dominios)</span>
                        <span class="badge-soft-primary">{{ $systemHealth }}%</span>
                    </div>
                    <div class="mt-1">
                        <div style="height:4px;background:var(--surface2);border-radius:99px;overflow:hidden;">
                            <div style="width:{{ $systemHealth }}%;height:100%;background:linear-gradient(90deg,var(--primary),var(--accent));border-radius:99px; transition: width 1s ease-in-out;"></div>
                        </div>
                        <div class="d-flex justify-content-between mt-1">
                            <span style="font-size:11px;color:var(--text-muted);">Progreso</span>
                            <span style="font-size:11px;color:var(--primary-l);">{{ $systemHealth }}% Completado</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
