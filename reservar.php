<?php
require_once 'conexion.php';
require_once 'utilidades.php';

$id_medico = isset($_GET['id_medico']) ? (int)$_GET['id_medico'] : 0;
$sentencia = $pdo->prepare("SELECT id_medico, nombre_completo, especialidad, COALESCE(foto_url,'imagenes/placeholder.jpg') AS foto FROM medicos WHERE id_medico = ?");
$sentencia->execute([$id_medico]);
$medico = $sentencia->fetch(PDO::FETCH_ASSOC);
if (!$medico) { exit('Médico no encontrado.'); }
require_once 'encabezado.php';
?>
<section aria-labelledby="titulo-reserva" class="space-y-4">
  <header><h2 id="titulo-reserva" class="text-2xl font-bold">Reservar con <?php echo h($medico['nombre_completo']); ?> <span class="text-sky-700">(<?php echo h($medico['especialidad']); ?>)</span></h2></header>
  <form class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm max-w-2xl" action="guardar_turno.php" method="post" autocomplete="on">
    <fieldset class="mb-4">
      <legend class="font-bold mb-2">Datos del turno</legend>
      <input type="hidden" name="id_medico" value="<?php echo (int)$medico['id_medico']; ?>">
      <section class="grid sm:grid-cols-2 gap-3">
        <section>
          <label class="font-medium" for="fecha_turno">Fecha</label>
          <input class="w-full border rounded-lg px-3 py-2" type="date" id="fecha_turno" name="fecha_turno" required min="<?php echo date('Y-m-d'); ?>">
        </section>
        <section>
          <label class="font-medium" for="hora_turno">Hora</label>
          <select class="w-full border rounded-lg px-3 py-2" id="hora_turno" name="hora_turno" required>
            <option value="">Elegí la fecha primero</option>
          </select>
        </section>
      </section>
    </fieldset>
    <fieldset class="mb-4">
      <legend class="font-bold mb-2">Datos del paciente</legend>
      <label class="font-medium" for="nombre_paciente">Nombre y apellido</label>
      <input class="w-full border rounded-lg px-3 py-2 mb-3" type="text" id="nombre_paciente" name="nombre_paciente" required>
      <label class="font-medium" for="dni_paciente">DNI</label>
      <input class="w-full border rounded-lg px-3 py-2 mb-3" type="text" id="dni_paciente" name="dni_paciente" required>
      <label class="font-medium" for="email_paciente">Correo electrónico</label>
      <input class="w-full border rounded-lg px-3 py-2 mb-3" type="email" id="email_paciente" name="email_paciente" required>
      <label class="font-medium" for="telefono_paciente">Teléfono</label>
      <input class="w-full border rounded-lg px-3 py-2" type="tel" id="telefono_paciente" name="telefono_paciente" required>
    </fieldset>
    <section class="flex gap-3">
      <button class="bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-700" type="submit">Confirmar reserva</button>
      <a class="bg-slate-100 text-blue-700 border border-blue-200 px-4 py-2 rounded-lg font-semibold" href="index.php">Volver</a>
    </section>
  </form>
</section>
<script>
(function(){
  const fecha = document.getElementById('fecha_turno');
  const hora = document.getElementById('hora_turno');
  const idMedico = <?php echo (int)$medico['id_medico']; ?>;
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
