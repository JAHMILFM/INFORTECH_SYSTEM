<?php

namespace App\Modules\Report\Controllers;

use App\Http\Controllers\Controller;
use App\Models\SoftwareCatalog;
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

        return view('reports.software.index', compact('software'));
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

        return redirect()->back()->with('success', 'Programa agregado al catalogo exitosamente.');
    }

    public function update(Request $request, SoftwareCatalog $software)
    {
        $this->requireWriteAccess();

        $validated = $request->validate([
            'name'            => 'required|string|max:100',
            'category'        => 'required|string|max:100',
            'requires_detail' => 'nullable|boolean',
            'sort_order'      => 'nullable|integer',
            'is_active'       => 'nullable|boolean',
        ]);

        $software->update([
            'name'            => trim($validated['name']),
            'category'        => trim($validated['category']),
            'requires_detail' => $request->boolean('requires_detail'),
            'sort_order'      => $validated['sort_order'] ?? $software->sort_order,
            'is_active'       => $request->boolean('is_active'),
        ]);

        return redirect()->back()->with('success', 'Programa "' . $software->name . '" actualizado correctamente.');
    }

    public function destroy(Request $request, SoftwareCatalog $software)
    {
        $this->requireWriteAccess();
        $name = $software->name;
        $software->delete();
        return redirect()->back()->with('success', 'Programa "' . $name . '" eliminado del catalogo.');
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

        return redirect()->back()->with('success', 'Categoria "' . $categoryName . '" y sus ' . $count . ' programa(s) eliminados.');
    }
}