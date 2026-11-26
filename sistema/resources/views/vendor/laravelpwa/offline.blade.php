<!doctype html>
<html lang="es" dir="ltr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sin conexión | FARM SJB</title>
  {{-- Solo se usan archivos guardados en la caché, porque esta página se muestra sin internet --}}
  <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
  <link rel="icon" href="{{ asset('images/icons/icon-96x96.png') }}">
</head>

<body class="bg-light">

<main class="min-vh-100 d-flex align-items-center justify-content-center p-3">
  <div class="card bg-white border-0 shadow-sm border-5 border-top border-secondary text-center w-100" style="max-width: 420px;">
    <div class="card-body p-4">
      <img src="{{ asset('images/icons/icon-96x96.png') }}" alt="FARM SJB" width="72" height="72" class="mb-3">

      <h1 class="h5 fw-bold mb-2">Sin conexión a internet</h1>
      <p class="text-secondary mb-4">
        No se pudo abrir esta página porque no hay conexión.
        Revisa tu red y vuelve a intentarlo.
      </p>

      <button type="button" class="btn btn-primary text-white w-100" onclick="location.reload()">
        Reintentar
      </button>
    </div>
  </div>
</main>

</body>
</html>
