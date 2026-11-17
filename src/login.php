<?php
require_once 'config.php';

if (isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    
    if ($accion === 'login') {
        $nombre = trim($_POST['nombre'] ?? '');
        $password = $_POST['password'] ?? '';
        
        $stmt = getPDO()->prepare("SELECT * FROM usuarios WHERE nombre = ?");
        $stmt->execute([$nombre]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['usuario_id'] = $user['id'];
            $_SESSION['usuario_nombre'] = $user['nombre'];
            header("Location: index.php");
            exit;
        } else {
            $error = "Usuario o contraseña incorrectos.";
        }
        
    } elseif ($accion === 'registro') {
        $nombre = trim($_POST['nombre'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if (strlen($nombre) < 3 || strlen($password) < 4) {
            $error = "El nombre debe tener al menos 3 letras y la contraseña 4.";
        } else {
            try {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = getPDO()->prepare("INSERT INTO usuarios (nombre, email, password_hash) VALUES (?, ?, ?)");
                $stmt->execute([$nombre, $email, $hash]);
                
                $_SESSION['usuario_id'] = getPDO()->lastInsertId();
                $_SESSION['usuario_nombre'] = $nombre;
                header("Location: index.php");
                exit;
            } catch (PDOException $e) {
                $error = "Ese nombre o email ya está en uso.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso - Nexus</title>
    <link rel="icon" href="icono.png" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <style>
        .tabs { display: flex; gap: 10px; margin-bottom: 20px; }
        .tab-btn { flex: 1; padding: 10px; background: #2a2a2a; border: none; color: #aaa; cursor: pointer; border-radius: 8px; font-weight: bold; }
        .tab-btn.activo { background: #8e44ad; color: white; }
        .form-container { display: none; }
        .form-container.activo { display: block; }
    </style>
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
            <a href="login.php" class="activo">Acceso</a>
        </nav>
    </div>
</header>

<section class="container" style="padding: 120px 20px 50px; max-width: 500px;">
    <div class="panel">
        
        <?php if ($error): ?>
            <div class="alerta error"><?= e($error) ?></div>
        <?php endif; ?>

        <?php if (isset($_GET['msg'])): ?>
            <div class="alerta <?= isset($_GET['err']) ? 'error' : 'ok' ?>"><?= e($_GET['msg']) ?></div>
        <?php endif; ?>

        <div class="tabs">
            <button class="tab-btn activo" onclick="cambiarTab('login')">Iniciar Sesión</button>
            <button class="tab-btn" onclick="cambiarTab('registro')">Registrarse</button>
        </div>

        <div id="form-login" class="form-container activo">
            <h2 style="margin-top: 0;">Bienvenido de nuevo</h2>
            <form method="POST">
                <input type="hidden" name="accion" value="login">
                <div class="form-grupo">
                    <label>Nombre de usuario</label>
                    <input type="text" name="nombre" required placeholder="Tu gamertag">
                </div>
                <div class="form-grupo">
                    <label>Contraseña</label>
                    <input type="password" name="password" required placeholder="••••••••">
                </div>
                
                <div style="text-align: right; margin-bottom: 15px;">
                    <a href="recuperar.php" style="color: #9b59b6; font-size: 0.85em; text-decoration: none;">¿Olvidaste tu contraseña?</a>
                </div>

                <button type="submit" class="btn-primario" style="width: 100%;">Entrar a Nexus</button>
            </form>
        </div>

        <div id="form-registro" class="form-container">
            <h2 style="margin-top: 0;">Crea tu cuenta</h2>
            <form method="POST">
                <input type="hidden" name="accion" value="registro">
                <div class="form-grupo">
                    <label>Nombre de usuario (Gamertag)</label>
                    <input type="text" name="nombre" required maxlength="50" placeholder="Ej: GamerPro99">
                </div>
                <div class="form-grupo">
                    <label>Email</label>
                    <input type="email" name="email" required maxlength="120" placeholder="tu@email.com">
                </div>
                <div class="form-grupo">
                    <label>Contraseña</label>
                    <input type="password" name="password" required placeholder="Mínimo 4 caracteres">
                </div>
                <button type="submit" class="btn-primario" style="width: 100%; background: linear-gradient(45deg, #27ae60, #2ecc71);">Crear cuenta</button>
            </form>
        </div>
    </div>
</section>

<footer>
    <div class="container"><p>&copy; Nexus 2026 - Club de Videojuegos</p></div>
</footer>

<script>
    function cambiarTab(tab) {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('activo'));
        document.querySelectorAll('.form-container').forEach(f => f.classList.remove('activo'));
        
        if (tab === 'login') {
            document.querySelectorAll('.tab-btn')[0].classList.add('activo');
            document.getElementById('form-login').classList.add('activo');
        } else {
            document.querySelectorAll('.tab-btn')[1].classList.add('activo');
            document.getElementById('form-registro').classList.add('activo');
        }
    }
</script>

</body>
</html>