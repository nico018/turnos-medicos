<?php
require_once '../conexion.php';
require_once 'auth.php'; requerido();
$lista = $pdo->query("SELECT * FROM auditoria ORDER BY creado_en DESC LIMIT 500")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Auditoría</title><script src="https://cdn.tailwindcss.com"></script></head>
<body class="bg-slate-50"><main class="max-w-6xl mx-auto p-4">
<header class="flex items-center justify-between mb-4"><h1 class="text-2xl font-bold">Auditoría</h1><a class="bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-700" href="panel.php">Volver</a></header>
<section class="overflow-x-auto bg-white border border-slate-200 rounded-2xl">
<table class="min-w-full text-sm"><thead class="bg-slate-100"><tr><th class="text-left p-3">Fecha</th><th class="text-left p-3">Entidad</th><th class="text-left p-3">Acción</th><th class="text-left p-3">Detalle</th><th class="text-left p-3">Usuario</th></tr></thead>
<tbody><?php foreach ($lista as $a): ?><tr class="border-t"><td class="p-3"><?php echo htmlspecialchars($a['creado_en']); ?></td><td class="p-3"><?php echo htmlspecialchars($a['entidad']); ?></td><td class="p-3"><?php echo htmlspecialchars($a['accion']); ?></td><td class="p-3"><?php echo htmlspecialchars($a['detalle']); ?></td><td class="p-3"><?php echo htmlspecialchars($a['usuario']); ?></td></tr><?php endforeach; ?></tbody></table>
</section></main></body></html>
