<?php



session_start();


ob_start();   
/*$_SESSION["controles"] = $_POST['controles'] ?? [
    "moverIzq" => "a",
    "moverDer" => "d",
    "ataque" => "p",
    "saltar" => "w",
    "ataqueEspecial" => "q",
    "pausa" => "Enter"
  ];*/
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_errors.log');
error_reporting(E_ALL);


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['controles'])) 
{
    $data = json_decode($_POST['controles'], true);

    // Verificamos que sea JSON válido y un array
    if (json_last_error() === JSON_ERROR_NONE && is_array($data))
    {
      $_SESSION["controles"] = $data;
    } 
}



if (!isset($_SESSION["controles"]) || !is_array($_SESSION["controles"]))
{
  $_SESSION["controles"] =  
  [
    "moverIzq" => "a",
    "moverDer" => "d",
    "ataque" => "p",
    "saltar" => "w",
    "ataqueEspecial" => "q",
    "pausa" => "Enter"
  ];
}


ob_end_clean();

 ?>
 <!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Opciones — RPG-1</title>
  <style>
    :root{
      --bg1: #0b0f14;
      --accent: #6dd3ff;
      --muted: #aeb8c3;
      --card: rgba(255,255,255,0.03);
      --radius: 12px;
    }
    *{box-sizing:border-box}
    html,body{height:100%; margin:0; font-family: Inter, system-ui, Arial; background: linear-gradient(180deg, rgba(34, 34, 34, 1), rgba(39, 38, 38, 0.63)); color:#eaf6ff}
    .wrap{max-width:1100px; margin:28px auto; padding:20px;}
    header{display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:18px;}
    h1{margin:0; font-size:20px; color:var(--accent)}
    .subtitle{color:var(--muted); font-size:13px}

    /* Menús */
    .panel { display:none; background: linear-gradient(180deg, rgba(255,255,255,0.02), rgba(0, 0, 0, 0.14)); padding:18px; border-radius:var(--radius); box-shadow:0 12px 40px rgba(0,0,0,0.6); }
    .panel.active { display:block; }

    .menu-buttons { display:flex; flex-direction:column; gap:12px; width:320px; }
    .menu-buttons button { padding:14px; border-radius:10px; border:none; cursor:pointer; background:transparent; color:#eaf6ff; text-align:left; font-weight:700; font-size:18px; box-shadow: inset 0 0 0 1px rgba(255,255,255,0.02); }
    .menu-buttons button:hover { transform:translateX(6px); color:var(--accent) }

    /* panel de controles */
    .controls-grid { display:grid; grid-template-columns: 1fr 160px 120px; gap:12px; align-items:center; margin-top:8px; }
    .controls-grid .label { color:var(--muted); }
    .key-box { background:var(--card); padding:10px 12px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-weight:700; }
    .change-btn { padding:8px 10px; border-radius:8px; border:1px solid rgba(255,255,255,0.04); background:transparent; color:#dfeffb; cursor:pointer; }
    .row-title{ font-weight:800; color:#fff; margin-bottom:6px; }

    .controls-actions { margin-top:14px; display:flex; gap:10px; }
    .btn-primary {background:#1f8cff; color:#051017; padding:10px 12px; border-radius:10px; border:none; cursor:pointer; font-weight:800; }
    .btn-ghost { background:#119ff9; color:var(--bg1); border:1px solid rgba(255,255,255,0.04); padding:10px 12px; border-radius:10px; cursor:pointer; font-weight:700; }

    /* overlay al reasignar */
    .key-overlay { position:fixed; inset:0; display:none; align-items:center; justify-content:center; z-index:9999; background:rgba(1,2,4,0.6); }
    .key-overlay.active { display:flex; }
    .key-overlay .box { background:#071017; padding:22px; border-radius:12px; text-align:center; color:#eaf6ff; box-shadow:0 12px 40px rgba(0,0,0,0.7); }
    .hint-small{ color:var(--muted); margin-top:8px; font-size:13px }

    @media (max-width:760px){
      .controls-grid{ grid-template-columns: 1fr 120px 120px; }
      .menu-buttons{ width:100%; }
    }
  </style>
</head>
<body>
  <div class="wrap">
    <header>
      <div>
        <h1>Opciones</h1>
        <div class="subtitle">Ajustes del juego</div>
      </div>
      <div>
        <button class="btn-ghost" onclick="location.href='index.php'">Volver</button>
      </div>
    </header>

    <div style="display:flex; gap:20px; align-items:flex-start;">
      <!-- menú lateral -->
      <nav class="menu-buttons" aria-label="Menú opciones">
        <button id="btnGeneral">General</button>
        <button id="btnControles">Controles</button>
        <button id="btnAudio">Audio</button>
      </nav>

      <!-- paneles -->
      <section id="panelGeneral" class="panel active" style="flex:1;">
        <div class="row-title">General</div>
        <p class="subtitle">Ajustes básicos del juego.</p>
        <div style="display:flex; gap:12px; margin-top:12px;">
          <label style="display:flex; gap:8px; align-items:center;">
            <input type="checkbox" id="fullscreenToggle"> Pantalla completa
          </label>
        </div>
      </section>

      <!-- panel de controles (filas estáticas en HTML, sin foreach) -->
      <section id="panelControles" class="panel" style="flex:2;">
        <div class="row-title">Controles</div>
        <p class="subtitle">Haz click en "Cambiar" — la reasignación se implementa en backend.</p>

        <div id="controlsList" class="controls-grid" role="list" aria-label="Lista de controles">
          <!-- filas estáticas -->

          <div class="label">Mover izquierda</div>
          <div class="key-box" data-action="moverIzq">A</div>
          <button onclick="boton('moverIzq');" class="change-btn" data-action="moverIzq">Cambiar</button>

          <div class="label">Mover derecha</div>
          <div class="key-box" data-action="moverDer">D</div>
          <button onclick="boton('moverDer');" class="change-btn" data-action="moverDer">Cambiar</button>

          <div class="label">Ataque</div>
          <div class="key-box" data-action="ataque">P</div>
          <button onclick="boton('ataque');" class="change-btn" data-action="ataque">Cambiar</button>

          <div class="label">Saltar</div>
          <div class="key-box" data-action="saltar">W</div>
          <button onclick="boton('saltar');" class="change-btn" data-action="saltar">Cambiar</button>


          <div class="label">Ataque Especial</div>
          <div class="key-box" data-action="ataqueEspecial">Q</div>
          <button onclick="boton('ataqueEspecial');" class="change-btn" data-action = "ataqueEspecial">Cambiar</button>

          <div class="label">Pausa</div>
          <div class="key-box" data-action="pausa">Enter</div>
          <button onclick="boton('pausa');" class="change-btn" data-action="pausa">Cambiar</button>
        </div>

       <!-- acciones -->
        <div class="controls-actions">
          <button class="btn-primary" id="restoreDefaults">Restaurar por defecto</button>
          <button class="btn-ghost" id="saveControls">Guardar</button>
        </div>
      </section>

      <!-- panel de audio -->
      <section id="panelAudio" class="panel" style="flex:1;">
        <div class="row-title">Audio</div>
        <p class="subtitle">Volúmenes y efectos.</p>
        <div style="display:flex; flex-direction:column; gap:12px; margin-top:12px;">
          <label>Volumen maestro <input id="volMaster" type="range" min="0" max="100" value="80"></label>
          <label>Volumen música <input id="volMusic" type="range" min="0" max="100" value="70"></label>
          <label>Volumen efectos <input id="volSfx" type="range" min="0" max="100" value="70"></label>
        </div>
      </section>
    </div>
  </div>

<script>
  // lógica mínima: estética intacta, reasignación delegada a backend
  const DEFAULT_CONTROLS = {
    moverIzq: 'A',
    moverDer: 'D',
    ataque: 'P',
    saltar: 'W',
    pausa: 'Enter',
    ataqueEspecial: 'Q'
  };

  // panel switching
  const btnGeneral = document.getElementById('btnGeneral');
  const btnControles = document.getElementById('btnControles');
  const btnAudio = document.getElementById('btnAudio');
  const panelGeneral = document.getElementById('panelGeneral');
  const panelControles = document.getElementById('panelControles');
  const panelAudio = document.getElementById('panelAudio');

  let controles = {
    "moverIzq":     false,
    "moverDer":       false,
    "ataque":        false,
    "saltar":        false,
    "ataqueEspecial":false,
    "pausa":         false
  } ;

    let controles_aux = {
    "moverIzq":     "<?php echo $_SESSION["controles"]["moverIzq"]; ?>",
    "moverDer":       "<?php echo $_SESSION["controles"]["moverDer"]; ?>",
    "ataque":        "<?php echo $_SESSION["controles"]["ataque"]; ?>",
    "saltar":        "<?php echo $_SESSION["controles"]["saltar"]; ?>",
    "ataqueEspecial":"<?php echo $_SESSION["controles"]["ataqueEspecial"]; ?>",
    "pausa":         "<?php echo $_SESSION["controles"]["pausa"]; ?>"
  };

  function showPanel(panel){
    panelGeneral.classList.remove('active');
    panelControles.classList.remove('active');
    panelAudio.classList.remove('active');
    panel.classList.add('active');
  }
  btnGeneral.addEventListener('click', ()=> showPanel(panelGeneral));
  btnControles.addEventListener('click', ()=> showPanel(panelControles));
  btnAudio.addEventListener('click', ()=> showPanel(panelAudio));

  function boton(a){
    controles[a] = true;
    Object.keys(controles).forEach ((key) => {
      if (key !== a){
        controles[key] = false;
      }
    });
  }
  document.addEventListener("keydown", (e) =>
    {
        Object.keys(controles).forEach ((key) => {
          if (controles[key] == true){
            controles_aux[key] = e.key.toLowerCase();
          }
        });
    });

  // bind a botones "Cambiar" (sin lógica de reasignación, solo aviso)
  document.querySelectorAll('#controlsList .change-btn').forEach(btn=>{
    btn.addEventListener('click', (e)=>{
      const action = btn.getAttribute('data-action') || 'acción';
      alert(`Reasignación no implementada aquí. Dejen esto a back para "${action}".`);
    });
  });

  // Restaurar valores por defecto (UI solamente)
  document.getElementById('restoreDefaults').addEventListener('click', ()=>{
    Object.keys(DEFAULT_CONTROLS).forEach(k=>{
      const el = document.querySelector(`#controlsList .key-box[data-action="${k}"]`);
      if(el) el.textContent = DEFAULT_CONTROLS[k];
    });
    alert('Interfaz restaurada a valores por defecto (sin persistir).');
  });

  // Guardar (solo aviso, backend debe implementar)
  document.getElementById('saveControls').addEventListener('click', ()=>
  {
    const form = document.createElement("form");
    form.method = "POST";
    form.action = '';

    const input = document.createElement("input");
    input.type = "hidden";
    input.name = "controles";
    input.value = JSON.stringify(controles_aux);

    form.appendChild(input);
    document.body.appendChild(form);
    form.submit();
  });

  // Fullscreen toggle
  const fullscreenToggle = document.getElementById('fullscreenToggle');
  if (fullscreenToggle) {
    fullscreenToggle.addEventListener('change', (e)=>{
      if (e.target.checked) {
        document.documentElement.requestFullscreen?.().catch(()=>{});
      } else {
        document.exitFullscreen?.().catch(()=>{});
      }
    });
  }

  // sliders de audio (persistencia local mínima)
  ['volMaster','volMusic','volSfx'].forEach(id=>{
    const el = document.getElementById(id);
    if (!el) return;
    el.addEventListener('input', (e)=>{
      localStorage.setItem('rpg_' + id, e.target.value);
    });
  });
  (function loadVolumes(){
    const master = localStorage.getItem('rpg_volMaster');
    const music = localStorage.getItem('rpg_volMusic');
    const sfx = localStorage.getItem('rpg_volSfx');
    if(master) document.getElementById('volMaster').value = master;
    if(music) document.getElementById('volMusic').value = music;
    if(sfx) document.getElementById('volSfx').value = sfx;
  })();

  // inicial: nada más que mostrar la UI (los botones ya están en HTML)
</script>

</body>
</html>