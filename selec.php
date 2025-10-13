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
  background: #222;
  border-radius: 15px;
  box-shadow: 0 5px 15px rgba(0,0,0,0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 22px;
  font-weight: bold;
  transition: all 0.5s;
}

.clase-estadisticas {
  background-color: rgba(0, 0, 0, 0.6);
  position: absolute;
  bottom: 0px;
  border-radius: 0px 0px 15px 15px;
  width: 200px;
  height: 120px;
  color: white;
  font-size: 14px;
  text-align: left;
}

.text-group {
  margin-top: 15px;
  font-size: 14px;
  margin-left: 10px;

}

.clase img {
  width: 80px;
  height: 80px;
  margin-bottom: 130px;
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

</style>
</head>
<body>

<div class="carrusel-container">
  <button id="izquierda" class="flecha">⟨</button>
  <div class="carrusel" id="carrusel">
  <div class="clase" style="--i:0;">
    <img src="imagenes/Guerrero.png" alt="Guerrero">
    <div class="clase-estadisticas">
     <div class="text-group">
      <strong>Guerrero</strong><br>
      Vida: <span style="color:chartreuse; font-weight:bold;">----</span><br> <!-- 20 --> 
      Defensa: <span style="color:chartreuse; font-weight:bold;">---</span><br> <!-- 15 -->
      Velocidad: <span style="color:chartreuse; font-weight:bold;">--</span><br> <!-- 10 --> 
      Ataque: <span style="color:chartreuse; font-weight:bold;">--</span> <span style="color:brown; font-weight:bold;">-</span> <!-- 12 -->
</div>
    </div>
  </div>
  <div class="clase" style="--i:1;">
    <img src="imagenes/Mago.png" alt="Mago">
    <div class="clase-estadisticas">
      <div class="text-group">
      <strong>Mago</strong><br>
      Vida: <span style="color:chartreuse; font-weight:bold;">---</span><br> <!-- 15 -->
      Defensa: <span style="color:chartreuse; font-weight:bold;">-</span><br> <!-- 5 -->
      Velocidad: <span style="color:chartreuse; font-weight:bold;">-</span><br> <!-- 5 -->
      Ataque: <span style="color:chartreuse; font-weight:bold;">----</span> <!-- 20 -->
    </div>
    </div>
  </div>
  <div class="clase" style="--i:2;">
    <img src="imagenes/Arquero.png" alt="Arquero">
    <div class="clase-estadisticas">
      <div class="text-group">
      <strong>Arquero</strong><br>
      Vida: <span style="color:chartreuse; font-weight:bold;">--</span><br> <!-- 10 -->
      Defensa: <span style="color:chocolate; font-weight:bold;">--</span><br> <!-- 6 -->
      Velocidad: <span style="color:chartreuse; font-weight:bold;">---</span><br> <!-- 15 -->
      Ataque: <span style="color:chartreuse; font-weight:bold;">---</span> <!-- 15 -->
    </div>
    </div>
  </div>
  <div class="clase" style="--i:3;">
    <img src="imagenes/Golem.png" alt="Golem">
    <div class="clase-estadisticas">
      <div class="text-group">
      <strong>Golem</strong><br>
      Vida: <span style="color:chartreuse; font-weight:bold;">------</span><br> <!-- 30 -->
      Defensa: <span style="color:chartreuse; font-weight:bold;">----</span><br> <!-- 20 -->
      Velocidad: <span style="color:chocolate; font-weight:bold;">-</span><br> <!-- 3 -->
      Ataque: <span style="color:chartreuse; font-weight:bold;">-</span> <!-- 5 -->
    </div>
    </div>
  </div>
  <div class="clase" style="--i:4;">
    <img src="imagenes/Ninja.png" alt="Ninja">
    <div class="clase-estadisticas">
      <div class="text-group">
      <strong>Ninja</strong><br>
      Vida: <span style="color:chartreuse; font-weight:bold;">--</span><span style="color:brown; font-weight:bold;"> -</span><br> <!-- 12 -->
      Defensa: <span style="color:chartreuse; font-weight:bold;">-</span><br> <!-- 5 -->
      Velocidad: <span style="color:chartreuse; font-weight:bold;">---</span><br> <!-- 15 -->
      Ataque: <span style="color:chartreuse; font-weight:bold;">---</span> <!-- 15 -->
    </div>
    </div>
  </div>
  <div class="clase" style="--i:5;">
    <img src="imagenes/Vampiro.png" alt="Vampiro">
    <div class="clase-estadisticas">
      <div class="text-group">
      <strong>Vampiro</strong><br>
      Vida: <span style="color:chartreuse; font-weight:bold;">--</span><br> <!-- 10 -->
      Defensa: <span style="color:chartreuse; font-weight:bold;">-</span><br> <!-- 5 -->
      Velocidad: <span style="color:chartreuse; font-weight:bold;">----</span><br> <!-- 20 -->
      Ataque: <span style="color:chartreuse; font-weight:bold;">--</span> <!-- 10 -->
    </div>
    </div>
  </div>
</div>
  <button id="derecha" class="flecha">⟩</button>
</div>

<script>
   /* Variables globales para el carrusel */
const carrusel = document.getElementById('carrusel');
const total = 6;
let angulo = 0;
let actual = 0;


/* Esta funcion hace que a la hora de estar en el carrusel, dependiendo de que clase este va a mostrar la
tarjeta principal con un agrandamiento y que gire cierto angulo en 3D */
function actualizarCarrusel() {
  carrusel.style.transform = `rotateY(${angulo}deg)`;
  clases.forEach((clase, i) => {
    const ang = (360 / total) * i;
    if (i === actual) {
      clase.style.transform = `rotateY(${ang}deg) translateZ(300px) scale(1.5)`;
      clase.classList.add('activo');
    } else {
      clase.style.transform = `rotateY(${ang}deg) translateZ(300px) scale(1)`;
      clase.classList.remove('activo');
    }
  });
}

/* Estos eventos hacen que al clicar en las flechas se mueva el carrusel y cambie la clase activa */

document.getElementById('derecha').onclick = () => {
  angulo -= 360 / total;
  actual = (actual + 1) % total;
  actualizarCarrusel();
};

/* Evento para la flecha izquierda */

document.getElementById('izquierda').onclick = () => {
  angulo += 360 / total;
  actual = (actual - 1 + total) % total;
  actualizarCarrusel();
};

 /* Selecciona todas las clases y actualiza el carrusel al cargar la pagina */

const clases = document.querySelectorAll('.clase');
actualizarCarrusel();
</script>

</body>
</html>
