<?php
require_once 'conexion.php';
require_once 'utilidades.php';
$id_medico = (int)($_GET['id_medico'] ?? 0);
$fecha = $_GET['fecha'] ?? '';
header('Content-Type: application/json');
if (!$id_medico || !$fecha) { echo json_encode([]); exit; }
echo json_encode(horarios_disponibles($pdo, $id_medico, $fecha));
