<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Juego</title>
    <style>
       canvas {
        width: 80%;
        height: 100%;
      }
    </style>
</head>
<body>
    <canvas id = "juego"></canvas>
</body>
</html>

<script>
    const canvas = document.getElementById("juego");
    const ctx = canvas.getContext("2d");
    ctx.fillStyle = 'red';
    ctx.fillRect(0,0,100,100);

    let teclas = {};

    let jugador = {x: 2, y: 3, altura:10, ancho:10, imagen: "imagen", base: [[0,0,0,0]], coalición: false, id: 1, aceleracion_x : 0.1, velocidadx: 0, velocidady : 0, velocidadx_max: 0.5};

    let personajes = [jugador];
    //let obstaculos = [];

    document.addEventListener("keydown", (e) =>
    {
        teclas[e.key.toLowerCase()] = true;
    });

    document.addEventListener("keyup", (e) =>
    {
        teclas[e.key.toLowerCase()] = false;

        jugador.velocidadx = 0;
    });
    function moverJugador ()
    {
        if (teclas["w"] && jugador.y - 1 > 0)
        {
            jugador.y -=1;
        }
        if (teclas["s"] && jugador.y + 1 < canvas.height - 1)
        {
            jugador.y +=1;
        }
        if (teclas["a"] && jugador.x - 1 > 0)
        {
            if (jugador.velocidadx < jugador.velocidadx_max)
            {
                jugador.velocidadx += 0.1;
            }
            jugador.x -=jugador.velocidadx;
        }
        if (teclas["d"] && jugador.x + 1 < canvas.width - 1)
        {
            if (jugador.velocidadx < jugador.velocidadx_max)
            {
                jugador.velocidadx += 0.1;
            }
            jugador.x +=jugador.velocidadx;
        }
    }

    function dibujar()
    {
        dibujar_personaje(personajes[0]);
        /*personajes.foreach(personaje, i)
        {
            dibujar_personaje(personaje);
        }*/
        /*obstaculos.foreach(obstaculo,i)
        {
            dibujar_obstaculo(obstáculo);
        }*/
    }

    function dibujar_personaje(personaje)
    {
        ctx.fillStyle = "black";
        ctx.fillRect(personaje.x * personaje.ancho,personaje.y * personaje.altura,personaje.ancho,personaje.altura);
    }

    function dibujar_obstaculo(obstaculo)
    {
        ctx.fillRect(obstaculo.x,obstaculo.y,obstaculo.ancho,obstaculo.altura);
    }

    function revisar_porcion(porcion)
    {
    let pixeles = ctx.getImageData(porcion.x, porcion.y, porcion.width, porcion.height).data;
        pixeles.foreach(pixel,i)
        {
            if (pixel != porcion.base[i] && porcion.base[i] != -1)
            {
                personajes[porcion.id].colicion= true;
            }
        }
    }

    function loop()
    {
        ctx.clearRect (0,0,canvas.width, canvas.height);
        moverJugador();
        dibujar();
        requestAnimationFrame(loop);
    }
    loop()
</script>
