<?php
require_once 'conexion.php';
require_once 'utilidades.php';
require_once 'encabezado.php';

$consulta = $pdo->query("SELECT id_medico, nombre_completo, especialidad, COALESCE(foto_url,'imagenes/placeholder.jpg') AS foto FROM medicos ORDER BY nombre_completo ASC");
$medicos = $consulta->fetchAll(PDO::FETCH_ASSOC);
?>
<section aria-labelledby="titulo-lista-medicos" class="space-y-4">
  <header><h2 id="titulo-lista-medicos" class="text-2xl font-bold">Profesionales y especialidades</h2>
  <p class="text-slate-600">Elegí un profesional para ver su agenda y reservar tu turno.</p></header>
  <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
  <?php foreach ($medicos as $m): ?>
    <article class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition">
      <figure class="m-0">
        <img class="w-full h-44 object-cover" src="<?php echo h($m['foto']); ?>" alt="Foto de <?php echo h($m['nombre_completo']); ?>">
        <figcaption class="oculto-visualmente">Foto</figcaption>
      </figure>
      <header class="p-4">
        <h3 class="text-lg font-semibold"><?php echo h($m['nombre_completo']); ?></h3>
        <p class="text-sky-700 font-medium"><?php echo h($m['especialidad']); ?></p>
      </header>
      <footer class="px-4 pb-4">
        <a class="inline-block bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-700" href="reservar.php?id_medico=<?php echo (int)$m['id_medico']; ?>">Ver agenda y reservar</a>
      </footer>
    </article>
  <?php endforeach; ?>
  </section>
</section>
<?php require_once 'pie.php'; ?>
