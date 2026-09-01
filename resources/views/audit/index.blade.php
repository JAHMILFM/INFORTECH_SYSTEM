@extends('layouts.app')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Historial de Auditoría 🕵️‍♂️</h4>
            <p class="mb-0">Registro inmutable de actividades en el sistema.</p>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Eventos Recientes</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered table-striped align-middle text-sm" style="font-size: 0.85rem;">
                        <thead class="thead-dark">
                            <tr>
                                <th>Fecha y Hora</th>
                                <th>Usuario</th>
                                <th>Acción</th>
                                <th>Módulo (ID)</th>
                                <th>Dirección IP</th>
                                <th>Detalle / Cambios</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                            <tr>
                                <td class="text-nowrap">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                                <td>
                                    @if($log->user)
                                        <strong>{{ $log->user->name }}</strong>
                                    @else
                                        <span class="text-muted">Sistema / Desconocido</span>
                                    @endif
                                </td>
                                <td>
                                    @if($log->action === 'CREATED')
                                        <span class="badge badge-success">CREACIÓN</span>
                                    @elseif($log->action === 'UPDATED')
                                        <span class="badge badge-warning">ACTUALIZACIÓN</span>
                                    @elseif($log->action === 'DELETED')
                                        <span class="badge badge-danger">ELIMINACIÓN</span>
                                    @else
                                        <span class="badge badge-secondary">{{ $log->action }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($log->company_id && !$log->service_record_id)
                                        <span class="badge badge-primary">Empresa</span>
                                        <small>(ID: {{ $log->company_id }})</small>
                                    @elseif($log->service_record_id)
                                        <span class="badge badge-info">Servicio</span>
                                        <small>(ID: {{ $log->service_record_id }})</small>
                                    @elseif($log->target_user_id)
                                        <span class="badge badge-dark">Usuario</span>
                                        <small>(ID: {{ $log->target_user_id }})</small>
                                    @else
                                        <span class="badge badge-secondary">Desconocido</span>
                                    @endif
                                </td>
                                <td>{{ $log->ip_address }}</td>
                                <td>
                                    <button type="button" class="btn btn-xs btn-outline-dark" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#logModal{{ $log->id }}">
                                        <i class="fa fa-eye"></i> Ver Datos
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fa fa-history fa-3x mb-3 text-light"></i>
                                    <h5>No hay registros de auditoría</h5>
                                    <p class="mb-0">Aún no se han realizado acciones en el sistema.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-3">
                    {{ $logs->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Render modals here at the bottom of the DOM to prevent Bootstrap flickering inside tables --}}
@php
if (!function_exists('flattenChanges')) {
    function flattenChanges($old, $new, $prefix = '') {
        $changes = [];
        $allKeys = array_unique(array_merge(is_array($old) ? array_keys($old) : [], is_array($new) ? array_keys($new) : []));
        foreach ($allKeys as $key) {
            $oldVal = is_array($old) && array_key_exists($key, $old) ? $old[$key] : null;
            $newVal = is_array($new) && array_key_exists($key, $new) ? $new[$key] : null;
            $fullKey = $prefix ? $prefix . '.' . $key : $key;
            
            if (is_array($oldVal) || is_array($newVal)) {
                $changes = array_merge($changes, flattenChanges(is_array($oldVal) ? $oldVal : [], is_array($newVal) ? $newVal : [], $fullKey));
            } else {
                if (in_array(strtolower($key), ['password', 'pwd'])) {
                    $oldVal = $oldVal ? '********' : null;
                    $newVal = $newVal ? '********' : null;
                }
                if ($oldVal !== $newVal || ($oldVal !== null && $newVal === null) || ($oldVal === null && $newVal !== null)) {
                    $changes[$fullKey] = ['old' => $oldVal, 'new' => $newVal];
                }
            }
        }
        return $changes;
    }
}
@endphp

@foreach($logs as $log)
<div class="modal fade" id="logModal{{ $log->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalle del Evento #{{ $log->id }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <h6 class="border-bottom pb-2 mb-3"><i class="fa fa-list-alt me-2"></i> Registro de Cambios</h6>
                        @php
                            $old = is_array($log->old_data) ? $log->old_data : [];
                            $new = is_array($log->new_data) ? $log->new_data : [];
                            $changes = flattenChanges($old, $new);
                        @endphp

                        <ul class="list-group list-group-flush" style="font-size: 0.9rem;">
                            @if(empty($changes))
                                <li class="list-group-item text-muted">No se registraron cambios específicos en este evento.</li>
                            @else
                                @foreach($changes as $key => $vals)
                                    @php
                                        $label = ucwords(str_replace(['_', '.'], ' ', $key));
                                        if (str_starts_with(strtolower($label), 'data ')) {
                                            $label = substr($label, 5);
                                        }
                                        $oldV = $vals['old'] === null ? 'N/A' : (string)$vals['old'];
                                        $newV = $vals['new'] === null ? 'N/A' : (string)$vals['new'];
                                    @endphp

                                    @if($log->action === 'CREATED')
                                        <li class="list-group-item border-0 px-0 pb-1">
                                            <i class="fa fa-plus-circle text-success me-2"></i> Se registró <strong>{{ $label }}</strong> con el valor: <span class="bg-light px-2 py-1 rounded border">{{ $newV }}</span>
                                        </li>
                                    @elseif($log->action === 'DELETED')
                                        <li class="list-group-item border-0 px-0 pb-1">
                                            <i class="fa fa-minus-circle text-danger me-2"></i> Se eliminó <strong>{{ $label }}</strong> (era: <span class="bg-light px-2 py-1 rounded border text-decoration-line-through">{{ $oldV }}</span>)
                                        </li>
                                    @elseif($log->action === 'UPDATED')
                                        <li class="list-group-item border-0 px-0 pb-1">
                                            <i class="fa fa-exchange-alt text-warning me-2"></i> <strong>{{ $label }}</strong> cambió de <span class="bg-light px-2 py-1 rounded border text-muted text-break">{{ $oldV }}</span> a <span class="bg-warning-light px-2 py-1 rounded border font-weight-bold text-dark text-break">{{ $newV }}</span>
                                        </li>
                                    @endif
                                @endforeach
                            @endif
                        </ul>
                    </div>
                </div>
                <hr>
                <small class="text-muted">User Agent: {{ $log->user_agent }}</small>
            </div>
        </div>
    </div>
</div>
@endforeach

@endsection
