<?php
require_once 'config.php';
require_once 'datos.php';

if (!isset($_GET['id'])) {
    header("Location: torneo.php");
    exit;
}

$id = (int)$_GET['id'];
$torneo = datos_torneo($id);

if (!$torneo) {
    die("<h1 style='color:white; text-align:center; margin-top:100px; font-family:sans-serif;'>Torneo no encontrado.</h1>");
}

$mensaje = $_GET['msg'] ?? '';
$error = $_GET['err'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($torneo['titulo']) ?> - Nexus</title>
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
            <a href="torneo.php" class="activo">Torneos</a>
            <a href="clubs.php">Clubs</a>
            <a href="servidores.php">Servidores</a>
            <a href="soporte.php">Soporte</a>
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

<section class="hero" style="min-height: 30vh;">
    <h1><?= e($torneo['titulo']) ?></h1>
    <p><?= strtoupper(e($torneo['juego'])) ?> · <?= date('d/m/Y H:i', strtotime($torneo['fecha_inicio'])) ?></p>
</section>

<section class="container" style="padding: 20px; display:grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 25px;">

    <div class="panel">
        <h2 style="margin-top:0;">Detalles del torneo</h2>
        <img src="<?= $torneo['imagen'] ? e($torneo['imagen']) : 'https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&w=600&q=80' ?>" 
             alt="<?= e($torneo['titulo']) ?>" style="width:100%; border-radius:12px; margin-bottom:20px;">
        <p><?= nl2br(e($torneo['descripcion'])) ?></p>
        
        <div class="torneo-stats" style="margin-top:20px;">
            <div class="stat-item">
                <div class="stat-numero"><?= e($torneo['premio']) ?></div>
                <div class="stat-label">Premio</div>
            </div>
            <div class="stat-item">
                <div class="stat-numero"><?= $torneo['inscritos'] ?>/<?= $torneo['max_jugadores'] ?></div>
                <div class="stat-label">Inscritos</div>
            </div>
        </div>
    </div>

    <div class="panel" id="inscribirse">
        <h2 style="margin-top:0;">📝 Inscripción</h2>

        <?php if ($mensaje): ?>
            <div class="alerta <?= $error ? 'error' : 'ok' ?>"><?= e($mensaje) ?></div>
        <?php endif; ?>

        <?php if ($torneo['inscritos'] >= $torneo['max_jugadores']): ?>
            <div class="alerta error">Este torneo ya está lleno.</div>
        <?php else: ?>
            <?php if (!isset($_SESSION['usuario_id'])): ?>
                <div class="alerta error">Debes iniciar sesión para inscribirte.</div>
                <button class="btn-primario" style="width: 100%;" onclick="location.href='login.php'">Iniciar Sesión</button>
            <?php else: ?>
                
                <?php 
                $pdo = getPDO();
                $stmt = $pdo->prepare("SELECT c.id, c.nombre FROM clubs c WHERE c.lider_id = ?");
                $stmt->execute([$_SESSION['usuario_id']]);
                $mi_club_lider = $stmt->fetch();
                ?>

                <?php if ($mi_club_lider): ?>
                    <div class="tabs" style="display: flex; gap: 10px; margin-bottom: 20px;">
                        <button class="tab-btn" id="btn-tab-solo" onclick="cambiarTabInscripcion('solo')" style="flex: 1; padding: 10px; border-radius: 8px; border: none; cursor: pointer; font-weight: bold; background: #8e44ad; color: white;">👤 Solo</button>
                        <button class="tab-btn" id="btn-tab-club" onclick="cambiarTabInscripcion('club')" style="flex: 1; padding: 10px; border-radius: 8px; border: none; cursor: pointer; font-weight: bold; background: #2a2a2a; color: #aaa;">🛡️ Mi Club</button>
                    </div>
                <?php endif; ?>

                <div id="form-solo" style="display: block;">
                    <p style="color: #aaa; font-size: 0.9em; margin-bottom: 15px;">Apúntate a ti mismo a este torneo.</p>
                    <form action="accion_inscripcion.php" method="POST">
                        <input type="hidden" name="torneo_id" value="<?= $torneo['id'] ?>">
                        <input type="hidden" name="modo" value="solo">
                        <button type="submit" class="btn-primario" style="width: 100%;">🎟️ Confirmar Inscripción</button>
                    </form>
                </div>

                <?php if ($mi_club_lider): ?>
                <div id="form-club" style="display: none;">
                    <p style="color: #aaa; font-size: 0.9em; margin-bottom: 15px;">
                        Apunta a tu club <strong style="color: white;"><?= e($mi_club_lider['nombre']) ?></strong> al torneo. Se apuntarán todos tus miembros.
                    </p>
                    <form action="accion_inscripcion.php" method="POST">
                        <input type="hidden" name="torneo_id" value="<?= $torneo['id'] ?>">
                        <input type="hidden" name="modo" value="club">
                        <input type="hidden" name="club_id" value="<?= $mi_club_lider['id'] ?>">
                        <button type="submit" class="btn-primario" style="width: 100%; background: linear-gradient(45deg, #9b59b6, #8e44ad);">🛡️ Inscribir a mi Club</button>
                    </form>
                </div>
                <?php endif; ?>

            <?php endif; ?>
        <?php endif; ?>
    </div>

    <div class="panel" style="margin-top: 0; width: 100%; grid-column: 1 / -1;">
        <h3 style="margin-top: 0; color: #2ecc71;">👥 Jugadores Inscritos</h3>
        <?php
        $stmt_inscritos = $pdo->prepare("SELECT u.id, u.nombre, i.fecha_inscripcion, c.nombre AS club_nombre 
                                         FROM inscripciones_torneo i 
                                         JOIN usuarios u ON i.usuario_id = u.id 
                                         LEFT JOIN clubs c ON i.club_id = c.id
                                         WHERE i.torneo_id = ? 
                                         ORDER BY i.fecha_inscripcion ASC");
        $stmt_inscritos->execute([$id]);
        $lista_inscritos = $stmt_inscritos->fetchAll();

        if (empty($lista_inscritos)): ?>
            <p style="color: #777; font-style: italic;">Nadie se ha inscrito todavía. ¡Sé el primero!</p>
        <?php else: ?>
            <div style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 15px;">
                <?php foreach ($lista_inscritos as $ins): ?>
                    <a href="perfil.php?id=<?= $ins['id'] ?>" style="text-decoration: none;">
                        <div style="background: #121212; border: 1px solid #333; padding: 10px 15px; border-radius: 10px; display: flex; align-items: center; gap: 10px; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                            <div style="width: 35px; height: 35px; background: #2a2a2a; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">🎮</div>
                            <div>
                                <span style="color: white; font-weight: bold; font-size: 0.95em;"><?= e($ins['nombre']) ?></span>
                                <?php if ($ins['club_nombre']): ?>
                                    <span style="display: block; color: #9b59b6; font-size: 0.75em;">🛡️ <?= e($ins['club_nombre']) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

</section>

<footer><div class="container"><p>&copy; Nexus 2026 - Club de Videojuegos</p></div></footer>

<script>
    function cambiarTabInscripcion(tab) {
        document.getElementById('form-solo').style.display = 'none';
        if(document.getElementById('form-club')) document.getElementById('form-club').style.display = 'none';
        
        document.getElementById('btn-tab-solo').style.background = '#2a2a2a';
        document.getElementById('btn-tab-solo').style.color = '#aaa';
        if(document.getElementById('btn-tab-club')) {
            document.getElementById('btn-tab-club').style.background = '#2a2a2a';
            document.getElementById('btn-tab-club').style.color = '#aaa';
        }

        if (tab === 'solo') {
            document.getElementById('form-solo').style.display = 'block';
            document.getElementById('btn-tab-solo').style.background = '#8e44ad';
            document.getElementById('btn-tab-solo').style.color = 'white';
        } else {
            if(document.getElementById('form-club')) document.getElementById('form-club').style.display = 'block';
            document.getElementById('btn-tab-club').style.background = '#8e44ad';
            document.getElementById('btn-tab-club').style.color = 'white';
        }
    }
</script>

</body>
</html>