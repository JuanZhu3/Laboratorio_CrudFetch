<?php
// crudfetch/index.php
declare(strict_types=1);
?><!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>CRUD Productos</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <script defer src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script defer src="script.js?v=switch-only-1"></script>
</head>
<body class="bg-light">
<div class="container py-4">
  <h1 class="h3 mb-3">Inventario de productos</h1>

  <form id="frm" class="card shadow-sm" onsubmit="return false;">
    <div class="card-body row g-3">
      <input type="hidden" id="idp" name="idp">
      <div class="col-md-3">
        <label for="codigo" class="form-label">Código</label>
        <input id="codigo" name="codigo" class="form-control" required minlength="1" maxlength="64" pattern="\S.{0,63}">
        <div class="invalid-feedback">Código requerido (1–64), no solo espacios.</div>
      </div>
      <div class="col-md-4">
        <label for="producto" class="form-label">Producto</label>
        <input id="producto" name="producto" class="form-control" required minlength="1" maxlength="255">
        <div class="invalid-feedback">Producto requerido (≤255).</div>
      </div>
      <div class="col-md-2">
        <label for="precio" class="form-label">Precio</label>
        <input id="precio" name="precio" type="number" class="form-control" required step="0.01" min="0.01" max="999999999.99">
        <div class="invalid-feedback">Precio > 0 (hasta 999,999,999.99).</div>
      </div>
      <div class="col-md-2">
        <label for="cantidad" class="form-label">Cantidad</label>
        <input id="cantidad" name="cantidad" type="number" class="form-control" required step="1" min="0" max="999999999">
        <div class="invalid-feedback">Cantidad entera ≥ 0 (hasta 999,999,999).</div>
      </div>
      <div class="col-md-1 d-grid">
        <button id="registrar" type="button" class="btn btn-primary mt-4">Registrar</button>
      </div>
    </div>
  </form>

  <div class="d-flex align-items-center justify-content-between my-3">
    <input id="buscar" class="form-control w-auto" style="min-width: 280px" placeholder="Buscar por código, nombre o precio…">
    <small class="text-muted">Doble clic en fila para editar.</small>
  </div>

  <div class="card shadow-sm">
    <div class="table-responsive">
      <table class="table table-striped table-hover align-middle mb-0">
        <thead class="table-light">
          <tr><th>ID</th><th>Código</th><th>Producto</th><th>Precio</th><th>Cantidad</th><th>Acciones</th></tr>
        </thead>
        <tbody id="resultado"></tbody>
      </table>
    </div>
  </div>
</div>

<script>
(function () {
  const frm = document.getElementById('frm');
  const btn = document.getElementById('registrar');
  btn.addEventListener('click', function () {
    switch (frm.checkValidity()) {
      case false: frm.classList.add('was-validated'); break;
      default: frm.classList.remove('was-validated');
    }
  });
  frm.addEventListener('submit', function (e) { e.preventDefault(); });
})();
</script>
</body>
</html>
