<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\StockService;
use App\Models\Company;

class StockController extends Controller
{
    protected StockService $service;

    public function __construct(StockService $service)
    {
        $this->service = $service;
    }

    public function compareDates(Request $request)
    {
        $request->validate([
            'company' => 'required|string',
            'start'   => 'required|date',
            'end'     => 'required|date|after_or_equal:start'
        ]);

        // 🔎 Find company by name
        $company = Company::where('name', $request->query('company'))->firstOrFail();

        $result = $this->service->compareBetweenDates(
            $company->id,
            $request->query('start'),
            $request->query('end')
        );

        return response()->json([
            'company_id'   => $company->id,
            'company_name' => $company->name,
            'data'         => $result
        ]);
    }

    public function getByPeriod(Request $request)
    {
        $request->validate([
            'company' => 'required|string',
            'period'  => 'required|string'
        ]);

        // 🔎 Find company by name
        $company = Company::where('name', $request->query('company'))->firstOrFail();

        $result = $this->service->getByPeriod(
            $company->id,
            strtoupper($request->query('period'))
        );

        return response()->json([
            'company_id'   => $company->id,
            'company_name' => $company->name,
            'period'       => strtoupper($request->query('period')),
            'data'         => $result
        ]);
    }
}
