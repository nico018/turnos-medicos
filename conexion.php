<?php
$servidor_bd = 'localhost';
$usuario_bd = 'root';
$contrasenia_bd = '';
$nombre_bd = 'consultorio';

try {
    $pdo = new PDO("mysql:host=$servidor_bd;dbname=$nombre_bd;charset=utf8mb4", $usuario_bd, $contrasenia_bd, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    exit('Error de conexión a la base de datos: ' . $e->getMessage());
}
