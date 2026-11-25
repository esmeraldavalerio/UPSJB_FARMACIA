@extends('template.administrador')

@section('title', 'Categorías')
@section('name_section', 'Categorías')
@section('description', 'Editar registro de categoría')
@section('item_section', 'Editar')
@section('route_section', route('categories.index'))
@section('section', 'Categorías')
@section('module', 'Configuraciones')

@section('content')
<div class="card bg-white border-0 shadow-sm border-5 border-top border-secondary">
    <div class="card-body">
        <form action="{{ route('categories.update', $category) }}" method="POST">
            @csrf
            @method('PUT')
            @include('settings.categories.form')
        </form>
    </div>
</div>
@endsection
