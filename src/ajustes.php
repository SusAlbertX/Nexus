<?php
require_once 'config.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$mensaje = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuevo_nombre = trim($_POST['nombre'] ?? '');
    $nuevo_email = trim($_POST['email'] ?? '');
    $nueva_pass = $_POST['password'] ?? '';
    
    if ($nuevo_nombre === '' || $nuevo_email === '' || !filter_var($nuevo_email, FILTER_VALIDATE_EMAIL)) {
        $error = "El nombre no puede estar vacío y el email debe ser válido.";
    } else {
        try {
            $pdo = getPDO();
            
            $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE (nombre = ? OR email = ?) AND id != ?");
            $stmt->execute([$nuevo_nombre, $nuevo_email, $_SESSION['usuario_id']]);
            if ($stmt->fetch()) {
                $error = "Ese gamertag o correo ya está en uso por otro jugador.";
            } else {
                $query = "UPDATE usuarios SET nombre = ?, email = ?";
                $params = [$nuevo_nombre, $nuevo_email];
                
                if ($nueva_pass !== '') {
                    if (strlen($nueva_pass) < 4) {
                        $error = "La nueva contraseña debe tener al menos 4 caracteres.";
                    } else {
                        $query .= ", password_hash = ?";
                        $params[] = password_hash($nueva_pass, PASSWORD_DEFAULT);
                    }
                }
                
                if (!$error) {
                    $query .= " WHERE id = ?";
                    $params[] = $_SESSION['usuario_id'];
                    
                    $stmt = $pdo->prepare($query);
                    $stmt->execute($params);
                    
                    $_SESSION['usuario_nombre'] = $nuevo_nombre;
                    $mensaje = "¡Tus datos se han actualizado correctamente!";
                }
            }
        } catch (PDOException $e) {
            $error = "Error en la base de datos: " . $e->getMessage();
        }
    }
}

$stmt = getPDO()->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$usuario = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajustes - Nexus</title>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<header>
    <div class="container">
        <p class="logo" onclick="location.href='index.php'">NEXUS</p>
        <nav>
            <a href="index.php">Inicio</a>
            <a href="torneos.php">Torneos</a>
            <a href="clubs.php">Clubs</a>
            <a href="servidores.php">Servidores</a>
            <a href="soporte.php">Soporte</a>
            
            <?php if (isset($_SESSION['usuario_id'])): ?>
                <a href="ajustes.php" style="color: #9b59b6; font-weight: bold; margin-left: 15px;">👤 <?= e($_SESSION['usuario_nombre']) ?></a>
                <a href="logout.php" style="color: #e74c3c;">Salir</a>
            <?php else: ?>
                <a href="login.php" style="margin-left: 15px;">Acceso</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<section class="hero" style="min-height: 25vh;">
    <h1>⚙️ Ajustes de Cuenta</h1>
    <p>Personaliza tu perfil de Nexus.</p>
</section>

<section class="container" style="padding: 20px; max-width: 600px;">
    <div class="panel">
        <h2 style="margin-top:0;">Tus Datos</h2>

        <?php if ($mensaje): ?>
            <div class="alerta ok"><?= e($mensaje) ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alerta error"><?= e($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-grupo">
                <label for="nombre">Gamertag (Nombre de usuario) *</label>
                <input type="text" id="nombre" name="nombre" value="<?= e($usuario['nombre']) ?>" required maxlength="50">
            </div>
            
            <div class="form-grupo">
                <label for="email">Correo electrónico *</label>
                <input type="email" id="email" name="email" value="<?= e($usuario['email']) ?>" required maxlength="120">
            </div>
            
            <div class="form-grupo" style="margin-top: 30px; border-top: 1px solid #333; padding-top: 20px;">
                <label for="password">Nueva Contraseña</label>
                <input type="password" id="password" name="password" placeholder="Déjalo en blanco para no cambiarla">
                <small style="color: #777; font-size: 0.85em; margin-top: 5px;">Si quieres cambiarla, escribe una nueva aquí.</small>
            </div>

            <button type="submit" class="btn-primario" style="width: 100%; margin-top: 10px;">💾 Guardar Cambios</button>
        </form>
    </div>
</section>

<footer>
    <div class="container"><p>&copy; Nexus 2026 - Club de Videojuegos</p></div>
</footer>

</body>
</html>