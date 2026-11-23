<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Soporte - Nexus</title>
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
            <a href="soporte.php" class="activo">Soporte</a>
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
    <h1>🛟 Soporte Nexus</h1>
    <p>¿Tienes un problema o una duda? Rellena el formulario y te responderemos lo antes posible.</p>
</section>

<section class="container" style="padding: 20px; max-width: 800px;">
    <div class="panel">
        <h2 style="margin-top:0;">📬 Envíanos un mensaje</h2>

        <?php
        $mensaje = $_GET['msg'] ?? '';
        $esError = ($_GET['err'] ?? '0') === '1';
        if ($mensaje): ?>
            <div class="alerta <?= $esError ? 'error' : 'ok' ?>"><?= e($mensaje) ?></div>
        <?php endif; ?>

        <form action="guardar_soporte.php" method="POST">
            <div class="form-grupo">
                <label for="nombre">Nombre *</label>
                <input type="text" id="nombre" name="nombre" required maxlength="80" placeholder="Tu nombre">
            </div>
            <div class="form-grupo">
                <label for="email">Email *</label>
                <input type="email" id="email" name="email" required maxlength="120" placeholder="tu@email.com">
            </div>
            <div class="form-grupo">
                <label for="categoria">Categoría *</label>
                <select id="categoria" name="categoria" required>
                    <option value="">Selecciona una categoría</option>
                    <option value="cuenta">Problema con mi cuenta</option>
                    <option value="torneo">Inscripción / torneo</option>
                    <option value="pago">Pagos y premios</option>
                    <option value="tecnico">Problema técnico</option>
                    <option value="otro">Otro</option>
                </select>
            </div>
            <div class="form-grupo">
                <label for="asunto">Asunto *</label>
                <input type="text" id="asunto" name="asunto" required maxlength="150" placeholder="Resumen breve">
            </div>
            <div class="form-grupo">
                <label for="mensaje">Mensaje *</label>
                <textarea id="mensaje" name="mensaje" rows="6" required maxlength="2000" placeholder="Cuéntanos qué ocurre..."></textarea>
            </div>
            <button type="submit" class="btn-primario" style="width:100%;">Enviar mensaje</button>
        </form>
    </div>
</section>

<footer>
    <div class="container"><p>&copy; Nexus 2026 - Club de Videojuegos</p></div>
</footer>

</body>
</html>