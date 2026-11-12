<?php
require 'config.php';

/* ============================================================
   CAPA DE DATOS (MOCK)
   Cuando configures SQL, sustituye el cuerpo de cada función
   por la consulta correspondiente. La forma del array devuelto
   debe ser la misma que las columnas de la tabla.
   ============================================================ */

function datos_juegos(): array {
    /* SQL futuro:
       return getPDO()->query("SELECT * FROM juegos ORDER BY id")->fetchAll();
    */
    return [
        ['id' => 1, 'slug' => 'fortnite', 'nombre' => 'Fortnite'],
        ['id' => 2, 'slug' => 'rocket',   'nombre' => 'Rocket League'],
        ['id' => 3, 'slug' => 'cod',      'nombre' => 'Call of Duty'],
        ['id' => 4, 'slug' => 'cs2',      'nombre' => 'CS2'],
    ];
}

function datos_torneos(?string $juegoSlug = null): array {
    /* SQL futuro:
       if ($juegoSlug === null || $juegoSlug === 'todos') {
           return getPDO()->query("SELECT * FROM torneos_con_inscritos WHERE activo = 1 ORDER BY fecha_inicio ASC")->fetchAll();
       }
       $stmt = getPDO()->prepare("SELECT * FROM torneos_con_inscritos WHERE activo = 1 AND juego_slug = ? ORDER BY fecha_inicio ASC");
       $stmt->execute([$juegoSlug]);
       return $stmt->fetchAll();
    */
    $todos = [
        [
            'id' => 1, 'juego_id' => 1, 'juego_slug' => 'fortnite', 'juego_nombre' => 'Fortnite',
            'titulo' => 'Fortnite Solos - Cash Cup',
            'descripcion' => "Torneo individual de Fortnite. Formato Battle Royale, puntuación por eliminaciones y posición.\nCada jugador compite por su cuenta.",
            'premio' => '$200', 'modalidad' => 'Solos', 'max_jugadores' => 100, 'inscritos' => 87,
            'fecha_inicio' => '2026-11-15 18:00:00',
            'imagen' => 'https://images.unsplash.com/photo-1589241062272-c0a000072dfa?auto=format&fit=crop&w=600&q=80',
        ],
        [
            'id' => 2, 'juego_id' => 2, 'juego_slug' => 'rocket', 'juego_nombre' => 'Rocket League',
            'titulo' => 'Rocket League 2v2 - Weekly',
            'descripcion' => "Torneo semanal de Rocket League en formato 2 contra 2. Eliminación directa.\nRequiere equipo de 2 jugadores.",
            'premio' => '$150', 'modalidad' => '2v2', 'max_jugadores' => 64, 'inscritos' => 32,
            'fecha_inicio' => '2026-11-14 20:00:00',
            'imagen' => 'https://images.unsplash.com/photo-1612287230202-1ff1d85d1bdf?auto=format&fit=crop&w=600&q=80',
        ],
        [
            'id' => 3, 'juego_id' => 3, 'juego_slug' => 'cod', 'juego_nombre' => 'Call of Duty',
            'titulo' => 'CoD Warzone - Squad Tournament',
            'descripcion' => "Torneo de escuadrones en Warzone. Equipos de 4 jugadores.\nPuntuación basada en eliminaciones y posición final.",
            'premio' => '$300', 'modalidad' => 'Squad', 'max_jugadores' => 60, 'inscritos' => 48,
            'fecha_inicio' => '2026-11-16 17:00:00',
            'imagen' => 'https://images.unsplash.com/photo-1552820728-8b83bb6b773f?auto=format&fit=crop&w=600&q=80',
        ],
        [
            'id' => 4, 'juego_id' => 4, 'juego_slug' => 'cs2', 'juego_nombre' => 'CS2',
            'titulo' => 'CS2 - 5v5 Premier',
            'descripcion' => "Torneo competitivo de Counter-Strike 2. Equipos de 5 jugadores, formato MR12.\nMapas al mejor de 3.",
            'premio' => '$250', 'modalidad' => '5v5', 'max_jugadores' => 50, 'inscritos' => 40,
            'fecha_inicio' => '2026-11-13 19:00:00',
            'imagen' => 'https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&w=600&q=80',
        ],
    ];

    if ($juegoSlug === null || $juegoSlug === 'todos') {
        return $todos;
    }
    return array_values(array_filter($todos, fn($t) => $t['juego_slug'] === $juegoSlug));
}

function datos_torneo(int $id): ?array {
    /* SQL futuro:
       $stmt = getPDO()->prepare("SELECT * FROM torneos_con_inscritos WHERE id = ?");
       $stmt->execute([$id]);
       return $stmt->fetch() ?: null;
    */
    foreach (datos_torneos() as $t) {
        if ((int)$t['id'] === $id) return $t;
    }
    return null;
}