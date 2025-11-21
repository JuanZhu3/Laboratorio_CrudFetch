<?php
// crudfetch/editar.php
declare(strict_types=1);
require "conexion.php";

$idRaw = file_get_contents("php://input");
$id = (int)$idRaw;

$stmt = $pdo->prepare("SELECT id,codigo,producto,precio,cantidad FROM productos WHERE id = :id");
$stmt->execute([':id' => $id]);
$res = $stmt->fetch(PDO::FETCH_ASSOC);

switch ($res === false) {
    case true:
        http_response_code(404);
        echo json_encode(['error'=>'no_encontrado'], JSON_UNESCAPED_UNICODE);
        exit;
    default:
        echo json_encode($res, JSON_UNESCAPED_UNICODE);
        exit;
}
