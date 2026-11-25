@extends('template.administrador')

@section('title', 'Reportes')
@section('name_section', 'Reportes')
@section('description', 'Reporte de ventas del ' . $from->format('d/m/Y') . ' al ' . $to->format('d/m/Y'))
@section('item_section', 'Inicio')
@section('route_section', route('reports.index'))
@section('section', 'Reportes')
@section('module', 'Principal')

@push('estilos')
<style>
    @media print {
        #sidebar, .app-header, .no-print, nav[aria-label="Ruta de navegación"] { display: none !important; }
        .app-content { overflow: visible !important; }
        .card { box-shadow: none !important; break-inside: avoid; }
    }
</style>
@endpush

@section('content')
<div class="card bg-white border-0 shadow-sm border-5 border-top border-secondary mb-3 no-print">
    <div class="card-body">
        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form action="{{ route('reports.index') }}" method="GET" class="row g-2 align-items-end">
            <div class="col-12 col-md-3">
                <label for="from" class="form-label small text-uppercase text-muted mb-1">Desde</label>
                <input type="date" name="from" id="from" class="form-control" value="{{ $from->format('Y-m-d') }}">
            </div>
            <div class="col-12 col-md-3">
                <label for="to" class="form-label small text-uppercase text-muted mb-1">Hasta</label>
                <input type="date" name="to" id="to" class="form-control" value="{{ $to->format('Y-m-d') }}">
            </div>
            <div class="col-12 col-md-3">
                <label for="user_id" class="form-label small text-uppercase text-muted mb-1">Vendedor</label>
                <select name="user_id" id="user_id" class="form-select">
                    <option value="">Todos los vendedores</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" @selected(request('user_id') == $user->id)>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-3 d-flex gap-1">
                <button type="submit" class="btn btn-outline-primary w-100" title="Filtrar"><i class="bi bi-search"></i></button>
                <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary w-100" title="Limpiar filtros"><i class="bi bi-x-lg"></i></a>
                <button type="button" class="btn btn-primary text-white w-100" title="Imprimir" onclick="window.print()"><i class="bi bi-printer"></i></button>
            </div>
        </form>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-12 col-md-6 col-xl-3">
        <div class="card bg-white border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="bi bi-cash-stack fs-2 text-success"></i>
                <div>
                    <div class="text-secondary small text-uppercase">Total vendido</div>
                    <div class="fs-4 fw-bold">S/ {{ number_format($totalAmount, 2) }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-xl-3">
        <div class="card bg-white border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="bi bi-cart3 fs-2 text-warning"></i>
                <div>
                    <div class="text-secondary small text-uppercase">Nº de ventas</div>
                    <div class="fs-4 fw-bold">{{ $salesCount }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-xl-3">
        <div class="card bg-white border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="bi bi-receipt fs-2 text-primary"></i>
                <div>
                    <div class="text-secondary small text-uppercase">IGV recaudado</div>
                    <div class="fs-4 fw-bold">S/ {{ number_format($totalIgv, 2) }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-xl-3">
        <div class="card bg-white border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="bi bi-graph-up fs-2 text-info"></i>
                <div>
                    <div class="text-secondary small text-uppercase">Ticket promedio</div>
                    <div class="fs-4 fw-bold">S/ {{ number_format($averageTicket, 2) }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-12 col-xl-7">
        <div class="card bg-white border-0 shadow-sm h-100">
            <div class="card-header bg-white">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-trophy me-2 text-warning"></i>Medicamentos más vendidos</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-hover w-100 bg-white mb-0">
                    <thead>
                        <tr>
                            <th class="text-center align-middle text-uppercase small">Nº</th>
                            <th class="text-center align-middle text-uppercase small">Medicamento</th>
                            <th class="text-center align-middle text-uppercase small">Cantidad</th>
                            <th class="text-center align-middle text-uppercase small">Importe</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($topMedicines as $item)
                            <tr>
                                <td class="text-center align-middle">{{ $loop->iteration }}</td>
                                <td class="text-start align-middle">{{ $item->medicine->name ?? 'Eliminado' }}</td>
                                <td class="text-center align-middle">{{ $item->quantity }}</td>
                                <td class="text-end align-middle">S/ {{ number_format($item->total_amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">Sin ventas en el periodo</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-5">
        <div class="card bg-white border-0 shadow-sm h-100">
            <div class="card-header bg-white">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-person-badge me-2 text-primary"></i>Ventas por vendedor</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-hover w-100 bg-white mb-0">
                    <thead>
                        <tr>
                            <th class="text-center align-middle text-uppercase small">Vendedor</th>
                            <th class="text-center align-middle text-uppercase small">Ventas</th>
                            <th class="text-center align-middle text-uppercase small">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($salesBySeller as $seller)
                            <tr>
                                <td class="text-start align-middle">{{ $seller['name'] }}</td>
                                <td class="text-center align-middle">{{ $seller['count'] }}</td>
                                <td class="text-end align-middle">S/ {{ number_format($seller['total'], 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-3">Sin ventas en el periodo</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="card bg-white border-0 shadow-sm">
    <div class="card-header bg-white">
        <h6 class="mb-0 fw-semibold"><i class="bi bi-exclamation-triangle me-2 text-danger"></i>Medicamentos con stock bajo</h6>
    </div>
    <div class="card-body">
        <table class="table table-sm table-hover w-100 bg-white mb-0">
            <thead>
                <tr>
                    <th class="text-center align-middle text-uppercase small">Nº</th>
                    <th class="text-center align-middle text-uppercase small">Medicamento</th>
                    <th class="text-center align-middle text-uppercase small">Categoría</th>
                    <th class="text-center align-middle text-uppercase small">Stock</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($lowStockMedicines as $medicine)
                    <tr>
                        <td class="text-center align-middle">{{ $loop->iteration }}</td>
                        <td class="text-start align-middle">{{ $medicine->name }}</td>
                        <td class="text-center align-middle">{{ $medicine->category->name }}</td>
                        <td class="text-center align-middle"><span class="badge bg-danger">{{ $medicine->stock }}</span></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-3">Todos los medicamentos tienen stock suficiente</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
