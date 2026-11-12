<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Servidores - Nexus</title>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<header>
    <div class="container">
        <p class="logo" onclick="location.href='index.php'">NEXUS</p>
        <nav>
            <a href="index.php">Inicio</a>
       
<section class="hero" style="min-height:     <a href="torneo.php">Torneos</a>
            <a href="servidores.php" class="activo">Servidores</a>
            <a href="soporte.php">Soporte</a>
        </nav>
    </div>
</header>
 30vh;">
    <h1>🌐 Servidores Nexus</h1>
    <p>Únete a nuestra comunidad y juega con nosotros. ¡Diviértete!</p>
</section>

<section class="container" style="padding: 20px;">
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 25px; margin-top: 20px;">
        
        <div class="panel" style="text-align: center; border-top: 4px solid #9b59b6; display: flex; flex-direction: column;">
            <div style="font-size: 4em; margin-bottom: 10px;">🎙️</div>
            <h2 style="margin-top:0;">Mumble Voice</h2>
            <p style="color: #aaa; flex-grow: 1;">El mejor voice chat para coordinarte con tu equipo en los torneos. Baja latencia y calidad de audio.</p>
            
            <div style="background: #121212; padding: 15px; border-radius: 10px; margin: 20px 0; display: flex; justify-content: space-between; align-items: center; border: 1px solid #333;">
                <span style="font-weight: bold; color: white;">IP:</span>
                <code style="font-size: 1.2em; color: #9b59b6; font-weight: bold; letter-spacing: 1px;" id="ip-mumble">192.168.1.10</code>
            </div>

            <button class="btn-primario" style="width: 100%;" onclick="copiarIP('ip-mumble', this)">Copiar IP</button>
        </div>

        <div class="panel" style="text-align: center; border-top: 4px solid #2ecc71; display: flex; flex-direction: column;">
            <div style="font-size: 4em; margin-bottom: 10px;">🧱</div>
            <h2 style="margin-top:0;">Minecraft Java</h2>
            <p style="color: #aaa; flex-grow: 1;">Nuestro mundo survival. Construye, explora y crea tu propia base con otros miembros del club.</p>
            
            <div style="background: #121212; padding: 15px; border-radius: 10px; margin: 20px 0; display: flex; justify-content: space-between; align-items: center; border: 1px solid #333;">
                <span style="font-weight: bold; color: white;">IP:</span>
                <code style="font-size: 1.2em; color: #2ecc71; font-weight: bold; letter-spacing: 1px;" id="ip-minecraft">192.168.1.10</code>
            </div>
            <div style="background: #121212; padding: 15px; border-radius: 10px; margin: 20px 0; display: flex; justify-content: space-between; align-items: center; border: 1px solid #333;">
                <span style="font-weight: bold; color: white;">Versión:</span>
                <code style="font-size: 1.2em; color: #2ecc71; font-weight: bold;">1.20.4</code>
            </div>

            <button class="btn-primario" style="width: 100%; background: linear-gradient(45deg, #27ae60, #2ecc71);" onclick="copiarIP('ip-minecraft', this)">Copiar IP</button>
        </div>

    </div>

    <div class="alerta error" style="margin-top: 40px;">
        ⚠️ <strong>Aviso importante:</strong> La IP <code>192.168.1.10</code> es una dirección de red local. Si los jugadores no están conectados a tu misma red Wi-Fi, no podrán acceder a los servidores. Para que sean públicos, necesitarás abrir los puertos en tu router.
    </div>

</section>

<footer>
    <div class="container"><p>&copy; Nexus 2026 - Club de Videojuegos</p></div>
</footer>

<script>
    function copiarIP(idElemento, boton) {
        const texto = document.getElementById(idElemento).innerText;
        
        const tempInput = document.createElement("input");
        tempInput.value = texto;
        document.body.appendChild(tempInput);
        
        tempInput.select();
        tempInput.setSelectionRange(0, 99999); // Para móviles
        
        try {
            const exitoso = document.execCommand("copy");
            if (exitoso) {
                const textoOriginal = boton.innerText;
                boton.innerText = '✅ ¡Copiado!';
                boton.style.background = '#2ecc71'; 
                
                setTimeout(() => {
                    boton.innerText = textoOriginal;
                    boton.style.background = ''; 
                }, 2000);
            } else {
                alert("No se pudo copiar automáticamente. La IP es: " + texto);
            }
        } catch (err) {
            alert("Tu navegador bloqueó la copia. La IP es: " + texto);
        }
        
        document.body.removeChild(tempInput);
    }
</script>

</body>
</html>