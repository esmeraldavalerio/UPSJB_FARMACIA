@extends('template.administrador')

@section('title', 'Dashboard')
@section('name_section', 'Dashboard')
@section('description', 'Resumen de estadísticas')
@section('item_section', 'Inicio')
@section('route_section', route('dashboard.index'))
@section('section', 'Dashboard')
@section('module', 'Principal')

@push('estilos')
<style>
    .stat-icon { width: 44px; height: 44px; }
    .chart-box { position: relative; height: 300px; }
</style>
@endpush

@section('content')
{{-- Tarjetas de resumen --}}
<div class="row g-3 mb-3">
    <div class="col-12 col-md-6 col-xl-3">
        <div class="card bg-white border-0 shadow-sm border-5 border-top border-secondary h-100">
            <div class="card-body d-flex align-items-center justify-content-between gap-3">
                <div>
                    <div class="text-secondary small">Vendido hoy</div>
                    <div class="fs-4 fw-bold">S/ {{ number_format($soldToday, 2) }}</div>
                </div>
                <div class="stat-icon rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center flex-shrink-0">
                    <i class="bi bi-cash-stack fs-5"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-xl-3">
        <div class="card bg-white border-0 shadow-sm border-5 border-top border-secondary h-100">
            <div class="card-body d-flex align-items-center justify-content-between gap-3">
                <div>
                    <div class="text-secondary small">Vendido este mes</div>
                    <div class="fs-4 fw-bold">S/ {{ number_format($soldMonth, 2) }}</div>
                </div>
                <div class="stat-icon rounded-circle bg-secondary-subtle text-secondary d-flex align-items-center justify-content-center flex-shrink-0">
                    <i class="bi bi-calendar-check fs-5"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-xl-3">
        <div class="card bg-white border-0 shadow-sm border-5 border-top border-secondary h-100">
            <div class="card-body d-flex align-items-center justify-content-between gap-3">
                <div>
                    <div class="text-secondary small">Clientes registrados</div>
                    <div class="fs-4 fw-bold">{{ $customers }}</div>
                </div>
                <div class="stat-icon rounded-circle bg-secondary-subtle text-secondary d-flex align-items-center justify-content-center flex-shrink-0">
                    <i class="bi bi-people fs-5"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-xl-3">
        <div class="card bg-white border-0 shadow-sm border-5 border-top border-secondary h-100">
            <div class="card-body d-flex align-items-center justify-content-between gap-3">
                <div>
                    <div class="text-secondary small">Medicamentos con stock bajo</div>
                    <div class="fs-4 fw-bold">{{ $lowStock }}</div>
                    <div class="text-secondary" style="font-size:.75rem">Stock igual o menor a {{ \App\Http\Controllers\MedicineController::LOW_STOCK }}</div>
                </div>
                <div class="stat-icon rounded-circle bg-danger-subtle text-danger d-flex align-items-center justify-content-center flex-shrink-0">
                    <i class="bi bi-exclamation-triangle fs-5"></i>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Ventas de los últimos 7 días --}}
<div class="card bg-white border-0 shadow-sm border-5 border-top border-secondary mb-3">
    <div class="card-body">
        <h6 class="fw-semibold mb-3">Ventas de los últimos 7 días</h6>
        <div class="chart-box">
            <canvas id="weekChart"></canvas>
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- Ventas del mes por categoría --}}
    <div class="col-12 col-xl-5">
        <div class="card bg-white border-0 shadow-sm border-5 border-top border-secondary h-100">
            <div class="card-body">
                <h6 class="fw-semibold mb-3">Ventas del mes por categoría</h6>
                @if ($salesByCategory->isEmpty())
                    <p class="text-center text-muted py-5 mb-0">Sin ventas este mes</p>
                @else
                    <div class="chart-box">
                        <canvas id="categoryChart"></canvas>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- 5 medicamentos más vendidos del mes --}}
    <div class="col-12 col-xl-7">
        <div class="card bg-white border-0 shadow-sm border-5 border-top border-secondary h-100">
            <div class="card-body">
                <h6 class="fw-semibold mb-3">5 medicamentos más vendidos del mes</h6>
                @if ($topMedicines->isEmpty())
                    <p class="text-center text-muted py-5 mb-0">Sin ventas este mes</p>
                @else
                    <div class="chart-box">
                        <canvas id="topChart"></canvas>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/chart.umd.min.js') }}"></script>
<script>
    {{-- Datos enviados desde DashboardController --}}
    const weekLabels = @json($weekLabels);
    const weekTotals = @json($weekTotals);
    const categoryLabels = @json($salesByCategory->pluck('name'));
    const categoryTotals = @json($salesByCategory->pluck('total')->map(fn ($total) => (float) $total));
    const topLabels = @json($topMedicines->pluck('name'));
    const topQuantities = @json($topMedicines->pluck('quantity')->map(fn ($quantity) => (int) $quantity));

    // Colores tomados de la plantilla (Bootstrap)
    const styles = getComputedStyle(document.documentElement);
    const primary = styles.getPropertyValue('--bs-primary').trim() || '#ff0a41';
    const palette = [primary, '#212529', '#4b5563', '#f87191', '#9ca3af'];

    Chart.defaults.font.family = getComputedStyle(document.body).fontFamily;
    Chart.defaults.color = '#4b5563';

    // Formato de moneda para ejes y tooltips
    const soles = value => 'S/ ' + Number(value).toFixed(2);

    // Se dibujan los gráficos cuando la fuente ya cargó, así los textos se miden bien
    document.fonts.ready.then(() => {
        // Gráfico de línea: ventas de los últimos 7 días
        new Chart(document.getElementById('weekChart'), {
            type: 'line',
            data: {
                labels: weekLabels,
                datasets: [{
                    label: 'Ventas',
                    data: weekTotals,
                    borderColor: primary,
                    backgroundColor: primary + '33',
                    fill: true,
                    tension: 0.4,
                    cubicInterpolationMode: 'monotone',
                    pointRadius: 4,
                    pointBackgroundColor: primary
                }]
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: context => soles(context.parsed.y) } }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { callback: soles } }
                }
            }
        });

        // Gráfico de dona: ventas del mes por categoría
        const categoryCanvas = document.getElementById('categoryChart');
        if (categoryCanvas) {
            new Chart(categoryCanvas, {
                type: 'doughnut',
                data: {
                    labels: categoryLabels,
                    datasets: [{
                        data: categoryTotals,
                        backgroundColor: categoryLabels.map((_, i) => palette[i % palette.length]),
                        borderColor: '#fff',
                        borderWidth: 2
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    cutout: '55%',
                    plugins: {
                        legend: { position: 'bottom', labels: { boxWidth: 12 } },
                        tooltip: { callbacks: { label: context => context.label + ': ' + soles(context.parsed) } }
                    }
                }
            });
        }

        // Gráfico de barras horizontales: 5 medicamentos más vendidos
        const topCanvas = document.getElementById('topChart');
        if (topCanvas) {
            new Chart(topCanvas, {
                type: 'bar',
                data: {
                    labels: topLabels,
                    datasets: [{
                        label: 'Unidades vendidas',
                        data: topQuantities,
                        backgroundColor: topLabels.map((_, i) => palette[i % palette.length]),
                        borderRadius: 4
                    }]
                },
                options: {
                    indexAxis: 'y',
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { beginAtZero: true, ticks: { precision: 0 } }
                    }
                }
            });
        }
    });
</script>
@endpush
