<?php

session_start();

//$_SESSION["username"] = "cachazo";

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RPG-1</title>
    <style>
        *{         
            padding: 0;
            margin: 0;
            scroll-behavior: smooth;
            user-select: none;
        }
        body {         
           color: #fff;
           background-clip: url('imagenes/fondo\ juego.mp4');
           font-family: 'Press Start 2P';
           min-height: 10vh;
           margin: 0;          
        }
        .menu {
            margin-top: 120px;
            margin-left: 40px;
            text-align: left;
        }
        .menu-btn {
            border: none;
            display: block;
            width: 320px;
            margin: 18px auto;
            padding: 18px 0;
            font-size: 0.9em;         
            border-radius: 12px;
            cursor: pointer;           
            font-family: inherit;
            text-shadow: 0 0 8px #6ec6ff;
            position: relative;
            background: rgba(255, 145, 145, 0);
            color: #fff;
        }
        .menu-btn:hover{
            transform: scale(1.15);
        }
.menu-btn::before,
 .menu-btn::after {
            content: '';
            opacity: 0;
            transition: opacity 0.2s, transform 0.2s;
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.5em;
        }
 .menu-btn::before {
  content: "";
  position: absolute;
  left: -30px;
  width: 80px;
  height: 80px;
  background-image: url('imagenes/aaaaaaaaflecha.png');
  background-size: contain;
  background-repeat: no-repeat;
  background-position: center;
  transform: translateY(-50%) scaleX(-1);
}
       .menu-btn::after {
  content: "";
  position: absolute;
  right: -30px;
  width: 80px;
  height: 80px;
  background-image: url('imagenes/aaaaaaaaflecha copia.png');
  background-size: contain;
  background-repeat: no-repeat;
  background-position: center;
  transform: translateY(-50%) scaleX(-1);
}
        .menu-btn:hover::before,
        .menu-btn:hover::after {
            opacity: 1;
            transform: translateY(-50%) scale(1.2);
        }
        .menu-btn:hover {
            color: rgba(192, 71, 91, 1);
            text-shadow: #ba1e1eff 0 0 12px;
            background: none;
        }
              
        .image-container {
            text-align: center;
            margin-top: 60px;
            width: 1570px;
            height:170PX;
            transition: transform 0.3s ease;
            animation: agrandar 2s ease-in infinite;
        }

        @keyframes agrandar {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
            }
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
.imagen-perfil {
  position: fixed;
  top: 60px;
  right: 18px;
  z-index: 20;
  display: flex;
  align-items: center;

}

.perfil-img {
  width: auto;
  height: 70px;
  border-radius: 6px;
  cursor: pointer;
  display: block;
}
       
.popup-fondo {
  display: none; /* oculto por defecto */
  position: fixed; /* lo posicionaremos con JS relativo a la imagen de perfil */
  z-index: 9998;
  align-items: flex-start; /* el contenido aparece debajo del avatar */
  justify-content: center;
  pointer-events: none; /* evita clicks cuando está oculto */
  opacity: 0;
}

.popup-fondo.show {
  display: flex;
  pointer-events: auto;
  opacity: 1;
  transform: translateY(0);
  animation: slideDown 1000ms cubic-bezier(.22,.9,.3,1);
}
@keyframes slideDown {
  from { transform: translateY(-12px); opacity: 0; }
  to   { transform: translateY(0); opacity: 1; }
}
.popup-contenido {
  background-color: rgba(170,170,170,0.42);
  padding: 14px;
  border-radius: 10px;
  text-align: left;
  position: relative;
  min-width: 220px;
  max-width: 320px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.6);
}




.cerrar-popup {
  color: #ddd;
  font-size: 20px;
  font-weight: 700;
  cursor: pointer;
  position: absolute;
  right: 8px;
  top: 6px;
}

.cerrar-popup:hover,
.cerrar-popup:focus {
  color: black;
}

#abrir-perfil {
    background: none;
    margin-bottom: 150px;    
    width: auto;
    height: 20px;
    right: 10px;
    cursor: pointer;
}

a {
  display: inline-block;
    text-decoration: none;
    color: white;
}

a:hover  {
    text-decoration: none;
    animation: transformBounce 1000ms infinite cubic-bezier(.22,.9,.3,.1) alternate;
    transform: translateY(-12px);
}
@keyframes transformBounce {
               from { transform: translateY(-6px);}
               to  { transform: translateY(6px); } 
    }
@keyframes arribaImg {
    from {
        transform: translateY(0);
    }
    to {
        transform: translateY(-10px);
    }
}
.imagen-perfil:hover {
  
  animation: arribaImg 2s ease infinite;

}

h2{
  font-size: 0.97em;
  margin: 10px 0;
}
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">

</head>
<body>
<audio id="audioFondo" src="rpg-titulo.wav" autoplay loop></audio>
    <div class="video-background-container">
  <video autoplay muted loop playsinline poster="imagen_carga.jpg" id="videoFondo">
    <source src="imagenes/fondo juego.mp4" type="video/mp4">
    <!-- Puedes agregar más etiquetas source para distintos formatos -->
    Tu navegador no soporta la etiqueta de video.
  </video>
  
    <div class="image-container">
    <img src="imagenes/logo khaos doom.png" alt="Logo" style="width:400px;">
    </div>




   <div class= "imagen-perfil" id="abrir-perfil">
   <img src="imagenes/perfil2.png" alt="Perfil" class="perfil-img"> 
    
</div>

<?php
if (isset($_SESSION["username"])) 
{
    echo "<div id='popup-perfil' class='popup-fondo'>
  <div class='popup-contenido'>
    <span class='cerrar-popup'>&times;</span>
    <img src='imagen/perfil2.png' alt='Foto de perfil' class='foto-perfil'>
    <h2>user</h2>
    <p>@nombredeusuario</p>
    <p><strong>Descripción:</strong> Descripción breve del usuario.</p>
  </div>
</div>";
}
else
{
    echo "<div id='popup-perfil' class='popup-fondo'>
  <div class='popup-contenido'>
    <span class='cerrar-popup'>&times;</span>
   <p> <h2><a href= 'login.php'>Inicia Sesión </a></h2> </p>
   <p> <h2><a href= 'registro.php'>Registrate </a></h2> </p>
  </div>
</div>";
}

?>

    <div class="menu">
        <button class="menu-btn" onclick="window.location.href='selec.php'">Iniciar partida</button>   
        <button class="menu-btn" onclick="window.location.href='Opciones.php'">Opciones</button>     
        <button class="menu-btn" onclick="window.location.href='exit.php'">Salir del juego</button>
    </div>

    <script> 
  /* selección de elementos */
const abrirBtn = document.getElementById('abrir-perfil');
const popup = document.getElementById('popup-perfil');
const cerrarBtn = document.querySelector('.cerrar-popup');
const audio = document.getElementById('audioFondo');

function abrirPopupCercaDelAvatar() {
  // primero hacemos visible el popup para medirlo (sin mostrar la animación aún)
  popup.style.display = 'flex';
  popup.classList.remove('show');

  // posicionar respecto al botón/avatar
  const rect = abrirBtn.getBoundingClientRect();
  const pw = popup.offsetWidth;
  const ph = popup.offsetHeight;

  // intentamos colocar a la izquierda del avatar; si no cabe, lo colocamos a la derecha
  let left = rect.left - pw - 12;
  if (left < 8) left = rect.right + 12;

  // colocamos la parte superior alineada con el avatar (puedes ajustar verticalOffset)
  const verticalOffset = 0; // px extra si quieres bajar un poco
  let top = rect.top + verticalOffset;
  // si se sale por abajo, ajustamos
  if (top + ph > window.innerHeight - 8) top = window.innerHeight - ph - 8;
  if (top < 8) top = 8;

  popup.style.left = `${left}px`;
  popup.style.top = `${top}px`;

  // pequeña espera para que el browser aplique estilos y luego activar la clase .show para animación
  requestAnimationFrame(() => requestAnimationFrame(() => popup.classList.add('show')));

  // permitir desmutear audio con el gesto del usuario
  document.addEventListener('pointerdown', desbloquearAudio, { once: true });
}

function cerrarPopup() {
  popup.classList.remove('show');
  // esperar fin de transición antes de ocultar display
  setTimeout(() => { if (!popup.classList.contains('show')) popup.style.display = 'none'; }, 260);
}

function desbloquearAudio() {
  try {
    audio.muted = false;
    audio.play().catch(()=>{});
  } catch(e){}
}

/* listeners */
abrirBtn.addEventListener('click', abrirPopupCercaDelAvatar);
cerrarBtn.addEventListener('click', cerrarPopup);
window.addEventListener('click', (event) => {
  if (event.target === popup) cerrarPopup();
});
document.addEventListener('keydown', (e) => { if (e.key === 'Escape') cerrarPopup(); });

    </script>
</body>
</html>