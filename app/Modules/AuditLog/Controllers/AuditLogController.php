<?php

namespace App\Modules\AuditLog\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with('user')->orderBy('created_at', 'desc');

        // Filtro BPM: Contexto por Empresa
        if ($request->has('company_id')) {
            $companyId = $request->input('company_id');
            $query->where('company_id', $companyId);
        }

        $logs = $query->paginate(50);
            
        return view('audit.index', compact('logs'));
    }
}
