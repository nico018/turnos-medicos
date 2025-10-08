<?php
function generar_codigo_reserva(int $longitud = 10): string {
    $caracteres = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    $codigo = '';
    for ($i = 0; $i < $longitud; $i++) {
        $codigo .= $caracteres[random_int(0, strlen($caracteres) - 1)];
    }
    return $codigo;
}
function h(?string $t): string { return htmlspecialchars((string)$t, ENT_QUOTES, 'UTF-8'); }

function horarios_disponibles(PDO $pdo, int $id_medico, string $fecha): array {
    $stmF = $pdo->prepare("SELECT COUNT(*) FROM feriados WHERE fecha=?");
    $stmF->execute([$fecha]);
    if ((int)$stmF->fetchColumn() > 0) return [];
    $dia_semana = (int)date('N', strtotime($fecha));
    $stm = $pdo->prepare("SELECT hora_inicio, hora_fin, duracion_minutos FROM medicos_horarios WHERE id_medico=? AND dia_semana=?");
    $stm->execute([$id_medico, $dia_semana]);
    $agenda = $stm->fetch(PDO::FETCH_ASSOC);
    if (!$agenda) return [];
    [$hIni, $hFin, $dur] = [$agenda['hora_inicio'], $agenda['hora_fin'], (int)$agenda['duracion_minutos']];
    $ocup = $pdo->prepare("SELECT hora FROM turnos WHERE id_medico=? AND fecha=? AND estado <> 'cancelado'");
    $ocup->execute([$id_medico, $fecha]);
    $tomadas = array_map(fn($r)=>substr($r['hora'],0,5), $ocup->fetchAll(PDO::FETCH_ASSOC));
    $slots = [];
    $t = strtotime($fecha . ' ' . $hIni);
    $fin = strtotime($fecha . ' ' . $hFin);
    while ($t < $fin) {
        $hhmm = date('H:i', $t);
        if (!in_array($hhmm, $tomadas, true))
        $t = strtotime("+{$dur} minutes", $t);
    }
    return $slots;
}
function auditar(PDO $pdo, string $entidad, string $accion, string $detalle, string $usuario): void {
    $s = $pdo->prepare("INSERT INTO auditoria (entidad, accion, detalle, usuario) VALUES (?,?,?,?)");
    $s->execute([$entidad, $accion, $detalle, $usuario]);
}
