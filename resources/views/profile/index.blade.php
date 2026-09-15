@extends('layouts.app')

@section('page-title', 'Mi Perfil Profesional')

@section('breadcrumb')
    <span class="mx-2 text-muted">/</span>
    <span class="text-muted">Mi Perfil</span>
@endsection

@section('content')
<div class="container-fluid px-0">
    <!-- Encabezado de Página -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h2 class="mb-1 fw-bold" style="font-family: 'Outfit', sans-serif; color: var(--text);">
                <i class="bi bi-person-badge text-primary me-2"></i> Mi Perfil Profesional
            </h2>
            <p class="text-muted mb-0 fs-14">Gestiona tu identidad corporativa, cargo técnico, firma digital y credenciales de acceso.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            @if($user->hasSignature())
                <span class="badge py-2 px-3 fs-12 d-flex align-items-center gap-1 shadow-sm" style="background: rgba(5, 150, 105, 0.15); color: #10b981; border: 1px solid rgba(5, 150, 105, 0.3);">
                    <i class="bi bi-patch-check-fill text-success"></i> Firma Digital Activa
                </span>
            @else
                <span class="badge py-2 px-3 fs-12 d-flex align-items-center gap-1 shadow-sm" style="background: rgba(245, 158, 11, 0.15); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.3);">
                    <i class="bi bi-exclamation-circle-fill text-warning"></i> Firma Digital Pendiente
                </span>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="background: rgba(5, 150, 105, 0.15); color: #34d399; border-left: 4px solid #059669 !important;">
            <i class="bi bi-check-circle-fill me-2 fs-15"></i>{{ session('success') }}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="background: rgba(220, 38, 38, 0.15); color: #f87171; border-left: 4px solid #dc2626 !important;">
            <strong><i class="bi bi-exclamation-triangle-fill me-2"></i> Atención:</strong>
            <ul class="mb-0 mt-2 ps-3 fs-14">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- COLUMNA IZQUIERDA: RESUMEN DE IDENTIDAD Y FIRMA DIGITAL OFICIAL -->
        <div class="col-xl-4 col-lg-5">
            <!-- Tarjeta de Identidad -->
            <div class="card border-0 shadow-sm mb-4" style="background: var(--surface); border: 1px solid var(--border) !important; border-radius: 16px; overflow: hidden;">
                <div class="p-4 text-center position-relative" style="background: linear-gradient(135deg, rgba(229,107,12,0.12), rgba(31,58,111,0.25)); border-bottom: 1px solid var(--border);">
                    <div class="avatar-wrapper mx-auto mb-3" style="width: 90px; height: 90px; border-radius: 50%; background: linear-gradient(135deg, var(--primary), #1f3a6f); display: flex; align-items: center; justify-content: center; font-size: 36px; font-weight: 800; color: #fff; box-shadow: 0 8px 25px rgba(229,107,12,0.35); border: 3px solid rgba(255,255,255,0.2);">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <h4 class="fw-bold mb-1" style="color: var(--text);">{{ $user->name }}</h4>
                    <p class="mb-2 fs-13" style="color: var(--primary-l); font-weight: 600;">
                        {{ $user->job_title ?: 'Técnico Especialista Infortech' }}
                    </p>
                    <div>
                        @if($user->role === 'SuperAdmin')
                            <span class="badge bg-danger text-uppercase px-3 py-1 fs-11" style="letter-spacing: 0.5px;">
                                <i class="bi bi-shield-fill me-1"></i> {{ $user->role }}
                            </span>
                        @elseif($user->role === 'Soporte')
                            <span class="badge bg-info text-dark text-uppercase px-3 py-1 fs-11" style="letter-spacing: 0.5px;">
                                <i class="bi bi-wrench-adjustable me-1"></i> {{ $user->role }}
                            </span>
                        @else
                            <span class="badge bg-secondary text-uppercase px-3 py-1 fs-11">{{ $user->role }}</span>
                        @endif
                    </div>
                </div>

                <div class="card-body p-4">
                    <div class="d-flex flex-column gap-3 fs-13">
                        <div class="d-flex align-items-center justify-content-between py-2" style="border-bottom: 1px solid var(--border);">
                            <span class="text-muted"><i class="bi bi-envelope me-2 text-primary"></i> Correo:</span>
                            <span class="fw-semibold text-truncate ms-2" style="color: var(--text); max-width: 190px;">{{ $user->email }}</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between py-2" style="border-bottom: 1px solid var(--border);">
                            <span class="text-muted"><i class="bi bi-telephone me-2 text-primary"></i> Teléfono:</span>
                            <span class="fw-semibold" style="color: var(--text);">{{ $user->phone ?: 'No registrado' }}</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between py-2" style="border-bottom: 1px solid var(--border);">
                            <span class="text-muted"><i class="bi bi-card-text me-2 text-primary"></i> DNI / Doc.:</span>
                            <span class="fw-semibold" style="color: var(--text);">{{ $user->document_id ?: 'No registrado' }}</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between py-2">
                            <span class="text-muted"><i class="bi bi-calendar3 me-2 text-primary"></i> Registro:</span>
                            <span class="fw-semibold" style="color: var(--text);">{{ $user->created_at ? $user->created_at->format('d/m/Y') : '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Módulo Visual de Firma Digital Oficial -->
            <div class="card border-0 shadow-sm" style="background: var(--surface); border: 1px solid var(--border) !important; border-radius: 16px;">
                <div class="card-header py-3 px-4 d-flex justify-content-between align-items-center" style="background: transparent; border-bottom: 1px solid var(--border);">
                    <h5 class="mb-0 fw-bold fs-15" style="color: var(--text);">
                        <i class="bi bi-pen-fill text-primary me-2"></i> Firma Digital Oficial
                    </h5>
                    @if($user->hasSignature())
                        <span class="badge bg-success fs-10 px-2 py-1">Registrada</span>
                    @else
                        <span class="badge bg-warning text-dark fs-10 px-2 py-1">No configurada</span>
                    @endif
                </div>
                <div class="card-body p-4 text-center">
                    @if($user->hasSignature())
                        <div class="signature-display-box p-3 rounded mb-3 position-relative" style="background: #ffffff; border: 1px solid rgba(0,0,0,0.1); height: 140px; display: flex; align-items: center; justify-content: center; background-image: radial-gradient(rgba(0,0,0,0.06) 1px, transparent 0); background-size: 10px 10px; border-radius: 12px;">
                            <img src="{{ $user->signature_data }}" alt="Firma Digital de {{ $user->name }}" style="max-height: 110px; max-width: 90%; object-fit: contain;">
                            <span class="position-absolute bottom-0 end-0 p-2 fs-10 text-muted fst-italic">Oficial Infortech</span>
                        </div>
                        <p class="fs-12 text-muted mb-3">
                            <i class="bi bi-shield-check text-success me-1"></i> Esta firma se inserta automáticamente al certificar reportes técnicos de servicio.
                            @if($user->signature_updated_at)
                                <br><small class="text-muted">Actualizada: {{ $user->signature_updated_at->format('d/m/Y H:i') }}</small>
                            @endif
                        </p>
                        <div class="d-flex gap-2 justify-content-center">
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="scrollToSignatureSection()">
                                <i class="bi bi-arrow-repeat me-1"></i> Reemplazar Firma
                            </button>
                            <form action="{{ route('profile.signature.destroy') }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar tu firma digital oficial?');" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar Firma">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="p-4 rounded mb-3 text-center" style="background: rgba(255,255,255,0.02); border: 2px dashed var(--border); border-radius: 12px;">
                            <i class="bi bi-pen fs-1 text-muted d-block mb-2"></i>
                            <h6 class="fw-bold mb-1" style="color: var(--text);">Sin Firma Digital</h6>
                            <p class="fs-12 text-muted mb-0">Sube tu firma escaneada o dibújala con el lápiz táctil para agilizar la entrega de reportes.</p>
                        </div>
                        <button type="button" class="btn btn-primary btn-sm w-100 shadow-sm" onclick="scrollToSignatureSection()" style="background: linear-gradient(135deg, var(--primary), var(--primary-d)); border: none;">
                            <i class="bi bi-plus-circle me-1"></i> Registrar Firma Ahora
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- COLUMNA DERECHA: EDICIÓN PROFESIONAL, GESTIÓN DE FIRMA Y SEGURIDAD -->
        <div class="col-xl-8 col-lg-7">
            <!-- 1. FORMULARIO DE INFORMACIÓN PROFESIONAL -->
            <div class="card border-0 shadow-sm mb-4" style="background: var(--surface); border: 1px solid var(--border) !important; border-radius: 16px;">
                <div class="card-header py-3 px-4 d-flex align-items-center justify-content-between" style="background: transparent; border-bottom: 1px solid var(--border);">
                    <div class="d-flex align-items-center gap-2">
                        <span class="rounded p-2 text-white" style="background: linear-gradient(135deg, var(--primary), var(--accent)); width: 34px; height: 34px; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-briefcase-fill fs-14"></i>
                        </span>
                        <h5 class="mb-0 fw-bold" style="color: var(--text);">Datos Personales y Laborales</h5>
                    </div>
                    <span class="fs-12 text-muted">Perfil Técnico</span>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold fs-13" style="color: var(--text);">
                                    Nombre Completo <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text" style="background: var(--surface2); border-color: var(--border); color: var(--text-muted);"><i class="bi bi-person"></i></span>
                                    <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required placeholder="Nombres y Apellidos" style="background: var(--surface); color: var(--text); border-color: var(--border);">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold fs-13" style="color: var(--text);">
                                    Correo Electrónico <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text" style="background: var(--surface2); border-color: var(--border); color: var(--text-muted);"><i class="bi bi-envelope"></i></span>
                                    <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required placeholder="usuario@infortech.pe" style="background: var(--surface); color: var(--text); border-color: var(--border);">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold fs-13" style="color: var(--text);">
                                    Cargo / Especialidad Laboral
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text" style="background: var(--surface2); border-color: var(--border); color: var(--text-muted);"><i class="bi bi-award"></i></span>
                                    <input type="text" name="job_title" list="job_titles_list" class="form-control" value="{{ old('job_title', $user->job_title) }}" placeholder="Ej. Técnico Especialista en Soporte TI" style="background: var(--surface); color: var(--text); border-color: var(--border);">
                                    <datalist id="job_titles_list">
                                        <option value="Técnico Especialista de Soporte TI">
                                        <option value="Ingeniero de Redes y Telecomunicaciones">
                                        <option value="Especialista en Mantenimiento de Hardware">
                                        <option value="Administrador de Servidores y Cloud">
                                        <option value="Jefe de Soporte Técnico y Operaciones">
                                        <option value="Consultor de Infraestructura TI">
                                    </datalist>
                                </div>
                                <small class="text-muted fs-11">Este cargo aparecerá impreso al pie de tus firmas en los reportes técnicos.</small>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold fs-13" style="color: var(--text);">
                                    Teléfono / Móvil
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text" style="background: var(--surface2); border-color: var(--border); color: var(--text-muted);"><i class="bi bi-telephone"></i></span>
                                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}" placeholder="+51 987 654 321" style="background: var(--surface); color: var(--text); border-color: var(--border);">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold fs-13" style="color: var(--text);">
                                    DNI / Cédula
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text" style="background: var(--surface2); border-color: var(--border); color: var(--text-muted);"><i class="bi bi-card-heading"></i></span>
                                    <input type="text" name="document_id" class="form-control" value="{{ old('document_id', $user->document_id) }}" placeholder="8 dígitos" style="background: var(--surface); color: var(--text); border-color: var(--border);">
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary px-4 shadow-sm" style="background: linear-gradient(135deg, var(--primary), var(--primary-d)); border: none;">
                                <i class="bi bi-check2-circle me-1"></i> Guardar Información Profesional
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- 2. GESTOR DE FIRMA DIGITAL OFICIAL (SUBIDA O TRAZADO TÁCTIL) -->
            <div id="signatureSection" class="card border-0 shadow-sm mb-4" style="background: var(--surface); border: 1px solid var(--border) !important; border-radius: 16px;">
                <div class="card-header py-3 px-4 d-flex align-items-center justify-content-between" style="background: transparent; border-bottom: 1px solid var(--border);">
                    <div class="d-flex align-items-center gap-2">
                        <span class="rounded p-2 text-white" style="background: linear-gradient(135deg, #059669, #10b981); width: 34px; height: 34px; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-pen-fill fs-14"></i>
                        </span>
                        <div>
                            <h5 class="mb-0 fw-bold" style="color: var(--text);">Configurar Firma Digital Oficial</h5>
                            <small class="text-muted fs-12">Elige cómo registrar tu firma para que aparezca formalmente en las actas de entrega</small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <!-- Pestañas de Modo de Firma -->
                    <ul class="nav nav-pills mb-3 gap-2" id="signatureTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active px-3 py-2 fs-13 fw-semibold d-flex align-items-center gap-2" id="tab-upload-btn" data-bs-toggle="pill" data-bs-target="#tab-upload" type="button" role="tab" style="border-radius: 8px;">
                                <i class="bi bi-cloud-arrow-up-fill"></i> Subir Imagen de Firma (Recomendado)
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link px-3 py-2 fs-13 fw-semibold d-flex align-items-center gap-2" id="tab-draw-btn" data-bs-toggle="pill" data-bs-target="#tab-draw" type="button" role="tab" style="border-radius: 8px;">
                                <i class="bi bi-brush-fill"></i> Dibujar con Lápiz / Mouse
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content pt-2" id="signatureTabsContent">
                        <!-- MODO 1: SUBIR IMAGEN DE FIRMA -->
                        <div class="tab-pane fade show active" id="tab-upload" role="tabpanel">
                            <form action="{{ route('profile.signature.update') }}" method="POST" enctype="multipart/form-data" id="uploadSignatureForm">
                                @csrf
                                <div class="row g-3">
                                    <div class="col-md-7">
                                        <label class="form-label fw-semibold fs-13" style="color: var(--text);">
                                            Selecciona tu firma escaneada o digitalizada
                                        </label>
                                        <div class="p-3 text-center rounded position-relative" style="background: rgba(255,255,255,0.02); border: 2px dashed var(--border); border-radius: 12px; cursor: pointer;" onclick="document.getElementById('signature_file_input').click()">
                                            <input type="file" name="signature_file" id="signature_file_input" accept="image/png,image/jpeg,image/svg+xml,image/webp" class="d-none" onchange="previewUploadedSignature(this)">
                                            <i class="bi bi-file-earmark-image fs-1 text-primary mb-2 d-block"></i>
                                            <p class="fs-13 fw-semibold mb-1" style="color: var(--text);">Haz clic para seleccionar o arrastra la imagen aquí</p>
                                            <small class="text-muted d-block fs-11">Formatos: PNG, JPG, WEBP (Fondo blanco o transparente). Máx. 4MB.</small>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label fw-semibold fs-13" style="color: var(--text);">
                                            Previsualización en Tiempo Real
                                        </label>
                                        <div id="upload_preview_container" class="rounded p-2 text-center d-flex align-items-center justify-content-center" style="background: #ffffff; border: 1px solid rgba(0,0,0,0.1); height: 155px; border-radius: 12px; background-image: radial-gradient(rgba(0,0,0,0.06) 1px, transparent 0); background-size: 10px 10px;">
                                            <div id="upload_preview_empty" class="text-muted fs-12">
                                                <i class="bi bi-eye d-block fs-4 mb-1"></i>
                                                Vista previa de tu firma
                                            </div>
                                            <img id="upload_preview_img" src="" alt="Previsualización" class="d-none" style="max-height: 135px; max-width: 95%; object-fit: contain;">
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end mt-3">
                                    <button type="submit" id="btnSubmitUpload" class="btn btn-success px-4 shadow-sm" disabled style="border-radius: 8px;">
                                        <i class="bi bi-check-circle me-1"></i> Guardar Firma Subida
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- MODO 2: DIBUJAR CON LÁPIZ TÁCTIL O MOUSE -->
                        <div class="tab-pane fade" id="tab-draw" role="tabpanel">
                            <form action="{{ route('profile.signature.update') }}" method="POST" id="drawSignatureForm">
                                @csrf
                                <input type="hidden" name="signature_data" id="profile_draw_signature_data">

                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fs-12 text-muted">
                                        <i class="bi bi-info-circle me-1 text-primary"></i> Traza tu rúbrica en el recuadro blanco usando el ratón o pantalla táctil
                                    </span>
                                    <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-3 fs-12" onclick="clearProfileCanvas()">
                                        <i class="bi bi-eraser me-1"></i> Limpiar Lienzo
                                    </button>
                                </div>

                                <div class="rounded p-1" style="background: #ffffff; border: 2px solid #cbd5e1; height: 180px; position: relative; border-radius: 12px; overflow: hidden;">
                                    <canvas id="profile_signature_canvas" width="600" height="180" style="width: 100%; height: 100%; cursor: crosshair; touch-action: none;"></canvas>
                                    <span class="position-absolute bottom-0 start-0 p-2 fs-11 text-muted" style="pointer-events: none; opacity: 0.6;">
                                        Lienzo Digital Infortech — Trazo Suavizado
                                    </span>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <small class="text-muted fs-11">Al guardar, se registrará como tu firma oficial de usuario.</small>
                                    <button type="submit" id="btnSubmitDraw" class="btn btn-success px-4 shadow-sm" style="border-radius: 8px;">
                                        <i class="bi bi-check-circle me-1"></i> Guardar Firma Trazada
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. SEGURIDAD Y CAMBIO DE CONTRASEÑA -->
            <div class="card border-0 shadow-sm" style="background: var(--surface); border: 1px solid var(--border) !important; border-radius: 16px;">
                <div class="card-header py-3 px-4 d-flex align-items-center justify-content-between" style="background: transparent; border-bottom: 1px solid var(--border);">
                    <div class="d-flex align-items-center gap-2">
                        <span class="rounded p-2 text-white" style="background: linear-gradient(135deg, #dc2626, #f5365c); width: 34px; height: 34px; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-shield-lock-fill fs-14"></i>
                        </span>
                        <h5 class="mb-0 fw-bold" style="color: var(--text);">Seguridad y Contraseña</h5>
                    </div>
                    <span class="fs-12 text-muted">Protección de Cuenta</span>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('profile.password') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold fs-13" style="color: var(--text);">
                                    Contraseña Actual <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text" style="background: var(--surface2); border-color: var(--border); color: var(--text-muted);"><i class="bi bi-key"></i></span>
                                    <input type="password" name="current_password" id="pwd_current" class="form-control" required placeholder="••••••••" style="background: var(--surface); color: var(--text); border-color: var(--border);">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('pwd_current', this)" style="border-color: var(--border);"><i class="bi bi-eye"></i></button>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold fs-13" style="color: var(--text);">
                                    Nueva Contraseña <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text" style="background: var(--surface2); border-color: var(--border); color: var(--text-muted);"><i class="bi bi-lock"></i></span>
                                    <input type="password" name="new_password" id="pwd_new" class="form-control" required placeholder="Mínimo 8 caracteres" style="background: var(--surface); color: var(--text); border-color: var(--border);">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('pwd_new', this)" style="border-color: var(--border);"><i class="bi bi-eye"></i></button>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold fs-13" style="color: var(--text);">
                                    Confirmar Nueva Contraseña <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text" style="background: var(--surface2); border-color: var(--border); color: var(--text-muted);"><i class="bi bi-check2-circle"></i></span>
                                    <input type="password" name="new_password_confirmation" id="pwd_confirm" class="form-control" required placeholder="Repetir contraseña" style="background: var(--surface); color: var(--text); border-color: var(--border);">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('pwd_confirm', this)" style="border-color: var(--border);"><i class="bi bi-eye"></i></button>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-danger px-4 shadow-sm" style="background: linear-gradient(135deg, #dc2626, #b91c1c); border: none;">
                                <i class="bi bi-shield-check me-1"></i> Actualizar Contraseña
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function scrollToSignatureSection() {
        const el = document.getElementById('signatureSection');
        if (el) {
            el.scrollIntoView({ behavior: 'smooth' });
            el.style.boxShadow = '0 0 20px rgba(229,107,12,0.4)';
            setTimeout(() => { el.style.boxShadow = ''; }, 1800);
        }
    }

    function togglePasswordVisibility(inputId, btn) {
        const input = document.getElementById(inputId);
        const icon = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    }

    // Previsualización de Firma Subida
    function previewUploadedSignature(input) {
        const emptyState = document.getElementById('upload_preview_empty');
        const img = document.getElementById('upload_preview_img');
        const btnSubmit = document.getElementById('btnSubmitUpload');

        if (input.files && input.files[0]) {
            const file = input.files[0];
            const reader = new FileReader();
            reader.onload = function(e) {
                img.src = e.target.result;
                img.classList.remove('d-none');
                emptyState.classList.add('d-none');
                btnSubmit.removeAttribute('disabled');
            };
            reader.readAsDataURL(file);
        } else {
            img.classList.add('d-none');
            emptyState.classList.remove('d-none');
            btnSubmit.setAttribute('disabled', 'disabled');
        }
    }

    // Canvas de Trazado de Firma Digital
    let profileCanvas = null;
    let profileCtx = null;
    let isDrawing = false;
    let hasDrawnSignature = false;

    document.addEventListener('DOMContentLoaded', () => {
        const canvas = document.getElementById('profile_signature_canvas');
        if (!canvas) return;

        profileCanvas = canvas;
        profileCtx = canvas.getContext('2d');
        profileCtx.strokeStyle = '#0b1120';
        profileCtx.lineWidth = 2.8;
        profileCtx.lineCap = 'round';
        profileCtx.lineJoin = 'round';

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
            isDrawing = true;
            hasDrawnSignature = true;
            const pos = getPos(e);
            profileCtx.beginPath();
            profileCtx.moveTo(pos.x, pos.y);
        }

        function draw(e) {
            if (!isDrawing) return;
            e.preventDefault();
            const pos = getPos(e);
            profileCtx.lineTo(pos.x, pos.y);
            profileCtx.stroke();
        }

        function stopDraw() {
            isDrawing = false;
        }

        canvas.addEventListener('mousedown', startDraw);
        canvas.addEventListener('mousemove', draw);
        canvas.addEventListener('mouseup', stopDraw);
        canvas.addEventListener('mouseleave', stopDraw);

        canvas.addEventListener('touchstart', startDraw, { passive: false });
        canvas.addEventListener('touchmove', draw, { passive: false });
        canvas.addEventListener('touchend', stopDraw);

        // Envío del formulario de dibujo
        document.getElementById('drawSignatureForm').addEventListener('submit', function(e) {
            if (!hasDrawnSignature) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Lienzo vacío',
                    text: 'Por favor dibuja tu firma con el lápiz antes de guardar.',
                    confirmButtonColor: '#e56b0c'
                });
                return;
            }
            document.getElementById('profile_draw_signature_data').value = canvas.toDataURL('image/png');
        });
    });

    function clearProfileCanvas() {
        if (profileCtx && profileCanvas) {
            profileCtx.clearRect(0, 0, profileCanvas.width, profileCanvas.height);
            hasDrawnSignature = false;
        }
    }
</script>
@endsection
