<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\DetailSale;
use App\Models\Medicine;
use App\Models\Sale;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now();

        // Tarjetas de resumen
        $soldToday = Sale::whereDate('date', $today)->sum('total');
        $soldMonth = Sale::whereYear('date', $today->year)
            ->whereMonth('date', $today->month)
            ->sum('total');
        $customers = Customer::count();
        $lowStock = Medicine::where('stock', '<=', MedicineController::LOW_STOCK)->count();

        // Ventas de los últimos 7 días, agrupadas por día
        $start = $today->copy()->subDays(6)->startOfDay();
        $salesByDay = Sale::where('date', '>=', $start)
            ->selectRaw('DATE(date) as day, SUM(total) as total')
            ->groupBy('day')
            ->pluck('total', 'day');

        // Se recorren los 7 días para que los días sin ventas aparezcan con 0
        $weekLabels = [];
        $weekTotals = [];
        for ($i = 0; $i < 7; $i++) {
            $day = $start->copy()->addDays($i);
            $weekLabels[] = $day->format('d/m');
            $weekTotals[] = (float) ($salesByDay[$day->format('Y-m-d')] ?? 0);
        }

        // Detalle de las ventas del mes actual (base para los dos gráficos siguientes)
        $monthDetails = DetailSale::join('sales', 'sales.id', '=', 'detail_sales.sale_id')
            ->join('medicines', 'medicines.id', '=', 'detail_sales.medicine_id')
            ->whereYear('sales.date', $today->year)
            ->whereMonth('sales.date', $today->month);

        // Ventas del mes por categoría (importe vendido)
        $salesByCategory = (clone $monthDetails)
            ->join('categories', 'categories.id', '=', 'medicines.category_id')
            ->selectRaw('categories.name, SUM(detail_sales.subtotal) as total')
            ->groupBy('categories.name')
            ->orderByDesc('total')
            ->get();

        // 5 medicamentos más vendidos del mes (unidades vendidas)
        $topMedicines = (clone $monthDetails)
            ->selectRaw('medicines.name, SUM(detail_sales.amount) as quantity')
            ->groupBy('medicines.id', 'medicines.name')
            ->orderByDesc('quantity')
            ->limit(5)
            ->get();

        return view('principal.dashboard.index', compact(
            'soldToday', 'soldMonth', 'customers', 'lowStock',
            'weekLabels', 'weekTotals', 'salesByCategory', 'topMedicines'
        ));
    }
}
