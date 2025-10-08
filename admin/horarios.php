<?php
require_once '../conexion.php';
require_once 'auth.php'; requerido();
$accion = $_GET['accion'] ?? 'listar';
if ($accion === 'guardar' && $_SERVER['REQUEST_METHOD']==='POST') {
  $id = (int)($_POST['id_horario'] ?? 0);
  $id_medico = (int)($_POST['id_medico'] ?? 0);
  $dia = (int)($_POST['dia_semana'] ?? 1);
  $ini = $_POST['hora_inicio'] ?? '09:00:00';
  $fin = $_POST['hora_fin'] ?? '12:00:00';
  $dur = (int)($_POST['duracion_minutos'] ?? 20);
  if ($id>0) { $s=$pdo->prepare("UPDATE medicos_horarios SET id_medico=?, dia_semana=?, hora_inicio=?, hora_fin=?, duracion_minutos=? WHERE id_horario=?"); $s->execute([$id_medico,$dia,$ini,$fin,$dur,$id]); }
  else { $s=$pdo->prepare("INSERT INTO medicos_horarios (id_medico, dia_semana, hora_inicio, hora_fin, duracion_minutos) VALUES (?,?,?,?,?)"); $s->execute([$id_medico,$dia,$ini,$fin,$dur]); }
  header('Location: horarios.php'); exit;
}
if ($accion === 'eliminar') { $id=(int)($_GET['id'] ?? 0); $pdo->prepare("DELETE FROM medicos_horarios WHERE id_horario=?")->execute([$id]); header('Location: horarios.php'); exit; }
$editar = null;
if ($accion === 'editar') { $id=(int)($_GET['id'] ?? 0); $stm=$pdo->prepare("SELECT * FROM medicos_horarios WHERE id_horario=?"); $stm->execute([$id]); $editar=$stm->fetch(PDO::FETCH_ASSOC); }
$medicos = $pdo->query("SELECT id_medico, nombre_completo FROM medicos ORDER BY nombre_completo")->fetchAll(PDO::FETCH_ASSOC);
$lista = $pdo->query("SELECT h.*, m.nombre_completo FROM medicos_horarios h JOIN medicos m ON m.id_medico=h.id_medico ORDER BY m.nombre_completo, h.dia_semana")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Agendas</title><script src="https://cdn.tailwindcss.com"></script></head>
<body class="bg-slate-50"><main class="max-w-6xl mx-auto p-4">
<header class="flex items-center justify-between mb-4"><h1 class="text-2xl font-bold">Agendas</h1><a class="bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-700" href="panel.php">Volver</a></header>
<section class="bg-white border border-slate-200 rounded-2xl p-4 mb-4">
<h2 class="text-lg font-semibold mb-3"><?php echo $editar?'Editar agenda':'Nueva agenda'; ?></h2>
<form method="post" action="horarios.php?accion=guardar" class="grid sm:grid-cols-5 gap-3">
  <input type="hidden" name="id_horario" value="<?php echo (int)($editar['id_horario'] ?? 0); ?>">
  <section><label class="font-medium">Médico</label><select class="w-full border rounded-lg px-3 py-2" name="id_medico" required>
    <?php foreach ($medicos as $m): ?><option value="<?php echo (int)$m['id_medico']; ?>" <?php echo isset($editar['id_medico'])&&$editar['id_medico']==$m['id_medico']?'selected':''; ?>><?php echo htmlspecialchars($m['nombre_completo']); ?></option><?php endforeach; ?>
  </select></section>
  <section><label class="font-medium">Día semana</label><select class="w-full border rounded-lg px-3 py-2" name="dia_semana" required>
    <?php foreach ([1=>'Lun',2=>'Mar',3=>'Mié',4=>'Jue',5=>'Vie',6=>'Sáb',7=>'Dom'] as $k=>$v): ?><option value="<?php echo $k; ?>" <?php echo (isset($editar['dia_semana']) && (int)$editar['dia_semana']===$k)?'selected':''; ?>><?php echo $v; ?></option><?php endforeach; ?>
  </select></section>
  <section><label class="font-medium">Inicio</label><input class="w-full border rounded-lg px-3 py-2" type="time" name="hora_inicio" required value="<?php echo htmlspecialchars($editar['hora_inicio'] ?? '09:00:00'); ?>"></section>
  <section><label class="font-medium">Fin</label><input class="w-full border rounded-lg px-3 py-2" type="time" name="hora_fin" required value="<?php echo htmlspecialchars($editar['hora_fin'] ?? '12:00:00'); ?>"></section>
  <section><label class="font-medium">Duración (min)</label><input class="w-full border rounded-lg px-3 py-2" type="number" name="duracion_minutos" min="5" step="5" required value="<?php echo (int)($editar['duracion_minutos'] ?? 20); ?>"></section>
  <section class="sm:col-span-5"><button class="bg-emerald-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-emerald-700">Guardar</button></section>
</form></section>
<section class="overflow-x-auto bg-white border border-slate-200 rounded-2xl">
<table class="min-w-full text-sm"><thead class="bg-slate-100"><tr><th class="text-left p-3">Médico</th><th class="text-left p-3">Día</th><th class="text-left p-3">Inicio</th><th class="text-left p-3">Fin</th><th class="text-left p-3">Duración</th><th class="text-left p-3">Acciones</th></tr></thead>
<tbody><?php foreach ($lista as $h): ?><tr class="border-t"><td class="p-3"><?php echo htmlspecialchars($h['nombre_completo']); ?></td><td class="p-3"><?php echo ['','Lun','Mar','Mié','Jue','Vie','Sáb','Dom'][(int)$h['dia_semana']]; ?></td><td class="p-3"><?php echo substr($h['hora_inicio'],0,5); ?></td><td class="p-3"><?php echo substr($h['hora_fin'],0,5); ?></td><td class="p-3"><?php echo (int)$h['duracion_minutos']; ?> min</td><td class="p-3"><a class="text-indigo-700 font-semibold" href="horarios.php?accion=editar&id=<?php echo (int)$h['id_horario']; ?>">Editar</a> | <a class="text-rose-700 font-semibold" href="horarios.php?accion=eliminar&id=<?php echo (int)$h['id_horario']; ?>" onclick="return confirm('¿Eliminar agenda?')">Eliminar</a></td></tr><?php endforeach; ?></tbody></table>
</section></main></body></html>
