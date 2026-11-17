<?php
require_once 'config.php';

if (!isset($_SESSION['usuario_id'])) { header("Location: login.php"); exit; }

$pdo = getPDO();
$pdo->exec("SET NAMES utf8mb4");
$mi_id = $_SESSION['usuario_id'];

$stmt = $pdo->prepare("SELECT * FROM clubs WHERE lider_id = ?");
$stmt->execute([$mi_id]);
$club = $stmt->fetch();

if (!$club) {
    header("Location: clubs.php?err=1&msg=" . urlencode('No eres el líder de ningún club.'));
    exit;
}

$stmt = $pdo->prepare("SELECT u.id, u.nombre, m.fecha_union FROM miembros_club m JOIN usuarios u ON m.usuario_id = u.id WHERE m.club_id = ? ORDER BY m.fecha_union ASC");
$stmt->execute([$club['id']]);
$miembros = $stmt->fetchAll();

$mensaje = $_GET['msg'] ?? '';
$error = $_GET['err'] ?? '';
$icono_actual = trim($club['icono']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Club - Nexus</title>
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
            <?php if (isset($_SESSION['usuario_id'])): ?>
                <div class="user-menu">
                    <a>👤 <?= e($_SESSION['usuario_nombre']) ?> ▾</a>
                    <div class="dropdown-content">
                        <a href="perfil.php">👤 Mi Perfil</a>
                        <a href="ajustes.php">⚙️ Ajustes de cuenta</a>
                        <a href="logout.php" class="logout-link">🚪 Cerrar Sesión</a>
                    </div>
                </div>
            <?php endif; ?>
        </nav>
    </div>
</header>

<section class="hero" style="min-height: 20vh;">
    <h1>⚙️ Panel de Líder: <?= e($club['nombre']) ?></h1>
    <p>Gestiona tu club y a tus miembros.</p>
</section>

<section class="container" style="padding: 20px;">

    <?php if ($mensaje): ?><div class="alerta <?= $error ? 'error' : 'ok' ?>"><?= e($mensaje) ?></div><?php endif; ?>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px; align-items: start;">
        
        <div class="panel">
            <h2 style="margin-top: 0;">Editar Club</h2>
            <form action="accion_club.php" method="POST">
                <input type="hidden" name="accion" value="editar">
                <input type="hidden" name="club_id" value="<?= $club['id'] ?>">
                
                <div class="form-grupo">
                    <label>Nombre del Club</label>
                    <input type="text" name="nombre" value="<?= e($club['nombre']) ?>" required maxlength="50">
                </div>
                <div class="form-grupo">
                    <label>Descripción</label>
                    <input type="text" name="descripcion" value="<?= e($club['descripcion']) ?>" maxlength="255">
                </div>
                <div class="form-grupo">
                    <label>Icono (Emoji)</label>
                    <select name="icono" style="font-size: 1.2em;">
                        <option value="🛡️" <?= $icono_actual == '🛡️' ? 'selected' : '' ?>>🛡️ Escudo</option>
                        <option value="🎯" <?= $icono_actual == '🎯' ? 'selected' : '' ?>>🎯 Diana</option>
                        <option value="🔥" <?= $icono_actual == '🔥' ? 'selected' : '' ?>>🔥 Fuego</option>
                        <option value="👾" <?= $icono_actual == '👾' ? 'selected' : '' ?>>👾 Alien</option>
                        <option value="🐺" <?= $icono_actual == '🐺' ? 'selected' : '' ?>>🐺 Lobo</option>
                        <option value="👑" <?= $icono_actual == '👑' ? 'selected' : '' ?>>👑 Corona</option>
                    </select>
                </div>
                <div class="form-grupo" style="flex-direction: row; align-items: center; gap: 10px;">
                    <input type="checkbox" name="es_privado" id="es_privado" value="1" <?= $club['es_privado'] ? 'checked' : '' ?> style="width: 20px; height: 20px;">
                    <label for="es_privado" style="margin: 0;">Club Privado</label>
                </div>
                
                <button type="submit" class="btn-primario" style="width: 100%;">💾 Guardar Cambios</button>
            </form>

            <div style="margin-top: 40px; border-top: 1px solid #333; padding-top: 20px;">
                <h3 style="color: #e74c3c;">Zona de Peligro</h3>
                <form action="accion_club.php" method="POST" onsubmit="return confirm('¿Estás completamente seguro de que quieres borrar el club?');">
                    <input type="hidden" name="accion" value="borrar">
                    <input type="hidden" name="club_id" value="<?= $club['id'] ?>">
                    <button type="submit" style="width: 100%; background: #e74c3c; border: none;">🗑️ Borrar Club</button>
                </form>
            </div>
        </div>

        <div class="panel">
            <h2 style="margin-top: 0;">Miembros (<?= count($miembros) ?>/10)</h2>
            <div style="display: flex; flex-direction: column; gap: 10px; margin-top: 20px;">
                <?php foreach ($miembros as $m): ?>
                    <div style="background: #121212; padding: 15px; border-radius: 10px; border: 1px solid #333; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <a href="perfil.php?id=<?= $m['id'] ?>" style="color: white; text-decoration: none;">
                                <strong><?= e($m['nombre']) ?></strong>
                            </a>
                            <?php if ($m['id'] == $club['lider_id']): ?>
                                <span style="background: #9b59b6; color: white; font-size: 0.7em; padding: 2px 6px; border-radius: 5px; margin-left: 10px;">LÍDER</span>
                            <?php endif; ?>
                            <div style="color: #777; font-size: 0.8em;">Se unió el <?= date('d/m/Y', strtotime($m['fecha_union'])) ?></div>
                        </div>
                        
                        <?php if ($m['id'] != $club['lider_id']): ?>
                            <form action="accion_club.php" method="POST" onsubmit="return confirm('¿Seguro que quieres expulsar a este miembro?');">
                                <input type="hidden" name="accion" value="expulsar">
                                <input type="hidden" name="club_id" value="<?= $club['id'] ?>">
                                <input type="hidden" name="usuario_id" value="<?= $m['id'] ?>">
                                <button type="submit" style="background: transparent; color: #e74c3c; border: 1px solid #e74c3c; padding: 5px 10px; font-size: 0.8em; box-shadow: none;">Expulsar</button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<footer><div class="container"><p>&copy; Nexus 2026 - Club de Videojuegos</p></div></footer>
</body>
</html>