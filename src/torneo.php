<?php
require 'datos.php';
$juegos  = datos_juegos();
$filtro  = $_GET['juego'] ?? 'todos';
$torneos = datos_torneos($filtro);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="icon" href="icono.png" type="image/png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Torneos - Nexus</title>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<header>
    <div class="container">
        <p class="logo" onclick="location.href='index.php'">NEXUS</p>
        <nav>
            <a href="index.php">Inicio</a>
            <a href="torneos.php" class="activo">Torneos</a>
            <a href="soporte.php">Soporte</a>
        </nav>
    </div>
</header>

<section class="hero" style="min-height: 30vh;">
    <h1>🏆 Torneos Nexus</h1>
    <p>Inscríbete en cualquiera de nuestros torneos activos y demuestra tu nivel.</p>
</section>

<div class="filtros">
    <a href="torneo.php?juego=todos" class="filtro-btn <?= $filtro === 'todos' ? 'activo' : '' ?>">Todos</a>
    <?php foreach ($juegos as $j): ?>
        <a href="torneo.php?juego=<?= e($j['slug']) ?>" class="filtro-btn <?= $filtro === $j['slug'] ? 'activo' : '' ?>"><?= e($j['nombre']) ?></a>
    <?php endforeach; ?>
</div>

    <div class="grid-torneos">
        <?php if (empty($torneos)): ?>
            <p style="grid-column:1/-1; text-align:center; color:#888;">No hay torneos en esta categoría.</p>
        <?php else: ?>
            <?php foreach ($torneos as $t): ?>
            <div class="torneo-card">
                <img class="torneo-imagen" src="<?= e($t['imagen']) ?>" alt="<?= e($t['titulo']) ?>" onerror="this.style.display='none'">
                <div class="torneo-contenido">
                    <span class="categoria cat-<?= e($t['juego_slug']) ?>"><?= e($t['juego_nombre']) ?></span>
                    <h3 class="torneo-titulo"><?= e($t['titulo']) ?></h3>
                    <div class="torneo-precio">🏆 <?= e($t['premio']) ?></div>
                    <div class="torneo-stats">
                        <div class="stat-item">
                            <div class="stat-numero"><?= $t['inscritos'] ?>/<?= $t['max_jugadores'] ?></div>
                            <div class="stat-label">Jugadores</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-numero" style="font-size:0.9em;"><?= date('d/m', strtotime($t['fecha_inicio'])) ?></div>
                            <div class="stat-label">Fecha</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-numero" style="font-size:0.8em;"><?= e($t['modalidad']) ?></div>
                            <div class="stat-label">Formato</div>
                        </div>
                    </div>
                    <div class="torneo-acciones">
                        <button class="btn-outline" onclick="location.href='torneo.php?id=<?= $t['id'] ?>'">Detalles</button>
                        <button onclick="location.href='torneo.php?id=<?= $t['id'] ?>#inscribirse'">Inscribirse</button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<footer>
    <div class="container"><p>&copy; Nexus 2026 - Club de Videojuegos</p></div>
</footer>

</body>
</html>