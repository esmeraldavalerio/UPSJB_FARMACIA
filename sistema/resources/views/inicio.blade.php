@extends('template.administrador')

@section('title', 'Dashboard')
@section('name-section', 'Dashboard')
@section('description', 'Resumen del sistema de venta de medicamentos')
@section('item-breadcrumb', 'Inicio')
@section('section-breadcrumb', 'Dashboard')

@section('content')
    <div class="row g-3">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm"><div class="card-body">
                <div class="text-secondary small">Módulo</div>
                <div class="fw-bold">Categorías</div>
            </div></div>
        </div>
    </div>
@endsection
