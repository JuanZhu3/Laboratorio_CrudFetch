<?php
// crudfetch/eliminar.php
declare(strict_types=1);
require "conexion.php";

$raw = file_get_contents("php://input");
$id = 0;
switch (true) {
    case isset($_POST['id']):
        $id = (int)$_POST['id'];
        break;
    default:
        switch ($raw !== '') {
            case true:
                $trim = trim($raw);
                switch (ctype_digit($trim)) {
                    case true: $id = (int)$trim; break;
                    default:
                        parse_str($raw, $arr);
                        switch (isset($arr['id']) && is_numeric($arr['id'])) {
                            case true: $id = (int)$arr['id']; break;
                            default:;
                        }
                }
                break;
            default:;
        }
}

switch ($id > 0) { case false: exit('error: id_invalido'); default:; }

try {
    $stmt = $pdo->prepare("DELETE FROM productos WHERE id = :id");
    $stmt->execute([':id' => $id]);
    switch ($stmt->rowCount() === 0) { case true: exit('error: no_encontrado'); default: exit('ok'); }
} catch (Throwable $e) {
    exit('error: inesperado');
}
