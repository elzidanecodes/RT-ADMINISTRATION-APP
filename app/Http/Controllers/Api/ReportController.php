<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(private ReportService $reportService) {}

    public function annualChart(Request $request): JsonResponse
    {
        $request->validate([
            'year' => ['nullable', 'integer', 'min:2020', 'max:2099'],
        ]);

        $year = $request->integer('year', Carbon::now()->year);
        $data = $this->reportService->getAnnualChart($year);

        return response()->json([
            'success' => true,
            'data'    => $data,
        ]);
    }

    public function monthlyDetail(Request $request): JsonResponse
    {
        $request->validate([
            'year'  => ['required', 'integer', 'min:2020', 'max:2099'],
            'month' => ['required', 'integer', 'min:1', 'max:12'],
        ]);

        $data = $this->reportService->getMonthlyDetail(
            $request->integer('year'),
            $request->integer('month')
        );

        return response()->json([
            'success' => true,
            'data'    => $data,
        ]);
    }

    public function dashboardSummary(): JsonResponse
    {
        $data = $this->reportService->getDashboardSummary();

        return response()->json([
            'success' => true,
            'data'    => $data,
        ]);
    }
}
