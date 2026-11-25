@extends('template.administrador')

@section('title', 'Dashboard')
@section('name_section', 'Dashboard')
@section('description', 'Resumen del sistema de venta de medicamentos')
@section('item_section', 'Inicio')
@section('route_section', route('dashboard.index'))
@section('section', 'Dashboard')
@section('module', 'Principal')

@section('content')
<div class="row g-3">
    <div class="col-12 col-md-6 col-xl-3">
        <div class="card bg-white border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="bi bi-box-seam fs-2 text-primary"></i>
                <div>
                    <div class="text-secondary small text-uppercase">Medicamentos</div>
                    <div class="fs-4 fw-bold">{{ $medicines }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-xl-3">
        <div class="card bg-white border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="bi bi-exclamation-triangle fs-2 text-danger"></i>
                <div>
                    <div class="text-secondary small text-uppercase">Stock bajo</div>
                    <div class="fs-4 fw-bold">{{ $lowStock }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-xl-3">
        <div class="card bg-white border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="bi bi-people fs-2 text-success"></i>
                <div>
                    <div class="text-secondary small text-uppercase">Clientes</div>
                    <div class="fs-4 fw-bold">{{ $customers }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-xl-3">
        <div class="card bg-white border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="bi bi-cart3 fs-2 text-warning"></i>
                <div>
                    <div class="text-secondary small text-uppercase">Ventas</div>
                    <div class="fs-4 fw-bold">{{ $sales }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
