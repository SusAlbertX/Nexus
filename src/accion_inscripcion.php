<?php
require_once 'config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$pdo = getPDO();
$pdo->exec("SET NAMES utf8mb4");
$torneo_id = (int)($_POST['torneo_id'] ?? 0);
$modo = $_POST['modo'] ?? 'solo'; 
$club_id = isset($_POST['club_id']) ? (int)$_POST['club_id'] : null;
$usuario_id = $_SESSION['usuario_id'];

function enviarCorreoInscripcion($email, $nombre, $torneo) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'nexusgamingclubsupport@gmail.com'; 
        $mail->Password   = 'TU_CONTRASEÑA_DE_16_LETRAS'; // <-- PON TU CONTRASEÑA AQUÍ
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';

        $mail->setFrom('nexusgamingclubsupport@gmail.com', 'Nexus Torneos');
        $mail->addAddress($email, $nombre);

        $mail->isHTML(true);
        $mail->Subject = "Confirmación de inscripción: " . $torneo['titulo'];
        
        $fecha = date('d/m/Y \a \l\a\s H:i', strtotime($torneo['fecha_inicio']));
        
        $mail->Body = "
            <h2>¡Hola, {$nombre}!</h2>
            <p>Te confirmamos que te has inscrito correctamente al torneo <strong>{$torneo['titulo']}</strong>.</p>
            <p><strong>Detalles del torneo:</strong></p>
            <ul>
                <li><strong>Juego:</strong> " . strtoupper($torneo['juego']) . "</li>
                <li><strong>Fecha:</strong> {$fecha}</li>
                <li><strong>Premio:</strong> {$torneo['premio']}</li>
            </ul>
            <p>¡Prepárate y mucha suerte! 🎮</p>
            <p>- El equipo de Nexus</p>
        ";
        $mail->send();
        
        // Si llega aquí, es que se ha enviado bien. Te avisamos en la web.
        // (Descomenta la siguiente línea si quieres verlo en la pantalla, pero romperá el diseño)
        // die("¡Correo enviado a " . $email . "!");

    } catch (Exception $e) {
        // Si falla, paramos la web y te mostramos el error de PHPMailer
        die("<h2 style='color:red; font-family:sans-serif; text-align:center; margin-top:50px;'>Fallo al enviar correo a $email</h2>
             <p style='text-align:center; font-family:sans-serif;'>" . $mail->ErrorInfo . "</p>");
    }
}

try {
    $stmt = $pdo->prepare("SELECT * FROM torneos WHERE id = ?");
    $stmt->execute([$torneo_id]);
    $torneo = $stmt->fetch();

    if (!$torneo) {
        header("Location: torneo.php?err=1&msg=" . urlencode('Torneo no encontrado.'));
        exit;
    }

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM inscripciones_torneo WHERE torneo_id = ?");
    $stmt->execute([$torneo_id]);
    $inscritos = $stmt->fetchColumn();
    $huecos_disponibles = $torneo['max_jugadores'] - $inscritos;

    if ($modo === 'solo') {
        if ($huecos_disponibles <= 0) {
            header("Location: detalle_torneo.php?id=$torneo_id&err=1&msg=" . urlencode('El torneo está lleno.'));
            exit;
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO inscripciones_torneo (torneo_id, usuario_id) VALUES (?, ?)");
            $stmt->execute([$torneo_id, $usuario_id]);
            
            $stmt = $pdo->prepare("SELECT nombre, email FROM usuarios WHERE id = ?");
            $stmt->execute([$usuario_id]);
            $user = $stmt->fetch();
            if ($user) {
                enviarCorreoInscripcion($user['email'], $user['nombre'], $torneo);
            }

            header("Location: detalle_torneo.php?id=$torneo_id&msg=" . urlencode('¡Inscripción individual confirmada! Revisa tu correo.'));
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                header("Location: detalle_torneo.php?id=$torneo_id&err=1&msg=" . urlencode('Ya estás inscrito en este torneo.'));
            } else {
                throw $e;
            }
        }
        exit;
    }

    elseif ($modo === 'club' && $club_id) {
        $stmt = $pdo->prepare("SELECT lider_id FROM clubs WHERE id = ?");
        $stmt->execute([$club_id]);
        $club = $stmt->fetch();
        
        if (!$club || $club['lider_id'] != $usuario_id) {
            header("Location: detalle_torneo.php?id=$torneo_id&err=1&msg=" . urlencode('No tienes permiso para inscribir a este club.'));
            exit;
        }

        $stmt = $pdo->prepare("SELECT id FROM inscripciones_torneo WHERE torneo_id = ? AND club_id = ? LIMIT 1");
        $stmt->execute([$torneo_id, $club_id]);
        if ($stmt->fetch()) {
            header("Location: detalle_torneo.php?id=$torneo_id&err=1&msg=" . urlencode('Tu club ya está inscrito en este torneo.'));
            exit;
        }

        $stmt = $pdo->prepare("SELECT u.id, u.nombre, u.email FROM miembros_club m JOIN usuarios u ON m.usuario_id = u.id WHERE m.club_id = ?");
        $stmt->execute([$club_id]);
        $miembros = $stmt->fetchAll();
        $total_miembros = count($miembros);

        if ($total_miembros === 0) {
            header("Location: detalle_torneo.php?id=$torneo_id&err=1&msg=" . urlencode('El club no tiene miembros.'));
            exit;
        }

        if ($total_miembros > $huecos_disponibles) {
            header("Location: detalle_torneo.php?id=$torneo_id&err=1&msg=" . urlencode("No hay suficientes huecos. Quedan $huecos_disponibles y tu club tiene $total_miembros miembros."));
            exit;
        }

        $inscritos_con_exito = 0;
        foreach ($miembros as $m) {
            try {
                $stmt = $pdo->prepare("INSERT INTO inscripciones_torneo (torneo_id, usuario_id, club_id) VALUES (?, ?, ?)");
                $stmt->execute([$torneo_id, $m['id'], $club_id]);
                $inscritos_con_exito++;
                
                // Enviar correo a cada miembro del club
                enviarCorreoInscripcion($m['email'], $m['nombre'], $torneo);
                
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) {
                    $inscritos_con_exito++;
                } else {
                    throw $e;
                }
            }
        }

        header("Location: detalle_torneo.php?id=$torneo_id&msg=" . urlencode("¡Club inscrito! Se han apuntado $inscritos_con_exito miembros y les ha llegado un correo."));
        exit;
    }

    elseif ($modo === 'desapuntar_solo') {
        $stmt = $pdo->prepare("DELETE FROM inscripciones_torneo WHERE torneo_id = ? AND usuario_id = ?");
        $stmt->execute([$torneo_id, $usuario_id]);
        header("Location: detalle_torneo.php?id=$torneo_id&msg=" . urlencode('Te has retirado del torneo.'));
        exit;
    }

    elseif ($modo === 'desapuntar_club' && $club_id) {
        $stmt = $pdo->prepare("SELECT lider_id FROM clubs WHERE id = ?");
        $stmt->execute([$club_id]);
        $club = $stmt->fetch();
        
        if ($club && $club['lider_id'] == $usuario_id) {
            $stmt = $pdo->prepare("DELETE FROM inscripciones_torneo WHERE torneo_id = ? AND club_id = ?");
            $stmt->execute([$torneo_id, $club_id]);
            header("Location: detalle_torneo.php?id=$torneo_id&msg=" . urlencode('Tu club ha sido retirado del torneo.'));
        } else {
            header("Location: detalle_torneo.php?id=$torneo_id&err=1&msg=" . urlencode('No tienes permiso.'));
        }
        exit;
    }

    header("Location: detalle_torneo.php?id=$torneo_id");
    exit;

} catch (PDOException $e) {
    die("Error de base de datos: " . $e->getMessage());
}