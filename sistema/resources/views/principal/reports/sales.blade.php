@extends('template.administrador')

@section('title', 'Reporte de ventas')
@section('name_section', 'Reporte de ventas')
@section('description', 'Consulta las ventas por periodo, cliente, vendedor y categoría')
@section('item_section', 'Reportes')
@section('route_section', route('reports.sales'))
@section('section', 'Ventas')
@section('module', 'Principal')

@push('estilos')
<style>
    .chart-box { position: relative; height: 340px; }
</style>
@endpush

@section('content')
{{-- Filtros --}}
<div class="card bg-white border-0 shadow-sm border-5 border-top border-secondary mb-3">
    <div class="card-body">
        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form action="{{ route('reports.sales') }}" method="GET" class="row g-2 align-items-end">
            <div class="col-12 col-md-6 col-xl-2">
                <label for="from" class="form-label small mb-1">Desde</label>
                <input type="date" name="from" id="from" class="form-control" value="{{ $from->format('Y-m-d') }}">
            </div>
            <div class="col-12 col-md-6 col-xl-2">
                <label for="to" class="form-label small mb-1">Hasta</label>
                <input type="date" name="to" id="to" class="form-control" value="{{ $to->format('Y-m-d') }}">
            </div>
            <div class="col-12 col-md-6 col-xl-2">
                <label for="customer_id" class="form-label small mb-1">Cliente</label>
                <select name="customer_id" id="customer_id" class="form-select">
                    <option value="">Todos</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->id }}" @selected(request('customer_id') == $customer->id)>{{ $customer->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-6 col-xl-2">
                <label for="user_id" class="form-label small mb-1">Vendedor</label>
                <select name="user_id" id="user_id" class="form-select">
                    <option value="">Todos</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" @selected(request('user_id') == $user->id)>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-6 col-xl-2">
                <label for="category_id" class="form-label small mb-1">Categoría</label>
                <select name="category_id" id="category_id" class="form-select">
                    <option value="">Todas</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-xl-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary text-white flex-grow-1">
                    <i class="bi bi-funnel me-1"></i>Filtrar
                </button>
                <a href="{{ route('reports.sales') }}" class="btn btn-outline-secondary" title="Limpiar filtros">
                    <i class="bi bi-x-lg"></i>
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Tarjetas de resumen --}}
<div class="row g-3 mb-3">
    <div class="col-12 col-md-6 col-xl-3">
        <div class="card bg-white border-0 shadow-sm border-5 border-top border-secondary h-100">
            <div class="card-body d-flex align-items-start justify-content-between gap-3">
                <div>
                    <div class="text-secondary small">Total vendido</div>
                    <div class="fs-4 fw-bold">S/ {{ number_format($totalAmount, 2) }}</div>
                </div>
                <i class="bi bi-cash-stack fs-3 text-primary"></i>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-xl-3">
        <div class="card bg-white border-0 shadow-sm border-5 border-top border-secondary h-100">
            <div class="card-body d-flex align-items-start justify-content-between gap-3">
                <div>
                    <div class="text-secondary small">Número de ventas</div>
                    <div class="fs-4 fw-bold">{{ $salesCount }}</div>
                </div>
                <i class="bi bi-receipt fs-3 text-secondary"></i>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-xl-3">
        <div class="card bg-white border-0 shadow-sm border-5 border-top border-secondary h-100">
            <div class="card-body d-flex align-items-start justify-content-between gap-3">
                <div>
                    <div class="text-secondary small">IGV</div>
                    <div class="fs-4 fw-bold">S/ {{ number_format($totalIgv, 2) }}</div>
                </div>
                <i class="bi bi-percent fs-3 text-secondary"></i>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-xl-3">
        <div class="card bg-white border-0 shadow-sm border-5 border-top border-secondary h-100">
            <div class="card-body d-flex align-items-start justify-content-between gap-3">
                <div>
                    <div class="text-secondary small">Ticket promedio</div>
                    <div class="fs-4 fw-bold">S/ {{ number_format($averageTicket, 2) }}</div>
                </div>
                <i class="bi bi-bar-chart fs-3 text-secondary"></i>
            </div>
        </div>
    </div>
</div>

{{-- Ventas por día --}}
<div class="card bg-white border-0 shadow-sm border-5 border-top border-secondary mb-3">
    <div class="card-body">
        <h6 class="fw-semibold mb-3">Ventas por día</h6>
        <div class="chart-box">
            <canvas id="dayChart"></canvas>
        </div>
    </div>
</div>

{{-- Detalle de ventas --}}
<div class="card bg-white border-0 shadow-sm border-5 border-top border-secondary">
    <div class="card-body">
        {{-- Las descargas usan los mismos filtros que se están viendo en pantalla --}}
        @php($filters = request()->only(['from', 'to', 'customer_id', 'user_id', 'category_id']))
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
            <h6 class="fw-semibold mb-0">Detalle de ventas</h6>
            <div class="d-flex gap-2">
                <a href="{{ route('reports.sales.pdf', $filters) }}" class="btn btn-sm btn-outline-danger">
                    <i class="bi bi-file-earmark-pdf me-1"></i>Descargar PDF
                </a>
                <a href="{{ route('reports.sales.excel', $filters) }}" class="btn btn-sm btn-outline-success">
                    <i class="bi bi-file-earmark-excel me-1"></i>Descargar Excel
                </a>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover w-100 bg-white mb-0">
                <thead>
                    <tr>
                        <th class="align-middle">N.°</th>
                        <th class="align-middle">Fecha</th>
                        <th class="align-middle">Cliente</th>
                        <th class="align-middle">Vendedor</th>
                        <th class="align-middle text-end">IGV</th>
                        <th class="align-middle text-end">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sales as $sale)
                        <tr>
                            {{-- El número de venta abre su detalle --}}
                            <td class="align-middle">
                                <a href="{{ route('sales.show', $sale) }}">{{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}</a>
                            </td>
                            <td class="align-middle text-nowrap">{{ $sale->date->format('d/m/Y H:i') }}</td>
                            <td class="align-middle">{{ $sale->customer->name }}</td>
                            <td class="align-middle">{{ $sale->user->name }}</td>
                            <td class="align-middle text-end text-nowrap">S/ {{ number_format($sale->igv, 2) }}</td>
                            <td class="align-middle text-end text-nowrap">S/ {{ number_format($sale->total, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-3">No hay ventas con los filtros seleccionados</td>
                        </tr>
                    @endforelse
                </tbody>
                @if ($sales->isNotEmpty())
                    <tfoot>
                        <tr>
                            <th colspan="4" class="text-end">Totales</th>
                            <th class="text-end text-nowrap">S/ {{ number_format($totalIgv, 2) }}</th>
                            <th class="text-end text-nowrap">S/ {{ number_format($totalAmount, 2) }}</th>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/chart.umd.min.js') }}"></script>
<script>
    {{-- Datos enviados desde ReportController --}}
    const dayLabels = @json($dayLabels);
    const dayTotals = @json($dayTotals);

    // Color principal de la plantilla (Bootstrap)
    const primary = getComputedStyle(document.documentElement).getPropertyValue('--bs-primary').trim() || '#ff0a41';
    const soles = value => 'S/ ' + Number(value).toFixed(2);

    Chart.defaults.font.family = getComputedStyle(document.body).fontFamily;
    Chart.defaults.color = '#4b5563';

    // Se dibuja el gráfico cuando la fuente ya cargó, así los textos se miden bien
    document.fonts.ready.then(() => {
        // Gráfico de barras: total vendido por día
        new Chart(document.getElementById('dayChart'), {
            type: 'bar',
            data: {
                labels: dayLabels,
                datasets: [{
                    label: 'Total vendido',
                    data: dayTotals,
                    backgroundColor: primary,
                    maxBarThickness: 24
                }]
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: context => soles(context.parsed.y) } }
                },
                scales: {
                    x: { ticks: { maxRotation: 45, minRotation: 45 } },
                    y: { beginAtZero: true, ticks: { callback: soles } }
                }
            }
        });
    });
</script>
@endpush
