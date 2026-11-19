<?php
require_once 'config.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$pdo = getPDO();
$torneo_id = (int)($_POST['torneo_id'] ?? 0);
$modo = $_POST['modo'] ?? 'solo';
$club_id = isset($_POST['club_id']) ? (int)$_POST['club_id'] : null;
$usuario_id = $_SESSION['usuario_id'];

try {
    $stmt = $pdo->prepare("SELECT max_jugadores, 
        (SELECT COUNT(*) FROM inscripciones_torneo WHERE torneo_id = ?) AS inscritos 
        FROM torneos WHERE id = ?");
    $stmt->execute([$torneo_id, $torneo_id]);
    $torneo = $stmt->fetch();

    if (!$torneo) {
        header("Location: torneo.php?err=1&msg=" . urlencode('Torneo no encontrado.'));
        exit;
    }
    if ($torneo['inscritos'] >= $torneo['max_jugadores']) {
        header("Location: detalle_torneo.php?id=$torneo_id&err=1&msg=" . urlencode('El torneo ya está lleno.'));
        exit;
    }

    if ($modo === 'solo') {
        $stmt = $pdo->prepare("INSERT INTO inscripciones_torneo (torneo_id, usuario_id) VALUES (?, ?)");
        $stmt->execute([$torneo_id, $usuario_id]);
        header("Location: detalle_torneo.php?id=$torneo_id&msg=" . urlencode('¡Inscripción individual confirmada! Buena suerte 🎉'));
        exit;
    }

        $stmt = $pdo->prepare("SELECT lider_id FROM clubs WHERE id = ?");
        $stmt->execute([$club_id]);
        $club = $stmt->fetch();
        
        if (!$club || $club['lider_id'] != $usuario_id) {
            header("Location: detalle_torneo.php?id=$torneo_id&err=1&msg=" . urlencode('No tienes permiso para inscribir a este club.'));
            exit;
        }

        $stmt = $pdo->prepare("SELECT usuario_id FROM miembros_club WHERE club_id = ?");
        $stmt->execute([$club_id]);
        $miembros = $stmt->fetchAll();

        if (empty($miembros)) {
            header("Location: detalle_torneo.php?id=$torneo_id&err=1&msg=" . urlencode('El club no tiene miembros.'));
            exit;
        }

        $inscritos_con_exito = 0;
        foreach ($miembros as $m) {
            try {
                $stmt = $pdo->prepare("INSERT INTO inscripciones_torneo (torneo_id, usuario_id, club_id) VALUES (?, ?, ?)");
                $stmt->execute([$torneo_id, $m['usuario_id'], $club_id]);
                $inscritos_con_exito++;
            } catch (PDOException $e) {
                // Si ya estaba inscrito, lo ignoramos y seguimos con el siguiente
                if ($e->getCode() != 23000) {
                    throw $e;
                }
            }
        }

        header("Location: detalle_torneo.php?id=$torneo_id&msg=" . urlencode("¡Club inscrito! Se han apuntado $inscritos_con_exito miembros al torneo."));
        exit;
    }

    header("Location: detalle_torneo.php?id=$torneo_id");
    exit;

} catch (PDOException $e) {
    header("Location: detalle_torneo.php?id=$torneo_id&err=1&msg=" . urlencode('Error al procesar la inscripción.'));
    exit;
}