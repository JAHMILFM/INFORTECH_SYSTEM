<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte {{ $report->code }} — INFORTECH</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --primary: #e56b0c;
            --primary-dark: #1e293b;
            --header-bg: #141d38;
            --border-color: #cbd5e1;
            --text-dark: #0f172a;
            --text-muted: #64748b;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            background: #f1f5f9;
            color: var(--text-dark);
            font-size: 12.5px;
            line-height: 1.4;
            padding: 30px 15px;
        }

        .no-print-toolbar {
            max-width: 820px;
            margin: 0 auto 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn-print {
            background: var(--primary);
            color: #fff;
            border: none;
            padding: 10px 22px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(229,107,12,0.3);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: background 0.2s;
        }
        .btn-print:hover { background: #d05e08; }

        .btn-back {
            background: #fff;
            color: #334155;
            border: 1px solid #cbd5e1;
            padding: 9px 18px;
            border-radius: 8px;
            font-weight: 500;
            font-size: 13px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-back:hover { background: #f8fafc; border-color: #94a3b8; }

        /* HOJA A4 IMPRESA */
        .document-page {
            max-width: 820px;
            margin: 0 auto;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 25px rgba(0,0,0,0.06);
            padding: 35px 40px;
            border-radius: 4px;
        }

        /* CABECERA FORMULARIO FOR-TI-001 */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border: 2px solid var(--primary-dark);
        }
        .header-table td {
            border: 1px solid var(--border-color);
            padding: 8px 12px;
            vertical-align: middle;
        }
        .logo-cell {
            width: 28%;
            text-align: center;
            background: #ffffff;
        }
        .logo-cell img {
            max-width: 160px;
            height: auto;
        }
        .title-cell {
            width: 44%;
            text-align: center;
        }
        .title-cell h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 15px;
            font-weight: 800;
            color: var(--primary-dark);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            line-height: 1.2;
        }
        .title-cell p {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 3px;
        }
        .meta-cell {
            width: 28%;
            font-size: 11px;
            background: #f8fafc;
        }
        .meta-cell table { width: 100%; border-collapse: collapse; }
        .meta-cell td { border: none; padding: 2px 4px; }
        .meta-label { font-weight: 600; color: #475569; }

        /* SECCIONES FORMULARIO */
        .section-box {
            margin-bottom: 16px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            overflow: hidden;
        }
        .section-header {
            background: var(--header-bg);
            color: #ffffff;
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            font-size: 12px;
            padding: 6px 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .section-body {
            padding: 10px 12px;
            background: #ffffff;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        .data-table th, .data-table td {
            border: 1px solid var(--border-color);
            padding: 6px 8px;
            font-size: 11.5px;
        }
        .data-table th {
            background: #f8fafc;
            color: #334155;
            font-weight: 600;
            text-align: left;
            width: 25%;
        }
        .data-table td {
            color: #0f172a;
        }

        /* GRID PROGRAMAS */
        .software-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 6px 12px;
            font-size: 11px;
        }
        .software-item {
            display: flex;
            align-items: flex-start;
            gap: 6px;
        }
        .check-box-icon {
            width: 14px;
            height: 14px;
            border: 1.5px solid var(--primary);
            background: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            color: var(--primary);
            font-weight: bold;
            margin-top: 1px;
            flex-shrink: 0;
        }

        /* CONFIGURACIONES TABLE */
        .config-table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
        }
        .config-table th {
            background: #f8fafc;
            border: 1px solid var(--border-color);
            padding: 6px;
            font-size: 11px;
            font-weight: 600;
        }
        .config-table td {
            border: 1px solid var(--border-color);
            padding: 6px;
            font-size: 11px;
        }

        /* FIRMAS */
        .signatures-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .signatures-table td {
            width: 50%;
            border: 1px solid var(--border-color);
            padding: 12px;
            text-align: center;
            vertical-align: bottom;
        }
        .sign-title {
            font-size: 11.5px;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 8px;
            color: var(--primary-dark);
        }
        .sign-box {
            height: 90px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 8px;
        }
        .sign-box img {
            max-height: 80px;
            max-width: 90%;
        }
        .sign-line {
            border-top: 1px solid #475569;
            width: 80%;
            margin: 0 auto 6px;
        }
        .sign-name {
            font-size: 11.5px;
            font-weight: 700;
        }
        .sign-role {
            font-size: 10.5px;
            color: var(--text-muted);
        }

        .footer-note {
            margin-top: 18px;
            text-align: center;
            font-size: 10px;
            color: var(--text-muted);
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
        }

        /* REGLAS PARA IMPRESIÓN DIRECTA */
        @media print {
            body {
                background: #fff;
                padding: 0;
                color: #000;
            }
            .no-print-toolbar {
                display: none !important;
            }
            .document-page {
                box-shadow: none;
                border: none;
                padding: 0;
                max-width: 100%;
            }
            .section-header {
                background: #141d38 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .check-box-icon {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            @page {
                size: A4 portrait;
                margin: 12mm 15mm;
            }
        }
    </style>
</head>
<body>

    <div class="no-print-toolbar">
        <a href="{{ route('reports.show', $report->id) }}" class="btn-back">
            <i class="bi bi-arrow-left"></i> Volver a la Ficha
        </a>
        <div style="display: flex; gap: 8px; align-items: center;">
            @if(!empty($report->decrypted_password))
                <button type="button" onclick="togglePrintPassword()" class="btn-back" id="btnTogglePwd" title="Ocultar/Mostrar contraseña en la impresión">
                    <i class="bi bi-eye"></i> Ocultar Clave
                </button>
            @endif
            <a href="{{ route('reports.downloadWord', $report->id) }}" class="btn-back" title="Descargar documento editable">
                <i class="bi bi-file-earmark-word-fill" style="color: #2563eb;"></i> Descargar Word (.doc)
            </a>
            <button onclick="window.print()" class="btn-print">
                <i class="bi bi-printer-fill"></i> Imprimir / Guardar como PDF
            </button>
        </div>
    </div>

    <div class="document-page">
        <!-- CABECERA FORMULARIO -->
        <table class="header-table">
            <tr>
                <td class="logo-cell" rowspan="2">
                    <img src="{{ asset('assets/images/logo-blanco.png') }}" alt="Infortech Logo" style="filter: invert(1) brightness(0.2);">
                    <div style="font-size: 9px; color: #475569; margin-top: 4px; font-weight: 600;">SOLUCIONES TECNOLÓGICAS</div>
                </td>
                <td class="title-cell" rowspan="2">
                    <h1>REPORTE DE SERVICIO TÉCNICO</h1>
                    <p>FORMATEO, MANTENIMIENTO Y ENTREGA DE EQUIPOS</p>
                </td>
                <td class="meta-cell">
                    <table>
                        <tr>
                            <td class="meta-label">Código:</td>
                            <td><strong>{{ $report->code }}</strong></td>
                        </tr>
                        <tr>
                            <td class="meta-label">Versión:</td>
                            <td>{{ $report->reportType->version ?? '02' }}</td>
                        </tr>
                        <tr>
                            <td class="meta-label">Fecha:</td>
                            <td>{{ \Carbon\Carbon::parse($report->service_date)->format('d/m/Y') }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td class="meta-cell" style="font-size: 10px; color: #475569;">
                    Estado: <strong>{{ strtoupper($report->status === 'confirmed' ? 'Confirmado' : 'Borrador') }}</strong>
                </td>
            </tr>
        </table>

        <!-- SECCIÓN A: DATOS DEL CLIENTE -->
        <div class="section-box">
            <div class="section-header">
                <span>Sección A — Datos del Cliente</span>
                <span style="font-size: 10px; font-weight: normal;">Prestador: INFORTECH S.A.C.</span>
            </div>
            <div class="section-body p-0">
                <table class="data-table">
                    <tr>
                        <th>Empresa Cliente:</th>
                        <td colspan="3"><strong>{{ $report->company->name ?? 'N/D' }}</strong></td>
                    </tr>
                    <tr>
                        <th>RUC:</th>
                        <td>{{ $report->company->tax_id ?? 'N/D' }}</td>
                        <th>Sede / Local:</th>
                        <td>{{ $report->data['client_branch'] ?? 'Principal' }}</td>
                    </tr>
                    <tr>
                        <th>Área / Depto:</th>
                        <td>{{ $report->data['client_area'] ?? 'No especificada' }}</td>
                        <th>Contacto / Teléfono:</th>
                        <td>{{ $report->data['client_contact'] ?? 'N/D' }} {{ !empty($report->data['client_phone']) ? '('.$report->data['client_phone'].')' : '' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- SECCIÓN B: DATOS DEL EQUIPO -->
        <div class="section-box">
            <div class="section-header">
                <span>Sección B — Datos del Equipo Informático</span>
            </div>
            <div class="section-body p-0">
                <table class="data-table">
                    <tr>
                        <th>Tipo de Equipo:</th>
                        <td><strong>{{ strtoupper($report->equipment->type ?? 'Laptop') }}</strong></td>
                        <th>Número de Serie (S/N):</th>
                        <td><strong style="font-family: monospace; font-size: 12px;">{{ $report->equipment->serial_number ?? 'N/D' }}</strong></td>
                    </tr>
                    <tr>
                        <th>Marca y Modelo:</th>
                        <td>{{ $report->equipment->brand ?? 'N/D' }} {{ $report->equipment->model ?? '' }}</td>
                        <th>Sistema Operativo:</th>
                        <td>{{ $report->data['os_installed'] ?? $report->equipment->os ?? 'Windows' }}</td>
                    </tr>
                    <tr>
                        <th>Fecha de Ejecución:</th>
                        <td>{{ \Carbon\Carbon::parse($report->service_date)->format('d/m/Y') }}</td>
                        <th>Técnico Responsable:</th>
                        <td>{{ $report->technician_name }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- SECCIÓN C: USUARIO Y ACCESOS (CONTRASEÑA VISIBLE) -->
        <div class="section-box">
            <div class="section-header">
                <span>Sección C — Usuario y Accesos al Equipo</span>
            </div>
            <div class="section-body p-0">
                <table class="data-table">
                    <tr>
                        <th>Nombre del Usuario:</th>
                        <td>{{ $report->data['user_name'] ?? 'No asignado' }}</td>
                        <th>Usuario de Acceso (Login):</th>
                        <td><code style="font-size: 11px;">{{ $report->data['user_login'] ?? 'N/D' }}</code></td>
                    </tr>
                    <tr>
                        <th>Hostname del Equipo:</th>
                        <td>{{ $report->data['hostname'] ?? 'N/D' }}</td>
                        <th>Contraseña de Acceso:</th>
                        <td>
                            @if(!empty($report->decrypted_password))
                                <strong class="password-cell" data-real="{{ $report->decrypted_password }}" style="font-family: monospace; font-size: 12px; color: var(--primary); letter-spacing: 0.5px;">{{ $report->decrypted_password }}</strong>
                            @elseif($report->data['credentials_configured'] ?? false)
                                <em>Configurada (Protegida)</em>
                            @else
                                <span style="color: #64748b;">Sin contraseña / Acceso libre</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- SECCIÓN D: PROGRAMAS INSTALADOS -->
        <div class="section-box">
            <div class="section-header">
                <span>Sección D — Programas y Aplicaciones Instaladas</span>
                <span style="font-size: 10px; font-weight: normal;">Verificación técnica en equipo</span>
            </div>
            <div class="section-body">
                <div class="software-grid">
                    @forelse($report->software as $soft)
                    <div class="software-item">
                        <div class="check-box-icon"><i class="bi bi-check"></i></div>
                        <div>
                            <strong>{{ $soft->effective_name }}</strong>
                            @if($soft->detail)
                                <div style="font-size: 10px; color: #475569;">{{ $soft->detail }}</div>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div style="grid-column: span 3; color: #64748b;">No se registraron aplicaciones adicionales.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- SECCIÓN D.2: TAREAS DE CONFIGURACIÓN -->
        <div class="section-box">
            <div class="section-header">
                <span>Sección D.2 — Tareas de Configuración y Mantenimiento</span>
            </div>
            <div class="section-body p-0">
                <table class="config-table">
                    <thead>
                        <tr>
                            <th>Backup Realizado</th>
                            <th>Antivirus Instalado</th>
                            <th>Tipo / Nombre de Antivirus</th>
                            <th>Activación de Licencia</th>
                            <th>Drivers Instalados</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>{{ ($report->data['backup_done'] ?? false) ? 'SÍ' : 'NO' }}</strong></td>
                            <td><strong>{{ ($report->data['antivirus_installed'] ?? false) ? 'SÍ' : 'NO' }}</strong></td>
                            <td>{{ ($report->data['antivirus_installed'] ?? false) ? ($report->data['antivirus_name'] ?? 'Windows Defender') : 'N/A' }}</td>
                            <td><strong>{{ ($report->data['license_activated'] ?? false) ? 'SÍ' : 'NO' }}</strong></td>
                            <td><strong>{{ ($report->data['drivers_installed'] ?? false) ? 'SÍ' : 'NO' }}</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- OBSERVACIONES -->
        @if($report->notes)
        <div class="section-box">
            <div class="section-header">
                <span>Observaciones y Recomendaciones</span>
            </div>
            <div class="section-body">
                <p style="font-size: 11px; color: #1e293b;">{{ $report->notes }}</p>
            </div>
        </div>
        @endif

        <!-- SECCIÓN E: BLOQUE FINAL DE FIRMAS (ENTREGA / RECEPCIÓN) -->
        <div class="section-box" style="margin-top: 20px;">
            <div class="section-header">
                <span>Sección E — Conformidad de Servicio y Firmas</span>
            </div>
            <div class="section-body p-0">
                <table class="signatures-table">
                    <tr>
                        <!-- Columna 1: INFORTECH -->
                        <td>
                            <div class="sign-title">Entregado por (INFORTECH S.A.C.)</div>
                            <div class="sign-box">
                                @if($report->deliverySignature && $report->deliverySignature->signature_data)
                                    <img src="{{ $report->deliverySignature->signature_data }}" alt="Firma Técnico">
                                @else
                                    <div style="height: 50px;"></div>
                                @endif
                            </div>
                            <div class="sign-line"></div>
                            <div class="sign-name">{{ $report->technician_name }}</div>
                            <div class="sign-role">Técnico Especialista de Sistemas</div>
                        </td>

                        <!-- Columna 2: CLIENTE -->
                        <td>
                            <div class="sign-title">Recibido por (CLIENTE)</div>
                            <div class="sign-box">
                                @if($report->receptionSignature && $report->receptionSignature->signature_data)
                                    <img src="{{ $report->receptionSignature->signature_data }}" alt="Firma Cliente">
                                @else
                                    <div style="height: 50px;"></div>
                                @endif
                            </div>
                            <div class="sign-line"></div>
                            <div class="sign-name">{{ $report->receptionSignature->signer_name ?? $report->data['receiver_name'] ?? 'Cliente Receptor' }}</div>
                            <div class="sign-role">{{ $report->receptionSignature->signer_role ?? $report->data['receiver_role'] ?? 'Conformidad de Recepción' }}</div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="footer-note">
            Documento emitido conforme al estándar de calidad de servicio técnico INFORTECH. Código de Formato: {{ $report->reportType->format_code ?? 'FOR-TI-001' }} — Versión: {{ $report->reportType->version ?? '02' }}.
        </div>
    </div>

    <script>
        function togglePrintPassword() {
            const el = document.querySelector('.password-cell');
            const btn = document.getElementById('btnTogglePwd');
            if (!el) return;
            if (el.getAttribute('data-hidden') === 'true') {
                el.textContent = el.getAttribute('data-real');
                el.removeAttribute('data-hidden');
                btn.innerHTML = '<i class="bi bi-eye"></i> Ocultar Clave';
            } else {
                el.setAttribute('data-hidden', 'true');
                el.textContent = '••••••••';
                btn.innerHTML = '<i class="bi bi-eye-slash"></i> Mostrar Clave';
            }
        }
    </script>
</body>
</html>
