<?php
require_once 'conexion.php';
require_once 'utilidades.php';
$id_turno = isset($_GET['id_turno']) ? (int)$_GET['id_turno'] : 0;
if ($id_turno <= 0) { exit('Turno inválido.'); }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $motivo = trim($_POST['motivo'] ?? '');
  $stm = $pdo->prepare("UPDATE turnos SET estado='cancelado', motivo_cancelacion=?, actualizado_por='publico' WHERE id_turno=?");
  $stm->execute([$motivo, $id_turno]);
  auditar($pdo, 'turno', 'cancelar', 'Cancelación pública', 'publico');
  header('Location: turnos.php'); exit;
}
require_once 'encabezado.php';
?>
<section aria-labelledby="titulo-cancelar" class="space-y-4">
  <header><h2 id="titulo-cancelar" class="text-2xl font-bold">Cancelar turno</h2></header>
  <form method="post" class="bg-white border border-slate-200 rounded-2xl p-4 max-w-xl">
    <label class="font-medium" for="motivo">Motivo (opcional)</label>
    <textarea class="w-full border rounded-lg px-3 py-2" name="motivo" id="motivo" rows="3" placeholder="Ej: el paciente no puede asistir"></textarea>
    <section class="flex gap-3 mt-3">
      <button class="bg-rose-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-rose-700" type="submit">Confirmar cancelación</button>
      <a class="bg-slate-100 text-blue-700 border border-blue-200 px-4 py-2 rounded-lg font-semibold" href="turnos.php">Volver</a>
    </section>
  </form>
</section>
<?php require_once 'pie.php'; ?>
