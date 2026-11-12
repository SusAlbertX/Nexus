<?php
require 'datos.php';
$destacados = array_slice(datos_torneos(), 0, 3);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="icon" href="icono.png" type="image/png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexus - Club de Videojuegos</title>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<header>
    <div class="container">
        <p class="logo" onclick="location.href='index.php'">NEXUS</p>
        <nav>
            <a href="index.php" class="activo">Inicio</a>
            <a href="torneo.php">Torneos</a>
            <a href="soporte.php">Soporte</a>
            <a href="servidores.php">Servidores</a>
        </nav>
    </div>
</header>

<section class="hero">
    <h1>🎮 Bienvenido a Nexus 🎮</h1>
    <p>El club donde la comunidad gamer se reúne para competir, mejorar y ganar. Torneos de Fortnite, Rocket League, Call of Duty y CS2.</p>
    <div style="margin-top: 30px; display: flex; gap: 15px; flex-wrap: wrap; justify-content: center;">
        <button class="btn-primario" onclick="location.href='torneos.php'">Ver torneos</button>
        <button class="btn-outline" onclick="location.href='soporte.php'">Necesito ayuda</button>
    </div>
</section>

<section class="container" style="padding: 40px 20px;">
    <h2 style="text-align:center;">🏆 Torneos destacados</h2>
    <div class="grid-torneos">
        <?php foreach ($destacados as $t): ?>
        <div class="torneo-card">
            <img class="torneo-imagen" src="<?= e($t['imagen']) ?>" alt="<?= e($t['titulo']) ?>" onerror="this.style.display='none'">
            <div class="torneo-contenido">
                <span class="categoria cat-<?= e($t['juego_slug']) ?>"><?= e($t['juego_nombre']) ?></span>
                <h3 class="torneo-titulo"><?= e($t['titulo']) ?></h3>
                <div class="torneo-precio">🏆 Premio: <?= e($t['premio']) ?></div>
                <div class="torneo-stats">
                    <div class="stat-item">
                        <div class="stat-numero"><?= $t['inscritos'] ?>/<?= $t['max_jugadores'] ?></div>
                        <div class="stat-label">Jugadores</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-numero" style="font-size:0.9em;"><?= date('d/m', strtotime($t['fecha_inicio'])) ?></div>
                        <div class="stat-label">Fecha</div>
                    </div>
                </div>
                <div class="torneo-acciones">
                    <button onclick="location.href='torneo.php?id=<?= $t['id'] ?>'">Ver torneo</button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <div style="text-align:center;">
        <button onclick="location.href='torneo.php'">Ver todos los torneos →</button>
    </div>
</section>

<section class="container" style="padding: 40px 20px;">
    <h2 style="text-align:center;">¿Por qué unirte a Nexus?</h2>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-top: 30px;">
        <div class="panel" style="text-align:center;">
            <div style="font-size: 3em;">🎯</div>
            <h3>Torneos diarios</h3>
            <p>Compite en torneos de tus juegos favoritos.</p>
        </div>
        <div class="panel" style="text-align:center;">
            <div style="font-size: 3em;">👥</div>
            <h3>Comunidad activa</h3>
            <p>Conecta con otros jugadores y forma equipos.</p>
        </div>
        <div class="panel" style="text-align:center;">
            <div style="font-size: 3em;">🏅</div>
            <h3>Premios reales</h3>
            <p>Gana skins, tarjetas regalo y hardware.</p>
        </div>
    </div>
</section>

<footer>
    <div class="container"><p>&copy; Nexus 2026 - Club de Videojuegos</p></div>
</footer>

</body>
</html>