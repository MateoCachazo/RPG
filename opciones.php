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
    html,body{height:100%; margin:0; font-family: Inter, system-ui, Arial; background: radial-gradient(circle at 10% 10%, #0b1014, #071018); color:#eaf6ff}
    .wrap{max-width:1100px; margin:28px auto; padding:20px;}
    header{display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:18px;}
    h1{margin:0; font-size:20px; color:var(--accent)}
    .subtitle{color:var(--muted); font-size:13px}

    /* Menús */
    .panel { display:none; background: linear-gradient(180deg, rgba(255,255,255,0.02), rgba(0,0,0,0.14)); padding:18px; border-radius:var(--radius); box-shadow:0 12px 40px rgba(0,0,0,0.6); }
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
    .hint-small{ color:var(--muted); margin-top:8px; font-size:13px; }

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
        <button class="btn-ghost" id="btnBackAll" onclick="location.href='index.php'">Volver</button>
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


      <!-- panel de controles -->
      <section id="panelControles" class="panel" style="flex:2;">
        <div class="row-title">Controles</div>
        <p class="subtitle">Haz click en "Cambiar" y presiona la tecla que quieras asignar.</p>

        <div id="controlsList" class="controls-grid" role="list">
          <!-- filas pobladas por JS -->
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
  // lógica reducida: mantener solo la estética del panel "Controles"
  const DEFAULT_CONTROLS = {
    moverArriba: 'W',
    moverAbajo: 'S',
    moverIzq: 'A',
    moverDer: 'D',
    ataque: 'J',
    saltar: 'K',
    interactuar: 'E',
    inventario: 'I',
    pausa: 'Escape'
  };

  // elementos UI
  const btnGeneral = document.getElementById('btnGeneral');
  const btnControles = document.getElementById('btnControles');
  const panelGeneral = document.getElementById('panelGeneral');
  const panelControles = document.getElementById('panelControles');
  const panelAudio = document.getElementById('panelAudio');
  const controlsList = document.getElementById('controlsList');

  // cambiar paneles
  function showPanel(panel){
    panelGeneral.classList.remove('active');
    panelControles.classList.remove('active');
    panelAudio.classList.remove('active');
    panel.classList.add('active');
  }
  btnGeneral.addEventListener('click', ()=> showPanel(panelGeneral));
  btnControles.addEventListener('click', ()=> showPanel(panelControles));
  btnAudio.addEventListener('click', ()=> showPanel(panelAudio));

  // Columna de controles

  function renderControlsUI(values = DEFAULT_CONTROLS){ // valores por defecto
    controlsList.innerHTML = ''; // limpiar
    Object.keys(values).forEach(action => { // por cada acción
      const label = document.createElement('div'); // etiqueta
      label.className = 'label'; // clase
      label.textContent = prettifyAction(action); // texto legible

      const keyBox = document.createElement('div'); // caja de tecla
      keyBox.className = 'key-box'; // clase
      keyBox.textContent = values[action]; // tecla asignada

      const changeBtn = document.createElement('button'); // botón cambiar
      changeBtn.className = 'change-btn'; // clase
      changeBtn.textContent = 'Cambiar'; // texto
      // botón sin funcionalidad: solo muestra estética


      controlsList.appendChild(label);
      controlsList.appendChild(keyBox);
      controlsList.appendChild(changeBtn);
    });
  }

  function prettifyAction(k){
    const map = {
      moverArriba: 'Mover arriba',
      moverAbajo: 'Mover abajo',
      moverIzq: 'Mover izquierda',
      moverDer: 'Mover derecha',
      ataque: 'Ataque',
      saltar: 'Saltar',
      interactuar: 'Interactuar',
      inventario: 'Abrir inventario',
      pausa: 'Pausa'
    };
    return map[k] || k;
  }

  // restaurar visual a los valores por defecto (solo UI)
  document.getElementById('restoreDefaults').addEventListener('click', ()=>{
    alert('Haganlo ustedes, no soy back :).');

  });

  // guardar: notificar que no está implementado (no toca controles)
  document.getElementById('saveControls').addEventListener('click', ()=>{
    alert('Haganlo ustedes, no soy back :).');
  });

  // Fullscreen 
  document.getElementById('fullscreenToggle').addEventListener('change', (e)=>{
    if (e.target.checked) {
      document.documentElement.requestFullscreen?.().catch(()=>{});
    } else {
      document.exitFullscreen?.().catch(()=>{});    
    }
  });

// Lista de controles
  renderControlsUI();

</script>

</body>
</html>