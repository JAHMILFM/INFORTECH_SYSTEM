<?php

namespace App\Modules\Dashboard\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\ServiceRecord;
use App\Modules\Dashboard\Services\MasterExportService;

class DashboardController extends Controller
{
    public function index()
    {
        $companiesCount = Company::count();
        
        // Total de todos los registros
        $totalServices = ServiceRecord::count();

        // Total de correos específicamente
        $totalEmails = ServiceRecord::where('type', 'email')->count();

        // Correos activos vs suspendidos (agnóstico al motor de base de datos usando sintaxis JSON nativa de Laravel)
        // [DBA OPTIMIZED] Usando la columna computada status_computed para habilitar Index Seeks
        $activeEmails = ServiceRecord::where('type', 'email')
            ->where('status_computed', 'Activo')
            ->count();
            
        $suspendedEmails = ServiceRecord::where('type', 'email')
            ->whereIn('status_computed', ['Suspendido', 'Bloqueada', 'Inactivo'])
            ->count();

        // Últimas 5 empresas agregadas
        $recentCompanies = Company::orderBy('created_at', 'desc')->take(5)->get();

        // Calcular Completitud / Salud del Sistema (Empresas con Dominio)
        $companiesWithDomain = Company::whereNotNull('domain')->count();
        $systemHealth = $companiesCount > 0 ? round(($companiesWithDomain / $companiesCount) * 100) : 0;

        return view('dashboard', compact(
            'companiesCount', 
            'totalServices', 
            'totalEmails', 
            'activeEmails', 
            'suspendedEmails', 
            'recentCompanies',
            'systemHealth'
        ));
    }

    public function exportMaster(MasterExportService $exportService)
    {
        return $exportService->exportCSV();
    }
}
