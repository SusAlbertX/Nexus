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

    $huecos_disponibles = $torneo['max_jugadores'] - $torneo['inscritos'];

    if ($modo === 'solo') {
        if ($huecos_disponibles <= 0) {
            header("Location: detalle_torneo.php?id=$torneo_id&err=1&msg=" . urlencode('El torneo está lleno.'));
            exit;
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO inscripciones_torneo (torneo_id, usuario_id) VALUES (?, ?)");
            $stmt->execute([$torneo_id, $usuario_id]);
            header("Location: detalle_torneo.php?id=$torneo_id&msg=" . urlencode('¡Inscripción individual confirmada!'));
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

        $stmt = $pdo->prepare("SELECT usuario_id FROM miembros_club WHERE club_id = ?");
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
                $stmt->execute([$torneo_id, $m['usuario_id'], $club_id]);
                $inscritos_con_exito++;
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) {
                    $inscritos_con_exito++;
                } else {
                    throw $e;
                }
            }
        }

        header("Location: detalle_torneo.php?id=$torneo_id&msg=" . urlencode("¡Club inscrito! Se han apuntado $inscritos_con_exito miembros."));
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