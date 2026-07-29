<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\Product;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function index(Request $request): View
    {
        $query = $request->get('q', '');
        $results = [];

        if (strlen($query) >= 2) {
            // Search Products
            $products = Product::where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('sku', 'like', "%{$query}%")
                  ->orWhere('item_label', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            })
            ->with('category')
            ->limit(10)
            ->get();

            // Search Inventory
            $inventory = InventoryItem::where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('sku', 'like', "%{$query}%")
                  ->orWhere('item_label', 'like', "%{$query}%")
                  ->orWhere('material_type', 'like', "%{$query}%");
            })
            ->with('vendor', 'storageLocation')
            ->limit(10)
            ->get();

            // Search Projects
            $projects = Project::where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhere('order_no', 'like', "%{$query}%");
            })
            ->with('client', 'projectManager')
            ->limit(10)
            ->get();

            $results = [
                'products' => $products,
                'inventory' => $inventory,
                'projects' => $projects,
            ];
        }

        return view('search.index', [
            'query' => $query,
            'results' => $results,
        ]);
    }
}
