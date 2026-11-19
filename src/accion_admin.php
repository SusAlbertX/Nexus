<?php
require_once 'config.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_nombre'] !== 'AlbertUltimate') {
    die("Acceso denegado.");
}

$pdo = getPDO();
$pdo->exec("SET NAMES utf8mb4");
$accion = $_POST['accion'] ?? '';

if ($accion === 'crear_torneo') {
    $titulo = trim($_POST['titulo'] ?? '');
    $juego = $_POST['juego'] ?? 'fortnite';
    $premio = trim($_POST['premio'] ?? '');
    $max_jugadores = (int)$_POST['max_jugadores'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $descripcion = trim($_POST['descripcion'] ?? '');
    $imagen = trim($_POST['imagen'] ?? '');

    if (empty($titulo) || empty($premio) || empty($fecha_inicio)) {
        header("Location: admin.php?err=1&msg=" . urlencode('Faltan campos obligatorios.'));
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO torneos (titulo, descripcion, juego, premio, max_jugadores, fecha_inicio, imagen) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$titulo, $descripcion, $juego, $premio, $max_jugadores, $fecha_inicio, $imagen]);
        
        header("Location: admin.php?msg=" . urlencode('¡Torneo creado con éxito!'));
        exit;
    } catch (PDOException $e) {
        header("Location: admin.php?err=1&msg=" . urlencode('Error al crear el torneo.'));
        exit;
    }
}
elseif ($accion === 'borrar_torneo') {
    $torneo_id = (int)$_POST['torneo_id'];
    
    $stmt = $pdo->prepare("DELETE FROM torneos WHERE id = ?");
    $stmt->execute([$torneo_id]);
    
    header("Location: admin.php?msg=" . urlencode('Torneo eliminado.'));
    exit;
}
elseif ($accion === 'banear') {
    $usuario_id = (int)$_POST['usuario_id'];
    $estado_actual = (int)$_POST['baneado_actual'];
    $nuevo_estado = $estado_actual === 1 ? 0 : 1;
    
    $stmt = $pdo->prepare("UPDATE usuarios SET baneado = ? WHERE id = ?");
    $stmt->execute([$nuevo_estado, $usuario_id]);
    
    header("Location: admin.php?msg=" . urlencode('Estado de baneo actualizado.'));
    exit;
}
elseif ($accion === 'borrar_club_admin') {
    $club_id = (int)$_POST['club_id'];
    
    $stmt = $pdo->prepare("DELETE FROM miembros_club WHERE club_id = ?");
    $stmt->execute([$club_id]);
    $stmt = $pdo->prepare("DELETE FROM solicitudes_club WHERE club_id = ?");
    $stmt->execute([$club_id]);
    
    $stmt = $pdo->prepare("DELETE FROM clubs WHERE id = ?");
    $stmt->execute([$club_id]);
    
    header("Location: admin.php?msg=" . urlencode('El club ha sido eliminado por el administrador.'));
    exit;
}
elseif ($accion === 'expulsar_admin') {
    $club_id = (int)$_POST['club_id'];
    $usuario_id = (int)$_POST['usuario_id'];
    
    $stmt = $pdo->prepare("DELETE FROM miembros_club WHERE club_id = ? AND usuario_id = ?");
    $stmt->execute([$club_id, $usuario_id]);
    
    header("Location: admin.php?msg=" . urlencode('Usuario expulsado del club.'));
    exit;
}
else {
    header("Location: admin.php");
    exit;
}