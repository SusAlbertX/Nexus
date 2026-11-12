<?php
require 'config.php';

/* AHORA: valida y redirige con mensaje de éxito.
   DESPUÉS (con SQL): descomenta el bloque PDO y listo. */

$torneoId = (int)($_POST['torneo_id'] ?? 0);
$nombre   = trim($_POST['nombre_jugador'] ?? '');
$email    = trim($_POST['email'] ?? '');
$equipo   = trim($_POST['equipo'] ?? '');

if ($torneoId <= 0 || $nombre === '' || $email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: torneo.php?id=$torneoId&err=1&msg=" . urlencode('Datos inválidos. Revisa el formulario.'));
    exit;
}

/* ----- BLOQUE SQL (descomentar cuando la BD esté lista) -----
$pdo = getPDO();

$stmt = $pdo->prepare("SELECT t.max_jugadores,
    (SELECT COUNT(*) FROM inscripciones i WHERE i.torneo_id = t.id) AS inscritos
    FROM torneos t WHERE t.id = ?");
$stmt->execute([$torneoId]);
$t = $stmt->fetch();

if (!$t) {
    header("Location: torneos.php?err=1&msg=" . urlencode('Torneo no encontrado.'));
    exit;
}
if ($t['inscritos'] >= $t['max_jugadores']) {
    header("Location: torneo.php?id=$torneoId&err=1&msg=" . urlencode('El torneo ya está lleno.'));
    exit;
}

try {
    $ins = $pdo->prepare("INSERT INTO inscripciones (torneo_id, nombre_jugador, email, equipo)
                          VALUES (?, ?, ?, ?)");
    $ins->execute([$torneoId, $nombre, $email, $equipo !== '' ? $equipo : null]);
} catch (PDOException $ex) {
    if ($ex->getCode() === '23000') {
        header("Location: torneo.php?id=$torneoId&err=1&msg=" . urlencode('Ya estás inscrito con ese nombre.'));
        exit;
    }
    header("Location: torneo.php?id=$torneoId&err=1&msg=" . urlencode('Error al guardar.'));
    exit;
}
--------------------------------------------------------------- */

header("Location: torneo.php?id=$torneoId&msg=" . urlencode('¡Inscripción confirmada! Buena suerte 🎉'));
exit;