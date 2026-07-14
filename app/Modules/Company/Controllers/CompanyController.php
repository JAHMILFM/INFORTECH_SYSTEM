<?php

namespace App\Modules\Company\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Modules\Company\Services\CompanyService;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    protected CompanyService $companyService;

    public function __construct(CompanyService $companyService)
    {
        $this->companyService = $companyService;
    }

    public function index()
    {
        $companies = Company::paginate(15);
        return view('companies.index', compact('companies'));
    }

    public function create()
    {
        $this->requireWriteAccess();
        return view('companies.create');
    }

    public function store(Request $request)
    {
        $this->requireWriteAccess();
        
        $validated = $request->validate([
            'name'          => 'required|string|max:255|unique:companies,name',
            'tax_id'        => 'required|string|max:50|unique:companies,tax_id',
            'domain'        => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
        ]);

        $this->companyService->createCompany($validated);

        return redirect()->route('companies.index')->with('success', 'Empresa registrada correctamente.');
    }

    public function show(Company $company)
    {
        return view('companies.show', compact('company'));
    }

    public function update(Request $request, Company $company)
    {
        $this->requireWriteAccess();
        
        $validated = $request->validate([
            'name'          => 'required|string|max:255|unique:companies,name,' . $company->id,
            'tax_id'        => 'required|string|max:50|unique:companies,tax_id,' . $company->id,
            'domain'        => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
        ]);

        $this->companyService->updateCompany($company, $validated);

        return redirect()->back()->with('success', 'Empresa actualizada correctamente.');
    }

    public function destroy(Company $company)
    {
        $this->requireWriteAccess();
        
        $this->companyService->deleteCompany($company);

        return redirect()->route('companies.index')->with('success', 'Empresa eliminada correctamente.');
    }

    public function updateZimbraConfig(Request $request, Company $company)
    {
        $this->requireWriteAccess();

        $validated = $request->validate([
            'zimbra_host'     => 'nullable|string|max:255',
            'zimbra_webmail'  => 'nullable|url|max:255',
            'zimbra_panel'    => 'nullable|url|max:255',
            'zimbra_username' => 'nullable|email|max:255',
            'zimbra_password' => 'nullable|string|max:255',
        ]);

        $adminRecord = \App\Models\ServiceRecord::where('company_id', $company->id)
            ->where('type', 'account')
            ->where('data->role', 'Administrador')
            ->where(function($q) {
                $q->whereNull('data->plataforma')->orWhere('data->plataforma', 'Zimbra');
            })
            ->first();

        $data = $adminRecord ? $adminRecord->data : [];
        $data['plataforma'] = 'Zimbra';
        $data['host'] = $validated['zimbra_host'] ?? null;
        $data['webmail_link'] = $validated['zimbra_webmail'] ?? null;
        $data['url'] = $validated['zimbra_panel'] ?? null;
        $data['email'] = $validated['zimbra_username'] ?? null;
        
        if (empty($data['username']) && !empty($data['email'])) {
            $data['username'] = $data['email'];
        }

        if (!empty($validated['zimbra_password'])) {
            $data['password'] = $validated['zimbra_password'];
        }

        if (!$adminRecord) {
            $data['role'] = 'Administrador';
            $data['status'] = 'Activo';
            
            \App\Models\ServiceRecord::create([
                'company_id' => $company->id,
                'type' => 'account',
                'data' => $data,
            ]);
        } else {
            $adminRecord->update(['data' => $data]);
        }

        return redirect()->back()->with('success', 'Configuración de Zimbra guardada correctamente.');
    }

    public function revealZimbraPassword(Request $request, Company $company)
    {
        $this->requireWriteAccess();

        $request->validate([
            'password' => 'required|string',
        ]);

        if (!\Illuminate\Support\Facades\Hash::check($request->password, auth()->user()->password)) {
            return response()->json(['error' => 'Contraseña incorrecta'], 403);
        }

        $adminRecord = \App\Models\ServiceRecord::where('company_id', $company->id)
            ->where('type', 'account')
            ->where('data->role', 'Administrador')
            ->where(function($q) {
                $q->whereNull('data->plataforma')->orWhere('data->plataforma', 'Zimbra');
            })
            ->first();

        if (!$adminRecord || empty($adminRecord->data['password'])) {
            return response()->json(['error' => 'Contraseña no configurada'], 404);
        }

        return response()->json(['password' => $adminRecord->data['password']]);
    }

    public function updateNextcloudConfig(Request $request, Company $company)
    {
        $this->requireWriteAccess();

        $validated = $request->validate([
            'nextcloud_url'      => 'nullable|url|max:255',
            'nextcloud_username' => 'nullable|string|max:255',
            'nextcloud_password' => 'nullable|string|max:255',
        ]);

        $adminRecord = \App\Models\ServiceRecord::where('company_id', $company->id)
            ->where('type', 'account')
            ->where('data->role', 'Administrador')
            ->where('data->plataforma', 'Nextcloud')
            ->first();

        $data = $adminRecord ? $adminRecord->data : [];
        $data['plataforma'] = 'Nextcloud';
        $data['url'] = $validated['nextcloud_url'] ?? null;
        $data['email'] = $validated['nextcloud_username'] ?? null;
        $data['username'] = $validated['nextcloud_username'] ?? null;

        if (!empty($validated['nextcloud_password'])) {
            $data['password'] = $validated['nextcloud_password'];
        }

        if (!$adminRecord) {
            $data['role'] = 'Administrador';
            $data['status'] = 'Activo';
            
            \App\Models\ServiceRecord::create([
                'company_id' => $company->id,
                'type' => 'account',
                'data' => $data,
            ]);
        } else {
            $adminRecord->update(['data' => $data]);
        }

        return redirect()->back()->with('success', 'Configuración de Nextcloud guardada correctamente.');
    }

    public function revealNextcloudPassword(Request $request, Company $company)
    {
        $this->requireWriteAccess();

        $request->validate([
            'password' => 'required|string',
        ]);

        if (!\Illuminate\Support\Facades\Hash::check($request->password, auth()->user()->password)) {
            return response()->json(['error' => 'Contraseña incorrecta'], 403);
        }

        $adminRecord = \App\Models\ServiceRecord::where('company_id', $company->id)
            ->where('type', 'account')
            ->where('data->role', 'Administrador')
            ->where('data->plataforma', 'Nextcloud')
            ->first();

        if (!$adminRecord || empty($adminRecord->data['password'])) {
            return response()->json(['error' => 'Contraseña no configurada'], 404);
        }

        return response()->json(['password' => $adminRecord->data['password']]);
    }
}

