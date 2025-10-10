<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dos Canvas Superpuestos</title>
  <style>
    body {
      margin: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      background: #ddd;
    }

    .contenedor {
      position: relative;
      width: 1000px;
      height: 1000px;
      border: 2px solid black;
      background: white;
    }

    canvas {
      position: absolute;
      top: 0;
      left: 0;
      width: 1000px;
      height: 1000px;
    }

    /* El orden de superposición */
    #no_se_ve {
      z-index: 0;
    }

    #juego {
      z-index: 1;
    }
  </style>
</head>
<body>
  <div class="contenedor">
    <canvas id="no_se_ve"></canvas>
    <canvas id="juego"></canvas>
  </div>
</body>
</html>


<script>
    const canvas = document.getElementById("juego");
    const ctx = canvas.getContext("2d", { willReadFrequently: true })
    const no_se_ve = document.getElementById("no_se_ve");
    const hitbox = no_se_ve.getContext("2d", { willReadFrequently: true })
    ctx.fillStyle = 'red';
    ctx.fillRect(0,0,100,100);

    let teclas = {};

    let jugador = {parado: true, x: 50, y: 50, altura:10, ancho:10, imagen: "imagen", base: [[0,0,0,0]], colicion: false, id: 1, aceleracion_x : 0.1, velocidadx: 0,velocidady : 0, velocidadx_max: 4, velocidady_max: 4};

    let personajes = [jugador];
    let piso = {x:0, y:canvas.height - 20,altura:20, ancho:canvas.width};
    let pared1 = {x:0, y:0, altura: canvas.height, ancho: 20};
    let pared2 = {ancho:20, y:0, altura: canvas.height, x: canvas.width - 20};
    let techo = {x:0,y:0, ancho:canvas.width, altura:20};
    let obstaculos = [piso, techo, pared1, pared2];

    document.addEventListener("keydown", (e) =>
    {
        teclas[e.key.toLowerCase()] = true;
    });

    document.addEventListener("keyup", (e) =>
    {
        teclas[e.key.toLowerCase()] = false;

        jugador.velocidadx = 0;
        jugador.velocidady = 0;
    });
    function moverJugador ()
    {
        if (teclas["w"] && /*jugador.y + jugador.velocidady > 0 &&*/ revisar_porcion(jugador))
        {
            if (jugador.velocidady < jugador.velocidady_max)
            {
                jugador.velocidady += 1;
            }
            jugador.y -=jugador.velocidady;
        }
        else if (teclas["s"] && revisar_porcion(jugador))
        {
            if (jugador.velocidady < jugador.velocidady_max)
            {
                jugador.velocidady += 1;
            }
            jugador.y +=jugador.velocidady;
        }
        else
        {
            jugador.velocidady = 0;
        }
        if (teclas["a"] && revisar_porcion(jugador))
        {
            if (jugador.velocidadx < jugador.velocidadx_max)
            {
                jugador.velocidadx += 1;
            }
            jugador.x -=jugador.velocidadx;
        }
        else if (teclas["d"] && revisar_porcion(jugador))
        {
            if (jugador.velocidadx < jugador.velocidadx_max)
            {
                jugador.velocidadx += 1;
            }
            jugador.x +=jugador.velocidadx;
        }
        else
        {
            jugador.velocidadx = 0;
        }
    }
    function dibujar(contexto)
    {
       
        for (let i = 0; i < personajes.length; i++)
        {
            contexto.fillStyle = "#FF0000";
            dibujar_personaje(personajes[i],contexto);
        }  
        for (let i = 0; i < obstaculos.length; i++)
        {
            contexto.fillStyle = "black";
            dibujar_obstaculo(obstaculos[i],contexto);
        }
    }

    function dibujar_personaje(a,contexto)
    {
        contexto.fillRect(a.x,a.y,a.ancho,a.altura);
    }

    function dibujar_obstaculo(obstaculo,contexto)
    {
        contexto.fillRect(obstaculo.x,obstaculo.y,obstaculo.ancho,obstaculo.altura);
    }

    function revisar_porcion(porcion)
    {
        hitbox.clearRect (0,0,canvas.width, canvas.height);
        /*porcion.x += porcion.velocidadx;
        porcion.y += porcion.velocidady;*/
        hitbox.fillStyle = "#FF0000";
        dibujar_personaje(porcion, hitbox);
        for (let i = 0; i < personajes.length; i++)
        {
            if(personajes[i].id != porcion.id)
            {
                dibujar_personaje(personajes[i], hitbox);
            }
        }  
       
        for (let i = 0; i < obstaculos.length; i++)
        {
            hitbox.fillStyle = "black";
            dibujar_obstaculo(obstaculos[i], hitbox);
        }
        let pixeles = hitbox.getImageData(porcion.x, porcion.y, porcion.ancho, porcion.altura).data;
        for(let i = 0; i < pixeles.length;i+=4)
        {
            //console.log(pixeles[i] + " " + pixeles[i+1] + " " + pixeles[i+2]);
            if (pixeles[i] != 255 || pixeles[i+1] != 0 || pixeles[i+2] != 0/* && pixeles[i+3] == 1*/)
            {
                return false;
            }
        }
        return true;
    }

    function loop()
    {
        hitbox.clearRect (0,0,canvas.width, canvas.height);
        ctx.clearRect (0,0,canvas.width, canvas.height);
        dibujar(ctx);
        moverJugador();
        requestAnimationFrame(loop);
    }
    loop()
</script>