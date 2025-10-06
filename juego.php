<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <canvas width = 100% height = 100% id = "juego"></canvas>
</body>
</html>

<script>
    const canvas = document.getElementById("juego");
    const ctx = canvas.getContext("2d");

    let teclas = {};

    let ubicacion = {x: 4, y: 4};

    let mapa = 
    [
        {0,0,0,0,0,0,0,0,0,0},
        {0,0,0,0,0,0,0,0,0,0},
        {0,0,0,0,0,0,0,0,0,0},
        {0,0,0,0,0,0,0,0,0,0},
        {0,0,0,0,1,0,0,0,0,0},
    ];
    
    document.addEventListener("keydown", (e) =>
    {
        teclas[e.key.toLowerCase()] = true;
    });

    document.addEventListener("keyup", (e) =>
    {
        teclas[e.key.toLowerCase()] = false;
    });


    function moverJugador ()
    {
        if (teclas["w"])
        {
            mapa[ubicacion.y][ubicacion.x] = 0;
            mapa[ubicacion.y-1][ubicacion.x] = 1;
        }
        if (teclas["s"])
        {
            mapa[ubicacion.y][ubicacion.x] = 0;
            mapa[ubicacion.y+1][ubicacion.x] = 1;
        }
        if (teclas["a"])
        {
            mapa[ubicacion.y][ubicacion.x] = 0;
            mapa[ubicacion.y][ubicacion.x-1] = 1;
        }
        if (teclas["d"])
        {
            mapa[ubicacion.y][ubicacion.x] = 0;
            mapa[ubicacion.y][ubicacion.x+1] = 1;
        }
    }

    function dibujarMapa()
    {
        for (let i = 0; i < mapa.lenght; i++)
        {
            for (let j = 0; j < mapa[0].lenght; j++)
            {
                ctx.font = "40px Arial";
                ctx.filltext(mapa[i][j], i*40, j*40);
            }
        }
    }

    function loop()
    {
        ctx.clearRect (0,0,canvas.width, canvas.height);
        moverJugador();
        dibujarMapa();
        requestAnimationFrame(loop);
    }

    loop();
</script>