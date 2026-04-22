<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Employee;
use App\Models\Product;
use App\Models\Sale;
use App\Models\WorkshopJob;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();

        $stats = [
            'sales_today'        => Sale::whereDate('created_at', $today)->where('status', 'completed')->count(),
            'revenue_today'      => Sale::whereDate('created_at', $today)->where('status', 'completed')->sum('total'),
            'revenue_month'      => Sale::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->where('status', 'completed')->sum('total'),
            'low_stock_products' => Product::where('track_stock', true)->whereRaw('stock <= min_stock')->where('is_active', true)->count(),
            'active_customers'   => Customer::where('is_active', true)->where('is_generic', false)->count(),
            'workshop_pending'   => WorkshopJob::whereIn('status', ['pending', 'in_progress'])->count(),
        ];

        $salesChart = Sale::where('status', 'completed')
            ->whereDate('created_at', '>=', now()->subDays(29))
            ->selectRaw('DATE(created_at) as date, SUM(total) as total, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $chartDays   = collect(range(29, 0))->map(fn($d) => now()->subDays($d)->toDateString());
        $chartLabels = $chartDays->map(fn($d) => \Carbon\Carbon::parse($d)->format('d/m'));
        $chartData   = $chartDays->map(fn($d) => $salesChart[$d]->total ?? 0);

        $topProducts = DB::table('sale_items')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->join('products', 'products.id', '=', 'sale_items.product_id')
            ->where('sales.status', 'completed')
            ->whereMonth('sales.created_at', now()->month)
            ->selectRaw('products.name, SUM(sale_items.quantity) as total_qty, SUM(sale_items.subtotal) as total_revenue')
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        $recentSales = Sale::with(['customer', 'user'])
            ->latest()
            ->limit(8)
            ->get();

        $lowStockProducts = Product::with('category')
            ->where('track_stock', true)
            ->whereRaw('stock <= min_stock')
            ->where('is_active', true)
            ->orderBy('stock')
            ->limit(8)
            ->get();

        return view('dashboard.index', compact(
            'stats', 'chartLabels', 'chartData',
            'topProducts', 'recentSales', 'lowStockProducts'
        ));
    }
}
