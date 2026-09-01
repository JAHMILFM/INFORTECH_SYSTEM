<?php

namespace App\Modules\ServiceRecord\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\ServiceRecord;
use App\Modules\ServiceRecord\Services\ServiceRecordService;
use App\Services\CsvExportService;
use Illuminate\Http\Request;

class ServiceRecordController extends Controller
{
    protected ServiceRecordService $serviceRecordService;

    public function __construct(ServiceRecordService $serviceRecordService)
    {
        $this->serviceRecordService = $serviceRecordService;
    }

    // Muestra todos los registros de un tipo para una empresa
    public function index(Company $company, string $type)
    {
        $types = ServiceRecord::typeConfig();
        abort_if(!isset($types[$type]), 404);

        $config  = $types[$type];
        $records = ServiceRecord::where('company_id', $company->id)
                                ->where('type', $type)
                                ->latest()
                                ->paginate(50);

        return view('services.index', compact('company', 'type', 'config', 'records'));
    }

    // Guarda un nuevo registro
    public function store(Request $request, Company $company, string $type)
    {
        $this->requireWriteAccess();
        $types = ServiceRecord::typeConfig();
        abort_if(!isset($types[$type]), 404);

        $config = $types[$type];
        $rules = $this->serviceRecordService->buildValidationRules($config);
        
        $inputData = $request->all();
        $this->serviceRecordService->applyPokaYokeEmail($type, $inputData, $company);
        $request->merge($inputData);

        $validated = $request->validate($rules);
        $this->serviceRecordService->createRecord($company, $type, $validated);

        return redirect()
            ->route('companies.services.index', [$company->id, $type])
            ->with('success', 'Registro agregado correctamente.');
    }

    // Actualiza un registro existente
    public function update(Request $request, Company $company, string $type, ServiceRecord $record)
    {
        $this->requireWriteAccess();
        abort_if((int) $record->company_id !== (int) $company->id, 403);
        $types  = ServiceRecord::typeConfig();
        abort_if(!isset($types[$type]), 404);

        $config = $types[$type];
        $rules = $this->serviceRecordService->buildValidationRules($config);
        
        $inputData = $request->all();
        $this->serviceRecordService->applyPokaYokeEmail($type, $inputData, $company);
        $request->merge($inputData);

        $validated = $request->validate($rules);
        $this->serviceRecordService->updateRecord($record, $validated);

        return redirect()
            ->route('companies.services.index', [$company->id, $type])
            ->with('success', 'Registro actualizado correctamente.');
    }

    // BPM: Toggle Rápido de Estado
    public function toggleStatus(Request $request, Company $company, string $type, ServiceRecord $record)
    {
        $this->requireWriteAccess();
        abort_if((int) $record->company_id !== (int) $company->id, 403);
        
        $success = $this->serviceRecordService->toggleStatus($record, $request->input('status'));
        
        if ($success) {
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 400);
    }

    public function revealPassword(Request $request, Company $company, string $type, ServiceRecord $record)
    {
        $this->requireWriteAccess();
        abort_if((int) $record->company_id !== (int) $company->id, 403);

        $request->validate([
            'password' => 'required|string',
            'field'    => 'nullable|string',
        ]);

        if (!\Illuminate\Support\Facades\Hash::check($request->password, auth()->user()->password)) {
            return response()->json(['error' => 'Contraseña incorrecta'], 403);
        }

        $data = $record->data;
        $field = $request->input('field', 'password');

        if (empty($data[$field])) {
            return response()->json(['error' => 'Contraseña no configurada'], 404);
        }

        $password = $data[$field];
        try {
            $password = \Illuminate\Support\Facades\Crypt::decryptString($password);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            // Texto plano heredado
        }

        return response()->json(['password' => $password]);
    }

    // Elimina un registro
    public function destroy(Company $company, string $type, ServiceRecord $record)
    {
        $this->requireWriteAccess();
        abort_if((int) $record->company_id !== (int) $company->id, 403);
        
        $this->serviceRecordService->deleteRecord($record);

        return redirect()
            ->route('companies.services.index', [$company->id, $type])
            ->with('success', 'Registro eliminado.');
    }

    // Exporta a CSV
    public function export(Company $company, string $type, CsvExportService $exportService)
    {
        $types = ServiceRecord::typeConfig();
        abort_if(!isset($types[$type]), 404);

        $query = ServiceRecord::where('company_id', $company->id)
            ->where('type', $type)
            ->orderBy('created_at', 'desc');

        $columns = $types[$type]['columns'];

        return $exportService->export($company, $type, $query, $columns);
    }

    // Guarda o actualiza credenciales de administrador de un servicio específico
    public function updateAdminConfig(Request $request, Company $company, string $type)
    {
        if (!auth()->user() || auth()->user()->role !== 'SuperAdmin') {
            abort(403, 'Acceso Denegado: Solo el Administrador del Sistema (SuperAdmin) puede configurar credenciales de servicio.');
        }
        
        $validated = $request->validate([
            'admin_url'      => 'nullable|url|max:255',
            'admin_username' => 'nullable|string|max:255',
            'admin_password' => 'nullable|string|max:255',
            'admin_notes'    => 'nullable|string',
        ]);

        $adminRecord = \App\Models\ServiceRecord::where('company_id', $company->id)
            ->where('type', 'admin_' . $type)
            ->first();

        $data = $adminRecord ? $adminRecord->data : [];
        $data['url'] = $validated['admin_url'] ?? null;
        $data['username'] = $validated['admin_username'] ?? null;
        $data['notes'] = $validated['admin_notes'] ?? null;

        if (!empty($validated['admin_password'])) {
            $data['password'] = \Illuminate\Support\Facades\Crypt::encryptString($validated['admin_password']);
        }

        if (!$adminRecord) {
            \App\Models\ServiceRecord::create([
                'company_id' => $company->id,
                'type' => 'admin_' . $type,
                'data' => $data,
            ]);
        } else {
            $adminRecord->update(['data' => $data]);
        }

        return redirect()->back()->with('success', 'Credenciales de administrador actualizadas correctamente.');
    }

    // Revela la contraseña del administrador del servicio específico
    public function revealAdminPassword(Request $request, Company $company, string $type)
    {
        if (!auth()->user() || auth()->user()->role !== 'SuperAdmin') {
            abort(403, 'Acceso Denegado: Solo el Administrador del Sistema (SuperAdmin) puede revelar credenciales.');
        }

        $request->validate([
            'password' => 'required|string',
        ]);

        if (!\Illuminate\Support\Facades\Hash::check($request->password, auth()->user()->password)) {
            return response()->json(['error' => 'Contraseña incorrecta'], 403);
        }

        $adminRecord = \App\Models\ServiceRecord::where('company_id', $company->id)
            ->where('type', 'admin_' . $type)
            ->first();

        if (!$adminRecord || empty($adminRecord->data['password'])) {
            return response()->json(['error' => 'Contraseña no configurada'], 404);
        }

        $password = $adminRecord->data['password'];
        try {
            $password = \Illuminate\Support\Facades\Crypt::decryptString($password);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            // fallback if plain text
        }

        // Registrar en logs de auditoría la visualización de la contraseña
        \App\Models\AuditLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'REVEAL_ADMIN_PASSWORD',
            'company_id' => $company->id,
            'service_record_id' => $adminRecord->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'old_data'   => ['plataforma' => 'Servicio ' . $type, 'role' => 'Administrador'],
            'new_data'   => ['status' => 'revealed'],
        ]);

        return response()->json(['password' => $password]);
    }
}
