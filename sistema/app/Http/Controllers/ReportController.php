<?php

namespace App\Http\Controllers;

use App\Models\DetailSale;
use App\Models\Medicine;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'from' => 'nullable|date',
            'to' => 'nullable|date|after_or_equal:from',
            'user_id' => 'nullable|exists:users,id',
        ], [
            'to.after_or_equal' => 'La fecha final debe ser igual o posterior a la fecha inicial.',
        ]);

        $from = $request->filled('from') ? Carbon::parse($request->from)->startOfDay() : now()->startOfMonth();
        $to = $request->filled('to') ? Carbon::parse($request->to)->endOfDay() : now()->endOfDay();

        $salesQuery = Sale::whereBetween('date', [$from, $to])
            ->when($request->user_id, function ($query, $userId) {
                $query->where('user_id', $userId);
            });

        $sales = (clone $salesQuery)->with('user')->get();

        $totalAmount = $sales->sum('total');
        $totalIgv = $sales->sum('igv');
        $salesCount = $sales->count();
        $averageTicket = $salesCount ? $totalAmount / $salesCount : 0;

        $topMedicines = DetailSale::with('medicine')
            ->whereIn('sale_id', (clone $salesQuery)->select('id'))
            ->selectRaw('medicine_id, SUM(amount) as quantity, SUM(subtotal) as total_amount')
            ->groupBy('medicine_id')
            ->orderByDesc('quantity')
            ->limit(10)
            ->get();

        $salesBySeller = $sales->groupBy('user_id')->map(function ($group) {
            return [
                'name' => $group->first()->user->name,
                'count' => $group->count(),
                'total' => $group->sum('total'),
            ];
        })->sortByDesc('total')->values();

        $lowStockMedicines = Medicine::with('category')
            ->where('stock', '<=', MedicineController::LOW_STOCK)
            ->orderBy('stock')
            ->get();

        $users = User::orderBy('name')->get();

        return view('principal.reports.index', compact(
            'from', 'to', 'sales', 'totalAmount', 'totalIgv', 'salesCount', 'averageTicket',
            'topMedicines', 'salesBySeller', 'lowStockMedicines', 'users'
        ));
    }
}
