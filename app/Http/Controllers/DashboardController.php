<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService
    ) {}

    public function index(Request $request): View
    {
        $year = $request->input('year', now()->year);

        $stats = $this->dashboardService->getStats();
        $upcomingProjects = $this->dashboardService->getUpcomingDeadlines();
        $recentProjects = $this->dashboardService->getRecentProjects();
        $chartData = $this->dashboardService->getChartData($year);

        return view('dashboard', [
            'activeProjects'    => $stats['active_projects'],
            'delayedProjects'   => $stats['delayed_projects'],
            'lowStockAlerts'    => $stats['low_stock_alerts'],
            'upcomingDeadlines' => $stats['upcoming_deadlines'],
            'upcomingProjects'  => $upcomingProjects,
            'recentProjects'    => $recentProjects,
            'chartData'         => $chartData,
            'year'              => $year,
        ]);
    }
}
