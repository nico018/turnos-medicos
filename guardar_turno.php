<?php
require_once 'conexion.php';
require_once 'utilidades.php';
require_once 'correo.php';

$id_medico         = isset($_POST['id_medico']) ? (int)$_POST['id_medico'] : 0;
$fecha_turno       = $_POST['fecha_turno'] ?? '';
$hora_turno        = $_POST['hora_turno'] ?? '';
$nombre_paciente   = trim($_POST['nombre_paciente'] ?? '');
$dni_paciente      = trim($_POST['dni_paciente'] ?? '');
$email_paciente    = trim($_POST['email_paciente'] ?? '');
$telefono_paciente = trim($_POST['telefono_paciente'] ?? '');

if (!$id_medico || !$fecha_turno || !$hora_turno || !$nombre_paciente || !$dni_paciente || !$email_paciente || !$telefono_paciente) {
    exit('Faltan datos obligatorios.');
}
$dispo = horarios_disponibles($pdo, $id_medico, $fecha_turno);
$hhmm = substr($hora_turno,0,5);
if (!in_array($hhmm, $dispo, true)) {
    exit('La hora seleccionada no es válida según la agenda del médico.');
}
$stm = $pdo->prepare("SELECT id_medico, nombre_completo, especialidad FROM medicos WHERE id_medico = ?");
$stm->execute([$id_medico]);
$info_medico = $stm->fetch(PDO::FETCH_ASSOC);
if (!$info_medico) { exit('El médico seleccionado no existe.'); }
$stm = $pdo->prepare("SELECT id_paciente FROM pacientes WHERE dni = ? AND email = ?");
$stm->execute([$dni_paciente, $email_paciente]);
$id_paciente = $stm->fetchColumn();
if (!$id_paciente) {
    $stm = $pdo->prepare("INSERT INTO pacientes (nombre_completo, dni, email, telefono) VALUES (?,?,?,?)");
    $stm->execute([$nombre_paciente, $dni_paciente, $email_paciente, $telefono_paciente]);
    $id_paciente = (int)$pdo->lastInsertId();
}
$codigo = generar_codigo_reserva(10);
try {
    $stm = $pdo->prepare("INSERT INTO turnos (id_medico, id_paciente, fecha, hora, codigo_reserva, creado_por) VALUES (?,?,?,?,?,?)");
    $stm->execute([$id_medico, $id_paciente, $fecha_turno, $hora_turno, $codigo, 'publico']);
    auditar($pdo, 'turno', 'crear', 'Turno creado desde público', 'publico');
} catch (PDOException $e) {
    if ($e->getCode() === '23000') exit('Ese horario ya fue reservado para este médico.');
    exit('Error al guardar el turno: ' . $e->getMessage());
}
$enlace = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']==='on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/comprobante.php?codigo=' . urlencode($codigo);
enviar_correo_reserva($email_paciente, $nombre_paciente, $enlace, $codigo, $info_medico['nombre_completo'], $info_medico['especialidad'], $fecha_turno, $hhmm);
header('Location: comprobante.php?codigo=' . urlencode($codigo));
exit;
