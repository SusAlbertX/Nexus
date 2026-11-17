<?php
require_once 'config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

$email = trim($_POST['email'] ?? '');

if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: recuperar.php?err=" . urlencode('Introduce un correo válido.'));
    exit;
}

try {
    $pdo = getPDO();
    
    $stmt = $pdo->prepare("SELECT id, nombre FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) {
        header("Location: recuperar.php?msg=" . urlencode('Si tu correo está en nuestro sistema, recibirás un mensaje con tu nueva contraseña.'));
        exit;
    }

    $nueva_pass = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 8);
    
    $hash = password_hash($nueva_pass, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE usuarios SET password_hash = ? WHERE id = ?");
    $stmt->execute([$hash, $user['id']]);

    $mail = new PHPMailer(true);
    
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'nexusgamingclubsupport@gmail.com'; 
    $mail->Password   = 'TU_CONTRASEÑA_DE_16_LETRAS'; AQUÍ
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    $mail->CharSet    = 'UTF-8';

    $mail->setFrom('nexusgamingclubsupport@gmail.com', 'Soporte Nexus');
    $mail->addAddress($email, $user['nombre']);

    $mail->isHTML(true);
    $mail->Subject = "Tu nueva contraseña de Nexus";
    $mail->Body    = "
        <h2>¡Hola, {$user['nombre']}!</h2>
        <p>Hemos recibido una solicitud para restablecer tu contraseña.</p>
        <p>Tu nueva contraseña temporal es: <strong style='font-size: 1.2em; color: #8e44ad;'>{$nueva_pass}</strong></p>
        <p>Te recomendamos iniciar sesión y cambiarla inmediatamente por una que tú elijas desde la sección 'Ajustes de cuenta'.</p>
        <br>
        <p>Si no solicitaste esto, ignora este mensaje.</p>
        <p>- El equipo de Nexus</p>
    ";

    $mail->send();
    header("Location: recuperar.php?msg=" . urlencode('¡Te hemos enviado una nueva contraseña! Revisa tu correo.'));
    exit;

} catch (Exception $e) {
    header("Location: recuperar.php?err=" . urlencode('Error al enviar el correo. Inténtalo más tarde.'));
    exit;
} catch (PDOException $e) {
    header("Location: recuperar.php?err=" . urlencode('Error en la base de datos.'));
    exit;
}