<?php require_once 'auth.php'; requerido(); ?>
<!DOCTYPE html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Panel administración</title><script src="https://cdn.tailwindcss.com"></script></head>
<body class="bg-slate-50">
<header class="bg-blue-600 text-white"><nav class="max-w-6xl mx-auto flex items-center justify-between p-4">
  <h1 class="text-lg font-semibold tracking-tight">Panel</h1>
  <ul class="list-none m-0 p-0 flex gap-4">
    <li><a class="hover:underline" href="panel.php">Inicio</a></li>
    <li><a class="hover:underline" href="medicos.php">Médicos</a></li>
    <li><a class="hover:underline" href="horarios.php">Agendas</a></li>
    <li><a class="hover:underline" href="feriados.php">Feriados</a></li>
    <li><a class="hover:underline" href="auditoria.php">Auditoría</a></li>
    <li><a class="hover:underline" href="salir.php">Salir</a></li>
  </ul>
</nav></header>
<main class="max-w-6xl mx-auto p-4">
  <section class="bg-white border border-slate-200 rounded-2xl p-4">
    <h2 class="text-xl font-bold mb-2">Bienvenido</h2>
    <p>Gestioná médicos, agendas, feriados y auditá acciones.</p>
  </section>
</main></body></html>
