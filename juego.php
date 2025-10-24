
<?php
$clase = $_POST['personaje'] ?? "Guerrero";
?>

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
      width: 100%;
      height: 100%;
      border: 2px solid black;
      background: white;
    }


    canvas {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
    }


    /* El orden de superposición */
    #no_se_ve {
      z-index: 0;
    }


    #juego {
      z-index: 1;
    }
    #hud
    {
        z-index: 2;
        image-rendering: pixelated;
    }
  </style>
</head>
<body>
  <div class="contenedor">
    <canvas id="no_se_ve"></canvas>
    <canvas id="juego"></canvas>
    <canvas id= "hud"></canvas>
  </div>
</body>
</html>
<?php
?>


<script>
    const canvas = document.getElementById("juego");
    const ctx = canvas.getContext("2d", { willReadFrequently: true })
    const no_se_ve = document.getElementById("no_se_ve");
    const hitbox = no_se_ve.getContext("2d", { willReadFrequently: true })
    const hud = document.getElementById("hud");
    const hud_ctx = hud.getContext("2d", {willReadFrequently: true})
    
    ctx.fillStyle = 'red';
    ctx.fillRect(0,0,100,100);
    const musica = new Audio('cancion-rpg.wav');
    musica.volume = 0.5;
    const snd_salto = new Audio('sonidos/salto.wav');
    const snd_daño = new Audio('sonidos/daño.wav');
    const snd_golpe_guerrero = new Audio('sonidos/golpe-guerrero.wav');

    canvas.width = screen.width;
    canvas.height = screen.height;
    no_se_ve.width = screen.width;
    no_se_ve.height = screen.height;
    let clasee = "<?php echo $clase;?>";


    let rutaBase = 'sprites/clases/';          //Creo una constante con una parte de las rutas de las imagees
    let clases = ['Arquero', 'Golem', 'Guerrero', 'Mago', 'Ninja', 'Vampiro'];    
    let accion = { quieto: ' Quieto', caminando: " Caminando", daño: " Daño", salto: " Salto", ataque: " Ataque-Melee", especial: " Ataque-Especial"};   //  "personajes" y "accion" se usan en la asignacion dinamica de las rutas de las imagenes

    let barra_vida = new Image();
    barra_vida.src = "sprites/Barra de Vida.png";

    let nivel1 = new Image();
    nivel1.src = "sprites/nivel1-1.webp";
    let nivel1_colision = new Image();
    nivel1_colision.src = "sprites/Nivel 1 Solo Colisiones.png";
    let nivel1_adelante = new Image();
    nivel1_adelante.src = "sprites/Nivel 1 Objetos por delante.png";

    const imagenes = { Guerrero: {}, Arquero: {}, Vampiro: {}, Ninja: {}, Mago: {}, Golem: {}, Esqueleto_Diabólico: {}};   // creo el objeto donde guardare las imagenes
    const promesasCarga = [];   //Creo un array donde guardare las "promesas" de la carga de las imagenes


    for (const a in accion) 
    {
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

    rutaBase = 'sprites/enemigos/';          //Creo una constante con una parte de las rutas de las imagees
    let enemigos = ['Esqueleto_Diabólico'];    
    let accion2 = { quieto: ' Quieto', caminando: " Caminando", daño: " Daño", /*salto: " Salto",*/ ataque: " Ataque-1", especial: " Ataque-2", muerte: " Muerte"};   //  "personajes" y "accion" se usan en la asignacion dinamica de las rutas de las imagenes


    for (const a in accion2) 
    {
    const sufijo = accion2[a];
    enemigos.forEach(p => {
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
    let estadisticas = 
    {
        "Guerrero":
        {
            velocidadx_max: 4,
            velocidady_max: 6,
            vida: 20,
            ataque: 12,
            defensa: 15
        },
        "Golem":
        {
            velocidadx_max: 1,
            velocidady_max: 10,
            vida: 30,
            ataque: 5,
            defensa: 20
        },
        "Mago":
        {
            velocidadx_max: 2,
            velocidady_max: 8,
            vida: 15,
            ataque: 20,
            defensa: 5
        },
        "Arquero":
        {
            velocidadx_max: 5,
            velocidady_max: 6,
            vida: 10,
            ataque: 15,
            defensa: 6
        },
        "Ninja":
        {
            velocidadx_max: 6,
            velocidady_max: 4,
            vida: 100,
            ataque: 15,
            defensa: 5
        },
        "Vampiro":
        {
            velocidadx_max: 7,
            velocidady_max: 2,
            vida: 10,
            ataque: 10,
            defensa: 5
        }
    };
    let jugador = {contador_limite: 6,orientado:1,contador: 0, ximagen: 0, yimagen: 0, anchoimagen: 48, altoimagen: 48,parado: true, x: 50, y: 400, altura:78, ancho:48, imagen: imagenes[clasee], base: [], colicion: false, id: 1, velocidadx: 0,velocidady : 0, velocidadx_max: estadisticas[clasee].velocidadx_max, velocidady_max: estadisticas[clasee].velocidady_max, saltando : false, salto : 0, estado: "quieto", animacion_continua: true, contador_ataque: 0, vida: estadisticas[clasee].vida, daño_aux: 0, ataque: estadisticas[clasee].ataque, critico: 1, defensa: estadisticas[clasee].defensa};
    let esqueletodiabolico = {vision: 200,contador_limite: 6,orientado:1,contador: 0, ximagen: 0, yimagen: 0, anchoimagen: 48, altoimagen: 48,parado: true, x: 10, y: 500, altura:78, ancho:48, imagen: imagenes.Esqueleto_Diabólico, base: [], colicion: false, id: 2, velocidadx: 0,velocidady : 0, velocidadx_max: 2, velocidady_max: 5, saltando : false, salto : 0, estado: "quieto", animacion_continua: true, contador_ataque: 0, vida: 7, daño_aux: 0, delay_ataque: 0, ataque: 5, defensa: 3, critico: 0};
    let personajes = [jugador, esqueletodiabolico];
    let piso = {x:0, y:canvas.height - 292,altura:20, ancho:canvas.width};
    let plataforma1 = {x: 54, y: canvas.height - 356, altura: 6, ancho: 15};
    let plataforma2 = {x: 31, y: canvas.height - 329, altura: 6, ancho: 12};
    let plataforma3 = {x:29, y:canvas.height - 383, altura: 6, ancho: 16};
    let piso2 = {x: 225, y: canvas.height - 700, altura: 20, ancho: 50};
    let pared2 = {ancho:20, y:0, altura: canvas.height, x: canvas.width - 20};
    let techo = {x:0,y:0, ancho:canvas.width, altura:20};
    let obstaculos = [piso, plataforma1, plataforma2, plataforma3];
    let camaray_aux = jugador.y;
    let camarax_aux = jugador.x;

    //console.log(clasee);
    hitbox.fillStyle = "black";


    function cambiar_estado ()
    {
       if (jugador.vida > 0)
        {
             if (jugador.animacion_continua == true)
            {
                jugador.contador_limite = 7;
                jugador.animacion_continua = true;
                jugador.ximagen = 0;
                if(teclas["a"])
                    {
                        jugador.orientado = -1;
                    }
                else if (teclas["d"])
                    {
                        jugador.orientado = 1;
                    }

                if ((teclas["a"] || teclas["d"]) && jugador.estado != "ataque" && jugador.estado != "especial" && jugador.estado != "daño" && jugador.estado != "salto")
                {
                    jugador.estado = "caminando";
                    jugador.contador_limite = 5;
                    
                    
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
                    jugador.ximagen = 0;
                }
                if(teclas["p"])
                {
                    jugador.contador_limite = 5;
                    jugador.contador = 0;
                    jugador.animacion_continua = false;
                    jugador.estado = "ataque";
                    jugador.ximagen = 0;
                    jugador.contador_ataque = 4;
                }
                if(teclas["q"])
                {
                    jugador.contador_limite = 5;
                    jugador.contador = 0;
                    jugador.animacion_continua = false;
                    jugador.estado = "especial";
                    jugador.ximagen = 0;
                    //jugador.contador_ataque = 4;
                }
                }
        }

       
       
    }
    document.addEventListener("keydown", (e) =>
    {
        teclas[e.key.toLowerCase()] = true;
        if (e.repeat == false)
        {
            cambiar_estado();
        }
    });


    document.addEventListener("keyup", (e) =>
    {
        teclas[e.key.toLowerCase()] = false;
        cambiar_estado();
    });
    function moverJugador ()
    {
        if (jugador.vida > 0)
        {
            
        if (jugador.estado == "daño" && jugador.daño_aux > 0)
            {
                jugador.vida -= 1;
                //console.log(jugador.vida);
                jugador.daño_aux -= 1;
            }

        /* Habria que agregar un if que verifica que no tenga colisiones abajo para todo esto, por ahora le da gravedad todo el tiempo */
            





       


       
        let aux = jugador.velocidady;
        jugador.velocidady = 0;
        if (aux < jugador.velocidady_max  && revisar_porcion(jugador).abajo == true)
        {
            aux += 2;
            if (jugador.estado != "ataque" && jugador.estado != "especial" && jugador.estado != "daño")
            {
                jugador.estado = "salto";
                jugador.contador = 0;
                jugador.ximagen = 1;
            }

            jugador.y += 1;
            //console.log(jugador.estado);
        }
       








        jugador.velocidady = aux;
       
       
        if (teclas["w"] && revisar_porcion(jugador).abajo == false && jugador.estado != "salto"  && jugador.estado != "ataque" && jugador.estado != "especial" && jugador.estado != "daño") //este if hay que cambiarlo para que solo revise colisiones de abajo
        {
            snd_salto.play();
            jugador.estado = "salto";
            jugador.contador = 0;
            jugador.ximagen = 1;
            jugador.velocidady -= 19;
        }


        if(revisar_porcion(jugador).arriba == false)
        {
            jugador.velocidady = 0;
        }








        let auux = false;
        if (revisar_porcion(jugador).abajo == false /*&& jugador.estado == "salto"*/)
        {
            //console.log(jugador.estado);
            for (jugador.velocidady; jugador.velocidady >= -1; jugador.velocidady--)
            {
                if (revisar_porcion(jugador).abajo)
                {
                   // console.log (jugador.velocidady);
                    jugador.velocidady+= 1;
                    auux = true;
                    break;
                }
            }
            if(jugador.estado == "salto")
            {
                jugador.estado = "quieto";
                //jugador.y +=1;
                jugador.animacion_continua = true;
                cambiar_estado();
            }
        }








        //if (revisar_porcion(jugador).abajo)
        //{*/
           jugador.y += jugador.velocidady;
        //}
        if(auux)
        {
            jugador.velocidady = 0;
        }
        if (revisar_porcion(jugador).derecha && jugador.orientado == 1)
        {
            jugador.x += jugador.velocidadx;
        }
        else if (revisar_porcion(jugador).izquierda && jugador.orientado == -1)
        {
            jugador.x += jugador.velocidadx;
        }
        if (teclas["a"] && revisar_porcion(jugador).izquierda && ((revisar_porcion(jugador).abajo==false && jugador.estado != "ataque" && jugador.estado != "especial" && jugador.estado != "daño") || revisar_porcion(jugador).abajo))
        {
            if (jugador.velocidadx > (jugador.velocidadx_max * -1))
            {
                jugador.velocidadx -= 2;
            }
        }
        else if (teclas["d"] && revisar_porcion(jugador).derecha && ((revisar_porcion(jugador).abajo==false && jugador.estado != "ataque" && jugador.estado != "especial" && jugador.estado != "daño") || revisar_porcion(jugador).abajo))
        {
            if (jugador.velocidadx < jugador.velocidadx_max)
            {
                jugador.velocidadx += 2;
            }
        }
        else
        {
            if (jugador.velocidadx < 0)
            {
                jugador.velocidadx += 1;
            }
            else if (jugador.velocidadx > 0)
            {
                jugador.velocidadx -= 1;
            }
            
        }

        
            
        }
    }
    function dibujar(contexto)
    {
        //ctx.fillStyle = "rgb(100,100,100)";
        //ctx.fillRect(0,0,canvas.width,canvas.height);
        ctx.drawImage(nivel1, 0, 0, 3000, 960, 0, 0, canvas.width, canvas.height);
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
        ctx.drawImage(nivel1_adelante, 0, 0);
        /*hud_ctx.fillStyle = "black";
        hud_ctx.strokeStyle = "white";
        hud_ctx.lineWidth = 2;
        hud_ctx.strokeText(jugador.vida, 70, 30);
        hud_ctx.fillText(jugador.vida, 70, 30);*/
        hud_ctx.fillStyle = "red";
       // hud_ctx.fillRect(19,20,((38 * jugador.vida) / estadisticas[clasee].vida),7);
        //hud_ctx.drawImage(barra_vida, 10, 0, 48, 48);


    }


    function dibujar_hitbox(a)
    {   
        if (a.id == 1 && clasee == "Golem")
        {
            hitbox.save();
            hitbox.translate(a.x +a.ancho / 2, a.y + a.altura);
            hitbox.fillRect(-14/2, -38, 14, 38);
            hitbox.fillStyle = "rgb(0 ,0, 255)";
            hitbox.fillRect(-10/2, -38, 6,1);
            hitbox.fillStyle = "rgb(255 ,0, 0)";
            hitbox.fillRect(-10/2, -1, 6,1);
            hitbox.restore();
        }
        else
        {
            hitbox.save();
            hitbox.translate(a.x +a.ancho / 2, a.y + a.altura);
            hitbox.fillRect(-14/2, -25, 10, 25);
            hitbox.fillStyle = "rgb(0 ,0, 255)";
            hitbox.fillRect(-10/2, -25, 6,1);
            hitbox.fillStyle = "rgb(255 ,0, 0)";
            hitbox.fillRect(-10/2, -1, 6,1);
            hitbox.restore();
        }
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


        if(estado == "salto" && a.id > 1)
        {
            contexto.drawImage(a.imagen["quieto"], a.ximagen * a.anchoimagen, a.yimagen * a.altoimagen, a.anchoimagen, a.altoimagen, a.x, a.y, a.ancho, a.altura);
        }
        else
        {
            contexto.drawImage(a.imagen[estado], a.ximagen * a.anchoimagen, a.yimagen * a.altoimagen, a.anchoimagen, a.altoimagen, a.x, a.y, a.ancho, a.altura);
        }
       contexto.restore();

       if (a.contador_ataque > 0 && a.estado == "ataque")
                {
                    hitbox.fillStyle = "rgba(0,255,0,0.5)";
                    if (a.orientado == 1)
                    {
                        hitbox.fillRect(a.x, a.y + 20 + 10, 55, 60);
                    }
                    else if (a.orientado == -1)
                    {
                        hitbox.fillRect(a.x - 15, a.y + 20 + 10, 55, 60);
                    }
                    snd_golpe_guerrero.play();
                    a.contador_ataque -= 1;
                }
    }
    function cambiar(a, b)
    {
         if(a.contador >= a.contador_limite)
        {
            a.contador = 0;
            a.ximagen+=b;
            if(a.estado == "salto")
            {
                a.ximagen = 1;
            }
            else if(a.ximagen >= a.imagen[a.estado].naturalWidth / 48 && a.animacion_continua)
            {
                a.ximagen = 0;
            }
            else if(a.ximagen >= a.imagen[a.estado].naturalWidth / 48 && a.animacion_continua == false)
            {
                if (a.estado == "muerte")
                {
                    personajes.splice(a.id-1, 1);
                }
                else
                {
                    a.ximagen = 0;
                    a.estado = "quieto";
                    a.animacion_continua = true;
                    a.contador_limite = 7;
                    cambiar_estado();
                }     
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
        hitbox.fillRect(obstaculo.x,obstaculo.y,obstaculo.ancho,obstaculo.altura);
        //contexto.drawImage(nivel1_colision, 0, 0, 3000, 960);
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
                        if(/*pixeles[i] == 0 && pixeles[i+1] == 255 && pixeles[i+2] == 0 */pixeles[i+3] == 26 && porcion.estado != "daño")
                        {
                            snd_daño.play();
                            if (porcion.id != 1)
                            {
                                jugador.critico = Math.floor(Math.random() * 3) + 1;
                                porcion.daño_aux = Math.floor(((2 * jugador.ataque)/5) + 2 * jugador.critico * (jugador.ataque / esqueletodiabolico.defensa) / 50 + 2);
                            }
                            else
                            {
                                porcion.daño_aux = Math.floor(((2 * esqueletodiabolico.ataque)/5) + 2 * esqueletodiabolico.critico * (esqueletodiabolico.ataque / jugador.defensa) / 50 + 2);
                            }
                            console.log(porcion.daño_aux);
                            porcion.velocidadx = 0;
                            porcion.contador_limite = 5;
                            porcion.contador = 0;
                            porcion.animacion_continua = false;
                            porcion.estado = "daño";
                            porcion.ximagen = 0;
                            //porcion.velocidadx += 5;
                           
                        }
                    }
                    else if (x > porcion.ancho / 2 && y < porcion.altura -2)
                    {
                        colisiones.derecha = false;
                        if(/*pixeles[i] == 0 && pixeles[i+1] == 255 && pixeles[i+2] == 0 */pixeles[i+3] == 128 && porcion.estado != "daño")
                        {
                            snd_daño.play();
                            if (porcion.id != 1)
                            {
                                jugador.critico = Math.floor(Math.random() * 3) + 1;
                                porcion.daño_aux = Math.floor(((2 * jugador.ataque)/5) + 2 * jugador.critico * (jugador.ataque / porcion.defensa) / 50 + 2);
                            }
                            else
                            {
                                esqueletodiabolico.critico = Math.floor(Math.random() * 2) + 1;
                                porcion.daño_aux = Math.floor(((2 * esqueletodiabolico.ataque)/5) + 2 * esqueletodiabolico.critico * (esqueletodiabolico.ataque / jugador.defensa) / 50 + 2);
                            }
                            console.log(porcion.daño_aux);
                            porcion.velocidadx = 0;
                            porcion.contador_limite = 5;
                            porcion.contador = 0;
                            porcion.animacion_continua = false;
                            porcion.estado = "daño";
                            porcion.ximagen = 0;
                            //porcion.velocidadx -= 5;
                        }
                    }
                }
                
            }
        }
        //console.log(colisiones.abajo,porcion.x, porcion.y, porcion.ancho, porcion.altura, porcion.velocidady/* + "    " + colisiones.izquierda*/);
       
        hitbox.clearRect (0,0,canvas.width, canvas.height);
        return colisiones;
    }


     function mover_enemigos(enemigo)
    {
        if (enemigo.vida > 0)
        {
            if (enemigo.delay_ataque > 0)
            {
                enemigo.delay_ataque -= 1;
            }
            if (enemigo.estado == "daño" && enemigo.daño_aux > 0)
            {
                enemigo.vida -= 1;
                //console.log(enemigo.vida);
                enemigo.daño_aux -= 1;
            }
            if (revisar_porcion(enemigo).abajo == false && enemigo.estado == "salto")
            {
                enemigo.velocidady = 0;
                enemigo.y +=1;
                cambiar_estado();
            }
            if (revisar_porcion(enemigo).abajo == false && enemigo.estado != "daño" && enemigo.estado != "ataque")
            {
                enemigo.estado = "caminando";
            }
        if(Math.abs(enemigo.x - jugador.x) + Math.abs(enemigo.y - jugador.y) <= 30 && (enemigo.estado == "caminando" || enemigo.estado == "salto" || enemigo.estado == "quieto") && enemigo.delay_ataque == 0)
            {
                enemigo.contador_limite = 5;
                enemigo.contador = 0;
                enemigo.animacion_continua = false;
                enemigo.estado = "ataque";
                enemigo.ximagen = 0;
                enemigo.contador_ataque = 4;
                enemigo.delay_ataque = 90;
            } 
            
            let aux = enemigo.velocidady;
            enemigo.velocidady = 0;
            if (enemigo.velocidady < enemigo.velocidady_max  && revisar_porcion(enemigo).abajo == true)
            {
                aux += 2;
                enemigo.estado = "salto";
                enemigo.contador = 0;
                enemigo.ximagen = 1;
            }
            
            enemigo.velocidady = aux;/*

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
            }*/
            
            if(enemigo.vision >= Math.abs(jugador.x - enemigo.x) + Math.abs(jugador.y - enemigo.y) && enemigo.estado != "ataque" && enemigo.estado != "daño")
            {
                enemigo.orientado = Math.abs(jugador.x - enemigo.x) / (jugador.x - enemigo.x);
                if(enemigo.orientado == 1)
                {
                    enemigo.orientado = 1;
                }
                else
                {
                    enemigo.orientado = -1;
                }
                //console.log(enemigo.orientado);
            }
            else
            {
                if((revisar_porcion(enemigo).izquierda == false && enemigo.orientado == -1)|| (enemigo.orientado == 1 && revisar_porcion(enemigo).derecha == false))
                {
                    enemigo.orientado *= -1;
                    enemigo.velocidadx = 0;
                }
            }

            if(enemigo.orientado == 1)
            {
                if (enemigo.velocidadx < enemigo.velocidadx_max)
                {
                    enemigo.velocidadx += 2;
                }
            }
            else
            {
                if (enemigo.velocidadx > (enemigo.velocidadx_max * -1))
                {
                    enemigo.velocidadx -= 2;
                }
            }
            if (revisar_porcion(enemigo).abajo)
            {
            enemigo.y += enemigo.velocidady;
            }
            if (revisar_porcion(enemigo).derecha && enemigo.orientado == 1)
            {
                enemigo.x += enemigo.velocidadx;
            }
            else if (revisar_porcion(enemigo).izquierda && enemigo.orientado == -1)
            {
                enemigo.x += enemigo.velocidadx;
            }
            if (enemigo.estado == "caminando")
            {
                enemigo.contador_limite = 5;
                cambiar(enemigo,1);
            }
        }
    else
    {
        enemigo.estado = "muerte";
    }
    }


    function loop()
    {
        //console.log(jugador.estado);
       // musica.play();
        hitbox.clearRect (0,0,canvas.width, canvas.height);
        ctx.clearRect (0,0,canvas.width, canvas.height);
        hud_ctx.clearRect(0,0,screen.width,screen.height);
        dibujar(ctx);
        //console.log(jugador.y);
        moverJugador();
        for (let i = 1; i < personajes.length; i++)
        {
            mover_enemigos(personajes[i]);
        }
        //hitbox.clearRect (0,0,canvas.width, canvas.height);
        if (jugador.y - camaray_aux >= 100 || jugador.y - camaray_aux <= -100)
        {
        }
        else if (revisar_porcion(jugador).abajo == false && (jugador.y - camaray_aux >= 30 || jugador.y - camaray_aux <= -30))
        {
            camaray_aux += 2;
        }

        if(revisar_porcion(jugador).abajo && Math.abs(jugador.y - camaray_aux) > 60)
        {
            camaray_aux += jugador.velocidady;
        }
        else if(revisar_porcion(jugador).abajo == false  && Math.abs(jugador.y - camaray_aux) > 30)
        {
            camaray_aux += (jugador.y - camaray_aux) / Math.abs(jugador.y - camaray_aux) * 2;
        }

        if (jugador.x - 300 > 0 && jugador.x - 300 < canvas.width)
        {
            camarax_aux = jugador.x;
        }
        else if (jugador.x - 300 <= 0)
        {
            camarax_aux = 300;
        }
        else if (jugador.x - 300 > canvas.width)
        {
            camarax_aux = canvas.width + 300;
        }

        hitbox.drawImage(canvas, camarax_aux - 300, camaray_aux - 300, jugador.ancho + 400, jugador.altura + 400, 0,0,canvas.width, canvas.height);
        ctx.clearRect (0,0,canvas.width, canvas.height);
        ctx.drawImage(no_se_ve, 0,0,canvas.width, canvas.height);
        hitbox.clearRect(0,0,canvas.width, canvas.height);
        //console.log(jugador.ximagen + " " + jugador.contador_limite + " " + jugador.contador);
        // console.log(personajes[0].vida);
        requestAnimationFrame(loop);
       
    }
    loop()
</script>


