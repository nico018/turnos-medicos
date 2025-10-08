<?php
require_once 'conexion.php';
$inicio = $_GET['inicio'] ?? date('Y-m-01');
$fin    = $_GET['fin'] ?? date('Y-m-t');
$stm = $pdo->prepare("SELECT t.fecha, t.hora, t.estado, t.codigo_reserva,
                             m.nombre_completo AS medico, m.especialidad,
                             p.nombre_completo AS paciente, p.dni, p.email, p.telefono
                      FROM turnos t
                      JOIN medicos m ON m.id_medico = t.id_medico
                      JOIN pacientes p ON p.id_paciente = t.id_paciente
                      WHERE t.fecha BETWEEN ? AND ?
                      ORDER BY t.fecha, t.hora");
$stm->execute([$inicio, $fin]);
$filas = $stm->fetchAll(PDO::FETCH_ASSOC);
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="turnos_'+$inicio+'_'+$fin+'.csv"');
$out = fopen('php://output', 'w');
fputcsv($out, array_keys($filas[0] ?? ['fecha','hora','estado','codigo_reserva','medico','especialidad','paciente','dni','email','telefono']));
foreach ($filas as $f) { fputcsv($out, $f); }
fclose($out);
