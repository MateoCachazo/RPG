<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Selector de Clases 3D</title>
<style>
body {
  background: radial-gradient(ellipse at center, #2f4b7bff 0%, #1e2b4aff 100%);
  color: white;
  font-family: sans-serif;
  display: flex;
  align-items: center;
  justify-content: center;
  height: 100vh;
  overflow: hidden;
  image-rendering: pixelated;
}

.carrusel-container {
  position: relative;
  perspective: 1000px;
  width: 400px;
  height: 300px;
}

.carrusel {
  width: 100%;
  height: 100%;
  position: absolute;
  transform-style: preserve-3d;
  transition: transform 1s;
}

.clase {
  position: absolute;
  width: 200px;
  height: 250px;
  left: 27%;
  top: 20%;
  transform: translate(-50%, -50%);
  border-radius: 15px;
  display: flex;
  align-items: center;
  justify-content: center;
  backface-visibility: hidden;
  font-size: 22px;
  font-weight: bold;
  transition: all 0.5s;
  cursor: pointer;
}

/* Borde cuando está seleccionada */
.clase.seleccionada {
  box-shadow: 0 0 25px 5px #00ff99;
  
}

/* Animación al elegir definitivamente */
.clase.elegida {
  animation: elegido 0.7s ease forwards;
}

@keyframes elegido {
  0% { transform: scale(1.6); box-shadow: 0 0 25px 5px #00ff99; }
  100% { transform: scale(2.5) rotateY(360deg); opacity: 0; }
}

.clase-estadisticas {
  background-color: rgba(0, 0, 0, 0.95);
  position: absolute;
  bottom: 0px;
  border-radius: 0px 0px 15px 15px;
  width: 200px;
  height: 120px;
  color: white;
  font-size: 14px;
  text-align: left;
  padding: 8px;
  box-sizing: border-box;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.text-group {
  margin-top: 15px;
  font-size: 14px;
  margin-left: 10px;
}

.clase img {
  width: auto;
  height: auto;
  margin-bottom: 70px;
}

.clase:hover {
  transform: scale(1.2);
  transition: transform 0.3s;
  margin-top: 2px;
}

.clase img:hover {
  transform: scale(1.3);
  transition: transform 0.3s;
  margin-top: -20px;
}

.flecha {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  font-size: 40px;
  color: white;
  background: rgba(255, 255, 255, 0);
  border: none;
  cursor: pointer;
  z-index: 10;
  padding: 10px;
  border-radius: 50%;
}

.clase.activo {
  z-index: 20;
}

#izquierda { left: -300px; }
#derecha { right: -300px; }

#videoFondo {
  position: fixed;
  right: 0;
  bottom: 0;
  min-width: 100%;
  min-height: 100%;
  z-index: -1;
  object-fit: cover;
}

.icon-corazon { background-image: url('imagenes/corazon.png'); }
.icon-escudo   { background-image: url('imagenes/escudo\ defensa.png'); }
.icon-rayo     { background-image: url('imagenes/rayo.png'); }
.icon-espada   { background-image: url('imagenes/espada.png'); }

.stat-line {
  display: flex;
  align-items: center;
  gap: 4px;
  margin: 3px 0;
}

/* icono */
.stat-icon {
  width: 12px;
  height: 12px;
  display: inline-block;
  vertical-align: middle;
  background-size: contain;
  background-repeat: no-repeat;
  background-position: center;
  
}

.stat-value-escudo{color: blue; font-size: 10px;}
.stat-value-vida { color: red; font-size: 10px;}
.stat-value-velocidad { color: orange; font-size: 10px;}
.stat-value-ataque { color: deepskyblue; font-size: 10px;}
/* texto del label */
.stat-label {
  color: #ddd;
  font-size: 10px;
}

</style>
</head>
<body>
<video autoplay muted loop playsinline poster="imagen_carga.jpg" id="videoFondo">
  <source src="imagenes/fondo juego.mp4" type="video/mp4">
  Tu navegador no soporta la etiqueta de video.
</video>

<!-- Formulario oculto para enviar la clase seleccionada -->
<form id="formSeleccion" action="juego.php" method="POST" style="display:none;">
  <input type="hidden" name="personaje" id="inputPersonaje">
</form>

<div class="carrusel-container">
  <button id="izquierda" class="flecha">⟨</button>
 <div class="carrusel" id="carrusel">
  <!-- Tarjetas -->
  <div class="clase" data-personaje="Guerrero" style="--i:0;">
    <img src="imagenes/Guerrero tarjeta.png" alt="Guerrero">
    <div class="clase-estadisticas">
      <div class="text-group">
        <strong>Guerrero</strong>
      </div>
      <div class="stat-line"><span class="stat-icon icon-corazon" ></span><span class="stat-value-vida">20</span><span class="stat-label">Vida</span></div>
      <div class="stat-line"><span class="stat-icon icon-escudo" ></span><span class="stat-value-escudo">15</span><span class="stat-label">Defensa</span></div>
      <div class="stat-line"><span class="stat-icon icon-rayo" ></span><span class="stat-value-velocidad">4</span><span class="stat-label">Velocidad</span></div>
      <div class="stat-line"><span class="stat-icon icon-espada" ></span><span class="stat-value-ataque">12</span><span class="stat-label">Ataque</span></div>
    </div>
  </div>

  <div class="clase" data-personaje="Mago" style="--i:1;">
    <img src="imagenes/Mago tarjeta.png" alt="Mago">
    <div class="clase-estadisticas">
      <div class="text-group"><strong>Mago</strong></div>
      <div class="stat-line"><span class="stat-icon icon-corazon" ></span><span class="stat-value-vida">15</span><span class="stat-label">Vida</span></div>
      <div class="stat-line"><span class="stat-icon icon-escudo" ></span><span class="stat-value-escudo">5</span><span class="stat-label">Defensa</span></div>
      <div class="stat-line"><span class="stat-icon icon-rayo" ></span><span class="stat-value-velocidad">2</span><span class="stat-label">Velocidad</span></div>
      <div class="stat-line"><span class="stat-icon icon-espada" ></span><span class="stat-value-ataque">20</span><span class="stat-label">Ataque</span></div>
    </div>
  </div>

  <div class="clase" data-personaje="Arquero" style="--i:2;">
    <img src="imagenes/Arquero tarjeta.png" alt="Arquero">
    <div class="clase-estadisticas">
      <div class="text-group"><strong>Arquero</strong></div>
      <div class="stat-line"><span class="stat-icon icon-corazon" ></span><span class="stat-value-vida">10</span><span class="stat-label">Vida</span></div>
      <div class="stat-line"><span class="stat-icon icon-escudo" ></span><span class="stat-value-escudo">6</span><span class="stat-label">Defensa</span></div>
      <div class="stat-line"><span class="stat-icon icon-rayo" ></span><span class="stat-value-velocidad">5</span><span class="stat-label">Velocidad</span></div>
      <div class="stat-line"><span class="stat-icon icon-espada" ></span><span class="stat-value-ataque">15</span><span class="stat-label">Ataque</span></div>
    </div>
  </div>

  <div class="clase" data-personaje="Golem" style="--i:3;">
    <img src="imagenes/Golem tarjeta.png" alt="Golem">
    <div class="clase-estadisticas">
      <div class="text-group"><strong>Golem</strong></div>
      <div class="stat-line"><span class="stat-icon icon-corazon" ></span><span class="stat-value-vida">30</span><span class="stat-label">Vida</span></div>
      <div class="stat-line"><span class="stat-icon icon-escudo" ></span><span class="stat-value-escudo">20</span><span class="stat-label">Defensa</span></div>
      <div class="stat-line"><span class="stat-icon icon-rayo" ></span><span class="stat-value-velocidad">1</span><span class="stat-label">Velocidad</span></div>
      <div class="stat-line"><span class="stat-icon icon-espada" ></span><span class="stat-value-ataque">5</span><span class="stat-label">Ataque</span></div>
    </div>
  </div>

  <div class="clase" data-personaje="Ninja" style="--i:4;">
    <img src="imagenes/Ninja tarjeta.png" alt="Ninja">
    <div class="clase-estadisticas">
      <div class="text-group"><strong>Ninja</strong></div>
      <div class="stat-line"><span class="stat-icon icon-corazon" ></span><span class="stat-value-vida">10</span><span class="stat-label">Vida</span></div>
      <div class="stat-line"><span class="stat-icon icon-escudo" ></span><span class="stat-value-escudo">5</span><span class="stat-label">Defensa</span></div>
      <div class="stat-line"><span class="stat-icon icon-rayo" ></span><span class="stat-value-velocidad">6</span><span class="stat-label">Velocidad</span></div>
      <div class="stat-line"><span class="stat-icon icon-espada" ></span><span class="stat-value-ataque">15</span><span class="stat-label">Ataque</span></div>
    </div>
  </div>

  <div class="clase" data-personaje="Vampiro" style="--i:5;">
    <img src="imagenes/Vampiro tarjeta.png" alt="Vampiro">
    <div class="clase-estadisticas">
      <div class="text-group"><strong>Vampiro</strong></div>
      <div class="stat-line"><span class="stat-icon icon-corazon" ></span><span class="stat-value-vida">10</span><span class="stat-label">Vida</span></div>
      <div class="stat-line"><span class="stat-icon icon-escudo" ></span><span class="stat-value-escudo">5</span><span class="stat-label">Defensa</span></div>
      <div class="stat-line"><span class="stat-icon icon-rayo" ></span><span class="stat-value-velocidad">6</span><span class="stat-label">Velocidad</span></div>
      <div class="stat-line"><span class="stat-icon icon-espada" ></span><span class="stat-value-ataque">10</span><span class="stat-label">Ataque</span></div>
    </div>
  </div>
</div>
  <button id="derecha" class="flecha">⟩</button>
</div>

<script>
const carrusel = document.getElementById('carrusel');
const clases = document.querySelectorAll('.clase');
const total = clases.length;
let angulo = 0;
let actual = 0;
let claseSeleccionada = null;

function actualizarCarrusel() {
  carrusel.style.transform = `rotateY(${angulo}deg)`;
  clases.forEach((clase, i) => {
    const ang = (360 / total) * i;
    // decide la escala: si es la tarjeta activa aumentala; si además tiene la clase 'seleccionada' hacela aún más grande
    let escala = 1;
    if (i === actual) escala = 1.5;          // la que está en frente
    if (clase.classList.contains('seleccionada')) escala = 1.9; // si fue marcada por clic
    clase.style.transform = `rotateY(${ang}deg) translateZ(300px) scale(${escala})`;
    if (i === actual) {
      clase.classList.add('activo');
    } else {
      clase.classList.remove('activo');
    }
  });
}

document.getElementById('derecha').onclick = () => {
  angulo -= 360 / total;
  actual = (actual + 1) % total;
  actualizarCarrusel();
};

document.getElementById('izquierda').onclick = () => {
  angulo += 360 / total;
  actual = (actual - 1 + total) % total;
  actualizarCarrusel();
};

/* --- SELECCIÓN DE CLASES --- */
clases.forEach((clase, i) => {
  clase.addEventListener('click', () => {
    const nombre = clase.dataset.personaje;

    if (claseSeleccionada === clase) {
      // Segundo clic → Enviar formulario
      document.getElementById('inputPersonaje').value = nombre;
      clase.classList.add('elegida');
      setTimeout(() => {
        document.getElementById('formSeleccion').submit();
      }, 600);
    } else {
      // Primer clic → marcar como seleccionada y centrarla
      const ang = (360 / total);
      angulo = -ang * i;
      actual = i;
      // marcar visual
      clases.forEach(c => c.classList.remove('seleccionada'));
      clase.classList.add('seleccionada');
      claseSeleccionada = clase;
      actualizarCarrusel();
    }
  });
});



actualizarCarrusel();
</script>

</body>
</html>
