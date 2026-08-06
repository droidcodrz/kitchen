<?php

namespace App\Services;

use App\Models\InventoryItem;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class DashboardService
{
    /**
     * Get dashboard statistics.
     */
    public function getStats(): array
    {
        $activeProjects = Project::whereIn('status', ['confirmed', 'design', 'in_production', 'inspection'])->count();

        $delayedProjects = Project::where('status', 'delayed')->count();

        $lowStockAlerts = InventoryItem::whereColumn('stock_quantity', '<', 'minimum_stock_level')
            ->where('is_active', true)
            ->count();

        $upcomingDeadlines = Project::whereNotNull('delivery_date')
            ->whereBetween('delivery_date', [Carbon::today(), Carbon::today()->addDays(7)])
            ->whereNotIn('status', ['delivered', 'finished'])
            ->count();

        return [
            'active_projects' => $activeProjects,
            'delayed_projects' => $delayedProjects,
            'low_stock_alerts' => $lowStockAlerts,
            'upcoming_deadlines' => $upcomingDeadlines,
        ];
    }

    /**
     * Get chart data for monthly project analytics.
     */
    public function getChartData(int $year): array
    {
        $monthlyData = Project::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();

        $data = [];
        for ($i = 1; $i <= 12; $i++) {
            $data[] = $monthlyData[$i] ?? 0;
        }

        return [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sept', 'Oct', 'Nov', 'Dec'],
            'data' => $data,
        ];
    }

    /**
     * Get monthly analytics for a given year.
     */
    public function getMonthlyAnalytics(int $year): Collection
    {
        return Project::selectRaw('MONTH(created_at) as month, status, COUNT(*) as count')
            ->whereYear('created_at', $year)
            ->groupBy('month', 'status')
            ->orderBy('month')
            ->get();
    }

    /**
     * Get projects with nearest delivery dates.
     */
    public function getUpcomingDeadlines(int $limit = 5): Collection
    {
        return Project::whereNotNull('delivery_date')
            ->where('delivery_date', '>=', Carbon::today())
            ->whereNotIn('status', ['delivered'])
            ->orderBy('delivery_date', 'asc')
            ->with(['client', 'projectManager'])
            ->limit($limit)
            ->get();
    }

    /**
     * Get most recently created projects.
     */
    public function getRecentProjects(int $limit = 10): Collection
    {
        return Project::with(['client', 'projectManager', 'teams', 'members'])
            ->latest()
            ->limit($limit)
            ->get();
    }
}
