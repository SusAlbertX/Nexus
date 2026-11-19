<?php
require_once 'config.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_nombre'] !== 'AlbertUltimate') {
    die("<h1 style='color:red; text-align:center; margin-top:100px; font-family:sans-serif;'>Acceso Denegado.</h1>");
}

$pdo = getPDO();
$pdo->exec("SET NAMES utf8mb4");
$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM torneos WHERE id = ?");
$stmt->execute([$id]);
$torneo = $stmt->fetch();

if (!$torneo) { 
    die("<h1 style='color:white; text-align:center; margin-top:100px; font-family:sans-serif;'>Torneo no encontrado.</h1>"); 
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Torneo - Admin Nexus</title>
    <link rel="icon" href="icono.png" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<header>
    <div class="container">
        <p class="logo" onclick="location.href='index.php'">NEXUS ADMIN</p>
        <nav>
            <a href="admin.php" style="color: #e74c3c; font-weight: bold;">👑 Volver al Panel</a>
            <a href="logout.php" style="color: #e74c3c;">Cerrar Sesión</a>
        </nav>
    </div>
</header>

<section class="hero" style="min-height: 20vh;">
    <h1>✏️ Editar Torneo</h1>
    <p>Modifica los datos del torneo seleccionado.</p>
</section>

<section class="container" style="padding: 20px; max-width: 600px;">
    <div class="panel">
        <form action="accion_admin.php" method="POST">
            <input type="hidden" name="accion" value="editar_torneo">
            <input type="hidden" name="torneo_id" value="<?= $torneo['id'] ?>">
            
            <div class="form-grupo">
                <label>Título del Torneo</label>
                <input type="text" name="titulo" value="<?= e($torneo['titulo']) ?>" required>
            </div>
            
            <div class="form-grupo">
                <label>Juego</label>
                <select name="juego" required>
                    <option value="fortnite" <?= $torneo['juego'] == 'fortnite' ? 'selected' : '' ?>>Fortnite</option>
                    <option value="rocket" <?= $torneo['juego'] == 'rocket' ? 'selected' : '' ?>>Rocket League</option>
                    <option value="cod" <?= $torneo['juego'] == 'cod' ? 'selected' : '' ?>>Call of Duty</option>
                    <option value="cs2" <?= $torneo['juego'] == 'cs2' ? 'selected' : '' ?>>CS2</option>
                </select>
            </div>
            
            <div class="form-grupo">
                <label>Premio</label>
                <input type="text" name="premio" value="<?= e($torneo['premio']) ?>" required>
            </div>
            
            <div class="form-grupo">
                <label>Máximo de Jugadores</label>
                <input type="number" name="max_jugadores" value="<?= $torneo['max_jugadores'] ?>" required min="2">
            </div>
            
            <div class="form-grupo">
                <label>Fecha y Hora</label>
                <input type="datetime-local" name="fecha_inicio" value="<?= date('Y-m-d\TH:i', strtotime($torneo['fecha_inicio'])) ?>" required>
            </div>
            
            <div class="form-grupo">
                <label>Imagen (URL)</label>
                <input type="url" name="imagen" value="<?= e($torneo['imagen']) ?>">
            </div>
            
            <div class="form-grupo">
                <label>Descripción</label>
                <textarea name="descripcion" rows="4"><?= e($torneo['descripcion']) ?></textarea>
            </div>
            
            <button type="submit" class="btn-primario" style="background: #f39c12; width: 100%; font-size: 1.1em;">💾 Guardar Cambios</button>
        </form>
    </div>
</section>

<footer><div class="container"><p>&copy; Nexus 2026 - Admin</p></div></footer>
</body>
</html>