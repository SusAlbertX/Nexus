<?php
require_once 'config.php';

if (isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña - Nexus</title>
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
            <a href="login.php">Acceso</a>
        </nav>
    </div>
</header>

<section class="container" style="padding: 120px 20px 50px; max-width: 500px;">
    <div class="panel">
        <h2 style="margin-top: 0; text-align: center;">🔑 Recuperar Contraseña</h2>
        <p style="text-align: center; color: #aaa; font-size: 0.9em; margin-bottom: 25px;">
            Introduce el correo con el que te registraste y te enviaremos una nueva contraseña temporal.
        </p>

        <?php
        $msg = $_GET['msg'] ?? '';
        $err = $_GET['err'] ?? '';
        if ($msg): ?>
            <div class="alerta ok" style="font-size: 0.9em;"><?= e($msg) ?></div>
        <?php endif; ?>
        <?php if ($err): ?>
            <div class="alerta error" style="font-size: 0.9em;"><?= e($err) ?></div>
        <?php endif; ?>

        <form action="enviar_recuperacion.php" method="POST">
            <div class="form-grupo">
                <label for="email">Correo electrónico</label>
                <input type="email" id="email" name="email" required placeholder="tu@email.com">
            </div>
            <button type="submit" class="btn-primario" style="width: 100%;">Enviar nueva contraseña</button>
        </form>

        <div style="text-align: center; margin-top: 20px;">
            <a href="login.php" style="color: #9b59b6; text-decoration: none; font-size: 0.9em;">Volver a Iniciar Sesión</a>
        </div>
    </div>
</section>

<footer><div class="container"><p>&copy; Nexus 2026 - Club de Videojuegos</p></div></footer>
</body>
</html>