<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Khaos Doom - Pausa</title>
  <style>
body {
  background: rgba(0, 0, 0, 0.4); /* negro translúcido */
    animation: fondo 10s ease-in-out infinite alternate;
    margin: 0;
    color: white;
    font-family: Arial, sans-serif;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

@keyframes fondo {
  from { background-position: 0 0; }
  to { background-position: 100% 100%; }
}
    
    .menu, .submenu {
      display: none;
      flex-direction: column;
      gap: 15px;
      text-align: center;
    }

    .menu.active, .submenu.active {
      display: flex;
    }

    button {
  transition: all 0.3s ease;
}
button:hover {
  transform: scale(1.05);
}

    #btn{
        border: none;
            display: block;
            width: 320px;
            margin: 18px auto;
            padding: 18px 0;
            font-size: 1.5em;         
            border-radius: 12px;
            cursor: pointer;           
            font-family: inherit;
            text-shadow: 0 0 8px #6ec6ff;
            position: relative;
            background: rgba(255, 145, 145, 0);
            color: #fff;
    }
    #btnResume{
        border: none;
            display: block;
            width: 320px;
            margin: 18px auto;
            padding: 18px 0;
            font-size: 1.5em;         
            border-radius: 12px;
            cursor: pointer;           
            font-family: inherit;
            text-shadow: 0 0 8px #6ec6ff;
            position: relative;
            background: rgba(255, 145, 145, 0);
            color: #fff;
    }
    #btnBack{
        border: none;
            display: block;
            width: 320px;
            margin: 18px auto;
            padding: 18px 0;
            font-size: 1.5em;         
            border-radius: 12px;
            cursor: pointer;           
            font-family: inherit;
            text-shadow: 0 0 8px #6ec6ff;
            position: relative;
            background: rgba(255, 145, 145, 0);
            color: #fff;
    }
    #btnResume{
        border: none;
            display: block;
            width: 320px;
            margin: 18px auto;
            padding: 18px 0;
            font-size: 1.5em;         
            border-radius: 12px;
            cursor: pointer;           
            font-family: inherit;
            text-shadow: 0 0 8px #6ec6ff;
            position: relative;
            background: rgba(255, 145, 145, 0);
            color: #fff;
    }
    #btnOptions{
        border: none;
            display: block;
            width: 320px;
            margin: 18px auto;
            padding: 18px 0;
            font-size: 1.5em;         
            border-radius: 12px;
            cursor: pointer;           
            font-family: inherit;
            text-shadow: 0 0 8px #6ec6ff;
            position: relative;
            background: rgba(255, 145, 145, 0);
            color: #fff;
    }
    #btnExit{
        border: none;
            display: block;
            width: 320px;
            margin: 18px auto;
            padding: 18px 0;
            font-size: 1.5em;         
            border-radius: 12px;
            cursor: pointer;           
            font-family: inherit;
            text-shadow: 0 0 8px #6ec6ff;
            position: relative;
            background: rgba(255, 145, 145, 0);
            color: #fff;
    }

    .menu button { /* Animación de aparición */
  opacity: 0;
  transform: translateY(20px);
  animation: aparecer forwards 0.6s; /*forwards para mantener el estado final*/
}
.menu button:nth-child(1) { animation-delay: 0.2s; } /* Retraso para cada botón */
.menu button:nth-child(2) { animation-delay: 0.4s; }
.menu button:nth-child(3) { animation-delay: 0.6s; }

@keyframes aparecer { /* Definición de la animación */
  to { opacity: 1; transform: translateY(0); }
}
  </style>
</head>
<body>

  <!-- Menú principal -->
  <div id="mainMenu" class="menu active">
    <button id="btnResume">Reanudar</button>
    <button id="btnOptions">Opciones</button>
    <button id="btnExit">Salir del juego</button>
  </div>

  <!-- Submenú de opciones -->
  <div id="optionsMenu" class="submenu">
    <button id="btn">Resolución</button>
    <button id="btn">Cambiar controles</button>
    <button id="btnBack">Volver</button>
  </div>

  <script>
    // Referencias a los menús
    const mainMenu = document.getElementById("mainMenu");
    const optionsMenu = document.getElementById("optionsMenu");

    // Botones principales
    document.getElementById("btnOptions").addEventListener("click", () => {
      mainMenu.classList.remove("active");
      optionsMenu.classList.add("active");
    });

    document.getElementById("btnBack").addEventListener("click", () => {
      optionsMenu.classList.remove("active");
      mainMenu.classList.add("active");
    });

    // Acciones de ejemplo
    document.getElementById("btnResume").addEventListener("click", () => {
      window.parent.postMessage("reanudar", "*");
    });

    document.getElementById("btnExit").addEventListener("click", () => {
      window.parent.postMessage("salir", "*");
    });
  </script>

</body>
</html>
