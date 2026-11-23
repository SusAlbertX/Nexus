<?php
require_once 'config.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$pdo = getPDO();
$pdo->exec("SET NAMES utf8mb4");
$mi_id = $_SESSION['usuario_id'];
$perfil_id = isset($_GET['id']) ? (int)$_GET['id'] : $mi_id;

$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$perfil_id]);
$usuario = $stmt->fetch();

if (!$usuario) {
    header("Location: index.php?err=1&msg=" . urlencode('El usuario que buscas no existe.'));
    exit;
}

$stmt = $pdo->prepare("SELECT c.* FROM clubs c JOIN miembros_club m ON c.id = m.club_id WHERE m.usuario_id = ?");
$stmt->execute([$perfil_id]);
$club = $stmt->fetch();

$es_mi_perfil = ($perfil_id === $mi_id);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de <?= e($usuario['nombre']) ?> - Nexus</title>
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
            <?php endif; ?>
        </nav>
    </div>
</header>

<section class="hero" style="min-height: 25vh; background-image: linear-gradient(0deg, rgba(0,0,0,0.8), rgba(0,0,0,0.8)), url('https://images.unsplash.com/photo-1511512578047-dfb367046420?auto=format&fit=crop&w=1600&q=80');">
    <h1>👤 Perfil de <?= e($usuario['nombre']) ?></h1>
</section>

<section class="container" style="padding: 20px; max-width: 900px;">
    
    <?php if (isset($_GET['msg'])): ?>
        <div class="alerta <?= isset($_GET['err']) ? 'error' : 'ok' ?>"><?= e($_GET['msg']) ?></div>
    <?php endif; ?>

    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 25px; align-items: start;">
        
        <div class="panel" style="text-align: center;">
            <div style="width: 120px; height: 120px; background: linear-gradient(45deg, #8e44ad, #9b59b6); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 4em; margin: 0 auto 20px auto; border: 4px solid #2a2a2a; overflow: hidden;">
                <?php if (!empty($usuario['avatar']) && file_exists($usuario['avatar'])): ?>
                    <img src="<?= e($usuario['avatar']) ?>" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover;">
                <?php else: ?>
                    🎮
                <?php endif; ?>
            </div>
            
            <h2 style="margin: 0; color: white;"><?= e($usuario['nombre']) ?></h2>
            <p style="color: #777; font-size: 0.9em; margin-top: 5px;">ID: #<?= $usuario['id'] ?></p>
            <p style="color: #9b59b6; font-size: 0.9em; font-weight: bold; margin-top: 5px;">📅 Miembro desde: <?= date('d/m/Y', strtotime($usuario['fecha_registro'])) ?></p>
            
            <?php if ($es_mi_perfil): ?>
                <button class="btn-primario" style="width: 100%; margin-top: 20px;" onclick="location.href='ajustes.php'">⚙️ Editar mi perfil</button>
            <?php endif; ?>
        </div>

        <div style="display: flex; flex-direction: column; gap: 25px;">
            
            <div class="panel">
                <h3 style="margin-top: 0; color: #9b59b6;">🛡️ Club Actual</h3>
                
                <?php if ($club): ?>
                    <div style="display: flex; align-items: center; gap: 15px; margin-top: 15px;">
                        <div style="font-size: 3em; background: #121212; padding: 15px; border-radius: 15px; border: 1px solid #333;">
                            <?= e($club['icono']) ?>
                        </div>
                        <div>
                            <h4 style="margin: 0; color: white; font-size: 1.3em;">
                                <a href="ver_club.php?id=<?= $club['id'] ?>" style="color: white; text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='#9b59b6'" onmouseout="this.style.color='white'">
                                    <?= e($club['nombre']) ?>
                                </a>
                            </h4>
                            <p style="margin: 5px 0 0 0; color: #aaa; font-size: 0.9em;"><?= e($club['descripcion']) ?></p>
                        </div>
                    </div>
                <?php else: ?>
                    <div style="background: #121212; border: 1px dashed #333; padding: 20px; border-radius: 10px; text-align: center; margin-top: 15px;">
                        <p style="color: #777; margin: 0;">Este jugador aún no pertenece a ningún club.</p>
                        <?php if ($es_mi_perfil): ?>
                            <button class="btn-outline" style="margin-top: 15px; font-size: 0.9em;" onclick="location.href='clubs.php'">Buscar un Club</button>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="panel">
                <h3 style="margin-top: 0; color: #2ecc71;">📊 Estadísticas</h3>
                <div class="torneo-stats" style="margin-top: 15px;">
                    <div class="stat-item">
                        <div class="stat-numero">0</div>
                        <div class="stat-label">Torneos Ganados</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-numero">0</div>
                        <div class="stat-label">Torneos Jugados</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-numero">0</div>
                        <div class="stat-label">Puntos Nexus</div>
                    </div>
                </div>
                <p style="color: #777; font-size: 0.8em; text-align: center; margin-top: 15px;">Las estadísticas se actualizarán cuando termine la primera temporada.</p>
            </div>

        </div>
    </div>
</section>

<footer><div class="container"><p>&copy; Nexus 2026 - Club de Videojuegos</p></div></footer>
</body>
</html>