<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Reporte Técnico - {{ $report->code }}</title>
    <!--[if gte mso 9]>
    <xml>
        <w:WordDocument>
            <w:View>Print</w:View>
            <w:Zoom>100</w:Zoom>
            <w:DoNotOptimizeForBrowser/>
        </w:WordDocument>
    </xml>
    <![endif]-->
    <style>
        @page Section1 {
            size: 595.3pt 841.9pt; /* A4 */
            margin: 25.0pt 30.0pt 25.0pt 30.0pt;
            mso-header-margin: 20.0pt;
            mso-footer-margin: 20.0pt;
        }
        div.Section1 { page: Section1; }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 8.5pt;
            color: #0f172a;
            line-height: 1.25;
            margin: 0;
            padding: 0;
        }
        table {
            border-collapse: collapse;
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
            width: 100%;
            table-layout: fixed;
            margin-bottom: 5pt;
        }
        td, th {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 8pt;
            vertical-align: middle;
        }

        /* 1. CABECERA */
        .header-tbl {
            border: 1.8pt solid #141d38;
            margin-bottom: 6pt;
        }
        .header-tbl td {
            border: 1pt solid #cbd5e1;
            padding: 4pt 6pt;
        }
        .logo-cell {
            text-align: center;
            background-color: #ffffff;
            vertical-align: middle;
        }
        .logo-sub {
            font-size: 6.5pt;
            color: #64748b;
            font-weight: bold;
            margin-top: 2pt;
        }
        .title-cell {
            text-align: center;
            vertical-align: middle;
        }
        .title-main {
            font-size: 11pt;
            font-weight: bold;
            color: #141d38;
            text-transform: uppercase;
        }
        .title-sub {
            font-size: 7.5pt;
            font-weight: bold;
            color: #e56b0c;
            margin-top: 2pt;
            text-transform: uppercase;
        }
        .meta-cell {
            background-color: #f8fafc;
            font-size: 7.5pt;
            padding: 3pt 5pt;
        }
        .meta-cell table {
            margin-bottom: 0;
        }
        .meta-cell table td {
            border: none;
            padding: 1pt 2pt;
            font-size: 7.5pt;
        }

        /* 2. TABLAS Y SECCIONES */
        .card-tbl {
            border: 1pt solid #cbd5e1;
            margin-bottom: 5pt;
        }
        .card-tbl td, .card-tbl th {
            border: 1pt solid #cbd5e1;
            padding: 3pt 5pt;
            font-size: 8pt;
        }
        .sec-header {
            background-color: #141d38;
            color: #ffffff;
            font-weight: bold;
            font-size: 8.5pt;
            padding: 3.5pt 6pt;
            text-transform: uppercase;
            letter-spacing: 0.3pt;
        }
        .lbl {
            background-color: #f8fafc;
            color: #334155;
            font-weight: bold;
        }
        .val {
            color: #0f172a;
        }
        .val-bold {
            color: #0f172a;
            font-weight: bold;
        }

        /* 3. DIAGNÓSTICO */
        .diag-in {
            background-color: #fef2f2;
            border-left: 3pt solid #ef4444;
            padding: 4pt 6pt;
            min-height: 28pt;
            font-size: 7.5pt;
            color: #1e293b;
        }
        .diag-out {
            background-color: #f0fdf4;
            border-left: 3pt solid #10b981;
            padding: 4pt 6pt;
            min-height: 28pt;
            font-size: 7.5pt;
            color: #1e293b;
        }

        /* 4. FIRMAS */
        .sign-cell {
            padding: 5pt 8pt;
            text-align: center;
            vertical-align: bottom;
            width: 50%;
        }
        .sign-box {
            height: 48pt;
            text-align: center;
            vertical-align: middle;
        }
        .sign-line {
            border-top: 1pt solid #475569;
            width: 75%;
            margin: 3pt auto 2pt;
        }

        /* 5. PIE */
        .footer-note {
            font-size: 6.8pt;
            color: #64748b;
            text-align: center;
            border-top: 1pt solid #cbd5e1;
            padding-top: 3pt;
            margin-top: 4pt;
        }
    </style>
</head>
<body>
@php
    $logoPath = public_path('assets/images/logo-blanco.png');
    $logoBase64 = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : null;
@endphp
<div class="Section1">

    <!-- CABECERA FORMULARIO FOR-TI-001 -->
    <table class="header-tbl">
        <colgroup>
            <col style="width: 26%;">
            <col style="width: 46%;">
            <col style="width: 28%;">
        </colgroup>
        <tr>
            <!-- Logo Infortech -->
            <td class="logo-cell">
                @if($logoBase64)
                    <img src="{{ $logoBase64 }}" width="140" height="37" style="width: 140pt; height: 37pt; max-width: 100%;" alt="INFORTECH">
                @else
                    <div style="font-size: 14pt; font-weight: 900; color: #e56b0c; letter-spacing: 1pt;">INFORTECH</div>
                @endif
                <div class="logo-sub">SOLUCIONES TECNOLÓGICAS & TI</div>
            </td>

            <!-- Título Documento -->
            <td class="title-cell">
                <div class="title-main">FORMATO DE REPORTE TÉCNICO DE SERVICIO</div>
                <div class="title-sub">{{ strtoupper($report->reportType->name ?? 'Formateo, Mantenimiento y Entrega de Equipos') }}</div>
            </td>

            <!-- Metadatos de Control -->
            <td class="meta-cell">
                <table style="width: 100%;">
                    <colgroup>
                        <col style="width: 42%;">
                        <col style="width: 58%;">
                    </colgroup>
                    <tr>
                        <td style="font-weight: bold; color: #475569;">CÓDIGO:</td>
                        <td style="font-weight: bold; color: #e56b0c;">{{ $report->code }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold; color: #475569;">FORMATO:</td>
                        <td style="color: #0f172a;">{{ $report->reportType->format_code ?? 'FOR-TI-001' }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold; color: #475569;">VERSIÓN:</td>
                        <td style="color: #0f172a;">{{ $report->reportType->version ?? '02' }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold; color: #475569;">FECHA:</td>
                        <td style="color: #0f172a;">{{ $report->service_date ? \Carbon\Carbon::parse($report->service_date)->format('d/m/Y') : date('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold; color: #475569;">ESTADO:</td>
                        <td style="font-weight: bold; color: {{ $report->status === 'confirmed' ? '#047857' : '#b45309' }};">
                            {{ strtoupper($report->status === 'confirmed' ? 'Confirmado' : 'Borrador') }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- SECCIÓN A: DATOS DE LA EMPRESA CLIENTE (Grid 18% + 32% | 18% + 32% = 50% | 50%) -->
    <table class="card-tbl">
        <colgroup>
            <col style="width: 18%;">
            <col style="width: 32%;">
            <col style="width: 18%;">
            <col style="width: 32%;">
        </colgroup>
        <tr>
            <th colspan="4" class="sec-header">
                <table style="width: 100%; margin: 0; border: none;">
                    <tr>
                        <td style="border: none; padding: 0; color: #ffffff; font-weight: bold;">SECCIÓN A — INFORMACIÓN DE LA EMPRESA CLIENTE</td>
                        <td style="border: none; padding: 0; text-align: right; color: #94a3b8; font-size: 7pt; font-weight: normal;">Prestador: INFORTECH S.A.C.</td>
                    </tr>
                </table>
            </th>
        </tr>
        <tr>
            <td class="lbl">Razón Social:</td>
            <td class="val-bold">{{ $report->company->name ?? 'N/D' }}</td>
            <td class="lbl">RUC / Doc:</td>
            <td class="val">{{ $report->company->tax_id ?? 'No registrado' }}</td>
        </tr>
        <tr>
            <td class="lbl">Sede / Sucursal:</td>
            <td class="val">{{ $report->data['client_branch'] ?? $report->company->branch ?? 'Principal' }}</td>
            <td class="lbl">Área / Depto:</td>
            <td class="val">{{ $report->data['client_area'] ?? $report->company->area ?? 'General' }}</td>
        </tr>
        <tr>
            <td class="lbl">Contacto / Resp.:</td>
            <td class="val">{{ $report->data['client_contact'] ?? $report->company->contact_name ?? 'No especificado' }}</td>
            <td class="lbl">Teléfono / Mail:</td>
            <td class="val">{{ $report->data['client_phone'] ?? $report->company->contact_phone ?? 'No especificado' }}</td>
        </tr>
    </table>

    <!-- SECCIÓN B: IDENTIFICACIÓN DEL EQUIPO Y HARDWARE (Grid 18% + 32% | 18% + 32% = 50% | 50%) -->
    <table class="card-tbl">
        <colgroup>
            <col style="width: 18%;">
            <col style="width: 32%;">
            <col style="width: 18%;">
            <col style="width: 32%;">
        </colgroup>
        <tr>
            <th colspan="4" class="sec-header">
                SECCIÓN B — IDENTIFICACIÓN DEL EQUIPO Y FICHA DE HARDWARE
            </th>
        </tr>
        <tr>
            <td class="lbl">Tipo de Equipo:</td>
            <td class="val-bold">{{ strtoupper($report->equipment->type ?? 'Laptop') }}</td>
            <td class="lbl">Marca / Modelo:</td>
            <td class="val">{{ $report->equipment->brand ?? 'N/D' }} {{ $report->equipment->model ?? '' }}</td>
        </tr>
        <tr>
            <td class="lbl">Número de Serie:</td>
            <td class="val-bold" style="color: #e56b0c; font-family: monospace;">{{ $report->equipment->serial_number ?? 'S/N' }}</td>
            <td class="lbl">Sistema Operativo:</td>
            <td class="val">{{ $report->data['os_installed'] ?? $report->equipment->os ?? 'Windows 11 Pro 64-bit' }}</td>
        </tr>
        <tr>
            <td class="lbl">Procesador (CPU):</td>
            <td class="val">{{ $report->data['processor'] ?? $report->equipment->processor ?? 'No especificado' }}</td>
            <td class="lbl">Memoria RAM:</td>
            <td class="val">{{ $report->data['ram'] ?? $report->equipment->ram ?? 'No especificado' }}</td>
        </tr>
        <tr>
            <td class="lbl">Almacenamiento:</td>
            <td class="val">{{ $report->data['storage'] ?? $report->equipment->storage ?? 'No especificado' }}</td>
            <td class="lbl">Hostname / Red:</td>
            <td class="val">{{ $report->data['hostname'] ?? $report->equipment->hostname ?? 'No asignado' }}</td>
        </tr>
    </table>

    <!-- SECCIÓN B.2: DIAGNÓSTICO INICIAL Y ESTADO DE OPERATIVIDAD FINAL (50% | 50%) -->
    <table class="card-tbl">
        <colgroup>
            <col style="width: 50%;">
            <col style="width: 50%;">
        </colgroup>
        <tr>
            <th colspan="2" class="sec-header">
                SECCIÓN B.2 — DIAGNÓSTICO INICIAL Y ESTADO DE OPERATIVIDAD FINAL
            </th>
        </tr>
        <tr>
            <td style="padding: 4pt 6pt; vertical-align: top;">
                <div style="font-weight: bold; color: #b91c1c; font-size: 7.2pt; margin-bottom: 2pt;">
                    ▼ MOTIVO DEL SERVICIO / DIAGNÓSTICO INICIAL (INGRESO):
                </div>
                <div class="diag-in">
                    {{ $report->data['initial_diagnosis'] ?? 'Mantenimiento preventivo/correctivo programado y formateo limpio del sistema operativo.' }}
                </div>
            </td>
            <td style="padding: 4pt 6pt; vertical-align: top;">
                <div style="font-weight: bold; color: #047857; font-size: 7.2pt; margin-bottom: 2pt;">
                    ▲ ESTADO FINAL DE ENTREGA Y PRUEBAS REALIZADAS:
                </div>
                <div class="diag-out">
                    {{ $report->data['final_state'] ?? 'Sistema operativo reinstalado en limpio, controladores optimizados y pruebas de operatividad superadas al 100%.' }}
                </div>
            </td>
        </tr>
    </table>

    <!-- SECCIÓN C: CONTROL Y ENTREGA DE ACCESORIOS (25% | 25% | 25% | 25% = 50% | 50%) -->
    @php
        $acc = $report->data['accessories'] ?? [];
        $accNotes = $report->data['accessories_notes'] ?? null;
    @endphp
    <table class="card-tbl">
        <colgroup>
            <col style="width: 25%;">
            <col style="width: 25%;">
            <col style="width: 25%;">
            <col style="width: 25%;">
        </colgroup>
        <tr>
            <th colspan="4" class="sec-header">
                SECCIÓN C — CONTROL Y ENTREGA DE ACCESORIOS
            </th>
        </tr>
        <tr>
            <td>
                @if(!empty($acc['charger']))
                    <strong style="color: #047857;">[✓]</strong> <strong style="color: #0f172a;">Cargador / Adaptador</strong>
                @else
                    <span style="color: #94a3b8;">[  ]</span> <span style="color: #64748b;">Cargador / Adaptador</span>
                @endif
            </td>
            <td>
                @if(!empty($acc['power_cable']))
                    <strong style="color: #047857;">[✓]</strong> <strong style="color: #0f172a;">Cable de poder</strong>
                @else
                    <span style="color: #94a3b8;">[  ]</span> <span style="color: #64748b;">Cable de poder</span>
                @endif
            </td>
            <td>
                @if(!empty($acc['bag']))
                    <strong style="color: #047857;">[✓]</strong> <strong style="color: #0f172a;">Funda / Mochila</strong>
                @else
                    <span style="color: #94a3b8;">[  ]</span> <span style="color: #64748b;">Funda / Mochila</span>
                @endif
            </td>
            <td>
                @if(!empty($acc['mouse']))
                    <strong style="color: #047857;">[✓]</strong> <strong style="color: #0f172a;">Mouse / Otros</strong>
                @else
                    <span style="color: #94a3b8;">[  ]</span> <span style="color: #64748b;">Mouse / Otros</span>
                @endif
            </td>
        </tr>
        @if($accNotes)
        <tr>
            <td class="lbl">Detalle Accesorios:</td>
            <td colspan="3" class="val">{{ $accNotes }}</td>
        </tr>
        @endif
    </table>

    <!-- SECCIÓN D: PROGRAMAS INSTALADOS Y TAREAS (50% | 50%) -->
    <table class="card-tbl">
        <colgroup>
            <col style="width: 50%;">
            <col style="width: 50%;">
        </colgroup>
        <tr>
            <th colspan="2" class="sec-header">
                SECCIÓN D — PROGRAMAS INSTALADOS Y TAREAS DE CONFIGURACIÓN
            </th>
        </tr>
        <tr style="background-color: #f1f5f9;">
            <th style="font-weight: bold; text-align: left; padding: 2.5pt 5pt;">Software / Aplicación Instalada</th>
            <th style="font-weight: bold; text-align: left; padding: 2.5pt 5pt;">Detalle / Versión / Licencia</th>
        </tr>
        @forelse($report->software as $soft)
        <tr>
            <td>
                <span style="color: #047857; font-weight: bold; margin-right: 2pt;">✓</span>
                <strong>{{ $soft->catalogItem ? $soft->catalogItem->name : $soft->software_name }}</strong>
            </td>
            <td>{{ $soft->detail ?: 'Configuración oficial estándar' }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="2" style="text-align: center; color: #64748b; font-style: italic;">Sin programas específicos detallados.</td>
        </tr>
        @endforelse
    </table>

    <!-- TAREAS DE CONFIGURACIÓN Y MANTENIMIENTO (25% | 25% | 25% | 25% = 50% | 50%) -->
    <table class="card-tbl">
        <colgroup>
            <col style="width: 25%;">
            <col style="width: 25%;">
            <col style="width: 25%;">
            <col style="width: 25%;">
        </colgroup>
        <tr style="background-color: #f8fafc;">
            <td style="text-align: center; padding: 3pt;">
                Backup:<br>
                <strong style="color: {{ !empty($report->data['backup_done']) ? '#047857' : '#64748b' }};">{{ !empty($report->data['backup_done']) ? 'SÍ (Realizado)' : 'No Requerido' }}</strong>
            </td>
            <td style="text-align: center; padding: 3pt;">
                Antivirus:<br>
                <strong style="color: {{ !empty($report->data['antivirus_installed']) ? '#047857' : '#b45309' }};">{{ !empty($report->data['antivirus_installed']) ? 'SÍ (' . ($report->data['antivirus_name'] ?? 'Defender') . ')' : 'NO' }}</strong>
            </td>
            <td style="text-align: center; padding: 3pt;">
                Licencia:<br>
                <strong style="color: {{ !empty($report->data['license_activated']) ? '#047857' : '#b45309' }};">{{ !empty($report->data['license_activated']) ? 'SÍ (Genuina)' : 'Pendiente' }}</strong>
            </td>
            <td style="text-align: center; padding: 3pt;">
                Drivers:<br>
                <strong style="color: {{ !empty($report->data['drivers_installed']) ? '#047857' : '#b45309' }};">{{ !empty($report->data['drivers_installed']) ? 'SÍ (Completos)' : 'NO' }}</strong>
            </td>
        </tr>
    </table>

    @if(!empty($report->notes))
    <!-- OBSERVACIONES (Grid 18% + 82%) -->
    <table class="card-tbl">
        <colgroup>
            <col style="width: 18%;">
            <col style="width: 82%;">
        </colgroup>
        <tr>
            <td class="lbl">Observaciones:</td>
            <td class="val" style="font-size: 7.5pt; line-height: 1.25;">{{ $report->notes }}</td>
        </tr>
    </table>
    @endif

    <!-- SECCIÓN E: CONFORMIDAD DE SERVICIO Y FIRMAS DIGITALES (50% | 50%) -->
    <table class="card-tbl">
        <colgroup>
            <col style="width: 50%;">
            <col style="width: 50%;">
        </colgroup>
        <tr>
            <th colspan="2" class="sec-header">
                SECCIÓN E — CONFORMIDAD DE SERVICIO Y FIRMAS DIGITALES
            </th>
        </tr>
        <tr>
            <!-- Columna 1: INFORTECH -->
            <td class="sign-cell">
                <div style="font-size: 7.5pt; font-weight: bold; color: #141d38; text-transform: uppercase; margin-bottom: 2pt;">
                    ENTREGADO POR (INFORTECH S.A.C.)
                </div>
                <div class="sign-box">
                    @if($report->deliverySignature && $report->deliverySignature->signature_data)
                        <img src="{{ $report->deliverySignature->signature_data }}" width="150" height="50" style="width: 150pt; height: 50pt;" alt="Firma Técnico">
                    @else
                        <div style="height: 40pt;"></div>
                    @endif
                </div>
                <div class="sign-line"></div>
                <div style="font-weight: bold; font-size: 8pt; color: #0f172a;">{{ $report->technician_name }}</div>
                <div style="font-size: 7pt; color: #64748b;">
                    {{ $report->deliverySignature->signer_role ?? ($report->technician ? $report->technician->job_title : null) ?? 'Técnico Especialista de Sistemas' }}
                </div>
            </td>

            <!-- Columna 2: CLIENTE -->
            <td class="sign-cell">
                <div style="font-size: 7.5pt; font-weight: bold; color: #047857; text-transform: uppercase; margin-bottom: 2pt;">
                    RECIBIDO POR (CLIENTE CONFORME)
                </div>
                <div class="sign-box">
                    @if($report->receptionSignature && $report->receptionSignature->signature_data)
                        <img src="{{ $report->receptionSignature->signature_data }}" width="150" height="50" style="width: 150pt; height: 50pt;" alt="Firma Cliente">
                    @else
                        <div style="height: 40pt;"></div>
                    @endif
                </div>
                <div class="sign-line"></div>
                <div style="font-weight: bold; font-size: 8pt; color: #0f172a;">
                    {{ $report->receptionSignature->signer_name ?? $report->data['receiver_name'] ?? 'Cliente Receptor' }}
                </div>
                <div style="font-size: 7pt; color: #64748b;">
                    {{ $report->receptionSignature->signer_role ?? $report->data['receiver_role'] ?? 'Conformidad de Recepción' }}
                </div>
            </td>
        </tr>
    </table>

    <div class="footer-note">
        Documento oficial emitido conforme al estándar de calidad de servicio técnico INFORTECH. Código de Formato: {{ $report->reportType->format_code ?? 'FOR-TI-001' }} — Versión: {{ $report->reportType->version ?? '02' }}.
    </div>

</div>
</body>
</html>
