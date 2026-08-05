<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    private const ACTIVE_STATUSES = ['draft', 'confirmed', 'design', 'in_production', 'delayed', 'inspection'];
    private const COMPLETED_STATUSES = ['finished', 'delivered'];

    /**
     * Reports landing page.
     */
    public function index(): View
    {
        $this->authorizeView();

        return view('reports.index');
    }

    /**
     * Active/completed project list report, with optional CSV export.
     */
    public function projects(Request $request): View|StreamedResponse
    {
        $this->authorizeView();

        $scope = $request->input('scope', 'active');

        $query = Project::with(['client', 'projectManager']);

        if ($scope === 'active') {
            $query->whereIn('status', self::ACTIVE_STATUSES);
        } elseif ($scope === 'completed') {
            $query->whereIn('status', self::COMPLETED_STATUSES);
        }

        $projects = $query->orderByDesc('created_at')->get();

        if ($request->input('export') === 'csv') {
            $this->authorizeExport();

            return $this->streamCsv('projects-report.csv', [
                'Order No', 'Project Name', 'Client', 'Status', 'Project Manager', 'Delivery Date', 'Production Deadline', 'Created',
            ], $projects->map(fn (Project $project) => [
                $project->order_no,
                $project->name,
                $project->client->name ?? '',
                ucfirst(str_replace('_', ' ', $project->status)),
                $project->projectManager->full_name ?? '',
                $project->delivery_date?->format('Y-m-d') ?? '',
                $project->production_deadline?->format('Y-m-d') ?? '',
                $project->created_at->format('Y-m-d'),
            ]));
        }

        return view('reports.projects', compact('projects', 'scope'));
    }

    /**
     * Inventory summary report (stock levels, low-stock flag), with optional CSV export.
     */
    public function inventory(Request $request): View|StreamedResponse
    {
        $this->authorizeView();

        $query = InventoryItem::with(['vendor', 'storageLocation']);

        if ($request->input('filter') === 'low_stock') {
            $query->whereColumn('stock_quantity', '<', 'minimum_stock_level');
        }

        $items = $query->orderBy('name')->get();

        $summary = [
            'total_items' => $items->count(),
            'total_stock_value' => $items->sum(fn (InventoryItem $item) => $item->stock_quantity * $item->unit_price),
            'low_stock_count' => $items->filter(fn (InventoryItem $item) => $item->is_low_stock)->count(),
        ];

        if ($request->input('export') === 'csv') {
            $this->authorizeExport();

            return $this->streamCsv('inventory-summary.csv', [
                'SKU', 'Name', 'Type', 'Vendor', 'Storage Location', 'Stock Quantity', 'Reserved Quantity', 'Minimum Stock Level', 'Unit Price', 'Stock Value', 'Low Stock',
            ], $items->map(fn (InventoryItem $item) => [
                $item->sku,
                $item->name,
                $item->item_type,
                $item->vendor->name ?? '',
                $item->storageLocation->name ?? '',
                $item->stock_quantity,
                $item->reserved_quantity,
                $item->minimum_stock_level,
                $item->unit_price,
                number_format($item->stock_quantity * $item->unit_price, 2),
                $item->is_low_stock ? 'Yes' : 'No',
            ]));
        }

        return view('reports.inventory', compact('items', 'summary'));
    }

    private function authorizeView(): void
    {
        if (!$this->hasPermission('view-reports')) {
            abort(403, 'You do not have permission to view reports.');
        }
    }

    private function authorizeExport(): void
    {
        if (!$this->hasPermission('export-reports')) {
            abort(403, 'You do not have permission to export reports.');
        }
    }

    private function hasPermission(string $permission): bool
    {
        $user = Auth::user();

        return $user && $user->role?->permissions()->where('slug', $permission)->exists();
    }

    /**
     * Stream an array of rows as a downloadable CSV.
     */
    private function streamCsv(string $filename, array $header, \Illuminate\Support\Collection $rows): StreamedResponse
    {
        return response()->streamDownload(function () use ($header, $rows) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $header);
            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
