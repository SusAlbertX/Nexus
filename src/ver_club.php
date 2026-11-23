<?php
require_once 'config.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$pdo = getPDO();
$pdo->exec("SET NAMES utf8mb4");
$mi_id = $_SESSION['usuario_id'];

$club_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT c.*, u.nombre AS lider_nombre FROM clubs c JOIN usuarios u ON c.lider_id = u.id WHERE c.id = ?");
$stmt->execute([$club_id]);
$club = $stmt->fetch();

if (!$club) {
    header("Location: clubs.php?err=1&msg=" . urlencode('El club no existe.'));
    exit;
}

$stmt = $pdo->prepare("SELECT id FROM miembros_club WHERE club_id = ? AND usuario_id = ?");
$stmt->execute([$club_id, $mi_id]);
$soy_miembro = $stmt->fetch() ? true : false;

if ($club['es_privado'] && !$soy_miembro && $club['lider_id'] != $mi_id) {
    header("Location: clubs.php?err=1&msg=" . urlencode('Este club es privado. Solo los miembros pueden ver su interior.'));
    exit;
}

$stmt = $pdo->prepare("SELECT u.id, u.nombre, m.fecha_union FROM miembros_club m JOIN usuarios u ON m.usuario_id = u.id WHERE m.club_id = ? ORDER BY m.fecha_union ASC");
$stmt->execute([$club_id]);
$miembros = $stmt->fetchAll();

$mensaje = $_GET['msg'] ?? '';
$error = $_GET['err'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($club['nombre']) ?> - Nexus</title>
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
            <a href="clubs.php" class="activo">Clubs</a>
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

<section class="hero" style="min-height: 25vh;">
    <h1><?= e($club['icono']) ?> <?= e($club['nombre']) ?></h1>
    <p><?= e($club['descripcion']) ?></p>
</section>

<section class="container" style="padding: 20px; max-width: 900px;">
    
    <?php if ($mensaje): ?>
        <div class="alerta <?= $error ? 'error' : 'ok' ?>"><?= e($mensaje) ?></div>
    <?php endif; ?>

    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 25px; align-items: start;">
        
        <div class="panel" style="text-align: center;">
            <div style="font-size: 5em; margin-bottom: 10px;"><?= e($club['icono']) ?></div>
            <h2 style="margin: 0; color: white;"><?= e($club['nombre']) ?></h2>
            <p style="color: #777; font-size: 0.9em; margin-top: 5px;">Creado el <?= date('d/m/Y', strtotime($club['fecha_creacion'])) ?></p>
            
            <div style="background: #121212; padding: 15px; border-radius: 10px; margin-top: 20px; border: 1px solid #333;">
                <p style="margin: 0; color: #aaa; font-size: 0.9em;">Líder del Club</p>
                <a href="perfil.php?id=<?= $club['lider_id'] ?>" style="color: #9b59b6; font-weight: bold; font-size: 1.1em; text-decoration: none;">
                    <?= e($club['lider_nombre']) ?>
                </a>
            </div>

            <div style="display: flex; justify-content: space-between; margin-top: 20px;">
                <div class="stat-item">
                    <div class="stat-numero"><?= count($miembros) ?>/10</div>
                    <div class="stat-label">Miembros</div>
                </div>
                <div class="stat-item">
                    <div class="stat-numero" style="color: <?= $club['es_privado'] ? '#e74c3c' : '#2ecc71' ?>;">
                        <?= $club['es_privado'] ? 'Privado' : 'Público' ?>
                    </div>
                    <div class="stat-label">Tipo</div>
                </div>
            </div>

            <?php if ($club['lider_id'] == $mi_id): ?>
                <button class="btn-primario" style="width: 100%; margin-top: 20px;" onclick="location.href='mi_club.php?id=<?= $club['id'] ?>'">⚙️ Gestionar mi Club</button>
            <?php endif; ?>
        </div>

        <div class="panel">
            <h2 style="margin-top: 0;">Plantilla de Jugadores</h2>
            <div style="display: flex; flex-direction: column; gap: 10px; margin-top: 20px;">
                <?php foreach ($miembros as $m): ?>
                    <div style="background: #121212; padding: 15px; border-radius: 10px; border: 1px solid #333; display: flex; justify-content: space-between; align-items: center;">
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <div style="width: 40px; height: 40px; background: #2a2a2a; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.2em;">
                                🎮
                            </div>
                            <div>
                                <a href="perfil.php?id=<?= $m['id'] ?>" style="color: white; text-decoration: none; font-weight: bold; font-size: 1.1em;">
                                    <?= e($m['nombre']) ?>
                                </a>
                                <?php if ($m['id'] == $club['lider_id']): ?>
                                    <span style="background: #9b59b6; color: white; font-size: 0.65em; padding: 2px 6px; border-radius: 5px; margin-left: 10px; vertical-align: middle;">LÍDER</span>
                                <?php endif; ?>
                                <div style="color: #777; font-size: 0.8em;">Se unió el <?= date('d/m/Y', strtotime($m['fecha_union'])) ?></div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px; margin-top: 25px;">
        
        <div class="panel" style="opacity: 0.7; border: 1px dashed #555; text-align: center; display: flex; flex-direction: column; justify-content: center; align-items: center; min-height: 250px;">
            <div style="font-size: 3em; margin-bottom: 10px;">💬</div>
            <h3 style="margin: 0; color: #f39c12;">Chat de Club</h3>
            <p style="color: #777; font-size: 0.9em; margin-top: 10px;">Un espacio privado para hablar con tus compañeros de equipo.</p>
            <span style="background: #333; color: #aaa; padding: 5px 15px; border-radius: 20px; font-size: 0.8em; font-weight: bold; margin-top: 15px;">🚧 Próximamente (v1.5)</span>
        </div>

        <div class="panel" style="opacity: 0.7; border: 1px dashed #555; text-align: center; display: flex; flex-direction: column; justify-content: center; align-items: center; min-height: 250px;">
            <div style="font-size: 3em; margin-bottom: 10px;">📊</div>
            <h3 style="margin: 0; color: #3498db;">Club Polls</h3>
            <p style="color: #777; font-size: 0.9em; margin-top: 10px;">Decidid juntos a qué torneos apuntaros o qué mapas jugar.</p>
            
            <?php if ($club['lider_id'] == $mi_id): ?>
                <button disabled style="background: #333; color: #777; padding: 8px 20px; border-radius: 8px; border: none; font-weight: bold; margin-top: 15px; cursor: not-allowed;">
                    ➕ Crear Encuesta
                </button>
            <?php else: ?>
                <span style="background: #333; color: #aaa; padding: 5px 15px; border-radius: 20px; font-size: 0.8em; font-weight: bold; margin-top: 15px;">🚧 Próximamente (v1.5)</span>
            <?php endif; ?>
        </div>

    </div>

</section>

<footer><div class="container"><p>&copy; Nexus 2026 - Club de Videojuegos</p></div></footer>
</body>
</html>