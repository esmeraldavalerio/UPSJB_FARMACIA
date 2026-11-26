<?php

namespace App\Http\Controllers;

use App\Exports\SalesExport;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    // Reporte de ventas filtrado por periodo, cliente, vendedor y categoría
    public function sales(Request $request)
    {
        [$from, $to, $sales] = $this->filterSales($request);

        // Tarjetas de resumen
        $totalAmount = $sales->sum('total');
        $totalIgv = $sales->sum('igv');
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
        $customers = Customer::orderBy('name')->get();
        $users = User::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

        return view('principal.reports.sales', compact(
            'from', 'to', 'sales', 'totalAmount', 'totalIgv', 'salesCount', 'averageTicket',
            'dayLabels', 'dayTotals', 'customers', 'users', 'categories'
        ));
    }

    // Descarga el reporte en PDF con los mismos filtros de la pantalla
    public function salesPdf(Request $request)
    {
        [$from, $to, $sales] = $this->filterSales($request);

        $pdf = Pdf::loadView('principal.reports.sales-pdf', [
            'from' => $from,
            'to' => $to,
            'sales' => $sales,
            'filters' => $this->filterNames($request),
            'totalAmount' => $sales->sum('total'),
            'totalIgv' => $sales->sum('igv'),
            'salesCount' => $sales->count(),
        ])->setPaper('a4')
            // Solo guarda las letras usadas de la fuente, así el PDF pesa mucho menos
            ->setOption('isFontSubsettingEnabled', true);

        return $pdf->download($this->fileName($from, $to, 'pdf'));
    }

    // Descarga el reporte en Excel con los mismos filtros de la pantalla
    public function salesExcel(Request $request)
    {
        [$from, $to, $sales] = $this->filterSales($request);

        return Excel::download(new SalesExport($sales), $this->fileName($from, $to, 'xlsx'));
    }

    // Valida los filtros y devuelve el periodo y las ventas encontradas
    private function filterSales(Request $request): array
    {
        $request->validate([
            'from' => 'nullable|date',
            'to' => 'nullable|date|after_or_equal:from',
            'customer_id' => 'nullable|exists:customers,id',
            'user_id' => 'nullable|exists:users,id',
            'category_id' => 'nullable|exists:categories,id',
        ], [
            'to.after_or_equal' => 'La fecha "Hasta" debe ser igual o posterior a la fecha "Desde".',
        ]);

        // Si no se envían fechas, se toma desde el inicio del mes hasta hoy
        $from = $request->filled('from') ? Carbon::parse($request->from)->startOfDay() : now()->startOfMonth();
        $to = $request->filled('to') ? Carbon::parse($request->to)->endOfDay() : now()->endOfDay();

        // Ventas del periodo; los filtros de cliente, vendedor y categoría solo se aplican si se eligieron
        $sales = Sale::with(['customer', 'user'])
            ->whereBetween('date', [$from, $to])
            ->when($request->customer_id, function ($query, $customerId) {
                $query->where('customer_id', $customerId);
            })
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

        return [$from, $to, $sales];
    }

    // Nombres del cliente, el vendedor y la categoría elegidos, para mostrarlos en el PDF
    private function filterNames(Request $request): array
    {
        return [
            'customer' => $request->customer_id ? Customer::find($request->customer_id)?->name : 'Todos',
            'user' => $request->user_id ? User::find($request->user_id)?->name : 'Todos',
            'category' => $request->category_id ? Category::find($request->category_id)?->name : 'Todas',
        ];
    }

    // Ejemplo: reporte-ventas-01-11-2026-al-26-11-2026.pdf
    private function fileName(Carbon $from, Carbon $to, string $extension): string
    {
        return 'reporte-ventas-'.$from->format('d-m-Y').'-al-'.$to->format('d-m-Y').'.'.$extension;
    }
}
