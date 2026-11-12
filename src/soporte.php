<?php
$mensaje = $_GET['msg'] ?? '';
$esError = ($_GET['err'] ?? '0') === '1';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="icon" href="icono.png" type="image/png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Soporte - Nexus</title>
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
            <a href="soporte.php" class="activo">Soporte</a>
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

        <?php if ($mensaje): ?>
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