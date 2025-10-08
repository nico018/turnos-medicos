<?php
require_once 'conexion.php';
require_once 'utilidades.php';
require_once 'encabezado.php';

$especialidad = $_GET['especialidad'] ?? '';
$id_medico = isset($_GET['id_medico']) ? (int)$_GET['id_medico'] : 0;
$dni = trim($_GET['dni'] ?? '');

$sql = "SELECT t.id_turno, t.fecha, t.hora, t.estado, t.codigo_reserva,
               m.id_medico, m.nombre_completo AS medico, m.especialidad,
               p.nombre_completo AS paciente, p.dni
        FROM turnos t
        JOIN medicos m ON m.id_medico = t.id_medico
        JOIN pacientes p ON p.id_paciente = t.id_paciente
        WHERE 1=1";
$param = [];
if ($especialidad !== '') { $sql .= " AND m.especialidad = ?"; $param[] = $especialidad; }
if ($id_medico > 0)       { $sql .= " AND m.id_medico = ?";   $param[] = $id_medico; }
if ($dni !== '')          { $sql .= " AND p.dni LIKE ?";      $param[] = "%$dni%"; }
$sql .= " ORDER BY t.fecha DESC, t.hora DESC LIMIT 300";
$stm = $pdo->prepare($sql);
$stm->execute($param);
$turnos = $stm->fetchAll(PDO::FETCH_ASSOC);

$esp = $pdo->query("SELECT DISTINCT especialidad FROM medicos ORDER BY especialidad")->fetchAll(PDO::FETCH_COLUMN);
$docs = $pdo->query("SELECT id_medico, nombre_completo FROM medicos ORDER BY nombre_completo")->fetchAll(PDO::FETCH_ASSOC);
?>
<section aria-labelledby="titulo-turnos" class="space-y-4">
  <header class="flex items-center justify-between">
    <h2 id="titulo-turnos" class="text-2xl font-bold">Turnos</h2>
    <a class="bg-emerald-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-emerald-700" href="turnos_csv.php">Exportar CSV</a>
  </header>
  <form method="get" class="bg-white border border-slate-200 rounded-2xl p-4 grid gap-3 sm:grid-cols-4">
    <section>
      <label class="font-medium" for="especialidad">Especialidad</label>
      <select class="w-full border rounded-lg px-3 py-2" name="especialidad" id="especialidad">
        <option value="">Todas</option>
        <?php foreach ($esp as $e): ?>
          <option value="<?php echo h($e); ?>" <?php echo $especialidad===$e?'selected':''; ?>><?php echo h($e); ?></option>
        <?php endforeach; ?>
      </select>
    </section>
    <section>
      <label class="font-medium" for="id_medico">Médico</label>
      <select class="w-full border rounded-lg px-3 py-2" name="id_medico" id="id_medico">
        <option value="0">Todos</option>
        <?php foreach ($docs as $d): ?>
          <option value="<?php echo (int)$d['id_medico']; ?>" <?php echo $id_medico===(int)$d['id_medico']?'selected':''; ?>>
            <?php echo h($d['nombre_completo']); ?>
          </option>
        <?php endforeach; ?>
      </select>
    </section>
    <section>
      <label class="font-medium" for="dni">DNI</label>
      <input class="w-full border rounded-lg px-3 py-2" type="text" id="dni" name="dni" value="<?php echo h($dni); ?>" placeholder="Buscar DNI">
    </section>
    <section class="flex items-end">
      <button class="bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-700" type="submit">Filtrar</button>
    </section>
  </form>
  <section class="overflow-x-auto bg-white border border-slate-200 rounded-2xl">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-100"><tr>
        <th class="text-left p-3">Fecha</th>
        <th class="text-left p-3">Hora</th>
        <th class="text-left p-3">Médico</th>
        <th class="text-left p-3">Especialidad</th>
        <th class="text-left p-3">Paciente</th>
        <th class="text-left p-3">DNI</th>
        <th class="text-left p-3">Estado</th>
        <th class="text-left p-3">Acciones</th>
      </tr></thead>
      <tbody>
        <?php foreach ($turnos as $t): ?>
          <tr class="border-t">
            <td class="p-3"><?php echo h($t['fecha']); ?></td>
            <td class="p-3"><?php echo h(substr($t['hora'],0,5)); ?></td>
            <td class="p-3"><?php echo h($t['medico']); ?></td>
            <td class="p-3"><?php echo h($t['especialidad']); ?></td>
            <td class="p-3"><?php echo h($t['paciente']); ?></td>
            <td class="p-3"><?php echo h($t['dni']); ?></td>
            <td class="p-3"><?php echo h($t['estado']); ?></td>
            <td class="p-3">
              <a class="text-blue-700 font-semibold" href="comprobante.php?codigo=<?php echo urlencode($t['codigo_reserva']); ?>">Ver</a> |
              <a class="text-rose-700 font-semibold" href="cancelar_turno.php?id_turno=<?php echo (int)$t['id_turno']; ?>">Cancelar</a> |
              <a class="text-emerald-700 font-semibold" href="reprogramar.php?id_turno=<?php echo (int)$t['id_turno']; ?>">Reprogramar</a> |
              <a class="text-indigo-700 font-semibold" href="confirmar_turno.php?id_turno=<?php echo (int)$t['id_turno']; ?>">Confirmar</a> |
              <a class="text-slate-700 font-semibold" href="atender_turno.php?id_turno=<?php echo (int)$t['id_turno']; ?>">Atendido</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </section>
</section>
<?php require_once 'pie.php'; ?>
