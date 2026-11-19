<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

$nombre    = trim($_POST['nombre'] ?? '');
$email     = trim($_POST['email'] ?? '');
$categoria = trim($_POST['categoria'] ?? '');
$asunto    = trim($_POST['asunto'] ?? '');
$mensaje   = trim($_POST['mensaje'] ?? '');

if ($nombre === '' || $email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)
    || $categoria === '' || $asunto === '' || $mensaje === '') {
    header("Location: soporte.php?err=1&msg=" . urlencode('Rellena todos los campos correctamente.'));
    exit;
}

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'nexusgamingclubsupport@gmail.com'; 
    
    $mail->Password   = 'TU_CONTRASEÑA_AQUI'; 
    
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    $mail->CharSet    = 'UTF-8';

    $mail->setFrom('nexusgamingclubsupport@gmail.com', 'Soporte Nexus');
    $mail->addAddress('nexusgamingclubsupport@gmail.com', 'Admin Nexus');
    $mail->addReplyTo($email, $nombre);
    $mail->isHTML(true);
    $mail->Subject = "Nuevo Ticket de Soporte: $asunto";
    $mail->Body    = "
        <h2>Nueva solicitud de soporte</h2>
        <p><strong>Nombre:</strong> {$nombre}</p>
        <p><strong>Email:</strong> {$email}</p>
        <p><strong>Categoría:</strong> {$categoria}</p>
        <p><strong>Asunto:</strong> {$asunto}</p>
        <p><strong>Mensaje:</strong></p>
        <p>" . nl2br(htmlspecialchars($mensaje)) . "</p>
    ";
    $mail->send();

    $mail->clearAddresses();
    $mail->clearReplyTos();

    $mail->addAddress($email, $nombre);
    
    $mail->Subject = "Hemos recibido tu mensaje - Soporte Nexus";
    $mail->Body    = "
        <h2>¡Hola, {$nombre}!</h2>
        <p>Este es un correo automático para confirmarte que hemos recibido tu solicitud de soporte.</p>
        <p>Nuestro equipo revisará tu caso y te responderá a este mismo correo lo antes posible.</p>
        <br>
        <p><strong>Resumen de tu mensaje:</strong></p>
        <p><strong>Asunto:</strong> {$asunto}</p>
        <p><strong>Categoría:</strong> {$categoria}</p>
        <p><em>\" " . nl2br(htmlspecialchars($mensaje)) . " \"</em></p>
        <br>
        <p>¡Gracias por confiar en Nexus!</p>
    ";
    $mail->send();

    header("Location: soporte.php?msg=" . urlencode('¡Mensaje enviado! Revisa tu bandeja de entrada, te hemos mandado una confirmación.'));
    exit;

} catch (Exception $e) {
    header("Location: soporte.php?err=1&msg=" . urlencode('Error al enviar el correo: ' . $mail->ErrorInfo));
    exit;
}