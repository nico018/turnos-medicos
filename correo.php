<?php
function enviar_correo_reserva(string $para, string $nombre, string $enlace, string $codigo, string $medico, string $esp, string $fecha, string $hora): void {
    $asunto = "Reserva confirmada - Código " . $codigo;
    $html = "<p>Hola " . htmlspecialchars($nombre) . ",</p>
             <p>Tu turno fue reservado.</p>
             <ul>
               <li><strong>Código:</strong> " . htmlspecialchars($codigo) . "</li>
               <li><strong>Médico:</strong> " . htmlspecialchars($medico) . " (" . htmlspecialchars($esp) . ")</li>
               <li><strong>Fecha/Hora:</strong> " . htmlspecialchars($fecha) . " " . htmlspecialchars($hora) . " hs</li>
             </ul>
             <p>Comprobante: <a href='" . htmlspecialchars($enlace) . "'>ver/descargar PDF</a></p>
             <p>Gracias.</p>";
    $enviado = false;
    $vendor = __DIR__ . '/vendor/autoload.php';
    if (file_exists($vendor)) {
        try {
            require_once $vendor;
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.tu_servidor.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'usuario@tu_servidor.com';
            $mail->Password = 'tu_password';
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->setFrom('no-reply@consultorio.test', 'Consultorio Salud+');
            $mail->addAddress($para, $nombre);
            $mail->isHTML(true);
            $mail->Subject = $asunto;
            $mail->Body = $html;
            $mail->AltBody = strip_tags(str_replace(['<br>','<br/>','<br />'], "\n", $html));
            $mail->send();
            $enviado = true;
        } catch (Throwable $e) {}
    }
    if (!$enviado) {
        $cab = "MIME-Version: 1.0\r\nContent-type: text/html; charset=UTF-8\r\nFrom: Consultorio Salud+ <no-reply@consultorio.test>";
        @mail($para, $asunto, $html, $cab);
    }
}
