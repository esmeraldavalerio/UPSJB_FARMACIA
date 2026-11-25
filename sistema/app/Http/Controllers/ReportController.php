<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Sale;
use App\Models\User;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReportController extends Controller
{
    // Reporte de ventas filtrado por periodo, vendedor y categoría
    public function sales(Request $request)
    {
        $request->validate([
            'from' => 'nullable|date',
            'to' => 'nullable|date|after_or_equal:from',
            'user_id' => 'nullable|exists:users,id',
            'category_id' => 'nullable|exists:categories,id',
        ], [
            'to.after_or_equal' => 'La fecha "Hasta" debe ser igual o posterior a la fecha "Desde".',
        ]);

        // Si no se envían fechas, se toma desde el inicio del mes hasta hoy
        $from = $request->filled('from') ? Carbon::parse($request->from)->startOfDay() : now()->startOfMonth();
        $to = $request->filled('to') ? Carbon::parse($request->to)->endOfDay() : now()->endOfDay();

        // Ventas del periodo; los filtros de vendedor y categoría solo se aplican si se eligieron
        $sales = Sale::with(['customer', 'user'])
            ->whereBetween('date', [$from, $to])
            ->when($request->user_id, function ($query, $userId) {
                $query->where('user_id', $userId);
            })
            ->when($request->category_id, function ($query, $categoryId) {
                // Ventas que tengan al menos un medicamento de la categoría elegida
                $query->whereHas('details.medicine', function ($medicine) use ($categoryId) {
                    $medicine->where('category_id', $categoryId);
                });
            })
            ->orderByDesc('date')
            ->get();

        // Tarjetas de resumen
        $totalAmount = $sales->sum('total');
        $salesCount = $sales->count();
        $averageTicket = $salesCount ? $totalAmount / $salesCount : 0;

        // Ventas por día: se recorre cada día del periodo para incluir los días sin ventas
        $totalsByDay = $sales->groupBy(fn ($sale) => $sale->date->format('Y-m-d'))
            ->map(fn ($group) => $group->sum('total'));

        $dayLabels = [];
        $dayTotals = [];
        foreach (CarbonPeriod::create($from->copy()->startOfDay(), $to->copy()->startOfDay()) as $day) {
            $dayLabels[] = $day->format('d/m/Y');
            $dayTotals[] = round((float) ($totalsByDay[$day->format('Y-m-d')] ?? 0), 2);
        }

        // Listas para los filtros
        $users = User::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

        return view('principal.reports.sales', compact(
            'from', 'to', 'sales', 'totalAmount', 'salesCount', 'averageTicket',
            'dayLabels', 'dayTotals', 'users', 'categories'
        ));
    }
}
