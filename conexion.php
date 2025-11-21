<?php
// crudfetch/conexion.php
declare(strict_types=1);
$servidor = "mysql:host=localhost;dbname=crud;charset=utf8mb4";
$user = "root";
$pass = "";
try {
    $pdo = new PDO($servidor, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    exit("error: conexion fallida");
}
