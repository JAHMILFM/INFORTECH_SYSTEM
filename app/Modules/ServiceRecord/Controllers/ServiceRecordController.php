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
        abort_if($record->company_id !== $company->id, 403);
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
        abort_if($record->company_id !== $company->id, 403);
        
        $success = $this->serviceRecordService->toggleStatus($record, $request->input('status'));
        
        if ($success) {
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 400);
    }

    // Elimina un registro
    public function destroy(Company $company, string $type, ServiceRecord $record)
    {
        $this->requireWriteAccess();
        abort_if($record->company_id !== $company->id, 403);
        
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
}
