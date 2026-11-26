<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de ventas</title>
    <style>
        @page { margin: 110px 35px 60px 35px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #212529; }

        header { position: fixed; top: -85px; left: 0; right: 0; height: 70px; border-bottom: 3px solid #ff0a41; }
        header .brand { font-size: 20px; font-weight: bold; color: #ff0a41; }
        header .subtitle { font-size: 10px; color: #6c757d; }
        header .date { position: absolute; top: 8px; right: 0; text-align: right; font-size: 10px; color: #6c757d; }

        footer { position: fixed; bottom: -40px; left: 0; right: 0; height: 25px; font-size: 9px; color: #6c757d; text-align: center; border-top: 1px solid #dee2e6; padding-top: 5px; }
        footer .page:after { content: counter(page); }

        h1 { font-size: 16px; margin: 0 0 10px 0; }

        .filters { width: 100%; margin-bottom: 12px; border-collapse: collapse; }
        .filters td { padding: 3px 0; }
        .filters .label { color: #6c757d; width: 80px; }

        .summary { width: 100%; margin-bottom: 15px; border-collapse: separate; border-spacing: 6px 0; }
        .summary td { width: 33%; background: #f8f9fa; border-top: 3px solid #6c757d; padding: 8px 10px; }
        .summary .title { font-size: 9px; color: #6c757d; text-transform: uppercase; }
        .summary .value { font-size: 15px; font-weight: bold; }

        .detail { width: 100%; border-collapse: collapse; }
        .detail th { background: #212529; color: #fff; padding: 6px; text-align: left; font-size: 10px; }
        .detail td { padding: 5px 6px; border-bottom: 1px solid #dee2e6; }
        .detail tr:nth-child(even) td { background: #f8f9fa; }
        .detail tfoot td { font-weight: bold; border-top: 2px solid #212529; border-bottom: none; background: #fff; }
        .right, .detail th.right { text-align: right; }
        .center, .detail th.center { text-align: center; }
        .empty { text-align: center; color: #6c757d; padding: 15px; }
    </style>
</head>
<body>
    <header>
        <div class="brand">FARM SJB</div>
        <div class="subtitle">Sistema de Venta de Medicamentos</div>
        <div class="date">
            Generado el {{ now()->format('d/m/Y H:i') }}<br>
            por {{ auth()->user()->name }}
        </div>
    </header>

    <footer>
        FARM SJB - Reporte de ventas &middot; Página <span class="page"></span>
    </footer>

    <h1>Reporte de ventas</h1>

    <table class="filters">
        <tr>
            <td class="label">Periodo:</td>
            <td>del {{ $from->format('d/m/Y') }} al {{ $to->format('d/m/Y') }}</td>
            <td class="label">Vendedor:</td>
            <td>{{ $filters['user'] }}</td>
        </tr>
        <tr>
            <td class="label">Cliente:</td>
            <td>{{ $filters['customer'] }}</td>
            <td class="label">Categoría:</td>
            <td>{{ $filters['category'] }}</td>
        </tr>
    </table>

    <table class="summary">
        <tr>
            <td>
                <div class="title">Total vendido</div>
                <div class="value">S/ {{ number_format($totalAmount, 2) }}</div>
            </td>
            <td>
                <div class="title">Número de ventas</div>
                <div class="value">{{ $salesCount }}</div>
            </td>
            <td>
                <div class="title">Ticket promedio</div>
                <div class="value">S/ {{ number_format($salesCount ? $totalAmount / $salesCount : 0, 2) }}</div>
            </td>
        </tr>
    </table>

    <table class="detail">
        <thead>
            <tr>
                <th class="center">N°</th>
                <th>Fecha</th>
                <th>Cliente</th>
                <th>DNI</th>
                <th>Vendedor</th>
                <th class="right">IGV</th>
                <th class="right">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($sales as $sale)
                <tr>
                    <td class="center">{{ $loop->iteration }}</td>
                    <td>{{ $sale->date->format('d/m/Y H:i') }}</td>
                    <td>{{ $sale->customer->name }}</td>
                    <td>{{ $sale->customer->dni }}</td>
                    <td>{{ $sale->user->name }}</td>
                    <td class="right">S/ {{ number_format($sale->igv, 2) }}</td>
                    <td class="right">S/ {{ number_format($sale->total, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="empty">No hay ventas con los filtros seleccionados</td>
                </tr>
            @endforelse
        </tbody>
        @if ($salesCount)
            <tfoot>
                <tr>
                    <td colspan="5" class="right">Totales</td>
                    <td class="right">S/ {{ number_format($totalIgv, 2) }}</td>
                    <td class="right">S/ {{ number_format($totalAmount, 2) }}</td>
                </tr>
            </tfoot>
        @endif
    </table>
</body>
</html>
