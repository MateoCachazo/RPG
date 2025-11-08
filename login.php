<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Khaos Doom - Inicio de Sesión</title>
</head>
<style>
   :root{
      --bg:#0f1113;
      --card: rgba(255,255,255,0.04);
      --accent:#5cc0ff;
      --muted:#bfc8d1;
      --glass: rgba(255,255,255,0.03);
      --radius:12px;
      --gap:18px;
    }
 body {
    background: radial-gradient(ellipse at center, #2f4b7bff 0%, #1e2b4aff 100%);
    color: #fff;
    font-family: 'Press Start 2P';
    text-align: center;
    min-height: 100vh;
    margin: 0;
        }
.form-container {
    background: linear-gradient(180deg, rgba(0, 0, 0, 1), rgba(39, 38, 38, 0.63));
      border-radius:var(--radius);
      padding:40px;
      box-shadow: 0 8px 30px rgba(0, 0, 0, 1);
  width: 320px;
  text-align: left;
  position: fixed;
  margin-left: 570px;
  margin-top: 100px;
  color: rgba(243, 244, 246, 1);
  transition: transform 0,3s ease;
  animation: agrandar 2s ease-in infinite;
}


@keyframes agrandar{
  0%, 100% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.01);
  }
}

.title {
  text-align: center;
  font-size: 1.5rem;
  line-height: 2rem;
  font-weight: 700;
}

.form {
  margin-top: 1.5rem;
}

.input-group {
  margin-top: 0.25rem;
  margin-right: 0.3rem;
  font-size: 0.875rem;
  line-height: 1.25rem;
}

.input-group label {
  display: block;
  color: rgba(156, 163, 175, 1);
  margin-bottom: 4px;
}

.input-group input {
  width: 100%;
  border-radius: 0.375rem;
  border: 1px solid rgba(55, 65, 81, 1);
  outline: 0;
  background-color: rgba(17, 24, 39, 1);
  padding: 0.75rem 1rem;
  color: rgba(243, 244, 246, 1);
  margin-left: -4%;
}

.input-group input:focus {
  border-color: rgba(167, 139, 250);
}

.forgot {
  display: flex;
  justify-content: flex-end;
  font-size: 0.75rem;
  line-height: 1rem;
  color: rgba(156, 163, 175,1);
  margin: 8px 0 14px 0;
}



.forgot a,.signup a {
  color: rgba(243, 244, 246, 1);
  text-decoration: none;
  font-size: 14px;
}

.forgot a:hover, .signup a:hover {
  text-decoration: underline rgba(167, 139, 250, 1);
}

.sign {
    display: block;
  width: 109%;
 background-color: #5cc0ff;
  padding: 0.75rem;
  text-align: center;
  margin-top: 20px;
  color: rgba(17, 24, 39, 1);
  margin-left: -4%;
  border: none;
  border-radius: 0.375rem;
  font-weight: 600;
  cursor: pointer;
}
.sign:hover{
  background-color: #129cf3ff;
}



.line {
  height: 1px;
  flex: 1 1 0%;
  background-color: rgba(55, 65, 81, 1);
}

.social-message .message {
  padding-left: 0.75rem;
  padding-right: 0.75rem;
  font-size: 0.875rem;
  line-height: 1.25rem;
  color: rgba(156, 163, 175, 1);
}

.signup {
  text-align: center;
  font-size: 0.75rem;
  line-height: 1rem;
  color: rgba(156, 163, 175, 1);
  cursor: pointer;
}

.input-group label {
  display: block;
  color: rgba(156, 163, 175, 1);
  margin-bottom: 4px;
  margin-left: -4%;
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
}

.btn-volver {
  position: absolute;
  bottom: 12px;
  right: 12px; 
  padding:8px 10px;
  border-radius:8px;
  font-weight:700;
  background: linear-gradient(#0f1113, #2d2d2dff);
  border:1px solid rgba(255,255,255,0.04);
  color:#fff;
  cursor:pointer;
  z-index: 5;
}
.navbar {
  position: fixed;    
  width: 100%;
  height: 70px;            
  background: transparent;
  display: flex;
  align-items: center;
}

.logo-link {
  display: flex;
  align-items: center;
  text-decoration: none;
}

.logo {
   margin-left: -20px;
  margin-top: 20px;
  height: 100px;         
  width: auto;
  cursor: pointer;
  transition: transform 0.3s ease;
}

.logo:hover {
  transform: scale(1.20);
}
</style>
<body>
    <audio id="audioFondo" src="rpg-titulo.wav" autoplay loop></audio>
<video autoplay muted loop playsinline poster="imagen_carga.jpg" id="videoFondo">
    <source src="imagenes/fondo juego.mp4" type="video/mp4">
    <!-- Puedes agregar más etiquetas source para distintos formatos -->
    Tu navegador no soporta la etiqueta de video.
  </video>
<nav class="navbar">
  <a href="index.php" class="logo-link">
    <img src="imagenes/khaos.png" alt="Logo" class="logo">
  </a>
</nav>

   

<div class="form-container">
   <div>
        <button class="btn-volver" onclick="location.href='index.php'">Volver</button>

       </div>
	<p class="title">Inicio de Sesión</p>
	<form class="form" action="login2.php" method="post">
		<div class="input-group">
			<label for="username">Nombre de Usuario: </label>
			<input type="text" name="username" id="username" placeholder="">
		</div>
		<div class="input-group">
			<label for="password">Contraseña: </label>
			<input type="password" name="password" id="password" placeholder="">
      <br>
      <br>
      <br>
			<div class="forgot">
				<a rel="noopener noreferrer" href="#">¿Olvidaste tu contraseña?</a>
			</div>
		</div>
		<button class="sign">Inicia Sesión</button>
	</form>
  <br>
  <br>
	
	<p class="signup">¿No tienes una cuenta?
		<a rel="" href="registro.php">Regístrate</a>
	</p>
</div>
</body> <!-- ESTA PAGINA VA A PONER EL LOGIN CENTRADO EN LA PAGINA, JUNTO CON UN VIDEO DESDE ATRAS DEL MISMO JUEGO -->
</html>
