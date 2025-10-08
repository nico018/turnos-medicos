<?php
require_once 'conexion.php';
require_once 'utilidades.php';
$id_turno = isset($_GET['id_turno']) ? (int)$_GET['id_turno'] : 0;
$stm = $pdo->prepare("SELECT id_turno, id_medico, fecha, hora FROM turnos WHERE id_turno=?");
$stm->execute([$id_turno]);
$turno = $stm->fetch(PDO::FETCH_ASSOC);
if (!$turno) { exit('Turno no encontrado.'); }
$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $fecha_nueva = $_POST['fecha_turno'] ?? '';
  $hora_nueva  = $_POST['hora_turno'] ?? '';
  $dispo = horarios_disponibles($pdo, (int)$turno['id_medico'], $fecha_nueva);
  $hhmm = substr($hora_nueva,0,5);
  if (!in_array($hhmm, $dispo, true)) {
      $mensaje = 'La hora elegida no pertenece a la agenda del médico.';
  } else {
      try {
        $upd = $pdo->prepare("UPDATE turnos SET fecha=?, hora=?, estado='reservado', actualizado_por='publico' WHERE id_turno=?");
        $upd->execute([$fecha_nueva, $hora_nueva, $id_turno]);
        auditar($pdo, 'turno', 'reprogramar', 'Reprogramado desde público', 'publico');
        $cod = $pdo->query("SELECT codigo_reserva FROM turnos WHERE id_turno=$id_turno")->fetchColumn();
        header("Location: comprobante.php?codigo=" . urlencode($cod));
        exit;
      } catch (PDOException $e) {
        if ($e->getCode()==='23000') { $mensaje = 'Ese horario ya está tomado para este médico.'; }
        else { $mensaje = 'Error: ' . $e->getMessage(); }
      }
  }
}
require_once 'encabezado.php';
?>
<section aria-labelledby="titulo-reprog" class="space-y-4">
  <header><h2 id="titulo-reprog" class="text-2xl font-bold">Reprogramar turno</h2></header>
  <?php if ($mensaje): ?>
    <section class="bg-amber-50 border border-amber-200 text-amber-900 rounded-2xl p-3"><?php echo h($mensaje); ?></section>
  <?php endif; ?>
  <form method="post" class="bg-white border border-slate-200 rounded-2xl p-4 max-w-xl">
    <fieldset class="mb-4">
      <legend class="font-bold mb-2">Nueva fecha y hora</legend>
      <section class="grid sm:grid-cols-2 gap-3">
        <section>
          <label class="font-medium" for="fecha_turno">Fecha</label>
          <input class="w-full border rounded-lg px-3 py-2" type="date" id="fecha_turno" name="fecha_turno" required min="<?php echo date('Y-m-d'); ?>">
        </section>
        <section>
          <label class="font-medium" for="hora_turno">Hora</label>
          <select class="w-full border rounded-lg px-3 py-2" id="hora_turno" name="hora_turno" required>
            <option value="">Elegí fecha primero</option>
          </select>
        </section>
      </section>
    </fieldset>
    <section class="flex gap-3">
      <button class="bg-emerald-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-emerald-700" type="submit">Guardar</button>
      <a class="bg-slate-100 text-blue-700 border border-blue-200 px-4 py-2 rounded-lg font-semibold" href="turnos.php">Volver</a>
    </section>
  </form>
</section>
<script>
(function(){
  const fecha = document.getElementById('fecha_turno');
  const hora = document.getElementById('hora_turno');
  const idMedico = <?php echo (int)$turno['id_medico']; ?>;
  fecha.addEventListener('change', async () => {
    hora.innerHTML = '<option value="">Cargando...</option>';
    if (!fecha.value) return;
    const res = await fetch(`horarios_api.php?id_medico=${idMedico}&fecha=${fecha.value}`);
    const horas = await res.json();
    hora.innerHTML = horas.length ? '' : '<option value="">Sin horarios disponibles</option>';
    horas.forEach(hh => {
      const op = document.createElement('option');
      op.value = hh + ':00';
      op.textContent = hh + ' hs';
      hora.appendChild(op);
    });
  });
})();
</script>
<?php require_once 'pie.php'; ?>
