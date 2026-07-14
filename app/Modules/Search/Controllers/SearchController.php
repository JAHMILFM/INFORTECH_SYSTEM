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
}
