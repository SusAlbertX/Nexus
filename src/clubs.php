<?php
$usuario_actual = "SusAlbertX"; 
$mi_club_id = 1;

$clubs = [
    [
        'id' => 1,
        'nombre' => 'Nexus Esports',
        'descripcion' => 'El club oficial de Nexus. Jugamos a todo, pero ganamos en CS2.',
        'lider' => 'SusAlbertX',
        'miembros' => 8,
        'max' => 10,
        'privado' => false,
        'icono' => '🛡️'
    ],
    [
        'id' => 2,
        'nombre' => 'Los Sin Nombre',
        'descripcion' => 'Somos malos pero nos divertimos. Buscamos gente para Fortnite.',
        'lider' => 'GamerPro99',
        'miembros' => 10,
        'max' => 10,
        'privado' => false,
        'icono' => '🎯'
    ],
    [
        'id' => 3,
        'nombre' => 'Clan Ultra Secreto',
        'descripcion' => 'Solo para jugadores con nivel Diamante o superior.',
        'lider' => 'ShadowMaster',
        'miembros' => 4,
        'max' => 10,
        'privado' => true,
        'icono' => '🕵️'
    ]
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="icon" href="icono.png" type="image/png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clubs - Nexus</title>
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
            
            <a href="#" style="color: #9b59b6; margin-left: 20px;">
                👤 <?= $usuario_actual ?>
            </a>
        </nav>
    </div>
</header>

<section class="hero" style="min-height: 30vh;">
    <h1>🛡️ Clubs Nexus</h1>
    <p>Únete a un equipo, compite junto a tus amigos y dominad la plataforma.</p>
</section>

<section class="container" style="padding: 20px;">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; flex-wrap: wrap; gap: 15px;">
        <h2 style="margin: 0;">Explorar Clubs</h2>
        <button class="btn-primario" onclick="alert('Aquí se abrirá el formulario de creación de club (Próximamente)')">➕ Crear Nuevo Club</button>
    </div>

    <div class="grid-torneos">
        <?php foreach ($clubs as $club): ?>
            <div class="torneo-card" style="border: 2px solid <?= $club['id'] == $mi_club_id ? '#9b59b6' : 'transparent' ?>;">
                
                <div style="background: linear-gradient(45deg, #1a1a1a, #2a2a2a); padding: 20px; text-align: center; border-bottom: 2px solid #333; position: relative;">
                    
                    <span style="position: absolute; top: 10px; right: 10px; font-size: 0.7em; padding: 4px 8px; border-radius: 10px; background: <?= $club['privado'] ? '#e74c3c' : '#2ecc71' ?>; color: white; font-weight: bold;">
                        <?= $club['privado'] ? '🔒 Privado' : '🌍 Público' ?>
                    </span>

                    <div style="font-size: 3em; margin-bottom: 5px;"><?= $club['icono'] ?></div>
                    <h3 class="torneo-titulo" style="margin: 0; color: white;"><?= $club['nombre'] ?></h3>
                    <p style="color: #aaa; font-size: 0.85em; margin-top: 5px;">Líder: <strong><?= $club['lider'] ?></strong></p>
                </div>

                <div class="torneo-contenido">
                    <p style="color: #ccc; font-size: 0.9em; min-height: 40px;"><?= $club['descripcion'] ?></p>
                    
                    <div class="torneo-stats" style="margin-top: 15px;">
                        <div class="stat-item">
                            <div class="stat-numero"><?= $club['miembros'] ?>/<?= $club['max'] ?></div>
                            <div class="stat-label">Miembros</div>
                        </div>
                        <div class="stat-item">
                            <?php if ($club['miembros'] >= $club['max']): ?>
                                <div class="stat-numero" style="color: #e74c3c; font-size: 0.9em;">Lleno</div>
                            <?php else: ?>
                                <div class="stat-numero" style="color: #2ecc71; font-size: 0.9em;"><?= $club['max'] - $club['miembros'] ?> huecos</div>
                            <?php endif; ?>
                            <div class="stat-label">Disponibilidad</div>
                        </div>
                    </div>

                    <div class="torneo-acciones" style="margin-top: 15px;">
                        <?php if ($club['id'] == $mi_club_id): ?>
                            <button style="background: #2a2a2a; color: #9b59b6; border: 1px solid #9b59b6; width: 100%; cursor: default;">
                                ✅ Eres miembro
                            </button>
                        <?php elseif ($club['miembros'] >= $club['max']): ?>
                            <button disabled style="width: 100%; background: #333; color: #777; border: none;">
                                ❌ Club Lleno
                            </button>
                        <?php elseif ($club['privado']): ?>
                            <button class="btn-outline" style="width: 100%;" onclick="alert('Para unirte a un club privado, el líder debe enviarte una invitación.')">
                                🔒 Solicitar Invitación
                            </button>
                        <?php else: ?>
                            <button class="btn-primario" style="width: 100%;" onclick="alert('¡Próximamente podrás unirte a este club!')">
                                ➕ Unirse al Club
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

</section>

<footer>
    <div class="container"><p>&copy; Nexus 2026 - Club de Videojuegos</p></div>
</footer>

</body>
</html>