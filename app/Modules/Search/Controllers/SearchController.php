<?php

namespace App\Modules\Search\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Search\Services\SearchService;

class SearchController extends Controller
{
    protected $searchService;

    public function __construct(SearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    public function index(Request $request)
    {
        $q = trim($request->input('q', ''));
        
        $results = $this->searchService->searchGlobal($q);

        $companyResults = $results['companies'];
        $serviceResults = $results['services'];

        return view('search', compact('q', 'companyResults', 'serviceResults'));
    }

    public function liveSearch(Request $request)
    {
        $q = trim($request->input('q', ''));
        if (strlen($q) < 2) {
            return response()->json(['companies' => [], 'services' => []]);
        }
        
        $results = $this->searchService->searchGlobal($q);

        // Limit results to 5 per category to avoid huge dropdowns
        $companies = $results['companies']->take(5)->map(function($c) {
            return ['id' => $c->id, 'name' => $c->name];
        });

        $services = $results['services']->take(5)->map(function($s) {
            return [
                'id' => $s->id, 
                'company_id' => $s->company_id, 
                'type' => $s->type, 
                'data' => $s->data
            ];
        });

        return response()->json([
            'companies' => $companies,
            'services' => $services
        ]);
    }
}
