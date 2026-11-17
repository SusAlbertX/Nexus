<?php
require_once 'config.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$pdo = getPDO();
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
            header("Location: clubs.php?err=1&msg=" . urlencode('Ya perteneces a un club. Debes salir de él para crear otro.'));
            exit;
        }

        $stmt = $pdo->prepare("INSERT INTO clubs (nombre, descripcion, icono, es_privado, lider_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$nombre, $descripcion, $icono, $es_privado, $mi_id]);
        $nuevo_club_id = $pdo->lastInsertId();

        $stmt = $pdo->prepare("INSERT INTO miembros_club (club_id, usuario_id) VALUES (?, ?)");
        $stmt->execute([$nuevo_club_id, $mi_id]);

        header("Location: clubs.php?msg=" . urlencode('¡Club creado con éxito! Ahora eres el líder.'));
        exit;

    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            header("Location: clubs.php?err=1&msg=" . urlencode('Ya existe un club con ese nombre.'));
        } else {
            header("Location: clubs.php?err=1&msg=" . urlencode('Error al crear el club: ' . $e->getMessage()));
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
            header("Location: clubs.php?err=1&msg=" . urlencode('Este club es privado.'));
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
else {
    header("Location: clubs.php");
    exit;

    elseif ($accion === 'editar') {
    $club_id = (int)$_POST['club_id'];
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    $icono = $_POST['icono'];
    $es_privado = isset($_POST['es_privado']) ? 1 : 0;

    $stmt = $pdo->prepare("SELECT lider_id FROM clubs WHERE id = ?");
    $stmt->execute([$club_id]);
    if ($stmt->fetch()['lider_id'] != $mi_id) { die("No tienes permiso."); }

    try {
        $stmt = $pdo->prepare("UPDATE clubs SET nombre = ?, descripcion = ?, icono = ?, es_privado = ? WHERE id = ?");
        $stmt->execute([$nombre, $descripcion, $icono, $es_privado, $club_id]);
        header("Location: mi_club.php?msg=" . urlencode('Club actualizado correctamente.'));
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            header("Location: mi_club.php?err=1&msg=" . urlencode('Ese nombre de club ya está en uso.'));
        } else {
            header("Location: mi_club.php?err=1&msg=" . urlencode('Error al actualizar.'));
        }
    }
    exit;
}
elseif ($accion === 'borrar') {
    $club_id = (int)$_POST['club_id'];

    $stmt = $pdo->prepare("SELECT lider_id FROM clubs WHERE id = ?");
    $stmt->execute([$club_id]);
    if ($stmt->fetch()['lider_id'] != $mi_id) { die("No tienes permiso."); }

    $stmt = $pdo->prepare("DELETE FROM clubs WHERE id = ?");
    $stmt->execute([$club_id]);
    header("Location: clubs.php?msg=" . urlencode('El club ha sido eliminado.'));
    exit;
}
elseif ($accion === 'expulsar') {
    $club_id = (int)$_POST['club_id'];
    $usuario_id = (int)$_POST['usuario_id'];

    $stmt = $pdo->prepare("SELECT lider_id FROM clubs WHERE id = ?");
    $stmt->execute([$club_id]);
    if ($stmt->fetch()['lider_id'] != $mi_id) { die("No tienes permiso."); }

    $stmt = $pdo->prepare("DELETE FROM miembros_club WHERE club_id = ? AND usuario_id = ?");
    $stmt->execute([$club_id, $usuario_id]);
    header("Location: mi_club.php?msg=" . urlencode('Miembro expulsado del club.'));
    exit;
}
}