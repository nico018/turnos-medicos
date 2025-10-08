<?php
require_once '../conexion.php';
require_once 'auth.php'; requerido();
$accion = $_GET['accion'] ?? 'listar';
if ($accion === 'guardar' && $_SERVER['REQUEST_METHOD']==='POST') {
  $id = (int)($_POST['id_feriado'] ?? 0);
  $fecha = $_POST['fecha'] ?? '';
  $motivo = trim($_POST['motivo'] ?? '');
  if ($id>0) { $s=$pdo->prepare("UPDATE feriados SET fecha=?, motivo=? WHERE id_feriado=?"); $s->execute([$fecha,$motivo,$id]); }
  else { $s=$pdo->prepare("INSERT INTO feriados (fecha, motivo) VALUES (?,?)"); $s->execute([$fecha,$motivo]); }
  header('Location: feriados.php'); exit;
}
if ($accion === 'eliminar') { $id=(int)($_GET['id'] ?? 0); $pdo->prepare("DELETE FROM feriados WHERE id_feriado=?")->execute([$id]); header('Location: feriados.php'); exit; }
$editar=null; if ($accion === 'editar') { $id=(int)($_GET['id'] ?? 0); $stm=$pdo->prepare("SELECT * FROM feriados WHERE id_feriado=?"); $stm->execute([$id]); $editar=$stm->fetch(PDO::FETCH_ASSOC); }
$lista = $pdo->query("SELECT * FROM feriados ORDER BY fecha DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Feriados</title><script src="https://cdn.tailwindcss.com"></script></head>
<body class="bg-slate-50"><main class="max-w-6xl mx-auto p-4">
<header class="flex items-center justify-between mb-4"><h1 class="text-2xl font-bold">Feriados / Bloqueos</h1><a class="bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-700" href="panel.php">Volver</a></header>
<section class="bg-white border border-slate-200 rounded-2xl p-4 mb-4">
<h2 class="text-lg font-semibold mb-3"><?php echo $editar?'Editar':'Nuevo'; ?></h2>
<form method="post" action="feriados.php?accion=guardar" class="grid sm:grid-cols-3 gap-3">
  <input type="hidden" name="id_feriado" value="<?php echo (int)($editar['id_feriado'] ?? 0); ?>">
  <section><label class="font-medium">Fecha</label><input class="w-full border rounded-lg px-3 py-2" type="date" name="fecha" required value="<?php echo htmlspecialchars($editar['fecha'] ?? ''); ?>"></section>
  <section class="sm:col-span-2"><label class="font-medium">Motivo</label><input class="w-full border rounded-lg px-3 py-2" name="motivo" required value="<?php echo htmlspecialchars($editar['motivo'] ?? ''); ?>"></section>
  <section class="sm:col-span-3"><button class="bg-emerald-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-emerald-700">Guardar</button></section>
</form></section>
<section class="overflow-x-auto bg-white border border-slate-200 rounded-2xl">
<table class="min-w-full text-sm"><thead class="bg-slate-100"><tr><th class="text-left p-3">Fecha</th><th class="text-left p-3">Motivo</th><th class="text-left p-3">Acciones</th></tr></thead>
<tbody><?php foreach ($lista as $f): ?><tr class="border-t"><td class="p-3"><?php echo htmlspecialchars($f['fecha']); ?></td><td class="p-3"><?php echo htmlspecialchars($f['motivo']); ?></td><td class="p-3"><a class="text-indigo-700 font-semibold" href="feriados.php?accion=editar&id=<?php echo (int)$f['id_feriado']; ?>">Editar</a> | <a class="text-rose-700 font-semibold" href="feriados.php?accion=eliminar&id=<?php echo (int)$f['id_feriado']; ?>" onclick="return confirm('¿Eliminar?')">Eliminar</a></td></tr><?php endforeach; ?></tbody></table>
</section></main></body></html>
