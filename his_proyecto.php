<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Historia del proyecto — RPG-1</title>
  <style>
    :root{
      --bg-0: #060812;
      --accent: #7fd4ff;
      --muted: #bcdff0;
      --card: rgba(255,255,255,0.03);
      --glass: rgba(255,255,255,0.02);
      --radius: 14px;
      --maxw: 1100px;
    }
    *{box-sizing:border-box}
    html,body{height:100%; margin:0; font-family:Inter,system-ui,Arial; background:
      linear-gradient(180deg,#020406 0%, #313436ff 60%); color:#e6eef7; -webkit-font-smoothing:antialiased;}
    .wrap{
      min-height:100vh;
      display:flex;
      align-items:center;
      justify-content:center;
      padding:40px 20px;
    }

    .panel{
      width:min(var(--maxw),96%);
      background: linear-gradient(180deg, rgba(126, 42, 42, 0.02), rgba(255, 255, 255, 0.18));
      border-radius:var(--radius);
      padding:26px;
      box-shadow: 0 18px 60px rgba(0,0,0,0.6);
      position:relative;
      overflow:hidden;
    }

    .header{
      display:flex;
      flex-direction:column;
      gap:6px;
      margin-bottom:16px;
      text-align:center;
    }
    .title{
      color:var(--accent);
      font-weight:800;
      font-size:1.9rem;
      margin:0;
      letter-spacing:0.02em;
    }
    .subtitle{
      color:var(--muted);
      font-size:0.98rem;
      margin:0;
      opacity:0.95;
    }

    /* area central: escenario */
    .stage{
      display:flex;
      gap:22px;
      align-items:stretch;
      justify-content:center;
      width:100%;
    }

    .scene-card{
      display:flex;
      gap:20px;
      align-items:center;
      padding:20px;
      border-radius:12px;
      background: linear-gradient(90deg, rgba(43, 41, 41, 0.01), rgba(0,0,0,0.12));
      width:100%;
      min-height:320px;
      box-shadow: 0 12px 40px rgba(0,0,0,0.5);
      transition: transform .28s ease, box-shadow .28s ease;
      cursor: pointer;
    }
    .scene-card:hover { transform: translateY(-6px); box-shadow:0 28px 80px rgba(0,0,0,0.6); }

    .scene-media{
      width:360px;
      min-width:180px;
      height:220px;
      border-radius:10px;
      overflow:hidden;
      background:linear-gradient(180deg,#071014,#0b1318);
      display:flex;
      align-items:center;
      justify-content:center;
      box-shadow: inset 0 1px 0 rgba(255,255,255,0.02);
      flex-shrink:0;
    }
    .scene-media img{ width:100%; height:100%; object-fit:cover; display:block; }

    .scene-body{ flex:1; display:flex; flex-direction:column; gap:12px; }
    .scene-title{ font-size:1.2rem; color:var(--accent); margin:0; font-weight:800; }
    .scene-text{ color:#dfeef8; line-height:1.65; font-size:1rem; white-space:pre-wrap; }

    /* navigation UI */
    .nav{
      position:absolute;
      left:0; right:0; top:50%;
      transform:translateY(-50%);
      display:flex;
      justify-content:space-between;
      pointer-events:none;
    }
    .nav button{
      pointer-events:auto;
      width:56px; height:56px;
      margin:0 18px;
      border-radius:999px;
      background:rgba(0,0,0,0.35);
      border:1px solid rgba(255,255,255,0.04);
      color:#fff; font-weight:800;
      cursor:pointer;
      box-shadow:0 8px 24px rgba(0,0,0,0.5);
    }
    .nav button:active{ transform:scale(.97); }

    /* pager dots bottom */
    .pager{
      display:flex;
      gap:8px;
      justify-content:center;
      margin-top:14px;
    }
    .dot{
      width:10px; height:10px; border-radius:999px;
      background:rgba(255,255,255,0.06);
      border:1px solid rgba(255,255,255,0.03);
    }
    .dot.active{ background:linear-gradient(90deg,var(--accent), #8ef0c6); box-shadow:0 6px 18px rgba(127,212,255,0.08); }

    /* pequeños detalles responsive */
    @media (max-width:980px){
      .scene-media{ width:220px; height:160px; }
    }
    @media (max-width:720px){
      .scene-card{ flex-direction:column; align-items:center; text-align:center; min-height: auto; padding:18px; }
      .scene-media{ width:80%; height:220px; }
      .scene-body{ width:100%; }
      .nav button{ width:46px; height:46px; }
    }
  </style>
</head>
<body>
  <div class="wrap">
    <section class="panel" aria-labelledby="hist-title">
      <header class="header">
        <h1 id="hist-title" class="title">Historia del proyecto</h1>
        <div class="subtitle">Origen, objetivo y visión del RPG-1 — haz click o usa → ← para avanzar</div>
      </header>

      <div class="stage" id="stage">
        <article class="scene-card" id="sceneCard" role="group" aria-live="polite" tabindex="0">
          <div class="scene-media" id="sceneMedia" aria-hidden="false">
            <!-- imagen opcional -->
            <img id="sceneImg" src="imagenes/escena1.png" alt="Escena 1" onerror="this.style.display='none'">
          </div>

          <div class="scene-body">
            <h2 id="sceneTitle" class="scene-title">Prólogo</h2>
            <div id="sceneText" class="scene-text">Cargando…</div>
          </div>
        </article>
      </div>

      <div class="nav" aria-hidden="false">
        <button id="prevBtn" aria-label="Anterior">&larr;</button>
        <button id="nextBtn" aria-label="Siguiente">&rarr;</button>
      </div>

      <div class="pager" id="pager" role="tablist" aria-label="Páginas de la historia"></div>
    </section>
  </div>

  <script>
    (function(){
      // escenarios: cambialos o agrega imágenes en carpeta imagenes/
      const scenes = [
        {
          title: "Ejemplo",
          text: `.`,
          img: "imagenes/escena1.png"
        },
        {
          title: "Ejemplo",
          text: `.`,
          img: "imagenes/escena2.png"
        },
        {
          title: "Ejemplo",
          text: `.`,
          img: "imagenes/escena3.png"
        },
        {
          title: "Ejemplo",
          text: `.`,
          img: "imagenes/escena4.png"
        }
      ];

      // elementos
      const sceneCard = document.getElementById('sceneCard');
      const sceneTitle = document.getElementById('sceneTitle');
      const sceneText  = document.getElementById('sceneText');
      const sceneImg   = document.getElementById('sceneImg');
      const prevBtn    = document.getElementById('prevBtn');
      const nextBtn    = document.getElementById('nextBtn');
      const pagerEl    = document.getElementById('pager');

      let idx = 0;

      // crear dots
      scenes.forEach((s,i)=>{
        const d = document.createElement('button');
        d.className = 'dot' + (i===0 ? ' active' : '');
        d.setAttribute('aria-label', `Página ${i+1}`);
        d.addEventListener('click', ()=> show(i));
        pagerEl.appendChild(d);
      });

      function updatePager(){
        const dots = pagerEl.children;
        for(let i=0;i<dots.length;i++){
          dots[i].classList.toggle('active', i===idx);
        }
      }

      function show(i){
        if(i < 0) i = 0;
        if(i >= scenes.length) i = scenes.length -1;
        idx = i;
        const s = scenes[idx];
        sceneTitle.textContent = s.title;
        sceneText.textContent = s.text;
        if(s.img){
          sceneImg.src = s.img;
          sceneImg.style.display = '';
        } else {
          sceneImg.style.display = 'none';
        }
        updatePager();
      }

      // avanzar / retroceder
      prevBtn.addEventListener('click', ()=> show(idx-1));
      nextBtn.addEventListener('click', ()=> show(idx+1));

      // click en la tarjeta avanza
      sceneCard.addEventListener('click', (e)=>{
        // si clic en botón nav, no avanzar (propagation handled)
        show(Math.min(idx+1, scenes.length-1));
      });

      // keyboard
      document.addEventListener('keydown', (e)=>{
        if(e.key === 'ArrowRight') show(idx+1);
        if(e.key === 'ArrowLeft') show(idx-1);
      });

      // inicializar centrado (ya el CSS lo centra) y mostrar primer
      show(0);

      // accesibilidad: permitir foco en card y Enter para avanzar
      sceneCard.addEventListener('keydown', (e)=>{
        if(e.key === 'Enter' || e.key === ' ') { e.preventDefault(); show(idx+1); }
      });
    })();
  </script>
</body>
</html>

