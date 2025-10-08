<?php
require_once '../conexion.php';
require_once 'auth.php'; requerido();
$accion = $_GET['accion'] ?? 'listar';
if ($accion === 'guardar' && $_SERVER['REQUEST_METHOD']==='POST') {
  $id = (int)($_POST['id_medico'] ?? 0);
  $nom = trim($_POST['nombre_completo'] ?? '');
  $esp = trim($_POST['especialidad'] ?? '');
  $foto = trim($_POST['foto_url'] ?? '');
  if ($id>0) { $s=$pdo->prepare("UPDATE medicos SET nombre_completo=?, especialidad=?, foto_url=? WHERE id_medico=?"); $s->execute([$nom,$esp,$foto,$id]); }
  else { $s=$pdo->prepare("INSERT INTO medicos (nombre_completo, especialidad, foto_url) VALUES (?,?,?)"); $s->execute([$nom,$esp,$foto]); }
  header('Location: medicos.php'); exit;
}
if ($accion === 'eliminar') { $id=(int)($_GET['id']??0); $pdo->prepare("DELETE FROM medicos WHERE id_medico=?")->execute([$id]); header('Location: medicos.php'); exit; }
$editar = null;
if ($accion === 'editar') { $id=(int)($_GET['id']??0); $stm=$pdo->prepare("SELECT * FROM medicos WHERE id_medico=?"); $stm->execute([$id]); $editar=$stm->fetch(PDO::FETCH_ASSOC); }
$lista = $pdo->query("SELECT * FROM medicos ORDER BY nombre_completo")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Médicos</title><script src="https://cdn.tailwindcss.com"></script></head>
<body class="bg-slate-50"><main class="max-w-6xl mx-auto p-4">
<header class="flex items-center justify-between mb-4"><h1 class="text-2xl font-bold">Médicos</h1><a class="bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-700" href="panel.php">Volver</a></header>
<section class="bg-white border border-slate-200 rounded-2xl p-4 mb-4">
<h2 class="text-lg font-semibold mb-3"><?php echo $editar?'Editar médico':'Nuevo médico'; ?></h2>
<form method="post" action="medicos.php?accion=guardar" class="grid sm:grid-cols-3 gap-3">
  <input type="hidden" name="id_medico" value="<?php echo (int)($editar['id_medico'] ?? 0); ?>">
  <section><label class="font-medium">Nombre completo</label><input class="w-full border rounded-lg px-3 py-2" name="nombre_completo" required value="<?php echo htmlspecialchars($editar['nombre_completo'] ?? ''); ?>"></section>
  <section><label class="font-medium">Especialidad</label><input class="w-full border rounded-lg px-3 py-2" name="especialidad" required value="<?php echo htmlspecialchars($editar['especialidad'] ?? ''); ?>"></section>
  <section><label class="font-medium">URL de foto</label><input class="w-full border rounded-lg px-3 py-2" name="foto_url" value="<?php echo htmlspecialchars($editar['foto_url'] ?? ''); ?>"></section>
  <section class="sm:col-span-3"><button class="bg-emerald-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-emerald-700">Guardar</button></section>
</form></section>
<section class="overflow-x-auto bg-white border border-slate-200 rounded-2xl">
<table class="min-w-full text-sm"><thead class="bg-slate-100"><tr><th class="text-left p-3">Nombre</th><th class="text-left p-3">Especialidad</th><th class="text-left p-3">Foto</th><th class="text-left p-3">Acciones</th></tr></thead>
<tbody><?php foreach ($lista as $m): ?><tr class="border-t"><td class="p-3"><?php echo htmlspecialchars($m['nombre_completo']); ?></td><td class="p-3"><?php echo htmlspecialchars($m['especialidad']); ?></td><td class="p-3"><a class="text-blue-700" href="../<?php echo htmlspecialchars($m['foto_url']); ?>" target="_blank">ver</a></td><td class="p-3"><a class="text-indigo-700 font-semibold" href="medicos.php?accion=editar&id=<?php echo (int)$m['id_medico']; ?>">Editar</a> | <a class="text-rose-700 font-semibold" href="medicos.php?accion=eliminar&id=<?php echo (int)$m['id_medico']; ?>" onclick="return confirm('¿Eliminar?')">Eliminar</a></td></tr><?php endforeach; ?></tbody></table>
</section></main></body></html>
