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
            background: radial-gradient(ellipse at center, #213250ff 0%, #0c1324ff 100%);
            color: #fff;
            font-family: 'Cinzel', serif;
            text-align: center;
            min-height: 100vh;
            margin: 0;
        }
        .menu {
            margin-top: 60px;
            text-align: left;
        }
        .menu-btn {
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
  left: -40px;
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
  right: -40px;
  width: 80px;
  height: 80px;
  background-image: url('imagenes/flechaaaaasss.png');
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
            background: rgba(60,80,120,0.9);
         
        }      
        .image-container {
            text-align: center;
            margin-top: 20px;
            width: 1520px;
            height:170PX;
            transition: transform 0,3s ease;
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
    </style>
</head>
<body>
    <div class="image-container">
    <img src="imagenes/logo khaos doom.png" alt="Logo" style="width:400px;">
    </div>
    
    <div class="menu">
        <button class="menu-btn" onclick="window.location.href='start.php'">Iniciar partida</button>        
        <button class="menu-btn" onclick="window.location.href='achievements.php'">Estadisticas</button>
        <button class="menu-btn" onclick="window.location.href='login.php'">Login</button>
        <button class="menu-btn" onclick="window.location.href='register.php'">Register</button>
        <button class="menu-btn" onclick="window.location.href='exit.php'">Salir del juego</button>
    </div>
</body>
</html>