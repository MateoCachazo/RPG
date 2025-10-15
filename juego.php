<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>RPG</title>
  <style>
    body 
    {
      margin: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      background: #ddd;
      image-rendering: pixelated;
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
<?php

$clase = $_POST["clase"] ?? "Guerrero";
?>

<script>
    const canvas = document.getElementById("juego");
    const ctx = canvas.getContext("2d", { willReadFrequently: true })
    const no_se_ve = document.getElementById("no_se_ve");
    const hitbox = no_se_ve.getContext("2d", { willReadFrequently: true })
    ctx.fillStyle = 'red';
    ctx.fillRect(0,0,100,100);



    const rutaBase = '/sprites/';          //Creo una constante con una parte de las rutas de las imagees
    const clases = ['Arquero', 'Golem', 'Guerrero', 'Mago', 'Ninja', 'Vampiro'];    
    const accion = { quieto: ' Quieto', caminando: " Caminando", daño: " Daño", salto: " Salto", ataque: " Ataque-Melee"};   //  "personajes" y "accion" se usan en la asignacion dinamica de las rutas de las imagenes

    const imagenes = { Guerrero: {}, Arquero: {}, Vampiro: {}, Ninja: {}, Mago: {}, Golem: {}};   // creo el objeto donde guardare las imagenes
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
    let jugador = {contador_limite: 6,orientado:1,contador: 0, ximagen: 0, yimagen: 0, anchoimagen: 48, altoimagen: 48,parado: true, x: 50, y: 50, altura:48, ancho:48, imagen: imagenes.Guerrero, base: [], colicion: false, id: 1, velocidadx: 0,velocidady : 0, velocidadx_max: 4, velocidady_max: 5, saltando : false, salto : 0, estado: "quieto", animacion_continua: true};
    let personajes = [jugador];
    let piso = {x:0, y:canvas.height - 20,altura:20, ancho:canvas.width};
    let pared1 = {x:0, y:0, altura: canvas.height, ancho: 20};
    let pared2 = {ancho:20, y:0, altura: canvas.height, x: canvas.width - 20};
    let techo = {x:0,y:0, ancho:canvas.width, altura:20};
    let obstaculos = [piso, techo, pared1, pared2];

    hitbox.fillStyle = "black"; 

    function cambiar_estado ()
    {
        if (jugador.animacion_continua == true)
        {
            jugador.contador_limite = 6;
            jugador.animacion_continua = true;
            if (teclas["a"] || teclas["d"])
            {
                jugador.estado = "caminando";
                
                if(teclas["a"])
                {
                    jugador.orientado = -1;
                }
                else
                {
                    jugador.orientado = 1;
                }
            }
            else
            {
                jugador.estado = "quieto";
            }
            if(teclas["o"])
            {
                jugador.contador_limite = 5;
                jugador.contador = 0;
                jugador.animacion_continua = false;
                jugador.estado = "daño";
                jugador.yimagen = 0;
                jugador.ximagen = 0;
                //teclas["o"] = false;
            }
            if(teclas["p"])
            {
                jugador.contador_limite = 5;
                jugador.contador = 0;
                jugador.animacion_continua = false;
                jugador.estado = "daño";
                jugador.yimagen = 0;
                jugador.ximagen = 0;
                //teclas["o"] = false;
            }
        }
        
        
    }
    document.addEventListener("keydown", (e) =>
    {
        teclas[e.key.toLowerCase()] = true;
        cambiar_estado();
    });

    document.addEventListener("keyup", (e) =>
    {
        teclas[e.key.toLowerCase()] = false;
        jugador.velocidadx = 0;
        cambiar_estado();
    });
    function moverJugador ()
    {
        /* Habria que agregar un if que verifica que no tenga colisiones abajo para todo esto, por ahora le da gravedad todo el tiempo */
        
        if (revisar_porcion(jugador).abajo == false && jugador.estado == "salto")
        {
            jugador.velocidady = 0;
            jugador.estado = "quieto";
            jugador.y +=1;
            cambiar_estado();
        }
        
        let aux = jugador.velocidady;
        jugador.velocidady = 0;
        if (jugador.velocidady < jugador.velocidady_max  && revisar_porcion(jugador).abajo == true)
        {
            aux += 2;
            jugador.estado = "salto";
            jugador.contador = 0;
            jugador.ximagen = 0;
            jugador.yimagen = 1;
            //console.log(jugador.estado);
        }
        
        jugador.velocidady = aux;
        
        
        if (teclas["w"] && revisar_porcion(jugador).abajo == false && jugador.estado != "salto") //este if hay que cambiarlo para que solo revise colisiones de abajo
        {
            jugador.estado = "salto";
            jugador.contador = 0;
            jugador.ximagen = 0;
            jugador.yimagen = 1;
            jugador.velocidady -= 14;
        }

        /*if (jugador.saltando == true)
        {
            jugador.salto -= 1;
            if (jugador.salto > 1)
            {
                
                jugador.y += jugador.velocidady; 
            }
            else if (jugador.salto < 2 && jugador.salto > 0)
            {
                jugador.velocidady = 0;
            }
            
            if (revisar_porcion(jugador).arriba == false || revisar_porcion(jugador).abajo == false)
            {
                jugador.salto = 0;
            }
            if (jugador.salto <= 0)
            {
                jugador.saltando = false;
                jugador.velocidady = 0;
            }
        }*/

        if (revisar_porcion(jugador).abajo)
        {
           jugador.y += jugador.velocidady;
        }
        if (revisar_porcion(jugador).derecha && jugador.orientado == 1)
        {
            jugador.x += jugador.velocidadx;
        }
        else if (revisar_porcion(jugador).izquierda && jugador.orientado == -1)
        {
            jugador.x += jugador.velocidadx;
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
            if (jugador.velocidadx > (jugador.velocidadx_max * -1))
            {
                jugador.velocidadx -= 2;
            }
        }
        else if (teclas["d"] && revisar_porcion(jugador).derecha)
        {
            if (jugador.velocidadx < jugador.velocidadx_max)
            {
                jugador.velocidadx += 2;
            }
        }
        else
        {
            jugador.velocidadx = 0;
        }
    }
    function dibujar(contexto)
    {
        ctx.fillStyle = "rgb(100,100,100)";
        ctx.fillRect(0,0,canvas.width,canvas.height);
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

    function dibujar_hitbox(a)
    {        
        hitbox.save();
        hitbox.translate(a.x +a.ancho / 2, a.y + a.altura);
        hitbox.fillRect(-14/2, -25, 10, 25);
        hitbox.fillStyle = "rgb(0 ,0, 255)";
        hitbox.fillRect(-10/2, -25, 7,1);
        hitbox.fillStyle = "rgb(255 ,0, 0)";
        hitbox.fillRect(-10/2, -1, 7,1);
        hitbox.restore();
    }
    function dibujar_personaje(a,contexto)
    {
        let estado = a.estado;
        //console.log(a.orientado);
        contexto.save();

        if(a.orientado == -1)
        {
            contexto.scale(-1, 1); // Invierte horizontalmente
            contexto.translate(-a.ancho - a.x * 2, 0);
        }

       contexto.drawImage(a.imagen[estado], a.ximagen * a.anchoimagen, a.yimagen * a.altoimagen, a.anchoimagen, a.altoimagen, a.x, a.y, a.ancho, a.altura);
       contexto.restore();
    }
    function cambiar(a, b)
    {
        /*  ESTE ES EL QUE HAY QUE USAR UNA VEZ SE CAMBIE EL FORMATO DE LOS SPRITES
        
        if(a.contador >= a.contador_limite)
        {
            a.contador = 0;
            a.ximagen+=b;
            if(a.ximagen >= naturalHeight && a.animacion_continua)
            {
                a.ximagen = 0;
            }
            else if(a.ximagen >= naturalHeight && a.animacion_continua == false)
            {
                a.ximagen = 0;
                a.estado = "quieto";
                a.animacion_continua = true;
                a.contador_limite = 6;
            }
            //console.log(a.ximagen + "  " + a.yimagen);
        }
        else
        {
            a.contador++;
        }*/


        if(a.contador >= a.contador_limite)
        {
            a.contador = 0;
            a.ximagen+=b;
            if (a.estado == "salto")
            {
                a.ximagen = 1;
            }
            else if(a.ximagen == 2)
            {
                a.yimagen+=b;
                a.ximagen = 0;
            }
            else if(a.ximagen < 0 || a.yimagen <0)
            {
                a.ximagen = 0;
                a.yimagen = 2;
            }
            else if(a.yimagen ==2 && a.ximagen == 1 && a.animacion_continua)
            {
                a.yimagen = 0;
                a.ximagen = 0;
            }
            else if(a.yimagen ==2 && a.ximagen == 1 && a.animacion_continua == false)
            {
                a.yimagen = 0;
                a.ximagen = 0;
                a.estado = "quieto";
                a.animacion_continua = true;
                a.contador_limite = 6;
            }
            //console.log(a.ximagen + "  " + a.yimagen);
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

    function importante(a)
    {
        for(let i = 0; i < a.length; i+=4)
        {
            a[i+3] = 0;
        }

    }
    function revisar_porcion(porcion)
    {
        porcion.x += porcion.velocidadx;
        porcion.y += porcion.velocidady;
        hitbox.fillStyle = "rgb(125,0,125)";
        //hitbox.fillRect (porcion.x, porcion.y, porcion.ancho, porcion.altura);
        dibujar_hitbox(porcion);
        
        porcion.base = hitbox.getImageData(porcion.x, porcion.y, porcion.ancho, porcion.altura).data;
        importante(porcion.base);
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
        porcion.x -= porcion.velocidadx;
        porcion.y -= porcion.velocidady;
        let colisiones = {abajo: true, arriba: true, izquierda: true, derecha: true};
        for(let i = 0; i < pixeles.length;i+=4)
        {
            if ((pixeles[i] != porcion.base[i] || porcion.base[i+1] != pixeles[i+1] || porcion.base[i+2] != pixeles[i+2] /*|| porcion.base[i+3] != pixeles[i+3]*/)/* && porcion.base[i+3] != 0*/)
            {
                let pixelIndex = i / 4;
                let x = pixelIndex % porcion.ancho;
                let y = Math.floor(pixelIndex / porcion.ancho);

                // ARRIBA / ABAJO
                if /*(*/(porcion.base[i+2] == 255 && porcion.base[i+1] == 0 && porcion.base[i] == 0 )/*|| (porcion.base[i+2] != 125 && porcion.base[i+1] && 0 && porcion.base[i] != 125))*/
                { 
                    colisiones.arriba = false;
                }
                else if(porcion.base[i+2] == 0 && porcion.base[i+1] == 0 && porcion.base[i] == 255 )
                {
                    colisiones.abajo = false;
                }
                if (y < porcion.altura - 3)
                {
                    if (x < porcion.ancho / 2 && y < porcion.altura -2) 
                    {
                        colisiones.izquierda = false;
                    }
                    else if (x > porcion.ancho / 2 && y < porcion.altura -2)
                    { 
                        colisiones.derecha = false;
                    }
                }
            }
        }
        //console.log(colisiones.abajo,porcion.x, porcion.y, porcion.ancho, porcion.altura, porcion.velocidady/* + "    " + colisiones.izquierda*/);
        
        hitbox.clearRect (0,0,canvas.width, canvas.height);
        return colisiones;
    }

    function loop()
    {
        //console.log(jugador.estado);
        hitbox.clearRect (0,0,canvas.width, canvas.height);
        ctx.clearRect (0,0,canvas.width, canvas.height);
        dibujar(ctx);
        moverJugador();
        requestAnimationFrame(loop);
    }
    loop()
</script>
