<?php

namespace App\Modules\Report\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Company;
use App\Models\Equipment;
use App\Models\Report;
use App\Models\ReportSignature;
use App\Models\ReportSoftware;
use App\Models\ReportType;
use App\Models\SoftwareCatalog;
use App\Models\User;
use App\Modules\Report\Services\ReportCodeGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    protected ReportCodeGenerator $codeGenerator;

    public function __construct(ReportCodeGenerator $codeGenerator)
    {
        $this->codeGenerator = $codeGenerator;
    }

    public function index(Request $request)
    {
        $query = Report::with(['company', 'equipment', 'technician', 'reportType'])
            ->orderBy('service_date', 'desc')
            ->orderBy('id', 'desc');

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('technician_name', 'like', "%{$search}%")
                  ->orWhereHas('company', function ($c) use ($search) {
                      $c->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('equipment', function ($e) use ($search) {
                      $e->where('serial_number', 'like', "%{$search}%")
                        ->orWhere('brand', 'like', "%{$search}%")
                        ->orWhere('model', 'like', "%{$search}%");
                  });
            });
        }

        $reports = $query->paginate(15)->withQueryString();

        // Estadísticas rápidas para dashboard de reportes
        $stats = [
            'total'     => Report::count(),
            'confirmed' => Report::where('status', 'confirmed')->count(),
            'draft'     => Report::where('status', 'draft')->count(),
            'this_month'=> Report::whereMonth('service_date', date('m'))
                                 ->whereYear('service_date', date('Y'))
                                 ->count(),
        ];

        $companies = Company::orderBy('name')->get();

        return view('reports.index', compact('reports', 'stats', 'companies'));
    }

    public function create(Request $request)
    {
        $this->requireWriteAccess();

        $reportType = ReportType::where('slug', 'formateo')->firstOrFail();
        $companies  = Company::where('is_active', true)->orderBy('name')->get();
        $technicians= User::orderBy('name')->get();

        $selectedCompanyId = $request->get('company_id');
        $equipments = $selectedCompanyId
            ? Equipment::where('company_id', $selectedCompanyId)->orderBy('brand')->get()
            : collect();

        $softwareCatalog = SoftwareCatalog::where('is_active', true)
            ->orderBy('category')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('category');

        $nextCode = $this->codeGenerator->generate($reportType);

        return view('reports.create', compact(
            'reportType',
            'companies',
            'technicians',
            'selectedCompanyId',
            'equipments',
            'softwareCatalog',
            'nextCode'
        ));
    }

    public function store(Request $request)
    {
        $this->requireWriteAccess();

        $validated = $request->validate([
            'company_id'       => 'required|exists:companies,id',
            'equipment_option' => 'required|in:existing,new',
            'equipment_id'     => 'nullable|required_if:equipment_option,existing|exists:equipment,id',
            // Si es equipo nuevo:
            'equipment_type'   => 'nullable|required_if:equipment_option,new|in:laptop,desktop,server',
            'equipment_serial' => 'nullable|required_if:equipment_option,new|string|max:100',
            'equipment_brand'  => 'nullable|required_if:equipment_option,new|string|max:100',
            'equipment_model'  => 'nullable|required_if:equipment_option,new|string|max:100',
            'equipment_hostname'=> 'nullable|string|max:100',
            'equipment_os'     => 'nullable|string|max:100',
            // Datos del reporte:
            'service_date'     => 'required|date',
            'technician_name'  => 'nullable|string|max:150',
            'user_name'        => 'nullable|string|max:150',
            'user_login'       => 'nullable|string|max:100',
            'hostname'         => 'nullable|string|max:100',
            'credentials_configured' => 'required|in:yes,no',
            'access_password'  => 'nullable|string|max:255',
            // Tareas de configuración:
            'backup_done'      => 'required|in:yes,no',
            'antivirus_installed' => 'required|in:yes,no',
            'antivirus_name'   => 'nullable|string|max:100',
            'license_activated'=> 'required|in:yes,no',
            'drivers_installed'=> 'required|in:yes,no',
            // Cierre y firmas:
            'receiver_name'    => 'nullable|string|max:150',
            'receiver_role'    => 'nullable|string|max:100',
            'notes'            => 'nullable|string',
            'status'           => 'required|in:draft,confirmed',
            'delivery_signature_data'  => 'nullable|string',
            'reception_signature_data' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($request, $validated) {
            $company = Company::findOrFail($validated['company_id']);

            // 1. Resolver el equipo (existente o crearlo nuevo)
            if ($validated['equipment_option'] === 'new') {
                $equipment = Equipment::create([
                    'company_id'    => $company->id,
                    'type'          => $validated['equipment_type'],
                    'serial_number' => trim($validated['equipment_serial']),
                    'brand'         => trim($validated['equipment_brand']),
                    'model'         => trim($validated['equipment_model']),
                    'hostname'      => $validated['equipment_hostname'] ?? null,
                    'os'            => $validated['equipment_os'] ?? null,
                ]);
            } else {
                $equipment = Equipment::findOrFail($validated['equipment_id']);
                // Actualizar OS o hostname si vinieron nuevos
                if (!empty($validated['equipment_os'])) {
                    $equipment->update(['os' => $validated['equipment_os']]);
                }
            }

            // 2. Resolver Tipo de Reporte y Generar Correlativo
            $reportType = ReportType::where('slug', 'formateo')->firstOrFail();
            $code = $this->codeGenerator->generate($reportType);

            // 3. Preparar JSON específico de Formateo
            $reportData = [
                // Datos del cliente en el momento del servicio
                'client_branch'  => $request->input('client_branch', $company->branch),
                'client_area'    => $request->input('client_area', $company->area),
                'client_contact' => $request->input('client_contact', $company->contact_name),
                'client_phone'   => $request->input('client_phone', $company->contact_phone),
                'client_email'   => $request->input('client_email', $company->contact_email),
                // Sección B: Equipo
                'os_installed'   => $validated['equipment_os'] ?? $equipment->os,
                // Sección C: Usuario y accesos
                'user_name'      => $validated['user_name'] ?? null,
                'user_login'     => $validated['user_login'] ?? null,
                'hostname'       => $validated['hostname'] ?? $equipment->hostname,
                'credentials_configured' => $validated['credentials_configured'] === 'yes',
                // Clave cifrada - NUNCA se muestra en el documento impreso
                'encrypted_access_password' => !empty($validated['access_password'])
                    ? Crypt::encryptString($validated['access_password'])
                    : null,
                // Sección D.2: Tareas de configuración
                'backup_done'        => $validated['backup_done'] === 'yes',
                'antivirus_installed'=> $validated['antivirus_installed'] === 'yes',
                'antivirus_name'     => $validated['antivirus_name'] ?? null,
                'license_activated'  => $validated['license_activated'] === 'yes',
                'drivers_installed'  => $validated['drivers_installed'] === 'yes',
                // Cierre
                'receiver_name'      => $validated['receiver_name'] ?? null,
                'receiver_role'      => $validated['receiver_role'] ?? null,
            ];

            // 4. Crear el Reporte
            $technicianName = !empty($validated['technician_name'])
                ? $validated['technician_name']
                : (auth()->user() ? auth()->user()->name : 'Técnico Infortech');

            $report = Report::create([
                'report_type_id'  => $reportType->id,
                'code'            => $code,
                'company_id'      => $company->id,
                'equipment_id'    => $equipment->id,
                'technician_id'   => auth()->id(),
                'technician_name' => $technicianName,
                'service_date'    => $validated['service_date'],
                'status'          => $validated['status'],
                'data'            => $reportData,
                'notes'           => $validated['notes'] ?? null,
            ]);

            // 5. Guardar Programas Instalados (N:N)
            $installedSoftware = $request->input('software', []);
            $softwareDetails   = $request->input('software_details', []);

            foreach ($installedSoftware as $softId => $value) {
                if ($value == '1') {
                    $catalogItem = SoftwareCatalog::find($softId);
                    $detail = $softwareDetails[$softId] ?? null;

                    ReportSoftware::create([
                        'report_id'     => $report->id,
                        'software_id'   => $catalogItem ? $catalogItem->id : null,
                        'software_name' => $catalogItem ? $catalogItem->name : null,
                        'detail'        => $detail,
                        'is_installed'  => true,
                    ]);
                }
            }

            // Programas personalizados ("Otro")
            $customSoftwareNames = $request->input('custom_software_names', []);
            $customSoftwareDetails = $request->input('custom_software_details', []);
            foreach ($customSoftwareNames as $idx => $name) {
                if (!empty(trim($name))) {
                    ReportSoftware::create([
                        'report_id'     => $report->id,
                        'software_id'   => null,
                        'software_name' => trim($name),
                        'detail'        => $customSoftwareDetails[$idx] ?? null,
                        'is_installed'  => true,
                    ]);
                }
            }

            // 6. Guardar Firmas Digitales
            if (!empty($validated['delivery_signature_data'])) {
                ReportSignature::create([
                    'report_id'      => $report->id,
                    'role'           => 'delivery',
                    'signer_name'    => $technicianName,
                    'signer_role'    => 'Técnico Especialista Infortech',
                    'signature_data' => $validated['delivery_signature_data'],
                    'signed_at'      => now(),
                ]);
            }

            if (!empty($validated['reception_signature_data']) || !empty($validated['receiver_name'])) {
                ReportSignature::create([
                    'report_id'      => $report->id,
                    'role'           => 'reception',
                    'signer_name'    => $validated['receiver_name'] ?? 'Cliente Receptor',
                    'signer_role'    => $validated['receiver_role'] ?? 'Recepción',
                    'signature_data' => $validated['reception_signature_data'] ?? null,
                    'signed_at'      => now(),
                ]);
            }

            // 7. Auditoría
            AuditLog::create([
                'user_id'    => auth()->id(),
                'action'     => 'CREATE_REPORT',
                'company_id' => $company->id,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'new_data'   => [
                    'report_id'   => $report->id,
                    'code'        => $report->code,
                    'format_code' => $reportType->format_code,
                    'status'      => $report->status,
                ],
            ]);

            return redirect()->route('reports.show', $report->id)
                ->with('success', "Reporte {$report->code} generado con éxito.");
        });
    }

    public function show(Report $report)
    {
        $report->load([
            'company',
            'equipment',
            'technician',
            'reportType',
            'software.catalogItem',
            'signatures',
        ]);

        return view('reports.show', compact('report'));
    }

    public function print(Report $report)
    {
        $report->load([
            'company',
            'equipment',
            'technician',
            'reportType',
            'software.catalogItem',
            'signatures',
        ]);

        return view('reports.print', compact('report'));
    }

    public function downloadWord(Report $report)
    {
        $report->load([
            'company',
            'equipment',
            'technician',
            'reportType',
            'software.catalogItem',
            'signatures',
        ]);

        $content = view('reports.print', compact('report'))->render();

        return response($content, 200, [
            'Content-Type'        => 'application/vnd.ms-word; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="Reporte-' . $report->code . '.doc"',
            'Cache-Control'       => 'max-age=0',
        ]);
    }

    public function confirm(Report $report)
    {
        $this->requireWriteAccess();

        if ($report->status === 'confirmed') {
            return redirect()->back()->with('info', 'El reporte ya se encuentra confirmado.');
        }

        $report->update(['status' => 'confirmed']);

        AuditLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'CONFIRM_REPORT',
            'company_id' => $report->company_id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'new_data'   => ['code' => $report->code, 'status' => 'confirmed'],
        ]);

        return redirect()->route('reports.show', $report->id)
            ->with('success', "Reporte {$report->code} confirmado correctamente.");
    }

    public function destroy(Report $report)
    {
        $user = auth()->user();
        if (!$user || $user->role !== 'SuperAdmin') {
            abort(403, 'Acceso Denegado: Solo SuperAdmin puede eliminar reportes técnicos.');
        }

        $code = $report->code;
        $report->delete();

        AuditLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'DELETE_REPORT',
            'company_id' => $report->company_id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'old_data'   => ['code' => $code],
        ]);

        return redirect()->route('reports.index')
            ->with('success', "Reporte {$code} eliminado.");
    }

    /**
     * Endpoint JSON para autocompletar equipos al seleccionar una empresa en el wizard
     */
    public function getEquipmentByCompany($companyId)
    {
        $equipment = Equipment::where('company_id', $companyId)
            ->orderBy('brand')
            ->get()
            ->map(function ($item) {
                return [
                    'id'            => $item->id,
                    'display_name'  => $item->display_name,
                    'type'          => $item->type,
                    'serial_number' => $item->serial_number,
                    'brand'         => $item->brand,
                    'model'         => $item->model,
                    'hostname'      => $item->hostname,
                    'os'            => $item->os,
                ];
            });

        $company = Company::find($companyId);

        return response()->json([
            'equipment' => $equipment,
            'company'   => $company ? [
                'ruc'           => $company->tax_id,
                'domain'        => $company->domain,
                'branch'        => $company->branch,
                'area'          => $company->area,
                'contact_name'  => $company->contact_name,
                'contact_phone' => $company->contact_phone,
                'contact_email' => $company->contact_email,
            ] : null,
        ]);
    }
}
