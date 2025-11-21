<?php
// crudfetch/listar.php
declare(strict_types=1);
require "conexion.php";

$txt = file_get_contents("php://input");
$busqueda = is_string($txt) ? trim($txt) : '';

switch ($busqueda === '') {
    case true:
        $stmt = $pdo->query("SELECT id,codigo,producto,precio,cantidad FROM productos ORDER BY id DESC");
        break;
    default:
        $like = "%{$busqueda}%";
        $sql = "SELECT id,codigo,producto,precio,cantidad
                FROM productos
                WHERE codigo LIKE :q1 OR producto LIKE :q2 OR CAST(precio AS CHAR) LIKE :q3
                ORDER BY id DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':q1'=>$like, ':q2'=>$like, ':q3'=>$like]);
        break;
}

$rows = $stmt->fetchAll();

function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }

foreach ($rows as $data) {
    echo "<tr ondblclick=\"Editar('".$data['id']."')\">
            <td>".(int)$data['id']."</td>
            <td>".e((string)$data['codigo'])."</td>
            <td>".e((string)$data['producto'])."</td>
            <td>".number_format((float)$data['precio'], 2, '.', '')."</td>
            <td>".(int)$data['cantidad']."</td>
            <td>
                <button type='button' class='btn btn-success btn-sm' onclick=\"Editar('".$data['id']."')\">Editar</button>
                <button type='button' class='btn btn-danger btn-sm' onclick=\"Eliminar('".$data['id']."')\">Eliminar</button>
            </td>
        </tr>";
}
