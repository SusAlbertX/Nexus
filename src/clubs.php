<?php
require_once 'config.php';

if (!isset($_SESSION['usuario_id'])) { header("Location: login.php"); exit; }

$pdo = getPDO();
$pdo->exec("SET NAMES utf8mb4");
$mi_id = $_SESSION['usuario_id'];
$mensaje = $_GET['msg'] ?? '';
$error = $_GET['err'] ?? '';

$stmt = $pdo->prepare("SELECT club_id FROM miembros_club WHERE usuario_id = ?");
$stmt->execute([$mi_id]);
$mi_club = $stmt->fetch();
$mi_club_id = $mi_club ? $mi_club['club_id'] : 0;

$query = "SELECT c.*, u.nombre AS lider_nombre, 
          (SELECT COUNT(*) FROM miembros_club m WHERE m.club_id = c.id) AS total_miembros 
          FROM clubs c 
          JOIN usuarios u ON c.lider_id = u.id 
          ORDER BY c.fecha_creacion DESC";
$clubs = $pdo->query($query)->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clubs - Nexus</title>
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

<section class="hero" style="min-height: 25vh;">
    <h1>🛡️ Clubs Nexus</h1>
    <p>Únete a un equipo, compite junto a tus amigos y dominad la plataforma.</p>
</section>

<section class="container" style="padding: 20px;">
    
    <?php if ($mensaje): ?>
        <div class="alerta <?= $error ? 'error' : 'ok' ?>"><?= e($mensaje) ?></div>
    <?php endif; ?>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; flex-wrap: wrap; gap: 15px;">
        <h2 style="margin: 0;">Explorar Clubs</h2>
        <?php if ($mi_club_id == 0): ?>
            <button class="btn-primario" onclick="abrirModalCrear()">➕ Crear Nuevo Club</button>
        <?php else: ?>
            <span style="color: #2ecc71; font-weight: bold; background: rgba(46, 204, 113, 0.1); padding: 10px 20px; border-radius: 10px; border: 1px solid #2ecc71;">✅ Ya perteneces a un club</span>
        <?php endif; ?>
    </div>

    <?php if (empty($clubs)): ?>
        <div class="panel" style="text-align: center;"><p>Todavía no hay clubes en Nexus. ¡Sé el primero en crear uno!</p></div>
    <?php else: ?>
        <div class="grid-torneos">
            <?php foreach ($clubs as $club): ?>
                <div class="torneo-card" style="border: 2px solid <?= $club['id'] == $mi_club_id ? '#9b59b6' : 'transparent' ?>;">
                    <div style="background: linear-gradient(45deg, #1a1a1a, #2a2a2a); padding: 20px; text-align: center; border-bottom: 2px solid #333; position: relative;">
                        <span style="position: absolute; top: 10px; right: 10px; font-size: 0.7em; padding: 4px 8px; border-radius: 10px; background: <?= $club['es_privado'] ? '#e74c3c' : '#2ecc71' ?>; color: white; font-weight: bold;">
                            <?= $club['es_privado'] ? '🔒 Privado' : '🌍 Público' ?>
                        </span>
                        <div style="font-size: 3em; margin-bottom: 5px;"><?= e($club['icono']) ?></div>
                        <h3 class="torneo-titulo" style="margin: 0; color: white;"><?= e($club['nombre']) ?></h3>
                        <p style="color: #aaa; font-size: 0.85em; margin-top: 5px;">Líder: <a href="perfil.php?id=<?= $club['lider_id'] ?>" style="color: #9b59b6; text-decoration: none; font-weight: bold;"><?= e($club['lider_nombre']) ?></a></p>
                    </div>
                    <div class="torneo-contenido">
                        <p style="color: #ccc; font-size: 0.9em; min-height: 40px;"><?= e($club['descripcion']) ?></p>
                        <div class="torneo-stats" style="margin-top: 15px;">
                            <div class="stat-item">
                                <div class="stat-numero"><?= $club['total_miembros'] ?>/10</div>
                                <div class="stat-label">Miembros</div>
                            </div>
                            <div class="stat-item">
                                <?php if ($club['total_miembros'] >= 10): ?>
                                    <div class="stat-numero" style="color: #e74c3c; font-size: 0.9em;">Lleno</div>
                                <?php else: ?>
                                    <div class="stat-numero" style="color: #2ecc71; font-size: 0.9em;"><?= 10 - $club['total_miembros'] ?> huecos</div>
                                <?php endif; ?>
                                <div class="stat-label">Disponibilidad</div>
                            </div>
                        </div>
                        <div class="torneo-acciones" style="margin-top: 15px;">
                            <?php if ($club['id'] == $mi_club_id): ?>
                                <div style="display: flex; gap: 10px; width: 100%;">
                                    <button style="background: #2a2a2a; color: #9b59b6; border: 1px solid #9b59b6; flex: 1; cursor: default;">✅ Miembro</button>
                                    <?php if ($club['lider_id'] == $mi_id): ?>
                                        <button class="btn-primario" style="flex: 1;" onclick="location.href='mi_club.php'">⚙️ Gestionar</button>
                                    <?php endif; ?>
                                </div>
                            <?php elseif ($mi_club_id != 0): ?>
                                <button disabled style="width: 100%; background: #333; color: #777; border: none;">Ya estás en otro club</button>
                            <?php elseif ($club['total_miembros'] >= 10): ?>
                                <button disabled style="width: 100%; background: #333; color: #777; border: none;">❌ Club Lleno</button>
                            <?php elseif ($club['es_privado']): ?>
                                <button class="btn-outline" style="width: 100%;" onclick="alert('Este club es privado. El líder debe invitarte.')">🔒 Solicitar Invitación</button>
                            <?php else: ?>
                                <form action="accion_club.php" method="POST" style="width: 100%;">
                                    <input type="hidden" name="accion" value="unirse">
                                    <input type="hidden" name="club_id" value="<?= $club['id'] ?>">
                                    <button type="submit" class="btn-primario" style="width: 100%;">➕ Unirse al Club</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<div id="modal-crear" class="modal-overlay">
    <div class="modal">
        <h3>🛡️ Crear Nuevo Club</h3>
        <form action="accion_club.php" method="POST">
            <input type="hidden" name="accion" value="crear">
            <div class="form-grupo">
                <label>Nombre del Club *</label>
                <input type="text" name="nombre" required maxlength="50" placeholder="Ej: Los Invencibles">
            </div>
            <div class="form-grupo">
                <label>Descripción corta</label>
                <input type="text" name="descripcion" maxlength="255" placeholder="¿A qué jugáis?">
            </div>
            <div class="form-grupo">
                <label>Icono (Emoji)</label>
                <select name="icono">
                    <option value="🛡️">🛡️ Escudo</option>
                    <option value="🎯">🎯 Diana</option>
                    <option value="🔥">🔥 Fuego</option>
                    <option value="👾">👾 Alien</option>
                    <option value="🐺">🐺 Lobo</option>
                    <option value="👑">👑 Corona</option>
                </select>
            </div>
            <div class="form-grupo" style="flex-direction: row; align-items: center; gap: 10px;">
                <input type="checkbox" name="es_privado" id="es_privado" value="1" style="width: 20px; height: 20px;">
                <label for="es_privado" style="margin: 0;">Hacer este club privado</label>
            </div>
            <div class="modal-buttons">
                <button type="button" class="modal-btn cancel" onclick="cerrarModalCrear()">Cancelar</button>
                <button type="submit" class="modal-btn confirm">Crear Club</button>
            </div>
        </form>
    </div>
</div>

<footer><div class="container"><p>&copy; Nexus 2026 - Club de Videojuegos</p></div></footer>

<script>
    function abrirModalCrear() {
        document.getElementById('modal-crear').style.display = 'flex';
        document.body.classList.add('modal-abierto');
    }
    function cerrarModalCrear() {
        document.getElementById('modal-crear').style.display = 'none';
        document.body.classList.remove('modal-abierto');
    }
</script>
</body>
</html>