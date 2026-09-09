<?php

namespace App\Modules\Report\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Company;
use App\Models\Equipment;
use Illuminate\Http\Request;

class EquipmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Equipment::with(['company', 'reports'])->orderBy('updated_at', 'desc');

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('serial_number', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('hostname', 'like', "%{$search}%")
                  ->orWhereHas('company', function ($c) use ($search) {
                      $c->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $equipments = $query->paginate(15)->withQueryString();
        $companies  = Company::where('is_active', true)->orderBy('name')->get();

        return view('reports.equipment.index', compact('equipments', 'companies'));
    }

    public function show(Equipment $equipment)
    {
        $equipment->load(['company', 'reports.technician', 'reports.reportType']);
        return view('reports.equipment.show', compact('equipment'));
    }

    public function store(Request $request)
    {
        $this->requireWriteAccess();

        $validated = $request->validate([
            'company_id'    => 'required|exists:companies,id',
            'type'          => 'required|in:laptop,desktop,server',
            'serial_number' => 'required|string|max:100',
            'brand'         => 'required|string|max:100',
            'model'         => 'required|string|max:100',
            'hostname'      => 'nullable|string|max:100',
            'os'            => 'nullable|string|max:100',
            'notes'         => 'nullable|string',
        ]);

        $equipment = Equipment::create($validated);

        return redirect()->back()->with('success', "Equipo {$equipment->display_name} registrado.");
    }

    public function update(Request $request, Equipment $equipment)
    {
        $this->requireWriteAccess();

        $validated = $request->validate([
            'type'          => 'required|in:laptop,desktop,server',
            'serial_number' => 'required|string|max:100',
            'brand'         => 'required|string|max:100',
            'model'         => 'required|string|max:100',
            'hostname'      => 'nullable|string|max:100',
            'os'            => 'nullable|string|max:100',
            'notes'         => 'nullable|string',
        ]);

        $equipment->update($validated);

        return redirect()->back()->with('success', 'Información del equipo actualizada.');
    }

    public function destroy(Equipment $equipment)
    {
        $user = auth()->user();
        if (!$user || $user->role !== 'SuperAdmin') {
            abort(403, 'Solo SuperAdmin puede eliminar equipos.');
        }

        $name = $equipment->display_name;
        $equipment->delete();

        return redirect()->route('equipment.index')
            ->with('success', "Equipo {$name} eliminado.");
    }
}
