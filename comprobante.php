<?php
require_once 'conexion.php';
require_once 'utilidades.php';
$codigo = $_GET['codigo'] ?? '';
$stm = $pdo->prepare("
SELECT t.codigo_reserva, t.fecha, t.hora, t.creado_en, t.estado,
       m.nombre_completo AS medico_nombre, m.especialidad,
       p.nombre_completo AS paciente_nombre, p.dni, p.email, p.telefono
FROM turnos t
JOIN medicos m ON m.id_medico = t.id_medico
JOIN pacientes p ON p.id_paciente = t.id_paciente
WHERE t.codigo_reserva = ?");
$stm->execute([$codigo]);
$turno = $stm->fetch(PDO::FETCH_ASSOC);
if (!$turno) { exit('Comprobante no encontrado.'); }
require_once 'encabezado.php';
$qrData = urlencode('RESERVA|' . $turno['codigo_reserva'] . '|FECHA:' . $turno['fecha'] . '|HORA:' . substr($turno['hora'],0,5));
?>
<article class="bg-white border border-slate-200 rounded-2xl p-4 max-w-3xl mx-auto" aria-labelledby="titulo-comprobante"
         data-codigo="<?php echo h($turno['codigo_reserva']); ?>"
         data-fecha="<?php echo h($turno['fecha']); ?>"
         data-hora="<?php echo h(substr($turno['hora'],0,5)); ?>"
         data-medico="<?php echo h($turno['medico_nombre']); ?>"
         data-especialidad="<?php echo h($turno['especialidad']); ?>"
         data-paciente="<?php echo h($turno['paciente_nombre']); ?>"
         data-dni="<?php echo h($turno['dni']); ?>"
         data-email="<?php echo h($turno['email']); ?>"
         data-telefono="<?php echo h($turno['telefono']); ?>"
         data-creado="<?php echo h($turno['creado_en']); ?>">
  <header class="border-b border-dashed border-slate-300 pb-2 mb-3">
    <h2 id="titulo-comprobante" class="text-2xl font-bold">Comprobante de Reserva</h2>
    <p class="text-slate-700"><strong>Código:</strong> <?php echo h($turno['codigo_reserva']); ?> |
      <strong>Estado:</strong> <?php echo h($turno['estado']); ?></p>
  </header>
  <section class="grid sm:grid-cols-2 gap-4">
    <section class="space-y-1">
      <h3 class="text-lg font-semibold">Datos del turno</h3>
      <p><strong>Fecha:</strong> <?php echo h($turno['fecha']); ?></p>
      <p><strong>Hora:</strong> <?php echo h(substr($turno['hora'],0,5)); ?> hs</p>
      <p><strong>Médico:</strong> <?php echo h($turno['medico_nombre']); ?> (<?php echo h($turno['especialidad']); ?>)</p>
      <h3 class="text-lg font-semibold mt-3">Paciente</h3>
      <p><strong>Nombre:</strong> <?php echo h($turno['paciente_nombre']); ?></p>
      <p><strong>DNI:</strong> <?php echo h($turno['dni']); ?></p>
      <p><strong>Correo:</strong> <?php echo h($turno['email']); ?></p>
      <p><strong>Teléfono:</strong> <?php echo h($turno['telefono']); ?></p>
      <p class="text-slate-700 mt-2">Reservado el: <?php echo h($turno['creado_en']); ?>.</p>
    </section>
    <section class="flex flex-col items-center justify-center">
      <img class="border p-2 rounded-lg" alt="Código QR" width="160" height="160"
           src="https://api.qrserver.com/v1/create-qr-code/?size=160x160&data=<?php echo $qrData; ?>"/>
      <p class="text-slate-500 text-sm mt-2">Escaneá el QR para check‑in.</p>
    </section>
  </section>
  <section class="flex gap-3 mt-5">
    <button class="bg-sky-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-sky-700" onclick="window.print()">Imprimir</button>
    <button id="boton-pdf" class="bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-700">Descargar PDF</button>
    <a class="bg-slate-100 text-blue-700 border border-blue-200 px-4 py-2 rounded-lg font-semibold" href="index.php">Inicio</a>
  </section>
</article>
<script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>
<script>
(function(){
  const $art = document.querySelector('article[aria-labelledby="titulo-comprobante"]');
  const $btn = document.getElementById('boton-pdf');
  const datos = {
    cod: $art.dataset.codigo, fecha: $art.dataset.fecha, hora: $art.dataset.hora,
    medico: $art.dataset.medico, esp: $art.dataset.especialidad,
    pac: $art.dataset.paciente, dni: $art.dataset.dni, email: $art.dataset.email,
    tel: $art.dataset.telefono, creado: $art.dataset.creado
  };
  $btn.addEventListener('click', async () => {
    if (!window.jspdf || !window.jspdf.jsPDF) { alert('No se pudo cargar jsPDF.'); return; }
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF({ unit: 'pt', format: 'a4' });
    let y = 60;
    doc.setFont('helvetica','bold'); doc.setFontSize(18);
    doc.text('Comprobante de Reserva', 60, y); y += 26;
    doc.setFont('helvetica','normal'); doc.setFontSize(12);
    doc.text('Código: ' + datos.cod, 60, y); y += 18;
    doc.text('Estado: <?php echo h($turno['estado']); ?>', 60, y); y += 22;
    doc.setFont('helvetica','bold'); doc.text('Datos del turno', 60, y); y += 18;
    doc.setFont('helvetica','normal');
    doc.text('Fecha: ' + datos.fecha, 60, y); y += 16;
    doc.text('Hora: ' + datos.hora + ' hs', 60, y); y += 16;
    doc.text('Médico: ' + datos.medico + ' (' + datos.esp + ')', 60, y); y += 24;
    doc.setFont('helvetica','bold'); doc.text('Paciente', 60, y); y += 18;
    doc.setFont('helvetica','normal');
    doc.text('Nombre: ' + datos.pac, 60, y); y += 16;
    doc.text('DNI: ' + datos.dni, 60, y); y += 16;
    doc.text('Correo: ' + datos.email, 60, y); y += 16;
    doc.text('Teléfono: ' + datos.tel, 60, y); y += 24;
    doc.setFont('helvetica','bold'); doc.text('Información', 60, y); y += 18;
    doc.setFont('helvetica','normal');
    doc.text('Reservado el: ' + datos.creado, 60, y); y += 16;
    doc.text('Presentá este comprobante el día del turno o el código de reserva.', 60, y);
    doc.save('comprobante_' + datos.cod + '.pdf');
  });
})();
</script>
<?php require_once 'pie.php'; ?>
