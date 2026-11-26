<!doctype html>
<html lang="es" dir="ltr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="San Juan – Sistema de Venta de Medicamentos">
  <title>@yield('title', 'Dashboard') | FARM SJB</title>
  <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
  <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
  @stack('estilos')
  {{-- PWA: manifest, íconos y registro del service worker --}}
  @laravelPWA
</head>

<body>

<div id="app-wrapper" class="d-flex">

  @include('partials.sidebar')

  <div class="main-wrapper d-flex flex-column flex-grow-1 overflow-hidden">
    @include('partials.header')

    <main class="app-content flex-grow-1 overflow-y-auto p-3 p-lg-4">
      <div class="container-fluid p-0">
        <div class="d-flex align-items-center justify-content-between flex-column flex-sm-row gap-2 mb-4">

          <p class="text-secondary mb-0">@yield('description')</p>

          <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);" aria-label="Ruta de navegación">
            <ol class="breadcrumb mb-0 small">
              @hasSection('module')
              <li class="breadcrumb-item">
                <span class="text-secondary">@yield('module')</span>
              </li>
              @endif
              @hasSection('section')
              <li class="breadcrumb-item">
                <a href="@yield('route_section', '#')" class="text-decoration-none">@yield('section')</a>
              </li>
              @endif
              <li class="breadcrumb-item active text-muted fw-medium" aria-current="page">
                <span>@yield('item_section', 'Inicio')</span>
              </li>
            </ol>
          </nav>
        </div>

        @yield('content')

      </div>
    </main>

  </div>
</div>

@include('partials.modal-logout')

<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('js/app.js') }}"></script>
@stack('scripts')

</body>
</html>
