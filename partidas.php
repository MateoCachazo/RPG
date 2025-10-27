<?php
session_start();

$archivo = 'partidas.json';

// Si no existe el archivo, lo creamos con 3 slots vacíos
if (!file_exists($archivo)) {
    $partidas = [
        ["id" => 1, "nombre" => null],
        ["id" => 2, "nombre" => null],
        ["id" => 3, "nombre" => null],
    ];
    file_put_contents($archivo, json_encode($partidas, JSON_PRETTY_PRINT));
}

// Leer partidas
$partidas = json_decode(file_get_contents($archivo), true);

// Crear partida nueva
if (isset($_POST['crear'])) {
    $id = (int)$_POST['id'];
    $nombre = trim($_POST['nombre']) ?: 'Jugador';
    $partidas[$id - 1]['nombre'] = $nombre;
    file_put_contents($archivo, json_encode($partidas, JSON_PRETTY_PRINT));
    $_SESSION['partida'] = $id;
    header("Location: selec.php");
    exit;
}

// Cargar partida existente
if (isset($_POST['cargar'])) {
    $id = (int)$_POST['id'];
    $_SESSION['partida'] = $id;
    header("Location: selec.php");
    exit;
}

// Borrar partida
if (isset($_POST['borrar'])) {
    $id = (int)$_POST['id'];
    $partidas[$id - 1]['nombre'] = null;
    file_put_contents($archivo, json_encode($partidas, JSON_PRETTY_PRINT));
    header("Location: partidas.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Seleccionar Partida — RPG-1</title>
<style>
  :root{
    --bg1: #0f1723;
    --bg2: #0b1220;
    --card: rgba(255,255,255,0.04);
    --accent: #6dd3ff;
    --muted: #aeb8c3;
    --glass: rgba(255,255,255,0.03);
    --radius: 14px;
    --gap: 20px;
    --max-width: 1100px;
  }
  *{box-sizing:border-box}
  body{
    margin:0;
    min-height:100vh;
    font-family: Inter, system-ui, -apple-system, "Segoe UI", Roboto, Arial;
    background: radial-gradient(circle at 10% 10%, #112233 0%, #081018 60%);
    color: #e6eef7;
    padding: 40px 18px;
    display:flex;
    justify-content:center;
  }

  .wrap{
    width:100%;
    max-width:var(--max-width);
  }

  header{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:16px;
    margin-bottom:24px;
  }
  h1{
    margin:0;
    font-size:20px;
    letter-spacing:0.02em;
  }
  .subtitle{ color:var(--muted); font-size:13px; }

  /* grid de slots */
  .slots-grid{
    display:grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap:var(--gap);
  }

  .slot{
    background: linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01));
    border-radius:var(--radius);
    padding:16px;
    box-shadow: 0 8px 30px rgba(0,0,0,0.6);
    border: 1px solid rgba(255,255,255,0.03);
    transition: transform .24s ease, box-shadow .24s ease;
    display:flex;
    flex-direction:column;
    align-items:stretch;
    gap:12px;
  }
  .slot:hover{
    transform: translateY(-6px);
    box-shadow: 0 18px 48px rgba(0,0,0,0.7);
  }

  .slot-head{
    display:flex;
    align-items:center;
    gap:12px;
  }
  .slot-id{
    width:56px;
    height:56px;
    border-radius:10px;
    background: linear-gradient(135deg, rgba(255,255,255,0.03), rgba(255,255,255,0.01));
    display:flex;
    align-items:center;
    justify-content:center;
    font-weight:800;
    font-size:18px;
    color:var(--accent);
    border:1px solid rgba(255,255,255,0.02);
  }
  .slot-info h3{ margin:0; font-size:16px; }
  .slot-info p{ margin:4px 0 0 0; color:var(--muted); font-size:13px; }

  /* contenido */
  .slot-body{ display:flex; gap:12px; align-items:center; justify-content:space-between; flex-wrap:wrap; }

  .controls{
    display:flex;
    gap:8px;
    align-items:center;
    margin-left:auto;
  }

  input[type="text"]{
    padding:10px 12px;
    border-radius:10px;
    border:1px solid rgba(255,255,255,0.05);
    background: rgba(255,255,255,0.02);
    color:inherit;
    font-size:13px;
    min-width:140px;
  }

  button{
    padding:10px 14px;
    border-radius:10px;
    border:none;
    cursor:pointer;
    font-weight:700;
    font-size:13px;
  }
  .btn-primary{
    background: linear-gradient(90deg,var(--accent), #8ef0c6);
    color:#071017;
  }
  .btn-ghost{
    background: transparent;
    color:var(--muted);
    border:1px solid rgba(255,255,255,0.04);
  }
  .btn-danger{
    background: #ff6b6b;
    color:#fff;
  }

  .empty-note{ color:var(--muted); font-size:13px; }

  /* footer / acciones globales */
  .actions-bar{
    margin-top:18px;
    display:flex;
    justify-content:flex-end;
    gap:12px;
  }

  @media (max-width:560px){
    header{ flex-direction:column; align-items:flex-start; gap:8px; }
    .slot-body{ flex-direction:column; align-items:stretch; gap:8px; }
    input[type="text"]{ width:100%; min-width:0; }
    .controls{ width:100%; justify-content:flex-end; }
  }
</style>
</head>
<body>
  <div class="wrap">
    <header>
      <div>
        <h1>Seleccionar Partida</h1>
        <div class="subtitle">Elige una ranura para crear, cargar o borrar tu partida</div>
      </div>
      <div class="subtitle">Slots disponibles: <?= count($partidas) ?></div>
    </header>

    <main class="slots-grid">
      <?php foreach ($partidas as $p): ?>
        <article class="slot" aria-labelledby="slot-<?= $p['id'] ?>">
          <div class="slot-head">
            <div class="slot-id">#<?= $p['id'] ?></div>
            <div class="slot-info">
              <h3 id="slot-<?= $p['id'] ?>">
                <?= $p['nombre'] ? htmlspecialchars($p['nombre']) : "Ranura vacía" ?>
              </h3>
              <p>
                <?= $p['nombre'] ? "Última vez jugado: —" : "Crea una nueva partida aquí" ?>
              </p>
            </div>
            <div class="controls" role="group" aria-label="Acciones slot <?= $p['id'] ?>">
              <?php if ($p['nombre']): ?>
                <form method="post" style="display:inline;">
                  <input type="hidden" name="id" value="<?= $p['id'] ?>">
                  <button type="submit" name="cargar" class="btn-primary" title="Cargar partida">Cargar</button>
                </form>
                <form method="post" style="display:inline;">
                  <input type="hidden" name="id" value="<?= $p['id'] ?>">
                  <button type="submit" name="borrar" class="btn-danger" title="Borrar partida" onclick="return confirm('¿Borrar la partida <?= htmlspecialchars($p['nombre']) ?>?')">Borrar</button>
                </form>
              <?php else: ?>
                <form method="post" style="display:flex; gap:8px; align-items:center;">
                  <input type="hidden" name="id" value="<?= $p['id'] ?>">
                  <input type="text" name="nombre" placeholder="Nombre del jugador" required>
                  <button type="submit" name="crear" class="btn-primary">Crear</button>
                </form>
              <?php endif; ?>
            </div>
          </div>

          <!-- información adicional / estética -->
          <div class="slot-body">
            <?php if ($p['nombre']): ?>
              <div class="empty-note">Partidas: 1 &nbsp; • &nbsp; Progreso: nivel 1</div>
              <div style="margin-left:auto; display:flex; gap:8px;">
                <a class="btn-ghost" href="estadisticas.php?id=<?= $p['id'] ?>">Ver estadísticas</a>
                <a class="btn-ghost" href="editar_partida.php?id=<?= $p['id'] ?>">Editar</a>
              </div>
            <?php else: ?>
              <div class="empty-note">Espacio libre — crea y guarda tu progreso aquí.</div>
            <?php endif; ?>
          </div>
        </article>
      <?php endforeach; ?>
    </main>

    <div class="actions-bar">
      <a class="btn-ghost" href="index.php">Volver al menú</a>
    </div>
  </div>
</body>
</html>