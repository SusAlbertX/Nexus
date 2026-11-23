<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forums - Nexus</title>
    <link rel="icon" href="icono.png" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<header>
    <div class="container">
        <p class="logo" onclick="location.href='index.php'">NEXUS</p>
        <nav>
            <a href="index.php">Inicio</a>
            <a href="torneo.php">Torneos</a>
            <a href="clubs.php">Clubs</a>
            <a href="forums.php" class="activo">Forums</a>
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

<section class="hero" style="min-height: 30vh;">
    <h1>🌐 Nexus Forums</h1>
    <p>El punto de encuentro de la comunidad. Debates, noticias y búsqueda de equipo.</p>
</section>

<section class="container" style="padding: 60px 20px; max-width: 800px;">
    
    <div class="panel" style="text-align: center; padding: 60px 40px; border: 2px dashed #3498db; background: linear-gradient(135deg, rgba(52, 152, 219, 0.05), rgba(0,0,0,0));">
        <div style="font-size: 5em; margin-bottom: 20px; animation: float 3s ease-in-out infinite;">💬</div>
        
        <h2 style="color: #3498db; font-size: 2.5em; margin-bottom: 10px;">Próximamente</h2>
        <h3 style="color: white; margin-top: 0;">Foros de la Comunidad (v2.0)</h3>
        
        <p style="color: #aaa; font-size: 1.1em; line-height: 1.6; max-width: 500px; margin: 20px auto;">
            Estamos construyendo un espacio global donde todos los jugadores de Nexus podrán debatir sobre sus juegos favoritos, buscar compañeros de equipo y compartir noticias.
        </p>

        <div style="display: flex; flex-wrap: wrap; gap: 15px; justify-content: center; margin-top: 30px;">
            <div style="background: #121212; padding: 10px 20px; border-radius: 20px; color: #ccc; font-size: 0.9em; border: 1px solid #333;">
                📌 Creación de Hilos
            </div>
            <div style="background: #121212; padding: 10px 20px; border-radius: 20px; color: #ccc; font-size: 0.9em; border: 1px solid #333;">
                💬 Comentarios
            </div>
            <div style="background: #121212; padding: 10px 20px; border-radius: 20px; color: #ccc; font-size: 0.9em; border: 1px solid #333;">
                🗂️ Categorías
            </div>
            <div style="background: #121212; padding: 10px 20px; border-radius: 20px; color: #ccc; font-size: 0.9em; border: 1px solid #333;">
                🛡️ Moderación
            </div>
        </div>

        <div style="margin-top: 40px; background: rgba(52, 152, 219, 0.1); padding: 15px; border-radius: 10px; border-left: 4px solid #3498db; display: inline-block; text-align: left;">
            <p style="margin: 0; color: #ddd; font-size: 0.95em;">
                💡 <strong>¿Tienes una idea para el foro?</strong><br>
                Mándanosla a través de la pestaña de <a href="soporte.php" style="color: #3498db; text-decoration: underline;">Soporte</a> (Categoría: Sugerencia).
            </p>
        </div>
    </div>

</section>

<footer><div class="container"><p>&copy; Nexus 2026 - Club de Videojuegos</p></div></footer>

<style>
    @keyframes float {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-15px); }
        100% { transform: translateY(0px); }
    }
</style>

</body>
</html>