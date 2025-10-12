<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>RPG</title>
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



    const rutaBase = '/Sprays de Clases/';          //Creo una constante con una parte de las rutas de las imagees
    const clases = [/*'Arquero', 'Golem', */'Guerrero'/*, 'Mago', 'Ninja', 'Vampiro'*/];    
    const accion = { quieto: ' Quieto', caminando: " Caminando"};   //  "personajes" y "accion" se usan en la asignacion dinamica de las rutas de las imagenes

    const imagenes = { Guerrero: {}};   // creo el objeto donde guardare las imagenes
    const promesasCarga = [];   //Creo un array donde guardare las "promesas" de la carga de las imagenes

    for (const a in accion) {
    const sufijo = accion[a];
    clases.forEach(p => {
        const img = new Image();
        const src = `${rutaBase}${p}${sufijo}.png`;
        img.src = src;
        imagenes[p][a] = img;

        promesasCarga.push(new Promise(res => {
        img.onload = res;
        img.onerror = () => {
            console.error(`Error al cargar imagen: ${src}`);
            res(); // continúa incluso si falla una imagen
        };
        }));
    });
    }

    let teclas = {};
    let jugador = {orientado:1,contador: 0, ximagen: 0, yimagen: 0, anchoimagen: 48, altoimagen: 48,parado: true, x: 50, y: 50, altura:50, ancho:50, imagen: imagenes.Guerrero, base: [], colicion: false, id: 1, aceleracion_x : 0.1, velocidadx: 0,velocidady : 0, velocidadx_max: 4, velocidady_max: 6, saltando : false, salto : 0, estado: "quieto"};
    let personajes = [jugador];
    let piso = {x:0, y:canvas.height - 20,altura:20, ancho:canvas.width};
    let pared1 = {x:0, y:0, altura: canvas.height, ancho: 20};
    let pared2 = {ancho:20, y:0, altura: canvas.height, x: canvas.width - 20};
    let techo = {x:0,y:0, ancho:canvas.width, altura:20};
    let obstaculos = [piso, techo, pared1, pared2];

       hitbox.fillStyle = "black"; 

    document.addEventListener("keydown", (e) =>
    {
        teclas[e.key.toLowerCase()] = true;
        jugador.estado = "caminando";
    });

    document.addEventListener("keyup", (e) =>
    {
        teclas[e.key.toLowerCase()] = false;
        jugador.estado = "quieto";
        jugador.velocidadx = 0;
    });
    function moverJugador ()
    {
        /* Habria que agregar un if que verifica que no tenga colisiones abajo para todo esto, por ahora le da gravedad todo el tiempo */
        
        if (jugador.velocidady < jugador.velocidady_max && jugador.saltando == false)
        {
            jugador.velocidady += 1;
        }
        
        if (teclas["w"] && revisar_porcion(jugador).abajo == false) //este if hay que cambiarlo para que solo revise colisiones de abajo
        {
            jugador.saltando = true;
            jugador.salto = 8;
        }

        if (jugador.saltando)
        {
            if (jugador.salto > 2)
            {
                jugador.velocidady += 0.5;
                jugador.y -= jugador.velocidady;
            }
            jugador.salto -= 1;
            if (revisar_porcion(jugador).abajo == false)
            {
                jugador.salto = 0;
            }
            if (jugador.salto <= 0)
            {
                jugador.saltando = false;
                jugador.velocidady = 0;
            }
        }

        if (revisar_porcion(jugador).abajo && jugador.saltando == false)
        {
           jugador.y += jugador.velocidady;
        }

        /*if (teclas["w"] && jugador.y + jugador.velocidady > 0 && revisar_porcion(jugador))
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
        }*/
        if (teclas["a"] && revisar_porcion(jugador).izquierda)
        {
            if (jugador.velocidadx < jugador.velocidadx_max)
            {
                jugador.velocidadx += 2;
            }
            jugador.x -=jugador.velocidadx;
            jugador.orientado = -1;
        }
        else if (teclas["d"] && revisar_porcion(jugador).derecha)
        {
            if (jugador.velocidadx < jugador.velocidadx_max)
            {
                jugador.velocidadx += 2;
            }
            jugador.x +=jugador.velocidadx;
            jugador.orientado = 1;
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
            cambiar(personajes[i], 1);
        }  
        for (let i = 0; i < obstaculos.length; i++)
        {
            contexto.fillStyle = "black";
            dibujar_obstaculo(obstaculos[i],contexto);
        }
    }

    function dibujar_personaje(a,contexto)
    {
        let estado = a.estado;
        console.log(a.orientado);
        contexto.save();

        /*if(a.orientado == -1)
        {
            contexto.translate(a.x + a.ancho / 2, 0); // Mueve al centro del personaje
            contexto.scale(-1, 1); // Invierte horizontalmente
            contexto.translate(-a.ancho * 2, 0);
        }*/

       contexto.drawImage(a.imagen[estado], a.ximagen * a.anchoimagen, a.yimagen * a.altoimagen, a.anchoimagen, a.altoimagen, a.x, a.y, a.ancho, a.altura);
       contexto.restore();
    }
    function cambiar(a, b)
    {
        if(a.contador == 6)
        {
            a.contador = 0;
            a.ximagen+=b;
            if(a.ximagen == 2)
            {
                a.yimagen+=b;
                a.ximagen = 0;
            }
            else if(a.ximagen < 0 || a.yimagen <0)
            {
                a.ximagen = 0;
                a.yimagen = 2;
            }
            else if(a.yimagen ==2 && a.ximagen == 1)
            {
                a.yimagen = 0;
                a.ximagen = 0;
            }
            console.log(a.ximagen + "  " + a.yimagen);
        }
        else
        {
            a.contador++;
        }
    }

    function dibujar_obstaculo(obstaculo,contexto)
    {
        contexto.fillRect(obstaculo.x,obstaculo.y,obstaculo.ancho,obstaculo.altura);
    }

    function revisar_porcion(porcion)
    {
        porcion.x += porcion.velocidadx;
        porcion.y += porcion.velocidady;
        hitbox.clearRect (0,0,canvas.width, canvas.height);
        
        dibujar_personaje(porcion, hitbox);
        porcion.base = hitbox.getImageData(porcion.x, porcion.y, porcion.ancho, porcion.altura).data;
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
        let colisiones = {abajo: true, arriba: true, izquierda: true, derecha: true};
        for(let i = 0; i < pixeles.length;i+=4)
        {
            if ((pixeles[i] != porcion.base[i] || porcion.base[i+1] != pixeles[i+1] || porcion.base[i+2] != pixeles[i+2]) && porcion.base != 0)
            {
                let pixelIndex = i / 4;
                let x = pixelIndex % porcion.ancho;
                let y = Math.floor(pixelIndex / porcion.ancho);

                // ARRIBA / ABAJO
                if (y < porcion.altura / 2)
                { 
                    colisiones.arriba = false;
                }
                else 
                {
                    colisiones.abajo = false;
                }
                if (y < porcion.altura - 3)
                {
                    if (x < porcion.ancho / 2 && y < porcion.altura -2) 
                    {
                        colisiones.izquierda = false;
                    }
                    else
                    { 
                        colisiones.derecha = false;
                    }
                }
            }
        }/*
        porcion.x -= 2*porcion.velocidadx;
        porcion.y -= 2*porcion.velocidady;
        hitbox.clearRect (0,0,canvas.width, canvas.height);

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
        pixeles = hitbox.getImageData(porcion.x, porcion.y, porcion.ancho, porcion.altura).data;

        for(let i = 0; i < pixeles.length;i+=4)
        {
            if (pixeles[i] != porcion.base[i] || porcion.base[i+1] != pixeles[i+1] || porcion.base[i+2] != pixeles[i+2] && (porcion.base[i] != -1 && porcion.base[i+1] != -1 && porcion.base[i+2] != -1))
            {
                let pixelIndex = i / 4;
                let x = pixelIndex % porcion.ancho;
                let y = Math.floor(pixelIndex / porcion.ancho);

                // ARRIBA / ABAJO
                if (y < porcion.altura / 2)
                { 
                    colisiones.arriba = false;
                }
                else 
                {
                    colisiones.abajo = false;
                }
                if (y < porcion.altura - 3)
                {
                    if (x < porcion.ancho / 2 && y < porcion.altura -2) 
                    {
                        colisiones.izquierda = false;
                    }
                    else
                    { 
                        colisiones.derecha = false;
                    }
                }
            }
        }*/
        console.log(colisiones.derecha + "    " + colisiones.izquierda);
        porcion.x -= porcion.velocidadx;
        porcion.y -= porcion.velocidady;
        hitbox.clearRect (0,0,canvas.width, canvas.height);
        return colisiones;
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