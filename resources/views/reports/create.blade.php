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
            <span class="fs-12 text-muted"><i class="bi bi-building me-1"></i> Prestador del servicio: INOFERTEC</span>
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

            <div class="row g-4">
                @foreach($softwareCatalog as $category => $programs)
                <div class="col-md-6">
                    <div class="p-3 h-100" style="background: rgba(255,255,255,0.02); border: 1px solid var(--border); border-radius: 10px;">
                        <h6 class="fw-bold mb-3 d-flex align-items-center gap-2" style="color: var(--primary);">
                            <i class="bi bi-collection-fill"></i> {{ $category ?: 'General' }}
                        </h6>
                        <div class="d-flex flex-column gap-2">
                            @foreach($programs as $prog)
                            <div class="program-item p-2 rounded" style="background: rgba(255,255,255,0.03);">
                                <div class="form-check d-flex align-items-center justify-content-between">
                                    <div>
                                        <input class="form-check-input prog-check" type="checkbox" 
                                               name="software[{{ $prog->id }}]" value="1" id="soft_{{ $prog->id }}"
                                               data-requires-detail="{{ $prog->requires_detail ? '1' : '0' }}"
                                               onchange="toggleSoftDetail({{ $prog->id }})"
                                               {{ in_array($prog->name, ['Microsoft Office', 'Microsoft Teams', 'AnyDesk', 'Google Chrome']) ? 'checked' : '' }}>
                                        <label class="form-check-label ms-1 fw-semibold fs-13" for="soft_{{ $prog->id }}" style="color: var(--text); cursor: pointer;">
                                            {{ $prog->name }}
                                        </label>
                                    </div>
                                    @if($prog->requires_detail)
                                        <span class="badge bg-secondary fs-10">Especificar productos</span>
                                    @endif
                                </div>

                                @if($prog->requires_detail)
                                <div id="detail_wrap_{{ $prog->id }}" class="mt-2 ps-4" style="display: none;">
                                    <input type="text" name="software_details[{{ $prog->id }}]" class="form-control form-control-sm" 
                                           placeholder="Detalla los productos (ej. AutoCAD 2024, Revit, Acrobat Pro)"
                                           style="background: var(--surface); color: var(--text); border-color: var(--border); font-size: 12px;">
                                </div>
                                @endif
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
                    <label class="form-label fw-semibold" style="color: var(--text);">Técnico Responsable (INOFERTEC)</label>
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

            <!-- LIENZOS DE FIRMA DIGITAL -->
            <div class="row g-4 mt-2">
                <!-- Columna 1: Firma Entrega (INOFERTEC) -->
                <div class="col-md-6">
                    <div class="p-3 rounded text-center" style="background: rgba(255,255,255,0.02); border: 1px solid var(--border);">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="fw-bold fs-13" style="color: var(--primary);">
                                <i class="bi bi-shield-check me-1"></i> ENTREGADO POR (INOFERTEC)
                            </span>
                            <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 fs-11" onclick="clearDeliveryCanvas()">
                                <i class="bi bi-eraser me-1"></i> Limpiar
                            </button>
                        </div>
                        <div style="background: #ffffff; border-radius: 8px; border: 1px solid #cbd5e1; overflow: hidden; height: 160px; position: relative;">
                            <canvas id="delivery_canvas" width="450" height="160" style="width: 100%; height: 100%; cursor: crosshair; touch-action: none;"></canvas>
                        </div>
                        <small class="text-muted mt-2 d-block fs-11">Firma digital del técnico usando ratón o pantalla táctil.</small>
                    </div>
                </div>

                <!-- Columna 2: Firma Recepción (CLIENTE) -->
                <div class="col-md-6">
                    <div class="p-3 rounded text-center" style="background: rgba(255,255,255,0.02); border: 1px solid var(--border);">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="fw-bold fs-13" style="color: var(--success);">
                                <i class="bi bi-person-check-fill me-1"></i> RECIBIDO POR (CLIENTE)
                            </span>
                            <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 fs-11" onclick="clearReceptionCanvas()">
                                <i class="bi bi-eraser me-1"></i> Limpiar
                            </button>
                        </div>
                        <div style="background: #ffffff; border-radius: 8px; border: 1px solid #cbd5e1; overflow: hidden; height: 160px; position: relative;">
                            <canvas id="reception_canvas" width="450" height="160" style="width: 100%; height: 100%; cursor: crosshair; touch-action: none;"></canvas>
                        </div>
                        <small class="text-muted mt-2 d-block fs-11">Firma de conformidad del cliente receptor.</small>
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
        const os = selected.getAttribute('data-os');
        const host = selected.getAttribute('data-hostname');
        if (os) document.getElementById('equipment_os').value = os;
        if (host) document.getElementById('eq_hostname').value = host;
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

    // 4. Detalle de programas complejos (Autodesk / Adobe)
    function toggleSoftDetail(softId) {
        const check = document.getElementById(`soft_${softId}`);
        const wrap = document.getElementById(`detail_wrap_${softId}`);
        if (wrap) {
            wrap.style.display = check.checked ? 'block' : 'none';
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

    // Interceptar submit para serializar firmas
    document.getElementById('reportForm').addEventListener('submit', function(e) {
        if (deliveryPad && deliveryPad.hasDrawn()) {
            document.getElementById('delivery_signature_data').value = deliveryPad.getDataURL();
        }
        if (receptionPad && receptionPad.hasDrawn()) {
            document.getElementById('reception_signature_data').value = receptionPad.getDataURL();
        }
    });
</script>
@endsection
