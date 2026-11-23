<?php
require_once 'config.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$pdo = getPDO();
$pdo->exec("SET NAMES utf8mb4"); 

$mi_id = $_SESSION['usuario_id'];
$accion = $_POST['accion'] ?? '';

if ($accion === 'crear') {
    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $icono = $_POST['icono'] ?? '🛡️';
    $es_privado = isset($_POST['es_privado']) ? 1 : 0;

    if ($nombre === '') {
        header("Location: clubs.php?err=1&msg=" . urlencode('El nombre del club es obligatorio.'));
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT id FROM miembros_club WHERE usuario_id = ?");
        $stmt->execute([$mi_id]);
        if ($stmt->fetch()) {
            header("Location: clubs.php?err=1&msg=" . urlencode('Ya perteneces a un club.'));
            exit;
        }

        $stmt = $pdo->prepare("INSERT INTO clubs (nombre, descripcion, icono, es_privado, lider_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$nombre, $descripcion, $icono, $es_privado, $mi_id]);
        $nuevo_club_id = $pdo->lastInsertId();

        $stmt = $pdo->prepare("INSERT INTO miembros_club (club_id, usuario_id) VALUES (?, ?)");
        $stmt->execute([$nuevo_club_id, $mi_id]);

        header("Location: clubs.php?msg=" . urlencode('¡Club creado con éxito!'));
        exit;

    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            header("Location: clubs.php?err=1&msg=" . urlencode('Ya existe un club con ese nombre.'));
        } else {
            header("Location: clubs.php?err=1&msg=" . urlencode('Error al crear el club.'));
        }
        exit;
    }
} 
elseif ($accion === 'unirse') {
    $club_id = (int)($_POST['club_id'] ?? 0);

    try {
        $stmt = $pdo->prepare("SELECT id FROM miembros_club WHERE usuario_id = ?");
        $stmt->execute([$mi_id]);
        if ($stmt->fetch()) {
            header("Location: clubs.php?err=1&msg=" . urlencode('Ya perteneces a un club.'));
            exit;
        }

        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM miembros_club WHERE club_id = ?");
        $stmt->execute([$club_id]);
        $total = $stmt->fetch()['total'];

        if ($total >= 10) {
            header("Location: clubs.php?err=1&msg=" . urlencode('El club ya está lleno (10/10).'));
            exit;
        }

        $stmt = $pdo->prepare("SELECT es_privado FROM clubs WHERE id = ?");
        $stmt->execute([$club_id]);
        $club = $stmt->fetch();
        
        if ($club['es_privado']) {
            header("Location: clubs.php?err=1&msg=" . urlencode('Este club es privado. Debes solicitar entrar.'));
            exit;
        }

        $stmt = $pdo->prepare("INSERT INTO miembros_club (club_id, usuario_id) VALUES (?, ?)");
        $stmt->execute([$club_id, $mi_id]);

        header("Location: clubs.php?msg=" . urlencode('¡Te has unido al club!'));
        exit;

    } catch (PDOException $e) {
        header("Location: clubs.php?err=1&msg=" . urlencode('Error al unirse al club.'));
        exit;
    }
}
elseif ($accion === 'solicitar') {
    $club_id = (int)$_POST['club_id'];
    
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM miembros_club WHERE club_id = ?");
        $stmt->execute([$club_id]);
        if ($stmt->fetch()['total'] >= 10) {
            header("Location: clubs.php?err=1&msg=" . urlencode('El club está lleno.'));
            exit;
        }
        
        $stmt = $pdo->prepare("INSERT INTO solicitudes_club (club_id, usuario_id) VALUES (?, ?)");
        $stmt->execute([$club_id, $mi_id]);

        header("Location: clubs.php?msg=" . urlencode('Solicitud enviada. El líder del club la revisará.'));
        exit;
    } catch (PDOException $e) {
        header("Location: clubs.php?err=1&msg=" . urlencode('Ya has enviado una solicitud a este club.'));
        exit;
    }
}
elseif ($accion === 'aceptar') {
    $solicitud_id = (int)$_POST['solicitud_id'];
    $club_id = (int)$_POST['club_id'];

    $stmt = $pdo->prepare("SELECT lider_id FROM clubs WHERE id = ?");
    $stmt->execute([$club_id]);
    if ($stmt->fetch()['lider_id'] != $mi_id) { die("No tienes permiso."); }

    $stmt = $pdo->prepare("SELECT usuario_id FROM solicitudes_club WHERE id = ? AND club_id = ?");
    $stmt->execute([$solicitud_id, $club_id]);
    $sol = $stmt->fetch();

    if ($sol) {
        $stmt = $pdo->prepare("SELECT id FROM miembros_club WHERE usuario_id = ?");
        $stmt->execute([$sol['usuario_id']]);
        if (!$stmt->fetch()) {
            $stmt = $pdo->prepare("INSERT INTO miembros_club (club_id, usuario_id) VALUES (?, ?)");
            $stmt->execute([$club_id, $sol['usuario_id']]);
        }
        $stmt = $pdo->prepare("DELETE FROM solicitudes_club WHERE id = ?");
        $stmt->execute([$solicitud_id]);
    }

    header("Location: mi_club.php?id=" . $club_id . "&msg=" . urlencode('Jugador aceptado en el club.'));
    exit;
}
elseif ($accion === 'rechazar') {
    $solicitud_id = (int)$_POST['solicitud_id'];
    $club_id = (int)$_POST['club_id'];

    $stmt = $pdo->prepare("SELECT lider_id FROM clubs WHERE id = ?");
    $stmt->execute([$club_id]);
    if ($stmt->fetch()['lider_id'] != $mi_id) { die("No tienes permiso."); }

    $stmt = $pdo->prepare("DELETE FROM solicitudes_club WHERE id = ? AND club_id = ?");
    $stmt->execute([$solicitud_id, $club_id]);

    header("Location: mi_club.php?id=" . $club_id . "&msg=" . urlencode('Solicitud rechazada.'));
    exit;
}
elseif ($accion === 'editar') {
    $club_id = (int)$_POST['club_id'];
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    $icono = $_POST['icono'];
    $es_privado = (int)($_POST['es_privado'] ?? 0);

    $stmt = $pdo->prepare("SELECT lider_id FROM clubs WHERE id = ?");
    $stmt->execute([$club_id]);
    if ($stmt->fetch()['lider_id'] != $mi_id) { die("No tienes permiso."); }

    try {
        $stmt = $pdo->prepare("UPDATE clubs SET nombre = ?, descripcion = ?, icono = ?, es_privado = ? WHERE id = ?");
        $stmt->execute([$nombre, $descripcion, $icono, $es_privado, $club_id]);
        header("Location: mi_club.php?id=" . $club_id . "&msg=" . urlencode('Club actualizado correctamente.'));
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            header("Location: mi_club.php?id=" . $club_id . "&err=1&msg=" . urlencode('Ese nombre de club ya está en uso.'));
        } else {
            header("Location: mi_club.php?id=" . $club_id . "&err=1&msg=" . urlencode('Error al actualizar.'));
        }
    }
    exit;
}
elseif ($accion === 'borrar') {
    $club_id = (int)$_POST['club_id'];

    $stmt = $pdo->prepare("SELECT lider_id FROM clubs WHERE id = ?");
    $stmt->execute([$club_id]);
    if ($stmt->fetch()['lider_id'] != $mi_id) { die("No tienes permiso."); }

    try {
        $stmt = $pdo->prepare("DELETE FROM miembros_club WHERE club_id = ?");
        $stmt->execute([$club_id]);

        $stmt = $pdo->prepare("DELETE FROM solicitudes_club WHERE club_id = ?");
        $stmt->execute([$club_id]);

        $stmt = $pdo->prepare("DELETE FROM clubs WHERE id = ?");
        $stmt->execute([$club_id]);
        
        header("Location: clubs.php?msg=" . urlencode('El club ha sido eliminado y todos sus miembros liberados.'));
        exit;
    } catch (PDOException $e) {
        header("Location: mi_club.php?id=" . $club_id . "&err=1&msg=" . urlencode('Error al borrar el club.'));
        exit;
    }
}
elseif ($accion === 'expulsar') {
    $club_id = (int)$_POST['club_id'];
    $usuario_id = (int)$_POST['usuario_id'];

    $stmt = $pdo->prepare("SELECT lider_id FROM clubs WHERE id = ?");
    $stmt->execute([$club_id]);
    if ($stmt->fetch()['lider_id'] != $mi_id) { die("No tienes permiso."); }

    $stmt = $pdo->prepare("DELETE FROM miembros_club WHERE club_id = ? AND usuario_id = ?");
    $stmt->execute([$club_id, $usuario_id]);
    header("Location: mi_club.php?id=" . $club_id . "&msg=" . urlencode('Miembro expulsado del club.'));
    exit;
}
elseif ($accion === 'borrar_encuesta') {
    $club_id = (int)$_POST['club_id'];

    $stmt = $pdo->prepare("SELECT lider_id FROM clubs WHERE id = ?");
    $stmt->execute([$club_id]);
    if ($stmt->fetch()['lider_id'] != $mi_id) { die("No tienes permiso."); }

    $stmt = $pdo->prepare("DELETE FROM encuestas_club WHERE club_id = ?");
    $stmt->execute([$club_id]);
    
    header("Location: ver_club.php?id=" . $club_id . "&msg=" . urlencode('Encuesta eliminada.'));
    exit;
}
else {
    header("Location: clubs.php");
    exit;
}