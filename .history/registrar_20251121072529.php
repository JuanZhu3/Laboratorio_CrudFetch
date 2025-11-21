<?php
// crudfetch/registrar.php  — versión switch-only
declare(strict_types=1);
require "conexion.php";

// Método
switch ($_SERVER['REQUEST_METHOD'] === 'POST') {
    case false: exit('error: metodo no permitido');
    default: /* continúa */;
}

// Entrada
$codigo    = isset($_POST['codigo'])   ? trim((string)$_POST['codigo'])   : '';
$producto  = isset($_POST['producto']) ? trim((string)$_POST['producto']) : '';
$precioRaw = $_POST['precio']   ?? null;
$cantRaw   = $_POST['cantidad'] ?? null;
$idp       = isset($_POST['idp'])      ? trim((string)$_POST['idp'])      : '';

// Normaliza precio (coma→punto)
switch (is_string($precioRaw)) {
    case true: $precioRaw = str_replace(',', '.', $precioRaw);
    default:   /* continúa */;
}
$precio   = $precioRaw;
$cantidad = $cantRaw;

// Validación
$errors = [];
switch (true) { case $codigo   === '' : $errors[] = 'codigo requerido';   default:; }
switch (true) { case $producto === '' : $errors[] = 'producto requerido'; default:; }
switch (true) {
    case ($precio === null) || (!is_numeric($precio)) || ((float)$precio <= 0):
        $errors[] = 'precio invalido';
    default:;
}
switch (true) {
    case ($cantidad === null) || (filter_var($cantidad, FILTER_VALIDATE_INT) === false) || ((int)$cantidad < 0):
        $errors[] = 'cantidad invalida';
    default:;
}
switch (empty($errors)) {
    case false: exit('error: ' . implode(', ', $errors));
    default: /* continúa */;
}

try {
    // Crear vs Actualizar
    switch ($idp === '') {
        // === CREAR ===
        case true:
            $q = $pdo->prepare("SELECT COUNT(*) FROM productos WHERE codigo = :c");
            $q->execute([':c' => $codigo]);
            switch ((int)$q->fetchColumn() > 0) {
                case true:  exit('error: el codigo ya existe');
                default:    /* continúa */;
            }

            $ins = $pdo->prepare(
                "INSERT INTO productos (codigo, producto, precio, cantidad)
                 VALUES (:c,:p,:pr,:ca)"
            );
            $ins->execute([
                ':c'  => $codigo,
                ':p'  => $producto,
                ':pr' => (float)$precio,
                ':ca' => (int)$cantidad
            ]);
            exit('ok');

        // === ACTUALIZAR ===
        default:
            $id = (int)$idp;

            $sel = $pdo->prepare("SELECT id,codigo,producto,precio,cantidad FROM productos WHERE id=:id");
            $sel->execute([':id' => $id]);
            $actual = $sel->fetch(PDO::FETCH_ASSOC);
            switch ($actual === false) {
                case true: exit('error: no_encontrado');
                default:   /* continúa */;
            }

            // Duplicado en otro registro
            $dup = $pdo->prepare("SELECT COUNT(*) FROM productos WHERE codigo=:c AND id<>:id");
            $dup->execute([':c' => $codigo, ':id' => $id]);
            switch ((int)$dup->fetchColumn() > 0) {
                case true: exit('error: el codigo ya existe en otro producto');
                default:   /* continúa */;
            }

            // Comparar para "sin_cambios"
            $mismos = (
                $actual['codigo']   === $codigo &&
                $actual['producto'] === $producto &&
                (float)$actual['precio']   == (float)$precio &&
                (int)$actual['cantidad']   == (int)$cantidad
            );
            switch ($mismos) {
                case true: exit('sin_cambios');
                default:   /* continúa */;
            }

            $upd = $pdo->prepare(
                "UPDATE productos
                 SET codigo=:c, producto=:p, precio=:pr, cantidad=:ca
                 WHERE id=:id"
            );
            $upd->execute([
                ':c'  => $codigo,
                ':p'  => $producto,
                ':pr' => (float)$precio,
                ':ca' => (int)$cantidad,
                ':id' => $id
            ]);
            exit('modificado');
    }
} catch (Throwable $e) {
    exit('error: inesperado');
}
