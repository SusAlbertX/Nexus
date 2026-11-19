<?php
require_once 'config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_nombre'] !== 'AlbertUltimate') {
    die("Acceso denegado.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $torneo_id = (int)$_POST['torneo_id'];
    $asunto = trim($_POST['asunto']);
    $mensaje = trim($_POST['mensaje']);

    if (empty($torneo_id) || empty($asunto) || empty($mensaje)) {
        header("Location: admin.php?err=1&msg=" . urlencode('Faltan datos para enviar el correo.'));
        exit;
    }

    $pdo = getPDO();

    $stmt = $pdo->prepare("SELECT titulo FROM torneos WHERE id = ?");
    $stmt->execute([$torneo_id]);
    $torneo = $stmt->fetch();

    if (!$torneo) {
        header("Location: admin.php?err=1&msg=" . urlencode('Torneo no encontrado.'));
        exit;
    }

    $stmt = $pdo->prepare("SELECT DISTINCT u.nombre, u.email 
                           FROM inscripciones_torneo i 
                           JOIN usuarios u ON i.usuario_id = u.id 
                           WHERE i.torneo_id = ?");
    $stmt->execute([$torneo_id]);
    $jugadores = $stmt->fetchAll();

    if (empty($jugadores)) {
        header("Location: admin.php?err=1&msg=" . urlencode('No hay jugadores inscritos en este torneo todavía.'));
        exit;
    }

    $mail = new PHPMailer(true);
    $enviados = 0;
    $fallidos = 0;

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'nexusgamingclubsupport@gmail.com'; 
        $mail->Password   = 'TU_CONTRASEÑA_DE_16_LETRAS';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';
        $mail->setFrom('nexusgamingclubsupport@gmail.com', 'Admin Nexus');

        foreach ($jugadores as $jugador) {
            try {
                $mail->clearAddresses();
                $mail->addAddress($jugador['email'], $jugador['nombre']);
                
                $mail->isHTML(true);
                $mail->Subject = "Aviso del torneo " . $torneo['titulo'] . ": " . $asunto;
                $mail->Body    = "
                    <h2>¡Hola, {$jugador['nombre']}!</h2>
                    <p>Te escribimos desde la administración de Nexus para darte un aviso sobre el torneo <strong>{$torneo['titulo']}</strong> en el que estás inscrito.</p>
                    <div style='background: #f4f4f4; padding: 15px; border-left: 4px solid #e67e22; margin: 20px 0;'>
                        <p><strong>Mensaje:</strong></p>
                        <p>" . nl2br(htmlspecialchars($mensaje)) . "</p>
                    </div>
                    <p>¡Gracias por participar!</p>
                    <p>- El equipo de Nexus</p>
                ";

                $mail->send();
                $enviados++;
            } catch (Exception $e) {
                $fallidos++;
            }
        }

        header("Location: admin.php?msg=" . urlencode("¡Correos enviados! Éxito: $enviados. Fallidos: $fallidos."));
        exit;

    } catch (Exception $e) {
        header("Location: admin.php?err=1&msg=" . urlencode('Error crítico al configurar el correo.'));
        exit;
    }
} else {
    header("Location: admin.php");
    exit;
}