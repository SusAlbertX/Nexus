<?php
require_once 'config.php';

if (!isset($_SESSION['usuario_id'])) { header("Location: login.php"); exit; }

if ($_SESSION['usuario_nombre'] !== 'AlbertUltimate') {
    die("<h1 style='color:red; text-align:center; margin-top:100px; font-family:sans-serif;'>Acceso Denegado. Solo el administrador puede ver esta página.</h1>");
}

$pdo = getPDO();
$pdo->exec("SET NAMES utf8mb4");
$mensaje = $_GET['msg'] ?? '';
$error = $_GET['err'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Admin - Nexus</title>
    <link rel="icon" href="icono.png" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<header>
    <div class="container">
        <p class="logo" onclick="location.href='index.php'">NEXUS ADMIN</p>
        <nav>
            <a href="index.php">Inicio</a>
            <a href="torneo.php">Ver Torneos</a>
            <a href="admin.php" class="activo" style="color: #e74c3c;"> Panel de Control</a>
            <a href="logout.php" style="color: #e74c3c;">Cerrar Sesión</a>
        </nav>
    </div>
</header>

<section class="hero" style="min-height: 20vh; background-image: linear-gradient(0deg, rgba(0,0,0,0.8), rgba(0,0,0,0.8)), url('https://images.unsplash.com/photo-1511512578047-dfb367046420?auto=format&fit=crop&w=1600&q=80');">
    <h1>Panel de Administración</h1>
</section>

<section class="container" style="padding: 20px;">
    
    <?php if ($mensaje): ?><div class="alerta <?= $error ? 'error' : 'ok' ?>"><?= e($mensaje) ?></div><?php endif; ?>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px; align-items: start;">
        
        <div class="panel">
            <h2 style="margin-top: 0; color: #9b59b6;"> Crear Nuevo Torneo</h2>
            <form action="accion_admin.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="accion" value="crear_torneo">
                
                <div class="form-grupo">
                    <label>Título del Torneo *</label>
                    <input type="text" name="titulo" required maxlength="100" placeholder="Ej: Cash Cup Fortnite">
                </div>
                
                <div class="form-grupo">
                    <label>Juego *</label>
                    <select name="juego" required>
                        <option value="fortnite">Fortnite</option>
                        <option value="rocket">Rocket League</option>
                        <option value="cod">Call of Duty</option>
                        <option value="cs2">CS2</option>
                    </select>
                </div>
                
                <div class="form-grupo">
                    <label>Premio *</label>
                    <input type="text" name="premio" required maxlength="100" placeholder="Ej: $200">
                </div>
                
                <div class="form-grupo">
                    <label>Máximo de Jugadores *</label>
                    <input type="number" name="max_jugadores" required min="2" max="500" placeholder="Ej: 100">
                </div>

                <div class="form-grupo">
                    <label>Fecha y Hora de Inicio *</label>
                    <input type="datetime-local" name="fecha_inicio" required>
                </div>

                <div class="form-grupo">
                    <label>Imagen del Torneo (URL)</label>
                    <input type="url" name="imagen" placeholder="https://ejemplo.com/imagen.jpg">
                    <small style="color: #777; font-size: 0.85em; margin-top: 5px;">Pega un enlace a una imagen (opcional).</small>
                </div>

                <div class="form-grupo">
                    <label>Descripción</label>
                    <textarea name="descripcion" rows="3" maxlength="500" placeholder="Reglas del torneo..."></textarea>
                </div>
                
                <button type="submit" class="btn-primario" style="width: 100%;"> Crear Torneo</button>
            </form>
        </div>

        <!-- GESTIÓN DE TORNEOS -->
        <div class="panel">
            <h2 style="margin-top: 0; color: #2ecc71;">⚙️ Torneos Activos</h2>
            <?php
            $torneos = $pdo->query("SELECT * FROM torneos ORDER BY fecha_inicio ASC")->fetchAll();
            if (empty($torneos)): ?>
                <p style="color: #aaa;">No hay torneos creados todavía.</p>
            <?php else: ?>
                <div style="display: flex; flex-direction: column; gap: 10px; margin-top: 15px;">
                    <?php foreach ($torneos as $t): ?>
                        <div style="background: #121212; padding: 15px; border-radius: 10px; border: 1px solid #333; display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <strong style="color: white;"><?= e($t['titulo']) ?></strong>
                                <div style="color: #9b59b6; font-size: 0.8em;"><?= e($t['juego']) ?> - <?= date('d/m/Y H:i', strtotime($t['fecha_inicio'])) ?></div>
                            </div>
                            <form action="accion_admin.php" method="POST" onsubmit="return confirm('¿Seguro que quieres borrar este torneo?');">
                                <input type="hidden" name="accion" value="borrar_torneo">
                                <input type="hidden" name="torneo_id" value="<?= $t['id'] ?>">
                                <button type="submit" style="background: #e74c3c; border: none; padding: 5px 15px; font-size: 0.8em;">Borrar</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

    </div>
</section>

<footer><div class="container"><p>&copy; Nexus 2026 - Admin</p></div></footer>
</body>
</html>