<?php

namespace App\Modules\Report\Controllers;

use App\Http\Controllers\Controller;
use App\Models\SoftwareCatalog;
use App\Models\SoftwareBaseline;
use Illuminate\Http\Request;

class SoftwareCatalogController extends Controller
{
    public function index()
    {
        $software = SoftwareCatalog::orderBy('category')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->groupBy('category');

        $baselines = SoftwareBaseline::orderBy('is_default', 'desc')
            ->orderBy('name')
            ->get();

        return view('reports.software.index', compact('software', 'baselines'));
    }

    public function store(Request $request)
    {
        $this->requireWriteAccess();

        $validated = $request->validate([
            'name'            => 'required|string|max:100',
            'category'        => 'required|string|max:100',
            'requires_detail' => 'nullable|boolean',
            'sort_order'      => 'nullable|integer',
        ]);

        SoftwareCatalog::create([
            'name'            => trim($validated['name']),
            'category'        => trim($validated['category']),
            'requires_detail' => $request->has('requires_detail'),
            'sort_order'      => $validated['sort_order'] ?? 0,
            'is_active'       => true,
        ]);

        return redirect()->back()->with('success', 'Programa agregado al catálogo exitosamente.');
    }

    public function update(Request $request, $id)
    {
        $this->requireWriteAccess();

        $softwareCatalog = SoftwareCatalog::findOrFail($id);

        $validated = $request->validate([
            'name'            => 'required|string|max:100',
            'category'        => 'required|string|max:100',
            'requires_detail' => 'nullable|boolean',
            'sort_order'      => 'nullable|integer',
            'is_active'       => 'nullable|boolean',
        ]);

        $softwareCatalog->update([
            'name'            => trim($validated['name']),
            'category'        => trim($validated['category']),
            'requires_detail' => $request->boolean('requires_detail'),
            'sort_order'      => $validated['sort_order'] ?? $softwareCatalog->sort_order,
            'is_active'       => $request->boolean('is_active'),
        ]);

        return redirect()->back()->with('success', 'Programa "' . $softwareCatalog->name . '" actualizado correctamente.');
    }

    public function destroy(Request $request, $id)
    {
        $this->requireWriteAccess();

        $softwareCatalog = SoftwareCatalog::findOrFail($id);
        $name = $softwareCatalog->name;
        $softwareCatalog->delete();

        return redirect()->back()->with('success', 'Programa "' . $name . '" eliminado del catálogo.');
    }

    public function destroyCategory(Request $request)
    {
        $this->requireWriteAccess();

        $request->validate([
            'category_name' => 'required|string|max:100',
        ]);

        $categoryName = $request->input('category_name');
        $count = SoftwareCatalog::where('category', $categoryName)->count();
        SoftwareCatalog::where('category', $categoryName)->delete();

        return redirect()->back()->with('success', 'Categoría "' . $categoryName . '" y sus ' . $count . ' programa(s) eliminados.');
    }

    // --- MÉTODOS PARA GESTIÓN DE PERFILES (BASELINES) ---

    public function storeBaseline(Request $request)
    {
        $this->requireWriteAccess();

        $validated = $request->validate([
            'name'         => 'required|string|max:100',
            'description'  => 'nullable|string|max:255',
            'icon'         => 'nullable|string|max:50',
            'software_ids' => 'nullable|array',
            'software_ids.*' => 'integer|exists:software_catalog,id',
        ]);

        if ($request->boolean('is_default')) {
            SoftwareBaseline::where('is_default', true)->update(['is_default' => false]);
        }

        SoftwareBaseline::create([
            'name'         => trim($validated['name']),
            'description'  => trim($validated['description'] ?? ''),
            'icon'         => $validated['icon'] ?: 'bi-briefcase-fill',
            'software_ids' => array_map('intval', $validated['software_ids'] ?? []),
            'is_default'   => $request->boolean('is_default'),
        ]);

        return redirect()->back()->with('success', 'Perfil de software "' . $validated['name'] . '" creado exitosamente.');
    }

    public function updateBaseline(Request $request, $id)
    {
        $this->requireWriteAccess();

        $baseline = SoftwareBaseline::findOrFail($id);

        $validated = $request->validate([
            'name'         => 'required|string|max:100',
            'description'  => 'nullable|string|max:255',
            'icon'         => 'nullable|string|max:50',
            'software_ids' => 'nullable|array',
            'software_ids.*' => 'integer|exists:software_catalog,id',
        ]);

        if ($request->boolean('is_default')) {
            SoftwareBaseline::where('is_default', true)->where('id', '!=', $id)->update(['is_default' => false]);
        }

        $baseline->update([
            'name'         => trim($validated['name']),
            'description'  => trim($validated['description'] ?? ''),
            'icon'         => $validated['icon'] ?: $baseline->icon,
            'software_ids' => array_map('intval', $validated['software_ids'] ?? []),
            'is_default'   => $request->boolean('is_default'),
        ]);

        return redirect()->back()->with('success', 'Perfil "' . $baseline->name . '" actualizado correctamente.');
    }

    public function destroyBaseline(Request $request, $id)
    {
        $this->requireWriteAccess();

        $baseline = SoftwareBaseline::findOrFail($id);
        $name = $baseline->name;
        $baseline->delete();

        return redirect()->back()->with('success', 'Perfil "' . $name . '" eliminado.');
    }
}