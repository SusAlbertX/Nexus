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
else {
    header("Location: admin.php");
    exit;
}