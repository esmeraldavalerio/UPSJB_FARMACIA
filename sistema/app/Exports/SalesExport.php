<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

// Hoja de Excel con el detalle de las ventas del reporte
class SalesExport implements FromCollection, WithHeadings, WithMapping, WithColumnFormatting, WithStyles, WithTitle, ShouldAutoSize
{
    protected $sales;
    protected $row = 0;

    // Recibe las ventas ya filtradas desde ReportController
    public function __construct($sales)
    {
        $this->sales = $sales;
    }

    public function collection()
    {
        return $this->sales;
    }

    public function headings(): array
    {
        return ['N°', 'Fecha', 'Cliente', 'DNI', 'Vendedor', 'IGV (S/)', 'Total (S/)'];
    }

    // Una fila por venta
    public function map($sale): array
    {
        return [
            ++$this->row,
            $sale->date->format('d/m/Y H:i'),
            $sale->customer->name,
            $sale->customer->dni,
            $sale->user->name,
            (float) $sale->igv,
            (float) $sale->total,
        ];
    }

    // Formato de moneda para las columnas de IGV y total
    public function columnFormats(): array
    {
        return [
            'F' => '#,##0.00',
            'G' => '#,##0.00',
        ];
    }

    // Encabezado en negrita con el color de la plantilla
    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'FF0A41']],
            ],
        ];
    }

    public function title(): string
    {
        return 'Ventas';
    }
}
