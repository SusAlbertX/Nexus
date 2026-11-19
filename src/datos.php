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

    $query = "SELECT t.*, 
              (SELECT COUNT(*) FROM inscripciones_torneo i WHERE i.torneo_id = t.id) AS inscritos 
              FROM torneos t";
    
    if ($juegoSlug === null || $juegoSlug === 'todos') {
        $stmt = $pdo->query($query . " ORDER BY t.fecha_inicio ASC");
        return $stmt->fetchAll();
    }
    
    $stmt = $pdo->prepare($query . " WHERE t.juego = ? ORDER BY t.fecha_inicio ASC");
    $stmt->execute([$juegoSlug]);
    return $stmt->fetchAll();
}

function datos_torneo(int $id): ?array {
    $pdo = getPDO();
    $stmt = $pdo->prepare("SELECT t.*, 
              (SELECT COUNT(*) FROM inscripciones_torneo i WHERE i.torneo_id = t.id) AS inscritos 
              FROM torneos t WHERE t.id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch() ?: null;
}