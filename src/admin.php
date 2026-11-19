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
            <a href="admin.php" class="activo" style="color: #e74c3c;">👑 Panel de Control</a>
            <a href="logout.php" style="color: #e74c3c;">Cerrar Sesión</a>
        </nav>
    </div>
</header>

<section class="hero" style="min-height: 20vh; background-image: linear-gradient(0deg, rgba(0,0,0,0.8), rgba(0,0,0,0.8)), url('https://images.unsplash.com/photo-1511512578047-dfb367046420?auto=format&fit=crop&w=1600&q=80');">
    <h1>👑 Panel de Administración</h1>
    <p>Bienvenido, dueño de Nexus.</p>
</section>

<section class="container" style="padding: 20px;">
    
    <?php if ($mensaje): ?><div class="alerta <?= $error ? 'error' : 'ok' ?>"><?= e($mensaje) ?></div><?php endif; ?>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px; align-items: start;">
        
        <div class="panel">
            <h2 style="margin-top: 0; color: #9b59b6;">🏆 Crear Nuevo Torneo</h2>
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
                
                <button type="submit" class="btn-primario" style="width: 100%;">🚀 Crear Torneo</button>
            </form>
        </div>

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
                            <div style="display: flex; gap: 10px;">
                                <!-- BOTÓN EDITAR CORREGIDO (Usa htmlspecialchars y comillas simples) -->
                                <button onclick='abrirModalEditar(<?= htmlspecialchars(json_encode($t), ENT_QUOTES, 'UTF-8') ?>)' style="background: #f39c12; border: none; padding: 5px 15px; font-size: 0.8em;">✏️ Editar</button>
                                
                                <!-- BOTÓN BORRAR -->
                                <form action="accion_admin.php" method="POST" onsubmit="return confirm('¿Seguro que quieres borrar este torneo?');">
                                    <input type="hidden" name="accion" value="borrar_torneo">
                                    <input type="hidden" name="torneo_id" value="<?= $t['id'] ?>">
                                    <button type="submit" style="background: #e74c3c; border: none; padding: 5px 15px; font-size: 0.8em;">Borrar</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

    </div>

    <div style="display: grid; grid-template-columns: 1fr; gap: 25px; margin-top: 25px;">
        <div class="panel">
            <h2 style="margin-top: 0; color: #e74c3c;">🚫 Gestión de Usuarios</h2>
            <p style="color: #aaa; font-size: 0.9em;">Desde aquí puedes banear a usuarios problemáticos. Un usuario baneado no podrá iniciar sesión.</p>
            
            <div style="display: flex; flex-direction: column; gap: 10px; margin-top: 15px;">
                <?php
                $usuarios = $pdo->query("SELECT id, nombre, email, baneado FROM usuarios WHERE nombre != 'AlbertUltimate' ORDER BY id DESC")->fetchAll();
                foreach ($usuarios as $u): ?>
                    <div style="background: #121212; padding: 15px; border-radius: 10px; border: 1px solid #333; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <strong style="color: <?= $u['baneado'] ? '#e74c3c' : 'white' ?>;"><?= e($u['nombre']) ?></strong>
                            <span style="color: #777; font-size: 0.8em; margin-left: 10px;">(<?= e($u['email']) ?>)</span>
                            <?php if ($u['baneado']): ?>
                                <span style="background: #e74c3c; color: white; font-size: 0.7em; padding: 2px 6px; border-radius: 5px; margin-left: 10px;">BANEADO</span>
                            <?php endif; ?>
                        </div>
                        <form action="accion_admin.php" method="POST" onsubmit="return confirm('¿Seguro que quieres cambiar el estado de baneo de este usuario?');">
                            <input type="hidden" name="accion" value="banear">
                            <input type="hidden" name="usuario_id" value="<?= $u['id'] ?>">
                            <input type="hidden" name="baneado_actual" value="<?= $u['baneado'] ?>">
                            <button type="submit" style="background: <?= $u['baneado'] ? '#2ecc71' : '#e74c3c' ?>; border: none; padding: 5px 15px; font-size: 0.8em;">
                                <?= $u['baneado'] ? 'Desbanear' : 'Banear' ?>
                            </button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr; gap: 25px; margin-top: 25px;">
        <div class="panel">
            <h2 style="margin-top: 0; color: #9b59b6;">🛡️ Gestión de Clubes</h2>
            <p style="color: #aaa; font-size: 0.9em;">Puedes borrar cualquier club de la plataforma o expulsar a sus miembros.</p>
            
            <?php
            $clubes = $pdo->query("SELECT c.*, u.nombre AS lider FROM clubs c JOIN usuarios u ON c.lider_id = u.id")->fetchAll();
            if (empty($clubes)): ?>
                <p style="color: #777;">No hay clubes creados aún.</p>
            <?php else: ?>
                <div style="display: flex; flex-direction: column; gap: 15px; margin-top: 15px;">
                    <?php foreach ($clubes as $c): ?>
                        <div style="background: #121212; padding: 15px; border-radius: 10px; border: 1px solid #333;">
                            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #333; padding-bottom: 10px; margin-bottom: 10px;">
                                <div>
                                    <strong style="color: white; font-size: 1.1em;"><?= e($c['icono']) ?> <?= e($c['nombre']) ?></strong>
                                    <div style="color: #777; font-size: 0.8em;">Líder: <?= e($c['lider']) ?></div>
                                </div>
                                <form action="accion_admin.php" method="POST" onsubmit="return confirm('¡CUIDADO! ¿Seguro que quieres borrar este club entero?');">
                                    <input type="hidden" name="accion" value="borrar_club_admin">
                                    <input type="hidden" name="club_id" value="<?= $c['id'] ?>">
                                    <button type="submit" style="background: transparent; color: #e74c3c; border: 1px solid #e74c3c; padding: 5px 10px; font-size: 0.8em;">🗑️ Borrar Club</button>
                                </form>
                            </div>
                            
                            <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                                <?php
                                $stmt_m = $pdo->prepare("SELECT u.id, u.nombre FROM miembros_club m JOIN usuarios u ON m.usuario_id = u.id WHERE m.club_id = ?");
                                $stmt_m->execute([$c['id']]);
                                $miembros = $stmt_m->fetchAll();
                                
                                foreach ($miembros as $m): ?>
                                    <div style="background: #2a2a2a; padding: 5px 10px; border-radius: 20px; display: flex; align-items: center; gap: 10px; font-size: 0.85em;">
                                        <?= e($m['nombre']) ?>
                                        <?php if ($m['id'] != $c['lider_id']): ?>
                                            <form action="accion_admin.php" method="POST" onsubmit="return confirm('¿Expulsar a este usuario del club?');">
                                                <input type="hidden" name="accion" value="expulsar_admin">
                                                <input type="hidden" name="club_id" value="<?= $c['id'] ?>">
                                                <input type="hidden" name="usuario_id" value="<?= $m['id'] ?>">
                                                <button type="submit" style="background: transparent; color: #e74c3c; border: none; font-size: 1.2em; padding: 0; cursor: pointer;" title="Expulsar">&times;</button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr; gap: 25px; margin-top: 25px;">
        <div class="panel" style="border-top: 4px solid #e67e22;">
            <h2 style="margin-top: 0; color: #e67e22;">✉️ Enviar Correo a Inscritos</h2>
            <p style="color: #aaa; font-size: 0.9em;">Selecciona un torneo y envía un aviso a todos los jugadores que estén inscritos en él.</p>
            
            <form action="accion_admin_correos.php" method="POST">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    
                    <div class="form-grupo">
                        <label>Selecciona el Torneo *</label>
                        <select name="torneo_id" required>
                            <option value="">-- Elige un torneo --</option>
                            <?php 
                            $stmt_t = $pdo->query("SELECT id, titulo FROM torneos ORDER BY fecha_inicio DESC");
                            while ($t = $stmt_t->fetch()) {
                                echo "<option value='{$t['id']}'>{$t['titulo']}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-grupo">
                        <label>Asunto del Correo *</label>
                        <input type="text" name="asunto" required placeholder="Ej: Cambio de hora en el torneo" maxlength="100">
                    </div>
                </div>

                <div class="form-grupo">
                    <label>Mensaje *</label>
                    <textarea name="mensaje" rows="4" required placeholder="Escribe aquí el mensaje que quieres enviar a todos los jugadores..."></textarea>
                </div>

                <button type="submit" class="btn-primario" style="background: linear-gradient(45deg, #e67e22, #d35400); width: 100%;">🚀 Enviar Correo a los Inscritos</button>
            </form>
        </div>
    </div>

    <div id="modal-editar" class="modal-overlay">
        <div class="modal">
            <h3 style="margin-top: 0; color: #f39c12;">✏️ Editar Torneo</h3>
            <form action="accion_admin.php" method="POST">
                <input type="hidden" name="accion" value="editar_torneo">
                <input type="hidden" name="torneo_id" id="edit-id">
                
                <div class="form-grupo">
                    <label>Título del Torneo</label>
                    <input type="text" name="titulo" id="edit-titulo" required>
                </div>
                <div class="form-grupo">
                    <label>Juego</label>
                    <select name="juego" id="edit-juego" required>
                        <option value="fortnite">Fortnite</option>
                        <option value="rocket">Rocket League</option>
                        <option value="cod">Call of Duty</option>
                        <option value="cs2">CS2</option>
                    </select>
                </div>
                <div class="form-grupo">
                    <label>Premio</label>
                    <input type="text" name="premio" id="edit-premio" required>
                </div>
                <div class="form-grupo">
                    <label>Máximo de Jugadores</label>
                    <input type="number" name="max_jugadores" id="edit-max" required min="2">
                </div>
                <div class="form-grupo">
                    <label>Fecha y Hora de Inicio</label>
                    <input type="datetime-local" name="fecha_inicio" id="edit-fecha" required>
                </div>
                <div class="form-grupo">
                    <label>Imagen del Torneo (URL)</label>
                    <input type="url" name="imagen" id="edit-imagen">
                </div>
                <div class="form-grupo">
                    <label>Descripción</label>
                    <textarea name="descripcion" id="edit-descripcion" rows="3"></textarea>
                </div>
                
                <div class="modal-buttons">
                    <button type="button" class="modal-btn cancel" onclick="cerrarModalEditar()">Cancelar</button>
                    <button type="submit" class="modal-btn confirm" style="background: #f39c12;">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>

</section>

<footer><div class="container"><p>&copy; Nexus 2026 - Admin</p></div></footer>

<script>
    function abrirModalEditar(torneo) {
        try {
            document.getElementById('edit-id').value = torneo.id;
            document.getElementById('edit-titulo').value = torneo.titulo;
            document.getElementById('edit-juego').value = torneo.juego;
            document.getElementById('edit-premio').value = torneo.premio;
            document.getElementById('edit-max').value = torneo.max_jugadores;
            document.getElementById('edit-imagen').value = torneo.imagen;
            document.getElementById('edit-descripcion').value = torneo.descripcion;
            
            let fechaFormateada = torneo.fecha_inicio.replace(' ', 'T');
            document.getElementById('edit-fecha').value = fechaFormateada;

            document.getElementById('modal-editar').style.display = 'flex';
            document.body.classList.add('modal-abierto');
        } catch (error) {
            console.error("Error al abrir el modal:", error);
            alert("Hubo un error al cargar los datos. Revisa la consola.");
        }
    }
    
    function cerrarModalEditar() {
        document.getElementById('modal-editar').style.display = 'none';
        document.body.classList.remove('modal-abierto');
    }

    document.getElementById('modal-editar').addEventListener('click', function(e) {
        if (e.target === this) {
            cerrarModalEditar();
        }
    });
</script>

</body>
</html>