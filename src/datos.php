<?php
require_once 'config.php';

function datos_juegos(): array {
    return [
        ['id' => 1, 'slug' => 'fortnite', 'nombre' => 'Fortnite'],
        ['id' => 2, 'slug' => 'rocket',   'nombre' => 'Rocket League'],
        ['id' => 3, 'slug' => 'cod',      'nombre' => 'Call of Duty'],
        ['id' => 4, 'slug' => 'cs2',      'nombre' => 'CS2'],
    ];
}

function datos_torneos(?string $juegoSlug = null): array {
    $pdo = getPDO();
    $pdo->exec("SET NAMES utf8mb4");


    if ($juegoSlug === null || $juegoSlug === 'todos') {
        $stmt = $pdo->query("SELECT *, 0 AS inscritos FROM torneos ORDER BY fecha_inicio ASC");
        return $stmt->fetchAll();
    }
    
    $stmt = $pdo->prepare("SELECT *, 0 AS inscritos FROM torneos WHERE juego = ? ORDER BY fecha_inicio ASC");
    $stmt->execute([$juegoSlug]);
    return $stmt->fetchAll();
}

function datos_torneo(int $id): ?array {
    $pdo = getPDO();
    $stmt = $pdo->prepare("SELECT *, 0 AS inscritos FROM torneos WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch() ?: null;
}