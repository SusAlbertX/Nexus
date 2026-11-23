<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Cambios - Nexus</title>
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
            <a href="servidores.php">Servidores</a>
            <a href="soporte.php">Soporte</a>
            <a href="actualizaciones.php" class="activo">Actualizaciones</a>
            
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

<section class="hero" style="min-height: 30vh; background-image: linear-gradient(0deg, rgba(0,0,0,0.8), rgba(0,0,0,0.8)), url('https://images.unsplash.com/photo-1550745165-9bc0b252726f?auto=format&fit=crop&w=1600&q=80');">
    <h1>📜 Registro de Cambios</h1>
    <p>Descubre todas las novedades y mejoras que llegan a Nexus.</p>
</section>

<section class="container" style="padding: 40px 20px; max-width: 800px;">
    
    <div style="position: relative; border-left: 2px solid #333; padding-left: 30px; margin-left: 20px;">
        
        <div style="position: relative; margin-bottom: 50px;">
            <div style="position: absolute; left: -41px; top: 0; width: 20px; height: 20px; background: #333; border-radius: 50%; border: 4px solid #121212;"></div>
            
            <div class="panel" style="margin: 0; opacity: 0.6; border: 1px dashed #555;">
                <span style="color: #3498db; font-weight: bold; font-size: 0.9em;">Próximamente</span>
                <h2 style="margin-top: 5px; color: white;">🌐 Nexus Forums (v2.0)</h2>
                <p style="color: #aaa; margin-bottom: 20px;">El foro oficial de Nexus. Un espacio global donde toda la comunidad podrá debatir, buscar equipo y compartir noticias.</p>
                
                <ul style="color: #ccc; line-height: 1.8; font-size: 0.95em; padding-left: 20px;">
                    <li><strong style="color: #3498db;">📌 Creación de Hilos:</strong> Publica temas con texto e imágenes (estilo Reddit).</li>
                    <li><strong style="color: #3498db;">💬 Comentarios:</strong> Responde a los hilos y cita a otros usuarios.</li>
                    <li><strong style="color: #3498db;">🔥 Categorías:</strong> Debates generales, buscar equipo, soporte técnico y más.</li>
                    <li><strong style="color: #3498db;">🛡️ Moderación:</strong> El admin y los moderadores podrán cerrar hilos o borrar comentarios ofensivos.</li>
                </ul>
            </div>
        </div>

        <div style="position: relative; margin-bottom: 50px;">
            <div style="position: absolute; left: -41px; top: 0; width: 20px; height: 20px; background: #333; border-radius: 50%; border: 4px solid #121212;"></div>
            
            <div class="panel" style="margin: 0; opacity: 0.8; border: 1px dashed #555;">
                <span style="color: #f39c12; font-weight: bold; font-size: 0.9em;">Próximamente</span>
                <h2 style="margin-top: 5px; color: white;">💬 Chat de Clubs y Encuestas (v1.5)</h2>
                <p style="color: #aaa; margin-bottom: 20px;">Mejoras enfocadas en la comunicación y organización de los equipos.</p>
                
                <ul style="color: #ccc; line-height: 1.8; font-size: 0.95em; padding-left: 20px;">
                    <li><strong style="color: #f39c12;">📝 Muro de mensajes:</strong> Un chat privado para cada club. ¡Ahora con soporte para imágenes (PNG/JPG)!</li>
                    <li><strong style="color: #f39c12;">🗑️ Moderación:</strong> Los líderes y dueños de los mensajes podrán borrarlos.</li>
                    <li><strong style="color: #f39c12;">📊 Club Polls (Encuestas):</strong> Los líderes podrán lanzar encuestas (hasta 4 opciones) para decidir mapas, torneos o horarios. ¡Con votación única o múltiple y fecha límite!</li>
                </ul>
            </div>
        </div>

        <div style="position: relative; margin-bottom: 50px;">
            <div style="position: absolute; left: -41px; top: 0; width: 20px; height: 20px; background: #9b59b6; border-radius: 50%; border: 4px solid #121212; box-shadow: 0 0 10px rgba(155, 89, 182, 0.5);"></div>
            
            <div class="panel" style="margin: 0;">
                <span style="color: #2ecc71; font-weight: bold; font-size: 0.9em;">Noviembre 2026</span>
                <h2 style="margin-top: 5px; color: white;">🚀 Nexus: Lanzamiento Oficial (v1.0)</h2>
                <p style="color: #aaa; margin-bottom: 20px;">¡Bienvenidos al futuro de los eSports! Estamos orgullosos de presentar la primera versión de Nexus Gaming Club.</p>
                
                <ul style="color: #ccc; line-height: 1.8; font-size: 0.95em; padding-left: 20px;">
                    <li><strong style="color: #9b59b6;">⚙️ Sistema de Usuarios:</strong> Registro, inicio de sesión seguro, recuperación de contraseña por correo y perfiles personalizados.</li>
                    <li><strong style="color: #9b59b6;">🛡️ Gestión de Clubs:</strong> Crea tu propio equipo, hazlo público o privado, invita a tus amigos y gestiona a tus miembros.</li>
                    <li><strong style="color: #9b59b6;">🏆 Torneos Competitivos:</strong> Inscríbete individualmente o apunta a todo tu club en los torneos de Fortnite, CS2, Call of Duty y Rocket League.</li>
                    <li><strong style="color: #9b59b6;">👑 Panel de Administración:</strong> Centro de control total para crear torneos, banear usuarios y enviar correos masivos.</li>
                    <li><strong style="color: #9b59b6;">📬 Soporte:</strong> Sistema de tickets directo a nuestro correo para ayudarte con cualquier problema.</li>
                </ul>
                
                <div style="margin-top: 20px; background: #121212; padding: 10px 15px; border-radius: 8px; border-left: 4px solid #9b59b6; font-size: 0.9em; color: #aaa;">
                    <em>"El comienzo de una gran comunidad. ¡Gracias por estar aquí desde el día 1!"</em>
                </div>
            </div>
        </div>

    </div>

</section>

<footer>
    <div class="container"><p>&copy; Nexus 2026 - Club de Videojuegos</p></div>
</footer>

</body>
</html>