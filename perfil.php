<?php
// ...existing code...
session_start();

$archivoUsuarios = 'USUARIOS.json';

// Tomamos valores desde la sesión (puede ser null)
$username = isset($_SESSION["username"]) ? $_SESSION["username"] : null;
$email    = isset($_SESSION["email"]) ? $_SESSION["email"] : null;
$nivel    = isset($_SESSION["nivel"]) ? intval($_SESSION["nivel"]) : 1;
$xp       = isset($_SESSION["xp"]) ? intval($_SESSION["xp"]) : 0;
$partidas = isset($_SESSION["partidas"]) ? intval($_SESSION["partidas"]) : 0;
$victorias= isset($_SESSION["victorias"]) ? intval($_SESSION["victorias"]) : 0;
$derrotas = isset($_SESSION["derrotas"]) ? intval($_SESSION["derrotas"]) : 0;

// Si hay un usuario en sesión, intentamos rellenar datos desde USUARIOS.json
if (!empty($username) && file_exists($archivoUsuarios)) {
    $contenido = file_get_contents($archivoUsuarios);
    $usuarios = json_decode($contenido, true);
    if (is_array($usuarios)) {
        foreach ($usuarios as $u) {
            if (isset($u['username']) && $u['username'] === $username) {
                // Sobrescribe email si no viene por sesión o está vacío
                if (empty($email) && !empty($u['email'])) {
                    $email = $u['email'];
                }
                // Si más adelante añades campos al JSON (nivel, xp, etc.), los puedes cargar aquí:
                // if (isset($u['nivel'])) { $nivel = intval($u['nivel']); }
                // if (isset($u['xp'])) { $xp = intval($u['xp']); }
                break;
            }
        }
    }
}

// Valores por defecto si no hay usuario válido
if (empty($username)) $username = "Invitado";
if (empty($email)) $email = "sin@correo.local";
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Perfil — RPG-1</title>
  <style>
    :root{
      --bg:#0f1113;
      --card: rgba(255,255,255,0.04);
      --accent:#5cc0ff;
      --muted:#bfc8d1;
      --glass: rgba(255,255,255,0.03);
      --radius:12px;
      --gap:18px;
    }
    *{box-sizing:border-box}
    body{
      margin:0;
      min-height:100vh;
      font-family: Inter, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
      color:#e9eef5;
      padding:32px;
    }

    .container{
      max-width:1100px;
      margin:0 auto;
      display:flex;
      gap:var(--gap);
      align-items:flex-start;
      padding:18px;
    }

    /* panel izquierdo: avatar + basic */
    .panel-left{
      width:320px;
      background: linear-gradient(180deg, rgba(0, 0, 0, 1), rgba(39, 38, 38, 0.63));
      border-radius:var(--radius);
      padding:20px;
      box-shadow: 0 8px 30px rgba(0,0,0,0.6);
      display:flex;
      flex-direction:column;
      align-items:center;
      gap:12px;
      position:relative;
    }
    .avatar{
      width:160px;
      height:160px;
      border-radius:16px;
      overflow:hidden;
      border:3px solid rgba(255,255,255,0.04);
      box-shadow: 0 8px 28px rgba(0,0,0,0.6);
      background:linear-gradient(180deg,#1a232b,#0f1417);
      display:flex;
      align-items:center;
      justify-content:center;
    }
    .avatar img{ width:100%; height:100%; object-fit:cover; display:block; }

    .name{
      font-size:20px;
      font-weight:700;
      letter-spacing:0.02em;
    }
    .tag{ color:var(--muted); font-size:13px; margin-top:-4px; }

    .level-badge{
      margin-top:8px;
      background: linear-gradient(90deg,var(--accent),#7ee0b8);
      color:#041017;
      padding:6px 12px;
      border-radius:999px;
      font-weight:700;
      font-size:13px;
      box-shadow: 0 6px 18px rgba(92,192,255,0.12);
    }

    /* panel derecho: filas/tarjetas */
    .panel-right{
      flex:1;
      display:flex;
      flex-direction:column;
      background:linear-gradient(180deg, rgba(0, 0, 0, 1), rgba(39, 38, 38, 0.63));
      gap:var(--gap);
    }

    .card{
      background: var(--card);
      border-radius:12px;
      padding:18px;
      display:flex;
      gap:16px;
      align-items:center;
      box-shadow: 0 8px 30px rgba(0,0,0,0.55);
      backdrop-filter: blur(6px);
    }

    .card h3{
      margin:0 0 6px 0;
      font-size:16px;
    }
    .card .meta{ color:var(--muted); font-size:13px; }

    /* layout dentro de tarjetas */
    .card-row{ display:flex; gap:16px; width:100%; flex-wrap:wrap; align-items:center; }
    .info-field{ flex:1; min-width:180px; }
    .info-field p{ margin:0; color:var(--muted); font-size:14px; }
    .info-field strong{ display:block; color:#fff; font-size:15px; margin-bottom:6px; }

    /* acciones */
    .actions{ display:flex; gap:10px; flex-wrap:wrap; }
    .btn{
      background:var(--accent);
      color:#041017;
      border:none;
      padding:10px 14px;
      border-radius:10px;
      font-weight:700;
      cursor:pointer;
    }
    .btn.ghost{
      background:transparent;
      color:var(--muted);
      border:1px solid rgba(255,255,255,0.04);
      font-weight:600;
    }

    /* estadisticas pequeñas */
    .stats{
      display:flex;
      gap:12px;
      align-items:center;
    }
    .stat{
      background:var(--glass);
      padding:10px 12px;
      border-radius:10px;
      text-align:center;
      min-width:90px;
    }
    .stat .n{ font-size:18px; font-weight:800; color:#fff; }
    .stat .label{ font-size:12px; color:var(--muted); margin-top:4px; }

    /* responsive */
    @media (max-width:880px){
      .container{ flex-direction:column; padding:18px; }
      .panel-left{ width:100%; flex-direction:row; align-items:center; padding:12px; }
      .avatar{ width:96px; height:96px; border-radius:10px; }
      .name{ font-size:18px }
      .panel-right{ width:100% }
    }
    .video-background-container {
    position: fixed; /* Fija el contenedor en la pantalla */
  width: 100%;
  height: 100%; /* Ocupa el 100% de la altura de la ventana (viewport) */
  overflow: hidden; /* Oculta cualquier parte del video que se desborde */
}

/* Estilos para el video de fondo */
#videoFondo {
  position: fixed; /* Mantiene el video fijo en su lugar y lo envia detras de todo*/
  right: 0;
  bottom: 0;
  min-width: 100%; /* Asegura que el video cubra todo el ancho */
  min-height: 100%; /* Asegura que el video cubra toda la altura */
  z-index: -1; /* Coloca el video detrás del resto del contenido */
  object-fit: cover; /* Recorta el video para que cubra todo el contenedor, manteniendo su relación de aspecto */
} 
  </style>
</head>
<body>
  <audio id="audioFondo" src="rpg-titulo.wav" autoplay loop></audio>
  <video autoplay muted loop playsinline poster="imagen_carga.jpg" id="videoFondo">
    <source src="imagenes/fondo juego.mp4" type="video/mp4">
    <!-- Puedes agregar más etiquetas source para distintos formatos -->
    Tu navegador no soporta la etiqueta de video.
  </video>
  <div class="container">
    <aside class="panel-left" aria-label="Perfil">
      <div class="avatar" title="Imagen de perfil">
        <img src="imagenes/perfil2.png" alt="Avatar de <?php echo htmlspecialchars($username, ENT_QUOTES); ?>">
      </div>
      <div style="text-align:center;">
        <div class="name"><?php echo htmlspecialchars($username, ENT_QUOTES); ?></div>
        <div class="tag">@<?php echo strtolower(htmlspecialchars($username, ENT_QUOTES)); ?></div>
        <div class="level-badge">Nivel <?php echo $nivel; ?> • <?php echo $xp; ?> XP</div>
      </div>
    </aside>

    <main class="panel-right">
      <!-- Cuenta / contacto -->
      <section class="card" aria-labelledby="cuenta-title">
        <div style="flex:1">
          <h3 id="cuenta-title">Cuenta</h3>
          <div class="meta">Información de la cuenta y contacto</div>

          <div class="card-row" style="margin-top:12px;">
            <div class="info-field">
              <strong>Correo</strong>
              <p><?php echo htmlspecialchars($email, ENT_QUOTES); ?></p>
            </div>
            <div class="info-field">
              <strong>Usuario</strong>
              <p><?php echo htmlspecialchars($username, ENT_QUOTES); ?></p>
            </div>

            <div style="display:flex; align-items:center; gap:8px;">
              <div class="actions">
                <a class="btn ghost" href="editar_perfil.php">Editar perfil</a>
                <a class="btn ghost" href="cambiar_contrasena.php">Cambiar contraseña</a>
                <a class="btn" href="logout.php">Cerrar sesión</a>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Estadísticas -->
      <section class="card" aria-labelledby="stats-title">
        <div style="flex:1">
          <h3 id="stats-title">Estadísticas del juego</h3>
          <div class="meta">Resumen rápido</div>

          <div class="card-row" style="margin-top:12px; align-items:center;">
            <div class="stats" role="list">
              <div class="stat" role="listitem">
                <div class="n"><?php echo $nivel; ?></div>
                <div class="label">Nivel</div>
              </div>
              <div class="stat" role="listitem">
                <div class="n"><?php echo $xp; ?></div>
                <div class="label">Experiencia</div>
              </div>
              <div class="stat" role="listitem">
                <div class="n"><?php echo $partidas; ?></div>
                <div class="label">Partidas</div>
              </div>
              <div class="stat" role="listitem">
                <div class="n"><?php echo $victorias; ?></div>
                <div class="label">Victorias</div>
              </div>
              <div class="stat" role="listitem">
                <div class="n"><?php echo $derrotas; ?></div>
                <div class="label">Derrotas</div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </main>
  </div>
</body>
</html>