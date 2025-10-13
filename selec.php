<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Selector de Clases 3D</title>
<style>
body {
  background: #111;
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

.clase img {
  width: 80px;
  height: 80px;
  margin-bottom: 10px;
}

.flecha {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  font-size: 40px;
  color: white;
  background: rgba(0,0,0,0.4);
  border: none;
  cursor: pointer;
  z-index: 10;
  padding: 10px;
  border-radius: 50%;
}

#izquierda { left: -60px; }
#derecha { right: -60px; }

</style>
</head>
<body>

<div class="carrusel-container">
  <button id="izquierda" class="flecha">⟨</button>
  <div class="carrusel" id="carrusel">
  <div class="clase" style="--i:0;">
    <img src="imagenes/Guerrero.png" alt="Guerrero">

  </div>
  <div class="clase" style="--i:1;">
    <img src="imagenes/Mago.png" alt="Mago">

  </div>
  <div class="clase" style="--i:2;">
    <img src="imagenes/Arquero.png" alt="Arquero">

  </div>
  <div class="clase" style="--i:3;">
    <img src="imagenes/Golem.png" alt="Golem">

  </div>
  <div class="clase" style="--i:4;">
    <img src="imagenes/Ninja.png" alt="Ninja">

  </div>
  <div class="clase" style="--i:5;">
    <img src="imagenes/Vampiro.png" alt="Vampiro">
  
  </div>
</div>
  <button id="derecha" class="flecha">⟩</button>
</div>

<script>
const carrusel = document.getElementById('carrusel');
const total = 6; // cantidad de clases
let angulo = 0;

function actualizarCarrusel() {
  carrusel.style.transform = `rotateY(${angulo}deg)`;
}

document.getElementById('derecha').onclick = () => {
  angulo -= 360 / total;
  actualizarCarrusel();
};

document.getElementById('izquierda').onclick = () => {
  angulo += 360 / total;
  actualizarCarrusel();
};

// Posicionar las clases en círculo 3D
const clases = document.querySelectorAll('.clase');
clases.forEach((clase, i) => {
  const ang = (360 / total) * i;
  clase.style.transform = `rotateY(${ang}deg) translateZ(300px)`;
});
</script>

</body>
</html>
