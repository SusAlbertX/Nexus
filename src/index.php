<?php
require_once 'config.php';
require_once 'datos.php';
$destacados = array_slice(datos_torneos(), 0, 3);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexus - Club de Videojuegos</title>
    <link rel="icon" href="icono.png" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<header>
    <div class="container">
        <p class="logo" onclick="location.href='index.php'">NEXUS</p>
        <nav>
            <a href="index.php" style="color: purple;">Inicio</a>
            <a href="torneo.php">Torneos</a>
            <a href="clubs.php">Clubs</a>
            <a href="forums.php">Forums</a>
            <a href="servidores.php">Servidores</a>
            <a href="soporte.php">Soporte</a>
            <a href="actualizaciones.php">Actualizaciones</a>
            <?php if (isset($_SESSION['usuario_id'])): ?>
                <div class="user-menu">
                    <a>👤 <?= e($_SESSION['usuario_nombre']) ?> ▾</a>
                    <div class="dropdown-content">
                        <a href="perfil.php">👤 Mi Perfil</a>
                        <a href="ajustes.php">⚙️ Ajustes de cuenta</a>
                        <?php if (isset($_SESSION['usuario_nombre']) && $_SESSION['usuario_nombre'] === 'AlbertUltimate'): ?>
                            <a href="admin.php" style="color: #e74c3c !important; font-weight: bold;">👑 Panel de Admin</a>
                        <?php endif; ?>
                        <a href="logout.php" class="logout-link">🚪 Cerrar Sesión</a>
                    </div>
                </div>
            <?php else: ?>
                <a href="login.php" style="margin-left: 15px;">Acceso</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<section class="hero">
    <h1>🎮 Bienvenido a Nexus 🎮</h1>
    <p>El club donde la <span class="color-acento">comunidad gamer</span> se reúne para competir, mejorar y ganar.</p>
    <div style="margin-top: 30px; display: flex; gap: 15px; flex-wrap: wrap; justify-content: center;">
        <button class="btn-primario" onclick="location.href='torneo.php'">Ver torneos</button>
        <button class="btn-outline" onclick="location.href='soporte.php'">Necesito ayuda</button>
    </div>
</section>

<section class="container" style="padding: 40px 20px;">
    <h2 style="text-align:center;">🏆 Torneos destacados</h2>
    <div class="grid-torneos">
        <?php if (empty($destacados)): ?>
            <p style="grid-column:1/-1; text-align:center; color:#888;">El administrador aún no ha creado ningún torneo.</p>
        <?php else: ?>
            <?php foreach ($destacados as $t): ?>
            <div class="torneo-card">
                <img class="torneo-imagen" src="<?= $t['imagen'] ? e($t['imagen']) : 'https://wallpapers.com/images/hd/purple-abstract-background-chafr7li4ofrhud6.jpg' ?>" alt="<?= e($t['titulo']) ?>">
                <div class="torneo-contenido">
                    <span class="categoria cat-<?= e($t['juego']) ?>"><?= strtoupper(e($t['juego'])) ?></span>
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
        <?php endif; ?>
    </div>
    <div style="text-align:center; margin-top: 20px;">
        <button onclick="location.href='torneo.php'">Ver todos los torneos →</button>
    </div>
</section>

<footer><div class="container"><p>&copy; Nexus 2026 - Club de Videojuegos</p></div></footer>
</body>
</html>