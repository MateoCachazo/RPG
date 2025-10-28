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
      image-rendering: pixelated;
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

    class proyectil
    {
        constructor (x, y, ancho, altura,id, imagen, imagen_fin, orientado, velocidady)
        {
            this.velocidadx = 5;
            this.velocidady_max = -5;
            this.velocidady = velocidady;
            this.orientado = orientado;
            this.anchoimagen = 48;
            this.ximagen = 0;
            this.yimagen = 0;
            this.altoimagen = 48;
            this.contador = 0;
            this.contador_limite = 5;
            this.animacion_continua = true;
            this.x = x;
            this.y = y;
            this.ancho = ancho;
            this.altura = altura;
            this.id = id;
            this.imagen = imagen;
            this.imagen_fin = imagen_fin;
        }
    }
    class laser
    {
        constructor (x, y, ancho, altura,id, imagen, imagen_fin)
        {
            this.anchoimagen = 48;
            this.altoimagen = 48;
            this.contador = 0;
            this.contador_limite = 5;
            this.x = x;
            this.y = y;
            this.ancho = ancho;
            this.altura = altura;
            this.id = id;
            this.imagen = imagen;
            this.imagen_fin = imagen_fin;
        }
    }
    class magia
    {
        constructor (x, y, ancho, altura,id, imagen, imagen_fin)
        {
            this.anchoimagen = 48;
            this.altoimagen = 48;
            this.contador = 0;
            this.contador_limite = 5;
            this.x = x;
            this.y = y;
            this.ancho = ancho;
            this.altura = altura;
            this.id = id;
            this.imagen = imagen;
            this.imagen_fin = imagen_fin;
        }
    }
    
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
    //clasee = "Arquero";

    let rutaBase = 'sprites/clases/';          //Creo una constante con una parte de las rutas de las imagees
    let clases = ['Arquero', 'Golem', 'Guerrero', 'Mago', 'Ninja', 'Vampiro'];    
    let accion = { quieto: ' Quieto', caminando: " Caminando", daño: " Daño", salto: " Salto", ataque: " Ataque-Melee", especial: " Ataque-Especial"};   //  "personajes" y "accion" se usan en la asignacion dinamica de las rutas de las imagenes

    let barra_vida = new Image();
    barra_vida.src = "sprites/Barra de Vida.png";
    let vida_contador = 0;

    let objetos_nivel1 = ['Nenúfar_N1', 'Tabla_N1', 'NenúfarFlor_N1', 'ColeccionableAlma_N1'];
    let objetos_accion = {animacion: ' (animación)', quieto: ' (quieto)'};

   

    let nivel1 = new Image();
    nivel1.src = "sprites/nivel1-1.webp";
    let nivel1_colision = new Image();
    nivel1_colision.src = "sprites/Nivel 1 Solo Colisiones.png";
    let nivel1_adelante = new Image();
    nivel1_adelante.src = "sprites/Nivel 1 Objetos por delante.png";

    const imagenes = { Guerrero: {}, Arquero: {}, Vampiro: {}, Ninja: {}, Mago: {}, Golem: {}, Esqueleto_Diabólico: {}, Nenúfar_N1: {}, Tabla_N1: {}, NenúfarFlor_N1: {}, ColeccionableAlma_N1: {}};   // creo el objeto donde guardare las imagenes
    const promesasCarga = [];   //Creo un array donde guardare las "promesas" de la carga de las imagenes

    let flecha_exp = new Image();
    flecha_exp.src = "sprites/clases/Flecha Explosiva (Ataque-Especial-Arquero).png";

    promesasCarga.push(new Promise(res => {
        flecha_exp.onload = res;
        flecha_exp.onerror = () => {
            console.error(`Error al cargar imagen: ${flecha_exp.src}`);
            res();
        };
    }));

    let fin = new Image();
    fin.src = "sprites/clases/Explosión (Ataque-Especial-Arquero).png";

    promesasCarga.push(new Promise(res => {
        fin.onload = res;
        fin.onerror = () => {
            console.error("aaaaaaaaaaaaaaaaaaaa");
            res();
        };
    }));

    for (const a in objetos_accion) 
    {
        const sufijo = objetos_accion[a];
        objetos_nivel1.forEach(p => {
            const img = new Image();
            const src = `sprites/${p}${sufijo}.png`;
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
            velocidady_max: 6,
            vida: 10,
            ataque: 15,
            defensa: 5
        },
        "Vampiro":
        {
            velocidadx_max: 6,
            velocidady_max: 3,
            vida: 10,
            ataque: 10,
            defensa: 5
        }
    };
    let jugador = {contador_limite: 6,orientado:1,contador: 0, ximagen: 0, yimagen: 0, anchoimagen: 48, altoimagen: 48,parado: true, x: 22, y:400, altura:78, ancho:48, imagen: imagenes[clasee], base: [], colicion: false, id: 1, velocidadx: 0,velocidady : 0, velocidadx_max: estadisticas[clasee].velocidadx_max, velocidady_max: estadisticas[clasee].velocidady_max, saltando : false, salto : 0, estado: "quieto", animacion_continua: true, contador_ataque: 0, vida: estadisticas[clasee].vida, daño_aux: 0, ataque: estadisticas[clasee].ataque, critico: 1, defensa: estadisticas[clasee].defensa};
    let esqueletodiabolico1 = {vision: 200,contador_limite: 6,orientado:1,contador: 0, ximagen: 0, yimagen: 0, anchoimagen: 48, altoimagen: 48,parado: true, x: 80, y: canvas.height - 400, altura:78, ancho:48, imagen: imagenes.Esqueleto_Diabólico, base: [], colicion: false, id: 2, velocidadx: 0,velocidady : 0, velocidadx_max: 2, velocidady_max: 5, saltando : false, salto : 0, estado: "quieto", animacion_continua: true, contador_ataque: 0, vida: 7, daño_aux: 0, delay_ataque: 0, ataque: 5, defensa: 3, critico: 0};
    let esqueletodiabolico2 = {vision: 200,contador_limite: 6,orientado:1,contador: 0, ximagen: 0, yimagen: 0, anchoimagen: 48, altoimagen: 48,parado: true, x: 500, y: canvas.height - 400, altura:78, ancho:48, imagen: imagenes.Esqueleto_Diabólico, base: [], colicion: false, id: 2, velocidadx: 0,velocidady : 0, velocidadx_max: 2, velocidady_max: 5, saltando : false, salto : 0, estado: "quieto", animacion_continua: true, contador_ataque: 0, vida: 7, daño_aux: 0, delay_ataque: 0, ataque: 5, defensa: 3, critico: 0};
    let esqueletodiabolico3 = {vision: 200,contador_limite: 6,orientado:1,contador: 0, ximagen: 0, yimagen: 0, anchoimagen: 48, altoimagen: 48,parado: true, x: 1400, y: canvas.height - 400, altura:78, ancho:48, imagen: imagenes.Esqueleto_Diabólico, base: [], colicion: false, id: 2, velocidadx: 0,velocidady : 0, velocidadx_max: 2, velocidady_max: 5, saltando : false, salto : 0, estado: "quieto", animacion_continua: true, contador_ataque: 0, vida: 7, daño_aux: 0, delay_ataque: 0, ataque: 5, defensa: 3, critico: 0};
    let personajes = [jugador, esqueletodiabolico1, esqueletodiabolico2, esqueletodiabolico3];

    let piso = {x:0, y:canvas.height - 291,altura:20, ancho:340};
    let piso2 = {x: 479, y: canvas.height - 291, altura: 20, ancho: canvas.width};
    let pisoagua = {x:340, y:canvas.height - 286,altura:20, ancho:139};
    let antisuicidio1 = {x:338, y:canvas.height - 296,altura:5, ancho:5};
    let antisuicidio2 = {x:479, y:canvas.height - 296,altura:5, ancho:5};
    /*let piso3 = {x: 592, y: canvas.height - 292, altura: 20, ancho: 22};
    let piso4 = {x: 640, y: canvas.height - 292, altura: 20, ancho: 20};
    let piso5 = {x: 684, y: canvas.height - 292, altura: 20, ancho: 214};
    let piso6 = {x: 939, y: canvas.height - 292, altura: 20, ancho: canvas.width};*/
    let agua = {x: 340, y: canvas.height - 288, altura: 20, ancho: 139};
    let plataforma1 = {x: 54, y: canvas.height - 356, altura: 6, ancho: 15};
    let plataforma2 = {x: 31, y: canvas.height - 329, altura: 6, ancho: 12};
    let plataforma3 = {x:29, y:canvas.height - 383, altura: 6, ancho: 16};
    let plataforma4 = {x:canvas.width - 252,y:canvas.height - 408, ancho: 34, altura:10};
    let caja1 = {ancho:20, y:canvas.height - 326, altura: 35, x: canvas.width - 297};
    let caja2 = {ancho:28, y:canvas.height - 342, altura:52, x: canvas.width - 275};
    let caja3 = {ancho:23, y:canvas.height - 315, altura: 24, x: canvas.width - 243};
    let pared = {x: canvas.width - 218, y: canvas.height - 452, ancho:27, altura: 180};
    let pared1 = {x: -5, y: 0, ancho:7, altura: canvas.height};
    let pared2 = {x: canvas.width - 10, y: 0, ancho:40, altura: canvas.height};
    let nenufar1 = {ximagen:0, yimagen:0,x: 360, y: canvas.height - 296, altura: 48, ancho: 48, altoimagen: 48, anchoimagen: 48, imagen: imagenes.NenúfarFlor_N1, estado: "quieto", contador: 0, contador_limite: 6, id: -1, animacion_continua: true};
    let tabla = {ximagen:0, yimagen:0,x: 400, y: canvas.height - 296, altura: 48, ancho: 48, altoimagen: 48, anchoimagen: 48, imagen: imagenes.Tabla_N1, estado: "quieto", contador: 0, contador_limite: 6, id: -2, animacion_continua: true};
    let nenufar2 = {ximagen:0, yimagen:0,x: 435, y: canvas.height - 296, altura: 48, ancho: 48, altoimagen: 48, anchoimagen: 48, imagen: imagenes.Nenúfar_N1, estado: "quieto", contador: 0, contador_limite: 6, id: -1, animacion_continua: true};
    let pocion1 = {ximagen:0, yimagen:0,x: 400, y: canvas.height - 350, altura: 48, ancho: 48, altoimagen: 48, anchoimagen: 48, imagen: imagenes.ColeccionableAlma_N1, estado: "quieto", contador: 0, contador_limite: 6, id: -3, animacion_continua: true};

    let proyectiles = [];

    let obstaculos = [pared1, pared2, antisuicidio1, antisuicidio2, piso, piso2, pisoagua, plataforma1, plataforma2, plataforma3, caja1, caja2, caja3, plataforma4, pared];
    let objetos = [nenufar1, tabla, nenufar2, pocion1];
    let obstaculos_daño = [agua];

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
        else if(jugador.daño_aux < 0 && jugador.estado != "daño")
        {
            jugador.vida += 1;
            jugador.daño_aux += 1;
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

    function ataque_especial(personaje)
    {
            switch(clasee)
            {
                case "Guerrero":
                    hitbox.fillStyle = "rgba(0,255,0,0.5)";
                    if (personaje.orientado == 1)
                    {
                        hitbox.fillRect(personaje.x, personaje.y + 20 + 10, 70, 60);
                    }
                    else if (personaje.orientado == -1)
                    {
                        hitbox.fillRect(personaje.x - 30, personaje.y + 20 + 10, 70, 60);
                    }
                    snd_golpe_guerrero.play();
                    personaje.contador_ataque -= 1;
                break;

                case "Vampiro": 
                break;

                case "Arquero":
                    let flecha = new proyectil(jugador.x, jugador.y + 20, 48, 48, jugador.id, flecha_exp, fin, jugador.orientado, 0);
                    proyectiles.push(flecha);
                break;


                case "Mago":
                break;

                case "Golem":
                break;

                case "Ninja":
                break;
            }
    }

    function dibujar(contexto)
    {
        //ctx.fillStyle = "rgb(100,100,100)";
        //ctx.fillRect(0,0,canvas.width,canvas.height);
        //ctx.drawImage(nivel1, 0, 0, 3000, 960, 0, 0, canvas.width, canvas.height);
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
        for (let i = 0; i < objetos.length; i++)
        {
            hitbox.fillStyle = "black";
            dibujar_objeto(objetos[i]);
            ctx.drawImage(objetos[i].imagen[objetos[i].estado], objetos[i].ximagen * objetos[i].anchoimagen, 0, objetos[i].anchoimagen, objetos[i].altoimagen, objetos[i].x - 14, objetos[i].y - 18, objetos[i].ancho, objetos[i].altura);
        }
        for (let i = 0; i < proyectiles.length; i++)
        {
            contexto.fillStyle = "black";
            dibujar_proyectil(proyectiles[i],contexto);
        }
        //ctx.drawImage(nivel1_adelante, 0, 0, 3000, 960, 0, 0, canvas.width, canvas.height);
        /*hud_ctx.fillStyle = "black";
        hud_ctx.strokeStyle = "white";
        hud_ctx.lineWidth = 2;
        hud_ctx.strokeText(jugador.vida, 70, 30);
        hud_ctx.fillText(jugador.vida, 70, 30);*/
        if (jugador.vida <= (estadisticas[clasee].vida * 45) / 100 && jugador.vida > (estadisticas[clasee].vida * 20) / 100)
        {
            if (vida_contador < 9)
            {
                vida_contador++;
                hud_ctx.fillStyle = "#ff9c9c";
                hud_ctx.fillRect(19,20,((38 * jugador.vida) / estadisticas[clasee].vida),7);
                hud_ctx.drawImage(barra_vida, 10, 0, 48, 48);     
            }
            else
            {
                vida_contador++;
                hud_ctx.fillStyle = "#ff4c4c";
                hud_ctx.fillRect(19,20,((38 * jugador.vida) / estadisticas[clasee].vida),7);
                hud_ctx.drawImage(barra_vida, 10, 0, 48, 48);
                if (vida_contador >= 19)
                {
                    vida_contador = 0;
                }
            }
            
        }
        else if (jugador.vida <= (estadisticas[clasee].vida * 20) / 100)
        {
            if (vida_contador < 4)
            {
                vida_contador++;
                hud_ctx.fillStyle = "#ff9c9c";
                hud_ctx.fillRect(19,20,((38 * jugador.vida) / estadisticas[clasee].vida),7);
                hud_ctx.drawImage(barra_vida, 10, 0, 48, 48);     
            }
            else
            {
                vida_contador++;
                hud_ctx.fillStyle = "#ff4c4c";
                hud_ctx.fillRect(19,20,((38 * jugador.vida) / estadisticas[clasee].vida),7);
                hud_ctx.drawImage(barra_vida, 10, 0, 48, 48);
                if (vida_contador >= 7)
                {
                    vida_contador = 0;
                }
            }
        }
        else
        {
            hud_ctx.fillStyle = "#ff4c4c";
            hud_ctx.fillRect(19,20,((38 * jugador.vida) / estadisticas[clasee].vida),7);
            hud_ctx.drawImage(barra_vida, 10, 0, 48, 48);
        }
        
        
      /*  for (let i = 0; i < obstaculos_daño.length; i++)
        {
            contexto.fillStyle = "rgba(0,255,0,0.5)";
            contexto.clearRect(obstaculos_daño[i].x, obstaculos_daño[i].y, obstaculos_daño[i].ancho, obstaculos_daño[i].altura);
            dibujar_obstaculo(obstaculos_daño[i],contexto);
        }*/
    }
    function dibujar_proyectil(a,contexto)
    {
        contexto.save();
        if(a.orientado == -1)
        {
            contexto.scale(-1, 1); // Invierte horizontalmente
            contexto.translate(-a.ancho - a.x * 2, 0);
        }
        contexto.drawImage(a.imagen, a.ximagen * a.anchoimagen, a.yimagen * a.altoimagen, a.anchoimagen, a.altoimagen, a.x, a.y, a.ancho, a.altura);
        contexto.restore();
        hitbox.fillStyle = "rgba(0,255,0,0.5)";
        hitbox.fillRect(a.x +20, a.y + 20,20, 20);
    }

    function dibujar_objeto(a)
    {
        if (a.id == -3)
        {
            hitbox.fillStyle = "rgb(255,255,64)";
        }
        hitbox.fillRect(a.x,a.y,20,a.altura);
        //cambiar(a,1);
        //console.log("dibujando");
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
        /*if (!(personaje.sprite instanceof HTMLImageElement)) {
            console.error("Sprite inválido en personaje:", personaje);
            return;
        }*/
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
        else if(a.estado == "especial" && a.contador == 3 && a.ximagen == 5)
        {
            ataque_especial(a);
        }
    }

    function cambiar(a, b)
    {
        switch(b)
        {
            case 1:
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
            break;

            case -1:
                if (a.id < 0)
                {
                    a.ximagen = 0;
                }
            else
            {
                    if(a.contador >= a.contador_limite)
                    {
                        a.contador = 0;
                        a.ximagen++;
                        if(a.ximagen >= a.imagen.naturalWidth / 48 && a.animacion_continua)
                        {
                            a.ximagen = 0;
                        }
                    }
                    else
                    {
                        a.contador++;
                    }
                    if(a.animacion_continua == false && a.ximagen == a.imagen.naturalWidth && a.contador == a.contador_limite)
                    {
                        proyectiles.splice(proyectiles.indexOf(a), 1);
                    }
            }
            break;
        }
    }

    


    function dibujar_obstaculo(obstaculo,contexto)
    {
        //contexto.fillStyle = "rgba(0,0,0,0.5)";
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
        let colisiones = {abajo: true, arriba: true, izquierda: true, derecha: true};
        if(porcion.id > 0)
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
            
                for (let i = 0; i < obstaculos_daño.length; i++)
                {
                    hitbox.fillStyle = "rgba(0,255,0,0.5)";
                    hitbox.clearRect(obstaculos_daño[i].x, obstaculos_daño[i].y, obstaculos_daño[i].ancho, obstaculos_daño[i].altura);
                    dibujar_obstaculo(obstaculos_daño[i],hitbox);
                }

                for (let i = 0; i < obstaculos.length; i++)
                {
                    hitbox.fillStyle = "black";
                    dibujar_obstaculo(obstaculos[i], hitbox);
                }
                
                for (let i = 0; i < objetos.length; i++)
                {
                    hitbox.fillStyle = "black";
                    //hitbox.clearRect(objetos[i].x, objetos[i].y, objetos[i].ancho, objetos[i].altura);
                    dibujar_objeto(objetos[i]);
                }
                for (let i = 0; i < proyectiles.length; i++)
                {
                    hitbox.fillStyle = "black";
                    if(proyectiles[i].id != porcion.id)
                    {
                        dibujar_proyectil(proyectiles[i],hitbox);
                    }
                }
                let pixeles = hitbox.getImageData(porcion.x, porcion.y, porcion.ancho, porcion.altura).data;
                porcion.x -= porcion.velocidadx;
                porcion.y -= porcion.velocidady;
                colisiones = {abajo: true, arriba: true, izquierda: true, derecha: true};
                for(let i = 0; i < pixeles.length;i+=4)
                {
                    if (pixeles[i] != porcion.base[i] || porcion.base[i+1] != pixeles[i+1] || porcion.base[i+2] != pixeles[i+2])
                    {
                        let pixelIndex = i / 4;
                        let x = pixelIndex % porcion.ancho;
                        let y = Math.floor(pixelIndex / porcion.ancho);


                        // ARRIBA / ABAJO
                        if /*(*/(porcion.base[i+2] == 255 && porcion.base[i+1] == 0 && porcion.base[i] == 0 )
                        {
                            if (pixeles[i] == 0 && pixeles[i+1] == 0 && pixeles[i+2] == 0 )
                            {
                                colisiones.arriba = false;
                            }      
                        }
                        else if(porcion.base[i+2] == 0 && porcion.base[i+1] == 0 && porcion.base[i] == 255 )
                        {
                        if (pixeles[i] == 0 && pixeles[i+1] == 0 && pixeles[i+2] == 0 )
                            {
                                colisiones.abajo = false;   
                            }
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
                                        porcion.daño_aux = Math.floor(((2 * jugador.ataque)/5) + 2 * jugador.critico * (jugador.ataque / esqueletodiabolico1.defensa) / 50 + 2);
                                    }
                                    else
                                    {
                                        esqueletodiabolico1.critico = Math.floor(Math.random() * 2) + 1;
                                        porcion.daño_aux = Math.floor(((2 * esqueletodiabolico1.ataque)/5) + 2 * esqueletodiabolico1.critico * (esqueletodiabolico1.ataque / jugador.defensa) / 50 + 2);
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
                                else if (pixeles[i] == 255 && pixeles[i+1] == 255 && pixeles[i+2] == 64)
                                {
                                    if (jugador.daño_aux == 0)
                                    {
                                        for (i = 0; i < objetos.length; i++)
                                        {
                                            if ((jugador.orientado == 1 && objetos[i] >= jugador.x && objetos[i] <= jugador.ancho) || (jugador.orientado == -1 && objetos[i] <= jugador.x && objetos[i] >= jugador.ancho))
                                            {
                                                objetos.splice(objetos[i].id-1, 1);
                                                break;
                                            }
                                        }
                                        if (jugador.vida + 7 <= estadisticas[clasee].vida)
                                        {
                                            jugador.daño_aux = -7;
                                        }
                                        else
                                        {
                                            jugador.daño_aux = estadisticas[clasee].vida - jugador.vida;
                                        }
                                    }                   
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
                                        esqueletodiabolico1.critico = Math.floor(Math.random() * 2) + 1;
                                        porcion.daño_aux = Math.floor(((2 * esqueletodiabolico1.ataque)/5) + 2 * esqueletodiabolico1.critico * (esqueletodiabolico1.ataque / jugador.defensa) / 50 + 2);
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
                            else if (pixeles[i] == 255 && pixeles[i+1] == 255 && pixeles[i+2] == 64)
                                {
                                    if (jugador.daño_aux == 0)
                                    {
                                        for (i = 0; i < objetos.length; i++)
                                        {
                                            if ((jugador.orientado == 1 && objetos[i] >= jugador.x && objetos[i] <= jugador.ancho) || (jugador.orientado == -1 && objetos[i] <= jugador.x && objetos[i] >= jugador.ancho))
                                            {
                                                objetos.splice(objetos[i].id-1, 1);
                                                break;
                                            }
                                        }
                                        if (jugador.vida + 7 <= estadisticas[clasee].vida)
                                        {
                                            jugador.daño_aux = -7;
                                        }
                                        else
                                        {
                                            jugador.daño_aux = estadisticas[clasee].vida - jugador.vida;
                                        }
                                    }                        
                                }
                        }
                        
                    }
                }
        }
        else
        {
            let pixeles = hitbox.getImageData(porcion.x, porcion.y, porcion.ancho, porcion.altura).data;
            colisiones = {arriba: true};
                for(let i = 0; i < pixeles.length;i+=4)
                {
                        // ARRIBA / ABAJO
                        if /*(*/(pixeles[i+2] != 0 || pixeles[i+1] == 0 || pixeles[i] == 0 )
                        {
                            colisiones.arriba = false;  
                        }
                    }
                }
        
        
        //console.log(colisiones.abajo,porcion.x, porcion.y, porcion.ancho, porcion.altura, porcion.velocidady/* + "    " + colisiones.izquierda*/);
       
        //hitbox.clearRect (0,0,canvas.width, canvas.height);
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
        if(Math.abs(enemigo.x - jugador.x) + Math.abs(enemigo.y - jugador.y) <= 60 && (enemigo.estado == "caminando" || enemigo.estado == "salto" || enemigo.estado == "quieto") && enemigo.delay_ataque == 0)
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

    function mover_proyectil(objeto)
    {
        let aux = objeto.velocidadx;
        objeto.velocidadx = 0;
        if(revisar_porcion(objeto).izquierda && revisar_porcion(objeto).izquierda/* true*/)
        {
            objeto.velocidadx = aux;
        }
        else if(objeto.animacion_continua != false)
        {
            objeto.imagen = objeto.imagen_fin;
            objeto.animacion_continua = false;
            objeto.ximagen = 0;
            objeto.contador_limite = 2;
        }
        objeto.x+=objeto.velocidadx * objeto.orientado;
        cambiar(objeto, -1);

    }

    function loop()
    {
        if (jugador.vida <= 0)
       {
        window.location.href = "index.php";
       }
       else
       {
         //console.log(jugador.estado);
        //musica.play();
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
        for (let i = 1; i < objetos.length; i++)
        {
            cambiar(objetos[i], -1);
        }
        for (let i = 1; i < proyectiles.length; i++)
        {
            mover_proyectil(proyectiles[i]);
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

        if (jugador.x - 300 > 0 && jugador.x - 300 < canvas.width - 450)
        {
            camarax_aux = jugador.x;
        }
        else if (jugador.x - 300 <= 0)
        {
            camarax_aux = 300;
        }
        else if (jugador.x - 300 > canvas.width - 450)
        {
            camarax_aux = canvas.width - 150;
        }

        hitbox.drawImage(canvas, camarax_aux - 300, camaray_aux - 300, jugador.ancho + 400, jugador.altura + 400, 0,0,canvas.width, canvas.height);
        ctx.clearRect (0,0,canvas.width, canvas.height);
        ctx.drawImage(no_se_ve, 0,0,canvas.width, canvas.height);
        hitbox.clearRect(0,0,canvas.width, canvas.height);
        //console.log(jugador.ximagen + " " + jugador.contador_limite + " " + jugador.contador);
        // console.log(personajes[0].vida);
       }
       
        requestAnimationFrame(loop);
       
    }
    loop()

</script>