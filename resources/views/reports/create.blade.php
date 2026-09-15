@extends('layouts.app')

@section('page-title', 'Nuevo Reporte FOR-TI-001')

@section('breadcrumb')
    <i class="bi bi-chevron-right mx-1"></i>
    <a href="{{ route('reports.index') }}" style="color: var(--text-muted); text-decoration: none;">Reportes Técnicos</a>
    <i class="bi bi-chevron-right mx-1"></i>
    <span>Nuevo Formateo (FOR-TI-001)</span>
@endsection

@section('content')
<div class="row page-titles mx-0 mb-4 align-items-center">
    <div class="col-sm-8 p-md-0">
        <div class="welcome-text">
            <h4 class="mb-1" style="color: var(--text);">Formato de Servicio Técnico — FOR-TI-001</h4>
            <p class="mb-0 text-muted">Constancia oficial de formateo, configuración y entrega de equipos de cómputo.</p>
        </div>
    </div>
    <div class="col-sm-4 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex align-items-center gap-2">
        <div class="badge px-3 py-2 fs-13" style="background: rgba(229,107,12,0.15); color: var(--primary); border: 1px solid rgba(229,107,12,0.3); border-radius: 8px;">
            <i class="bi bi-tag-fill me-1"></i> Código: <strong>{{ $nextCode }}</strong>
        </div>
        <a href="{{ route('reports.index') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
    </div>
</div>

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 12px;">
    <strong><i class="bi bi-exclamation-triangle-fill me-2"></i> Por favor revisa los siguientes errores:</strong>
    <ul class="mb-0 mt-2">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<form action="{{ route('reports.store') }}" method="POST" id="reportForm">
    @csrf
    <input type="hidden" name="delivery_signature_data" id="delivery_signature_data">
    <input type="hidden" name="reception_signature_data" id="reception_signature_data">

    <!-- SECCIÓN A: DATOS DEL CLIENTE -->
    <div class="card mb-4" style="background: var(--surface); border: 1px solid var(--border); border-radius: 14px;">
        <div class="card-header py-3 px-4 d-flex align-items-center justify-content-between" style="border-bottom: 1px solid var(--border);">
            <div class="d-flex align-items-center gap-2">
                <div class="step-num" style="width: 28px; height: 28px; border-radius: 50%; background: var(--primary); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 700;">A</div>
                <h5 class="card-title mb-0" style="color: var(--text);">Datos de la Empresa Cliente</h5>
            </div>
            <span class="fs-12 text-muted"><i class="bi bi-building me-1"></i> Prestador del servicio: INFORTECH</span>
        </div>
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold" style="color: var(--text);">Empresa Cliente <span class="text-danger">*</span></label>
                    <select name="company_id" id="company_select" class="form-select" required style="background: var(--surface); color: var(--text); border-color: var(--border);">
                        <option value="">-- Seleccionar Empresa --</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}" 
                                    data-ruc="{{ $company->tax_id }}"
                                    data-branch="{{ $company->branch }}"
                                    data-area="{{ $company->area }}"
                                    data-contact="{{ $company->contact_name }}"
                                    data-phone="{{ $company->contact_phone }}"
                                    data-email="{{ $company->contact_email }}"
                                    {{ (old('company_id', $selectedCompanyId) == $company->id) ? 'selected' : '' }}>
                                {{ $company->name }} (RUC: {{ $company->tax_id }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label text-muted fs-13">RUC (Autocompletado)</label>
                    <input type="text" id="client_ruc" class="form-control" readonly style="background: rgba(255,255,255,0.05); color: var(--text); border-color: var(--border);">
                </div>
                <div class="col-md-3">
                    <label class="form-label text-muted fs-13">Sede / Local</label>
                    <input type="text" name="client_branch" id="client_branch" class="form-control" value="{{ old('client_branch') }}" placeholder="Ej. Principal - Lima" style="background: var(--surface); color: var(--text); border-color: var(--border);">
                </div>
                <div class="col-md-4">
                    <label class="form-label text-muted fs-13">Área / Departamento</label>
                    <input type="text" name="client_area" id="client_area" class="form-control" value="{{ old('client_area') }}" placeholder="Ej. Contabilidad, Finanzas" style="background: var(--surface); color: var(--text); border-color: var(--border);">
                </div>
                <div class="col-md-4">
                    <label class="form-label text-muted fs-13">Persona de Contacto</label>
                    <input type="text" name="client_contact" id="client_contact" class="form-control" value="{{ old('client_contact') }}" placeholder="Nombre y Apellidos" style="background: var(--surface); color: var(--text); border-color: var(--border);">
                </div>
                <div class="col-md-4">
                    <label class="form-label text-muted fs-13">Teléfono / Correo de Contacto</label>
                    <input type="text" name="client_phone" id="client_phone" class="form-control" value="{{ old('client_phone') }}" placeholder="Teléfono o correo" style="background: var(--surface); color: var(--text); border-color: var(--border);">
                </div>
            </div>
        </div>
    </div>

    <!-- SECCIÓN B: DATOS DEL EQUIPO -->
    <div class="card mb-4" style="background: var(--surface); border: 1px solid var(--border); border-radius: 14px;">
        <div class="card-header py-3 px-4 d-flex align-items-center justify-content-between" style="border-bottom: 1px solid var(--border);">
            <div class="d-flex align-items-center gap-2">
                <div class="step-num" style="width: 28px; height: 28px; border-radius: 50%; background: var(--primary); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 700;">B</div>
                <h5 class="card-title mb-0" style="color: var(--text);">Datos del Equipo y Fecha del Servicio</h5>
            </div>
            <div class="d-flex gap-3">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="equipment_option" id="opt_existing" value="existing" checked onchange="toggleEquipmentMode()">
                    <label class="form-check-label fs-13" for="opt_existing" style="color: var(--text);">Equipo Existente</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="equipment_option" id="opt_new" value="new" onchange="toggleEquipmentMode()">
                    <label class="form-check-label fs-13" for="opt_new" style="color: var(--text);">Registrar Nuevo Equipo</label>
                </div>
            </div>
        </div>
        <div class="card-body p-4">
            <!-- Modo 1: Seleccionar Existente -->
            <div id="existing_equipment_wrap" class="mb-3">
                <label class="form-label fw-semibold" style="color: var(--text);">Seleccionar Equipo Registrado <span class="text-danger">*</span></label>
                <select name="equipment_id" id="equipment_select" class="form-select" style="background: var(--surface); color: var(--text); border-color: var(--border);">
                    <option value="">-- Selecciona primero una empresa --</option>
                </select>
                <small class="text-muted mt-1 d-block">Si el equipo no está en la lista, selecciona arriba <em>"Registrar Nuevo Equipo"</em> para agregarlo automáticamente al catálogo.</small>
            </div>

            <!-- Modo 2: Registrar Nuevo Equipo -->
            <div id="new_equipment_wrap" style="display: none;">
                <div class="alert alert-info py-2 px-3 mb-3 fs-13 d-flex align-items-center gap-2" style="border-radius: 8px;">
                    <i class="bi bi-info-circle"></i>
                    Este equipo se registrará automáticamente en la base de datos de la empresa seleccionada para futuros mantenimientos.
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold" style="color: var(--text);">Tipo de Equipo <span class="text-danger">*</span></label>
                        <select name="equipment_type" id="new_eq_type" class="form-select" style="background: var(--surface); color: var(--text); border-color: var(--border);">
                            <option value="laptop">Laptop</option>
                            <option value="desktop">Desktop</option>
                            <option value="server">Servidor</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold" style="color: var(--text);">Marca <span class="text-danger">*</span></label>
                        <input type="text" name="equipment_brand" id="new_eq_brand" class="form-control" placeholder="Ej. Lenovo, HP, Dell" style="background: var(--surface); color: var(--text); border-color: var(--border);">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold" style="color: var(--text);">Modelo <span class="text-danger">*</span></label>
                        <input type="text" name="equipment_model" id="new_eq_model" class="form-control" placeholder="Ej. ThinkPad E14 Gen 4" style="background: var(--surface); color: var(--text); border-color: var(--border);">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold" style="color: var(--text);">Número de Serie <span class="text-danger">*</span></label>
                        <input type="text" name="equipment_serial" id="new_eq_serial" class="form-control text-uppercase" placeholder="Ej. PF3X889K" style="background: var(--surface); color: var(--text); border-color: var(--border);">
                    </div>
                </div>
            </div>

            <hr style="border-color: var(--border);">

            <!-- Campos vitales comunes de la Sección B -->
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold" style="color: var(--text);">Sistema Operativo Instalado <span class="text-danger">*</span></label>
                    <input type="text" name="equipment_os" id="equipment_os" class="form-control" required value="{{ old('equipment_os', 'Windows 11 Pro 64-bit') }}" placeholder="Ej. Windows 11 Pro 64-bit, Ubuntu 24.04 LTS" style="background: var(--surface); color: var(--text); border-color: var(--border);">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold" style="color: var(--text);">Fecha del Servicio <span class="text-danger">*</span></label>
                    <input type="date" name="service_date" class="form-control" required value="{{ old('service_date', date('Y-m-d')) }}" style="background: var(--surface); color: var(--text); border-color: var(--border);">
                </div>
            </div>

            <!-- Ficha de Hardware Vital -->
            <div class="row g-3 mt-1">
                <div class="col-md-4">
                    <label class="form-label fw-semibold" style="color: var(--text);">Procesador (CPU)</label>
                    <input type="text" name="equipment_processor" id="equipment_processor" class="form-control" value="{{ old('equipment_processor') }}" placeholder="Ej. Intel Core i5-1135G7 @ 2.40GHz" style="background: var(--surface); color: var(--text); border-color: var(--border);">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold" style="color: var(--text);">Memoria RAM</label>
                    <input type="text" name="equipment_ram" id="equipment_ram" class="form-control" value="{{ old('equipment_ram') }}" placeholder="Ej. 16 GB DDR4 3200MHz" style="background: var(--surface); color: var(--text); border-color: var(--border);">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold" style="color: var(--text);">Almacenamiento (Disco)</label>
                    <input type="text" name="equipment_storage" id="equipment_storage" class="form-control" value="{{ old('equipment_storage') }}" placeholder="Ej. 512 GB SSD NVMe M.2" style="background: var(--surface); color: var(--text); border-color: var(--border);">
                </div>
            </div>
        </div>
    </div>

    <!-- SECCIÓN B.2: DIAGNÓSTICO INICIAL Y ESTADO DE ENTREGA -->
    <div class="card mb-4" style="background: var(--surface); border: 1px solid var(--border); border-radius: 14px;">
        <div class="card-header py-3 px-4 d-flex align-items-center justify-content-between" style="border-bottom: 1px solid var(--border);">
            <div class="d-flex align-items-center gap-2">
                <div class="step-num" style="width: 28px; height: 28px; border-radius: 50%; background: var(--primary); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 700;">B.2</div>
                <h5 class="card-title mb-0" style="color: var(--text);">Diagnóstico Inicial y Estado de Operatividad Final</h5>
            </div>
            <span class="badge px-3 py-1" style="background: rgba(16,185,129,0.15); color: #34d399; border: 1px solid rgba(16,185,129,0.3); border-radius: 6px;">
                <i class="bi bi-clipboard2-pulse me-1"></i> Control de Calidad
            </span>
        </div>
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold" style="color: var(--text);">
                        <i class="bi bi-exclamation-triangle-fill text-warning me-1"></i> Motivo del Servicio / Diagnóstico Inicial (Síntoma)
                    </label>
                    <textarea name="initial_diagnosis" id="initial_diagnosis" rows="3" class="form-control" placeholder="Ej. Equipo lento, presencia de software no deseado o solicitud de formateo e instalación limpia." style="background: var(--surface); color: var(--text); border-color: var(--border);">{{ old('initial_diagnosis') }}</textarea>
                    <small class="text-muted">Describe cómo ingresó o qué falla presentaba el equipo.</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold" style="color: var(--text);">
                        <i class="bi bi-check-circle-fill text-success me-1"></i> Estado Final de Operatividad y Pruebas Realizadas
                    </label>
                    <textarea name="final_state" id="final_state" rows="3" class="form-control" placeholder="Ej. Sistema operativo instalado en limpio y activado, controladores actualizados, pruebas de temperatura y operatividad superadas al 100%." style="background: var(--surface); color: var(--text); border-color: var(--border);">{{ old('final_state') }}</textarea>
                    <small class="text-muted">Detalla el resultado de las pruebas técnicas antes de la entrega.</small>
                </div>
            </div>
        </div>
    </div>

    <!-- SECCIÓN C: USUARIO Y ACCESOS -->
    <div class="card mb-4" style="background: var(--surface); border: 1px solid var(--border); border-radius: 14px;">
        <div class="card-header py-3 px-4 d-flex align-items-center justify-content-between" style="border-bottom: 1px solid var(--border);">
            <div class="d-flex align-items-center gap-2">
                <div class="step-num" style="width: 28px; height: 28px; border-radius: 50%; background: var(--primary); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 700;">C</div>
                <h5 class="card-title mb-0" style="color: var(--text);">Usuario Asignado y Accesos al Equipo</h5>
            </div>
            <span class="badge px-3 py-1" style="background: rgba(220,38,38,0.15); color: #f87171; border: 1px solid rgba(220,38,38,0.3); border-radius: 6px;">
                <i class="bi bi-shield-lock-fill me-1"></i> Confidencial
            </span>
        </div>
        <div class="card-body p-4">
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold" style="color: var(--text);">Nombre del Usuario Final</label>
                    <input type="text" name="user_name" class="form-control" value="{{ old('user_name') }}" placeholder="Ej. Carlos Mendoza" style="background: var(--surface); color: var(--text); border-color: var(--border);">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold" style="color: var(--text);">Usuario de Acceso (Login / Windows)</label>
                    <input type="text" name="user_login" class="form-control" value="{{ old('user_login') }}" placeholder="Ej. cmendoza / Administrador" style="background: var(--surface); color: var(--text); border-color: var(--border);">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold" style="color: var(--text);">Hostname del Equipo</label>
                    <input type="text" name="hostname" id="eq_hostname" class="form-control" value="{{ old('hostname') }}" placeholder="Ej. LENOVO-CORP-01" style="background: var(--surface); color: var(--text); border-color: var(--border);">
                </div>
            </div>

            <div class="p-3 mb-2" style="background: rgba(255,255,255,0.02); border: 1px solid var(--border); border-radius: 10px;">
                <div class="row align-items-center g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold mb-1" style="color: var(--text);">¿Credenciales Configuradas?</label>
                        <select name="credentials_configured" id="credentials_configured" class="form-select" onchange="togglePasswordInput()" style="background: var(--surface); color: var(--text); border-color: var(--border);">
                            <option value="yes" selected>Sí (Equipo con usuario y clave)</option>
                            <option value="no">No (Sin contraseña / acceso libre)</option>
                        </select>
                        <div class="fs-12 text-muted mt-1">
                            <i class="bi bi-lock-fill text-warning me-1"></i>
                            <strong>Regla de Seguridad:</strong> La clave se guardará cifrada en la base de datos y <u>NUNCA</u> saldrá impresa en el reporte.
                        </div>
                    </div>
                    <div class="col-md-6" id="password_input_col">
                        <label class="form-label fw-semibold mb-1" style="color: var(--text);">Contraseña de Acceso Configurada</label>
                        <div class="input-group">
                            <input type="password" name="access_password" id="access_password" class="form-control" placeholder="Ingresa la clave temporal o definitiva" style="background: var(--surface); color: var(--text); border-color: var(--border);">
                            <button type="button" class="btn btn-outline-secondary" onclick="togglePassVisibility()">
                                <i class="bi bi-eye" id="pass_eye_icon"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SECCIÓN C.2: CONTROL Y ENTREGA DE ACCESORIOS -->
    <div class="card mb-4" style="background: var(--surface); border: 1px solid var(--border); border-radius: 14px;">
        <div class="card-header py-3 px-4 d-flex align-items-center justify-content-between" style="border-bottom: 1px solid var(--border);">
            <div class="d-flex align-items-center gap-2">
                <div class="step-num" style="width: 28px; height: 28px; border-radius: 50%; background: var(--primary); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 700;">C.2</div>
                <h5 class="card-title mb-0" style="color: var(--text);">Control y Entrega de Accesorios</h5>
            </div>
            <span class="badge px-3 py-1" style="background: rgba(59,130,246,0.15); color: #60a5fa; border: 1px solid rgba(59,130,246,0.3); border-radius: 6px;">
                <i class="bi bi-box-seam me-1"></i> Inventario de Entrega
            </span>
        </div>
        <div class="card-body p-4">
            <p class="text-muted fs-13 mb-3">Marca los accesorios recibidos o entregados junto con el equipo para garantizar la conformidad:</p>
            <div class="row g-3 mb-3">
                <div class="col-sm-6 col-md-3">
                    <div class="form-check p-2 rounded" style="background: rgba(255,255,255,0.02); border: 1px solid var(--border);">
                        <input class="form-check-input ms-1" type="checkbox" name="accessories[charger]" id="acc_charger" value="1" {{ old('accessories.charger') ? 'checked' : '' }}>
                        <label class="form-check-label ms-2 fw-semibold fs-13" for="acc_charger" style="color: var(--text);">
                            <i class="bi bi-plug-fill text-warning me-1"></i> Cargador / Adaptador
                        </label>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <div class="form-check p-2 rounded" style="background: rgba(255,255,255,0.02); border: 1px solid var(--border);">
                        <input class="form-check-input ms-1" type="checkbox" name="accessories[power_cable]" id="acc_power" value="1" {{ old('accessories.power_cable') ? 'checked' : '' }}>
                        <label class="form-check-label ms-2 fw-semibold fs-13" for="acc_power" style="color: var(--text);">
                            <i class="bi bi-lightning-charge-fill text-primary me-1"></i> Cable de poder
                        </label>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <div class="form-check p-2 rounded" style="background: rgba(255,255,255,0.02); border: 1px solid var(--border);">
                        <input class="form-check-input ms-1" type="checkbox" name="accessories[bag]" id="acc_bag" value="1" {{ old('accessories.bag') ? 'checked' : '' }}>
                        <label class="form-check-label ms-2 fw-semibold fs-13" for="acc_bag" style="color: var(--text);">
                            <i class="bi bi-backpack text-info me-1"></i> Funda / Mochila
                        </label>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <div class="form-check p-2 rounded" style="background: rgba(255,255,255,0.02); border: 1px solid var(--border);">
                        <input class="form-check-input ms-1" type="checkbox" name="accessories[mouse]" id="acc_mouse" value="1" {{ old('accessories.mouse') ? 'checked' : '' }}>
                        <label class="form-check-label ms-2 fw-semibold fs-13" for="acc_mouse" style="color: var(--text);">
                            <i class="bi bi-mouse text-secondary me-1"></i> Mouse / Otros
                        </label>
                    </div>
                </div>
            </div>
            <div>
                <label class="form-label text-muted fs-13">Detalles o accesorios adicionales</label>
                <input type="text" name="accessories_notes" id="accessories_notes" class="form-control" value="{{ old('accessories_notes') }}" placeholder="Ej. Mouse inalámbrico Logitech, docking station USB-C, adaptador HDMI..." style="background: var(--surface); color: var(--text); border-color: var(--border);">
            </div>
        </div>
    </div>

    <!-- SECCIÓN D: PROGRAMAS INSTALADOS -->
    <div class="card mb-4" style="background: var(--surface); border: 1px solid var(--border); border-radius: 14px;">
        <div class="card-header py-3 px-4 d-flex align-items-center justify-content-between" style="border-bottom: 1px solid var(--border);">
            <div class="d-flex align-items-center gap-2">
                <div class="step-num" style="width: 28px; height: 28px; border-radius: 50%; background: var(--primary); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 700;">D</div>
                <h5 class="card-title mb-0" style="color: var(--text);">Programas y Aplicaciones Instaladas</h5>
            </div>
            <a href="{{ route('software-catalog.index') }}" target="_blank" class="fs-12 text-muted text-decoration-none">
                <i class="bi bi-gear-fill me-1"></i> Administrar Catálogo
            </a>
        </div>
        <div class="card-body p-4">
            <p class="text-muted fs-13 mb-3">
                Marca los programas que se instalaron en el equipo. En el reporte final se detallará la lista completa de software verificado.
            </p>

            @if(isset($baselines) && $baselines->count() > 0)
            <div class="p-3 mb-4 rounded-3 border" style="background: rgba(99,102,241,0.05); border-color: rgba(99,102,241,0.25) !important;">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                    <span class="fw-bold fs-13 text-primary d-flex align-items-center gap-2">
                        <i class="bi bi-magic me-1"></i> Carga Rápida por Perfil (Software Baseline):
                    </span>
                    <button type="button" class="btn btn-link btn-sm p-0 text-muted fs-12 text-decoration-none" onclick="clearAllSoftware()">
                        <i class="bi bi-x-circle me-1"></i> Desmarcar todo
                    </button>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    @foreach($baselines as $b)
                    <button type="button" class="btn btn-sm btn-outline-primary fw-semibold d-inline-flex align-items-center gap-2 rounded-pill px-3 py-1"
                            data-ids='@json($b->software_ids ?? [])'
                            data-name="{{ $b->name }}"
                            onclick="applyBaseline(this)">
                        <i class="bi {{ $b->icon ?: 'bi-briefcase-fill' }}"></i> {{ $b->name }}
                    </button>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="row g-4">
                @foreach($softwareCatalog as $category => $programs)
                <div class="col-md-6">
                    <div class="p-3 h-100" style="background: rgba(255,255,255,0.02); border: 1px solid var(--border); border-radius: 10px;">
                        <h6 class="fw-bold mb-1 d-flex align-items-center gap-2" style="color: var(--primary);">
                            <i class="bi bi-collection-fill"></i> {{ $category ?: 'General' }}
                        </h6>
                        @if($programs->first()->description)
                            <p class="text-muted fs-11 mb-3" style="line-height: 1.3;">{{ $programs->first()->description }}</p>
                        @else
                            <div class="mb-3"></div>
                        @endif
                        <div class="d-flex flex-column gap-2">
                            @foreach($programs as $prog)
                            <div class="program-item p-2 rounded" style="background: rgba(255,255,255,0.03);">
                                <div class="form-check d-flex align-items-center justify-content-between">
                                    <div>
                                        <input class="form-check-input prog-check" type="checkbox" 
                                               name="software[{{ $prog->id }}]" value="1" id="soft_{{ $prog->id }}"
                                               data-requires-detail="{{ ($prog->requires_detail || $prog->default_version) ? '1' : '0' }}"
                                               onchange="toggleSoftDetail({{ $prog->id }})"
                                               {{ in_array($prog->name, ['Microsoft Office (Paquete Completo)', 'Microsoft Teams', 'AnyDesk', 'Google Chrome', 'Adobe Acrobat / Acrobat Reader', 'WinRAR', 'ESET NOD32']) ? 'checked' : '' }}>
                                        <label class="form-check-label ms-1 fw-semibold fs-13" for="soft_{{ $prog->id }}" style="color: var(--text); cursor: pointer;">
                                            {{ $prog->name }}
                                        </label>
                                    </div>
                                    <div class="d-flex align-items-center gap-1">
                                        @if($prog->default_version)
                                            <span class="badge bg-info text-dark fs-10" style="cursor: pointer;" onclick="toggleSoftDetailManual({{ $prog->id }})" title="Versión sugerida / Haz clic para editar">
                                                <i class="bi bi-bookmark-fill me-1"></i>v. {{ $prog->default_version }}
                                            </span>
                                        @elseif($prog->requires_detail)
                                            <span class="badge bg-secondary fs-10" style="cursor: pointer;" onclick="toggleSoftDetailManual({{ $prog->id }})" title="Especificar versión o edición">
                                                Especificar versión
                                            </span>
                                        @else
                                            <button type="button" class="btn btn-link btn-sm p-0 text-muted fs-11 text-decoration-none" onclick="toggleSoftDetailManual({{ $prog->id }})" title="Añadir versión">
                                                <i class="bi bi-plus-circle"></i> Versión
                                            </button>
                                        @endif
                                    </div>
                                </div>

                                <div id="detail_wrap_{{ $prog->id }}" class="mt-2 ps-4" style="display: none;">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text py-0" style="background: rgba(255,255,255,0.05); color: var(--text-muted); font-size: 11px; border-color: var(--border);">
                                            <i class="bi bi-tag me-1"></i> Versión / Detalle:
                                        </span>
                                        <input type="text" name="software_details[{{ $prog->id }}]" class="form-control form-control-sm" 
                                               placeholder="{{ $prog->default_version ? 'Ej. ' . $prog->default_version : 'Ej. 2024 (64-bit), Pro Plus, v11, etc.' }}"
                                               value="{{ $prog->default_version ?? '' }}"
                                               style="background: var(--surface); color: var(--text); border-color: var(--border); font-size: 12px;">
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Programas adicionales / personalizados ("Otro") -->
            <div class="mt-4 p-3" style="background: rgba(255,255,255,0.02); border: 1px dashed var(--border); border-radius: 10px;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <h6 class="fw-bold mb-0" style="color: var(--text);">
                        <i class="bi bi-plus-square-dotted text-primary me-2"></i> Otros Programas Instalados No Listados
                    </h6>
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="addCustomSoftwareRow()">
                        <i class="bi bi-plus-circle me-1"></i> Agregar Otro Programa
                    </button>
                </div>
                <div id="custom_software_container" class="d-flex flex-column gap-2 mt-2">
                    <!-- Filas dinámicas añadidas vía JS -->
                </div>
            </div>
        </div>
    </div>

    <!-- SECCIÓN D.2: TAREAS DE CONFIGURACIÓN -->
    <div class="card mb-4" style="background: var(--surface); border: 1px solid var(--border); border-radius: 14px;">
        <div class="card-header py-3 px-4 d-flex align-items-center gap-2" style="border-bottom: 1px solid var(--border);">
            <div class="step-num" style="width: 28px; height: 28px; border-radius: 50%; background: var(--primary); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 700;">D.2</div>
            <h5 class="card-title mb-0" style="color: var(--text);">Tareas de Configuración y Mantenimiento</h5>
        </div>
        <div class="card-body p-4">
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="p-3 text-center rounded h-100" style="background: rgba(255,255,255,0.02); border: 1px solid var(--border);">
                        <i class="bi bi-cloud-arrow-up fs-2 text-primary d-block mb-2"></i>
                        <label class="form-label fw-semibold fs-13 d-block" style="color: var(--text);">Backup Realizado</label>
                        <select name="backup_done" class="form-select text-center" style="background: var(--surface); color: var(--text); border-color: var(--border);">
                            <option value="yes" selected>Sí</option>
                            <option value="no">No</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3 text-center rounded h-100" style="background: rgba(255,255,255,0.02); border: 1px solid var(--border);">
                        <i class="bi bi-shield-check fs-2 text-success d-block mb-2"></i>
                        <label class="form-label fw-semibold fs-13 d-block" style="color: var(--text);">Antivirus Instalado</label>
                        <select name="antivirus_installed" id="antivirus_installed" class="form-select text-center" onchange="toggleAntivirusName()" style="background: var(--surface); color: var(--text); border-color: var(--border);">
                            <option value="yes" selected>Sí</option>
                            <option value="no">No</option>
                        </select>
                        <input type="text" name="antivirus_name" id="antivirus_name" class="form-control form-control-sm mt-2" placeholder="Nombre (ej. Windows Defender, ESET)" value="Windows Defender" style="background: var(--surface); color: var(--text); border-color: var(--border); font-size: 11.5px;">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3 text-center rounded h-100" style="background: rgba(255,255,255,0.02); border: 1px solid var(--border);">
                        <i class="bi bi-key-fill fs-2 text-warning d-block mb-2"></i>
                        <label class="form-label fw-semibold fs-13 d-block" style="color: var(--text);">Activación de Licencia</label>
                        <select name="license_activated" class="form-select text-center" style="background: var(--surface); color: var(--text); border-color: var(--border);">
                            <option value="yes" selected>Sí (Windows / Office)</option>
                            <option value="no">No</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3 text-center rounded h-100" style="background: rgba(255,255,255,0.02); border: 1px solid var(--border);">
                        <i class="bi bi-cpu-fill fs-2 text-info d-block mb-2"></i>
                        <label class="form-label fw-semibold fs-13 d-block" style="color: var(--text);">Drivers Actualizados</label>
                        <select name="drivers_installed" class="form-select text-center" style="background: var(--surface); color: var(--text); border-color: var(--border);">
                            <option value="yes" selected>Sí</option>
                            <option value="no">No</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SECCIÓN E: CIERRE Y FIRMAS DIGITALES -->
    <div class="card mb-4" style="background: var(--surface); border: 1px solid var(--border); border-radius: 14px;">
        <div class="card-header py-3 px-4 d-flex align-items-center justify-content-between" style="border-bottom: 1px solid var(--border);">
            <div class="d-flex align-items-center gap-2">
                <div class="step-num" style="width: 28px; height: 28px; border-radius: 50%; background: var(--primary); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 700;">E</div>
                <h5 class="card-title mb-0" style="color: var(--text);">Cierre, Observaciones y Firmas de Conformidad</h5>
            </div>
            <span class="fs-12 text-muted"><i class="bi bi-pen me-1"></i> Firmas Digitales en Pantalla</span>
        </div>
        <div class="card-body p-4">
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label fw-semibold" style="color: var(--text);">Técnico Responsable (INFORTECH)</label>
                    <input type="text" name="technician_name" class="form-control" value="{{ old('technician_name', auth()->user()->name) }}" style="background: var(--surface); color: var(--text); border-color: var(--border);">
                    <small class="text-muted">Tomado automáticamente de tu sesión de usuario.</small>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold" style="color: var(--text);">Nombre de Quien Recibe (Cliente)</label>
                    <input type="text" name="receiver_name" class="form-control" value="{{ old('receiver_name') }}" placeholder="Nombre y Apellidos del receptor" style="background: var(--surface); color: var(--text); border-color: var(--border);">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold" style="color: var(--text);">Cargo de Quien Recibe</label>
                    <input type="text" name="receiver_role" class="form-control" value="{{ old('receiver_role') }}" placeholder="Ej. Asistente TI, Gerente de Operaciones" style="background: var(--surface); color: var(--text); border-color: var(--border);">
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold" style="color: var(--text);">Observaciones Finales / Recomendaciones</label>
                    <textarea name="notes" class="form-control" rows="3" placeholder="Ingresa recomendaciones técnicas para el cliente o detalles relevantes del equipo..." style="background: var(--surface); color: var(--text); border-color: var(--border);">{{ old('notes') }}</textarea>
                </div>
            </div>

            <!-- SECCIÓN DE FIRMAS DIGITALES Y CONFORMIDAD -->
            <div class="row g-4 mt-2">
                <!-- Columna 1: Firma Entrega (INFORTECH) -->
                <div class="col-md-6">
                    <div class="p-3 rounded" style="background: rgba(255,255,255,0.02); border: 1px solid var(--border);">
                        <div class="d-flex align-items-center justify-content-between mb-2 pb-2" style="border-bottom: 1px solid var(--border);">
                            <span class="fw-bold fs-13" style="color: var(--primary);">
                                <i class="bi bi-shield-check me-1"></i> ENTREGADO POR (INFORTECH)
                            </span>
                            @if(auth()->user() && auth()->user()->hasSignature())
                                <span class="badge bg-success-subtle text-success border border-success-subtle fs-11">
                                    <i class="bi bi-patch-check-fill me-1"></i> Firma Oficial Lista
                                </span>
                            @endif
                        </div>

                        <!-- Selector de Modo Técnico -->
                        <ul class="nav nav-pills nav-fill mb-2 gap-1" id="deliveryTabs" role="tablist">
                            @if(auth()->user() && auth()->user()->hasSignature())
                            <li class="nav-item">
                                <button class="nav-link active py-1 px-2 fs-11 fw-semibold" id="deliv-profile-tab" data-bs-toggle="pill" data-bs-target="#deliv-profile-pane" type="button" role="tab" onclick="setDeliveryMode('profile')">
                                    <i class="bi bi-person-check-fill me-1"></i> Mi Firma Oficial
                                </button>
                            </li>
                            @endif
                            <li class="nav-item">
                                <button class="nav-link {{ (!auth()->user() || !auth()->user()->hasSignature()) ? 'active' : '' }} py-1 px-2 fs-11 fw-semibold" id="deliv-draw-tab" data-bs-toggle="pill" data-bs-target="#deliv-draw-pane" type="button" role="tab" onclick="setDeliveryMode('draw')">
                                    <i class="bi bi-pen me-1"></i> Trazar con Lápiz
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link py-1 px-2 fs-11 fw-semibold" id="deliv-upload-tab" data-bs-toggle="pill" data-bs-target="#deliv-upload-pane" type="button" role="tab" onclick="setDeliveryMode('upload')">
                                    <i class="bi bi-cloud-arrow-up me-1"></i> Subir Archivo
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content">
                            @if(auth()->user() && auth()->user()->hasSignature())
                            <!-- Modo 1: Firma de Perfil -->
                            <div class="tab-pane fade show active" id="deliv-profile-pane" role="tabpanel">
                                <div class="text-center p-2 rounded" style="background: #ffffff; border: 1px solid #cbd5e1; height: 160px; display: flex; flex-direction: column; align-items: center; justify-content: center; background-image: radial-gradient(rgba(0,0,0,0.06) 1px, transparent 0); background-size: 8px 8px;">
                                    <img src="{{ auth()->user()->signature_data }}" alt="Firma de {{ auth()->user()->name }}" style="max-height: 95px; max-width: 90%; object-fit: contain;">
                                    <div class="mt-1">
                                        <div class="fw-bold fs-12 text-dark">{{ auth()->user()->name }}</div>
                                        <small class="text-muted fs-11">{{ auth()->user()->getJobTitleOrDefault() }}</small>
                                    </div>
                                </div>
                                <small class="text-muted mt-2 d-block fs-11 text-center">Firma oficial vinculada automáticamente a tu perfil.</small>
                            </div>
                            @endif

                            <!-- Modo 2: Trazar Canvas -->
                            <div class="tab-pane fade {{ (!auth()->user() || !auth()->user()->hasSignature()) ? 'show active' : '' }}" id="deliv-draw-pane" role="tabpanel">
                                <div class="d-flex justify-content-end mb-1">
                                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 fs-11" onclick="clearDeliveryCanvas()">
                                        <i class="bi bi-eraser me-1"></i> Limpiar
                                    </button>
                                </div>
                                <div style="background: #ffffff; border-radius: 8px; border: 1px solid #cbd5e1; overflow: hidden; height: 135px; position: relative;">
                                    <canvas id="delivery_canvas" width="450" height="135" style="width: 100%; height: 100%; cursor: crosshair; touch-action: none;"></canvas>
                                </div>
                                <small class="text-muted mt-2 d-block fs-11 text-center">Firma digital trazada con ratón o pantalla táctil.</small>
                            </div>

                            <!-- Modo 3: Subir Imagen -->
                            <div class="tab-pane fade" id="deliv-upload-pane" role="tabpanel">
                                <div class="p-2 text-center rounded position-relative" style="background: rgba(255,255,255,0.02); border: 1px dashed var(--border); cursor: pointer;" onclick="document.getElementById('deliv_file_input').click()">
                                    <input type="file" id="deliv_file_input" accept="image/png,image/jpeg,image/svg+xml,image/webp" class="d-none" onchange="previewDeliveryUpload(this)">
                                    <div id="deliv_upload_empty">
                                        <i class="bi bi-file-earmark-image fs-2 text-primary d-block mb-1"></i>
                                        <span class="fs-12 fw-semibold d-block" style="color: var(--text);">Clic para seleccionar imagen de firma</span>
                                        <small class="text-muted fs-11">PNG o JPG (fondo blanco o transparente)</small>
                                    </div>
                                    <div id="deliv_upload_preview" class="d-none" style="background: #ffffff; border-radius: 6px; padding: 4px; height: 110px; display: flex; align-items: center; justify-content: center;">
                                        <img id="deliv_upload_img" src="" alt="Firma subida" style="max-height: 100px; max-width: 90%; object-fit: contain;">
                                    </div>
                                </div>
                                <small class="text-muted mt-2 d-block fs-11 text-center">Sube un archivo de firma en formato de imagen.</small>
                            </div>
                        </div>

                        @if(!auth()->user() || !auth()->user()->hasSignature())
                        <div class="mt-2 text-center">
                            <a href="{{ route('profile.index') }}" target="_blank" class="fs-11 text-decoration-none" style="color: var(--primary-l);">
                                <i class="bi bi-gear-fill me-1"></i> Guarda tu firma en Mi Perfil para usarla en 1 clic
                            </a>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Columna 2: Firma Recepción (CLIENTE) -->
                <div class="col-md-6">
                    <div class="p-3 rounded" style="background: rgba(255,255,255,0.02); border: 1px solid var(--border);">
                        <div class="d-flex align-items-center justify-content-between mb-2 pb-2" style="border-bottom: 1px solid var(--border);">
                            <span class="fw-bold fs-13" style="color: var(--success);">
                                <i class="bi bi-person-check-fill me-1"></i> RECIBIDO POR (CLIENTE)
                            </span>
                            <span class="text-muted fs-11">Conformidad de entrega</span>
                        </div>

                        <!-- Selector de Modo Cliente -->
                        <ul class="nav nav-pills nav-fill mb-2 gap-1" id="receptionTabs" role="tablist">
                            <li class="nav-item">
                                <button class="nav-link active py-1 px-2 fs-11 fw-semibold" id="rec-draw-tab" data-bs-toggle="pill" data-bs-target="#rec-draw-pane" type="button" role="tab" onclick="setReceptionMode('draw')">
                                    <i class="bi bi-pen me-1"></i> Trazar en Pantalla
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link py-1 px-2 fs-11 fw-semibold" id="rec-upload-tab" data-bs-toggle="pill" data-bs-target="#rec-upload-pane" type="button" role="tab" onclick="setReceptionMode('upload')">
                                    <i class="bi bi-cloud-arrow-up me-1"></i> Subir Firma / Sello
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <!-- Modo 1: Trazar Canvas Cliente -->
                            <div class="tab-pane fade show active" id="rec-draw-pane" role="tabpanel">
                                <div class="d-flex justify-content-end mb-1">
                                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 fs-11" onclick="clearReceptionCanvas()">
                                        <i class="bi bi-eraser me-1"></i> Limpiar
                                    </button>
                                </div>
                                <div style="background: #ffffff; border-radius: 8px; border: 1px solid #cbd5e1; overflow: hidden; height: 135px; position: relative;">
                                    <canvas id="reception_canvas" width="450" height="135" style="width: 100%; height: 100%; cursor: crosshair; touch-action: none;"></canvas>
                                </div>
                                <small class="text-muted mt-2 d-block fs-11 text-center">Firma de conformidad del cliente receptor.</small>
                            </div>

                            <!-- Modo 2: Subir Sello o Firma Cliente -->
                            <div class="tab-pane fade" id="rec-upload-pane" role="tabpanel">
                                <div class="p-2 text-center rounded position-relative" style="background: rgba(255,255,255,0.02); border: 1px dashed var(--border); cursor: pointer;" onclick="document.getElementById('rec_file_input').click()">
                                    <input type="file" id="rec_file_input" accept="image/png,image/jpeg,image/svg+xml,image/webp" class="d-none" onchange="previewReceptionUpload(this)">
                                    <div id="rec_upload_empty">
                                        <i class="bi bi-patch-check fs-2 text-success d-block mb-1"></i>
                                        <span class="fs-12 fw-semibold d-block" style="color: var(--text);">Clic para seleccionar sello o firma</span>
                                        <small class="text-muted fs-11">PNG o JPG escaneado</small>
                                    </div>
                                    <div id="rec_upload_preview" class="d-none" style="background: #ffffff; border-radius: 6px; padding: 4px; height: 110px; display: flex; align-items: center; justify-content: center;">
                                        <img id="rec_upload_img" src="" alt="Firma cliente subida" style="max-height: 100px; max-width: 90%; object-fit: contain;">
                                    </div>
                                </div>
                                <small class="text-muted mt-2 d-block fs-11 text-center">Sube la firma o sello digitalizado del cliente.</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer py-3 px-4 d-flex justify-content-between align-items-center" style="border-top: 1px solid var(--border);">
            <button type="button" class="btn btn-outline-secondary" onclick="window.history.back()">
                Cancelar
            </button>
            <div class="d-flex gap-2">
                <button type="submit" name="status" value="draft" class="btn btn-warning px-4 d-flex align-items-center gap-2">
                    <i class="bi bi-file-earmark"></i> Guardar como Borrador
                </button>
                <button type="submit" name="status" value="confirmed" class="btn btn-primary px-4 d-flex align-items-center gap-2 shadow">
                    <i class="bi bi-check-circle-fill"></i> Confirmar y Generar Reporte
                </button>
            </div>
        </div>
    </div>
</form>

<script>
    // 1. Manejo dinámico de Empresa y Equipos
    const companySelect = document.getElementById('company_select');
    const rucInput = document.getElementById('client_ruc');
    const branchInput = document.getElementById('client_branch');
    const areaInput = document.getElementById('client_area');
    const contactInput = document.getElementById('client_contact');
    const phoneInput = document.getElementById('client_phone');
    const equipmentSelect = document.getElementById('equipment_select');

    companySelect.addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        if (this.value) {
            rucInput.value = selected.getAttribute('data-ruc') || '';
            branchInput.value = selected.getAttribute('data-branch') || '';
            areaInput.value = selected.getAttribute('data-area') || '';
            contactInput.value = selected.getAttribute('data-contact') || '';
            phoneInput.value = selected.getAttribute('data-phone') || '';

            // Cargar equipos registrados vía AJAX
            fetch(`/reports/api/equipment/${this.value}`)
                .then(res => res.json())
                .then(data => {
                    equipmentSelect.innerHTML = '<option value="">-- Seleccionar Equipo Registrado --</option>';
                    if (data.equipment && data.equipment.length > 0) {
                        data.equipment.forEach(eq => {
                            const opt = document.createElement('option');
                            opt.value = eq.id;
                            opt.textContent = `${eq.display_name} - S.O: ${eq.os || 'N/A'}`;
                            opt.setAttribute('data-hostname', eq.hostname || '');
                            opt.setAttribute('data-os', eq.os || '');
                            opt.setAttribute('data-processor', eq.processor || '');
                            opt.setAttribute('data-ram', eq.ram || '');
                            opt.setAttribute('data-storage', eq.storage || '');
                            equipmentSelect.appendChild(opt);
                        });
                    } else {
                        equipmentSelect.innerHTML = '<option value="">(No hay equipos registrados para este cliente)</option>';
                    }
                })
                .catch(err => console.error('Error cargando equipos:', err));
        } else {
            rucInput.value = '';
            equipmentSelect.innerHTML = '<option value="">-- Selecciona primero una empresa --</option>';
        }
    });

    equipmentSelect.addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        if (!selected) return;
        const os = selected.getAttribute('data-os');
        const host = selected.getAttribute('data-hostname');
        const proc = selected.getAttribute('data-processor');
        const ram = selected.getAttribute('data-ram');
        const disk = selected.getAttribute('data-storage');
        if (os) document.getElementById('equipment_os').value = os;
        if (host) document.getElementById('eq_hostname').value = host;
        if (proc) document.getElementById('equipment_processor').value = proc;
        if (ram) document.getElementById('equipment_ram').value = ram;
        if (disk) document.getElementById('equipment_storage').value = disk;
    });

    // 2. Modo de equipo: Existente vs Nuevo
    function toggleEquipmentMode() {
        const isNew = document.getElementById('opt_new').checked;
        document.getElementById('existing_equipment_wrap').style.display = isNew ? 'none' : 'block';
        document.getElementById('new_equipment_wrap').style.display = isNew ? 'block' : 'none';
        
        document.getElementById('equipment_select').required = !isNew;
        document.getElementById('new_eq_type').required = isNew;
        document.getElementById('new_eq_brand').required = isNew;
        document.getElementById('new_eq_model').required = isNew;
        document.getElementById('new_eq_serial').required = isNew;
    }

    // 3. Credenciales y Contraseña
    function togglePasswordInput() {
        const configured = document.getElementById('credentials_configured').value === 'yes';
        document.getElementById('password_input_col').style.display = configured ? 'block' : 'none';
    }

    function togglePassVisibility() {
        const input = document.getElementById('access_password');
        const icon = document.getElementById('pass_eye_icon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'bi bi-eye-slash';
        } else {
            input.type = 'password';
            icon.className = 'bi bi-eye';
        }
    }

    // 4. Detalle y versiones de programas instalados
    function toggleSoftDetail(softId) {
        const check = document.getElementById(`soft_${softId}`);
        const wrap = document.getElementById(`detail_wrap_${softId}`);
        if (wrap) {
            wrap.style.display = check.checked ? 'block' : 'none';
        }
    }

    function toggleSoftDetailManual(softId) {
        const wrap = document.getElementById(`detail_wrap_${softId}`);
        const check = document.getElementById(`soft_${softId}`);
        if (wrap) {
            const isHidden = (wrap.style.display === 'none' || wrap.style.display === '');
            wrap.style.display = isHidden ? 'block' : 'none';
            if (check && isHidden) {
                check.checked = true;
            }
        }
    }

    // 5. Antivirus
    function toggleAntivirusName() {
        const isInstalled = document.getElementById('antivirus_installed').value === 'yes';
        const input = document.getElementById('antivirus_name');
        input.style.display = isInstalled ? 'block' : 'none';
        if (!isInstalled) input.value = '';
    }

    // 6. Filas de programas personalizados ("Otro")
    let customSoftIndex = 0;
    function addCustomSoftwareRow() {
        customSoftIndex++;
        const container = document.getElementById('custom_software_container');
        const row = document.createElement('div');
        row.className = 'row g-2 align-items-center';
        row.id = `custom_soft_row_${customSoftIndex}`;
        row.innerHTML = `
            <div class="col-md-5">
                <input type="text" name="custom_software_names[]" class="form-control form-control-sm" placeholder="Nombre del programa (ej. Visual Studio Code, SAP Client)" style="background: var(--surface); color: var(--text); border-color: var(--border);">
            </div>
            <div class="col-md-6">
                <input type="text" name="custom_software_details[]" class="form-control form-control-sm" placeholder="Detalle / Versión / Licencia (opcional)" style="background: var(--surface); color: var(--text); border-color: var(--border);">
            </div>
            <div class="col-md-1 text-end">
                <button type="button" class="btn btn-outline-danger btn-sm" onclick="document.getElementById('custom_soft_row_${customSoftIndex}').remove()">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        `;
        container.appendChild(row);
    }

    // 7. CANVAS DE FIRMA DIGITAL HTML5 (Delivery & Reception)
    function initCanvas(canvasId) {
        const canvas = document.getElementById(canvasId);
        if (!canvas) return null;
        const ctx = canvas.getContext('2d');
        ctx.strokeStyle = '#0f172a';
        ctx.lineWidth = 2.5;
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';

        let drawing = false;
        let hasDrawn = false;

        function getPos(e) {
            const rect = canvas.getBoundingClientRect();
            const clientX = e.touches ? e.touches[0].clientX : e.clientX;
            const clientY = e.touches ? e.touches[0].clientY : e.clientY;
            const scaleX = canvas.width / rect.width;
            const scaleY = canvas.height / rect.height;
            return {
                x: (clientX - rect.left) * scaleX,
                y: (clientY - rect.top) * scaleY
            };
        }

        function startDraw(e) {
            e.preventDefault();
            drawing = true;
            hasDrawn = true;
            const pos = getPos(e);
            ctx.beginPath();
            ctx.moveTo(pos.x, pos.y);
        }

        function draw(e) {
            if (!drawing) return;
            e.preventDefault();
            const pos = getPos(e);
            ctx.lineTo(pos.x, pos.y);
            ctx.stroke();
        }

        function stopDraw() {
            drawing = false;
        }

        canvas.addEventListener('mousedown', startDraw);
        canvas.addEventListener('mousemove', draw);
        canvas.addEventListener('mouseup', stopDraw);
        canvas.addEventListener('mouseleave', stopDraw);

        canvas.addEventListener('touchstart', startDraw, { passive: false });
        canvas.addEventListener('touchmove', draw, { passive: false });
        canvas.addEventListener('touchend', stopDraw);

        return {
            clear: () => {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                hasDrawn = false;
            },
            hasDrawn: () => hasDrawn,
            getDataURL: () => hasDrawn ? canvas.toDataURL('image/png') : null
        };
    }

    let deliveryPad = null;
    let receptionPad = null;
    let deliveryMode = @json(auth()->user() && auth()->user()->hasSignature() ? 'profile' : 'draw');
    let receptionMode = 'draw';
    let savedProfileSignature = @json(auth()->user()->signature_data ?? '');
    let delivUploadedData = null;
    let recUploadedData = null;

    function setDeliveryMode(mode) {
        deliveryMode = mode;
    }

    function setReceptionMode(mode) {
        receptionMode = mode;
    }

    function previewDeliveryUpload(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                delivUploadedData = e.target.result;
                document.getElementById('deliv_upload_img').src = delivUploadedData;
                document.getElementById('deliv_upload_preview').classList.remove('d-none');
                document.getElementById('deliv_upload_empty').classList.add('d-none');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function previewReceptionUpload(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                recUploadedData = e.target.result;
                document.getElementById('rec_upload_img').src = recUploadedData;
                document.getElementById('rec_upload_preview').classList.remove('d-none');
                document.getElementById('rec_upload_empty').classList.add('d-none');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        deliveryPad = initCanvas('delivery_canvas');
        receptionPad = initCanvas('reception_canvas');

        // Inicializar checkboxes con detalle visible si están marcados
        document.querySelectorAll('.prog-check').forEach(chk => {
            if (chk.getAttribute('data-requires-detail') === '1') {
                const id = chk.id.replace('soft_', '');
                toggleSoftDetail(id);
            }
        });

        // Trigger change si ya hay una empresa seleccionada al cargar
        if (companySelect.value) {
            companySelect.dispatchEvent(new Event('change'));
        }
    });

    function clearDeliveryCanvas() { if (deliveryPad) deliveryPad.clear(); }
    function clearReceptionCanvas() { if (receptionPad) receptionPad.clear(); }

    // Interceptar submit para serializar firmas según el modo activo
    document.getElementById('reportForm').addEventListener('submit', function(e) {
        // Serializar firma de entrega (Técnico Infortech)
        if (deliveryMode === 'profile' && savedProfileSignature) {
            document.getElementById('delivery_signature_data').value = savedProfileSignature;
        } else if (deliveryMode === 'draw' && deliveryPad && deliveryPad.hasDrawn()) {
            document.getElementById('delivery_signature_data').value = deliveryPad.getDataURL();
        } else if (deliveryMode === 'upload' && delivUploadedData) {
            document.getElementById('delivery_signature_data').value = delivUploadedData;
        }

        // Serializar firma de recepción (Cliente Receptor)
        if (receptionMode === 'draw' && receptionPad && receptionPad.hasDrawn()) {
            document.getElementById('reception_signature_data').value = receptionPad.getDataURL();
        } else if (receptionMode === 'upload' && recUploadedData) {
            document.getElementById('reception_signature_data').value = recUploadedData;
        }
    });
</script>
@endsection
