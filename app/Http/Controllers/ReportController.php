<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $from   = $request->get('from', now()->startOfMonth()->toDateString());
        $to     = $request->get('to', now()->toDateString());
        $period = $request->get('period', 'daily');

        // Sales summary
        $summary = Sale::where('status', 'completed')
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)
            ->selectRaw('COUNT(*) as total_sales, SUM(total) as total_revenue, AVG(total) as avg_sale, SUM(discount) as total_discount, SUM(tax_amount) as total_tax')
            ->first();

        // Sales by period
        $groupFormat = match($period) {
            'monthly' => 'DATE_FORMAT(created_at, "%Y-%m")',
            'weekly'  => 'YEARWEEK(created_at)',
            default   => 'DATE(created_at)',
        };

        $salesByPeriod = Sale::where('status', 'completed')
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)
            ->selectRaw("{$groupFormat} as period, COUNT(*) as count, SUM(total) as total")
            ->groupBy('period')
            ->orderBy('period')
            ->get();

        // Top products
        $topProducts = DB::table('sale_items')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->join('products', 'products.id', '=', 'sale_items.product_id')
            ->where('sales.status', 'completed')
            ->whereDate('sales.created_at', '>=', $from)
            ->whereDate('sales.created_at', '<=', $to)
            ->selectRaw('products.name, SUM(sale_items.quantity) as total_qty, SUM(sale_items.subtotal) as total_revenue')
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->get();

        // Sales by payment method
        $byPayment = Sale::where('status', 'completed')
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)
            ->selectRaw('payment_method, COUNT(*) as count, SUM(total) as total')
            ->groupBy('payment_method')
            ->get();

        // Hourly sales pattern
        $byHour = Sale::where('status', 'completed')
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)
            ->selectRaw('HOUR(created_at) as hour, COUNT(*) as count, SUM(total) as total')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->keyBy('hour');

        $hourLabels = collect(range(0, 23))->map(fn($h) => str_pad($h, 2, '0', STR_PAD_LEFT) . ':00');
        $hourData   = collect(range(0, 23))->map(fn($h) => $byHour[$h]->total ?? 0);

        return view('reports.index', compact(
            'summary', 'salesByPeriod', 'topProducts', 'byPayment',
            'hourLabels', 'hourData', 'from', 'to', 'period'
        ));
    }

    public function exportExcel()
    {
        abort(404);
    }
}
