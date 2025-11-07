<?php
$json = json_decode(file_get_contents("partidas.json"), true);
$partida = $_POST['partida'] ?? 0;

        $xinicio = 79;
        $yinicio = 555;
        //$clase = "Guerrero";

    //$xinicio = $j['x'] ?? 22;
    //$yinicio = $j['y'] ?? 400;
    $clase = $_POST['personaje'] ?? "Guerrero";
    $clase = ucfirst(strtolower($clase));

?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Khaos Doom - Juego</title>
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

    #menuPausa 
    {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 9999;
    }

    #iframePausa 
    {
        width: 100%;
        height: 100%;
        border: none;
    }

  </style>
</head>
<body>
  <div class="contenedor">
    <canvas id="no_se_ve"></canvas>
    <canvas id="juego"></canvas>
    <canvas id= "hud"></canvas>
  </div>
  <div id="menuPausa" style="display:none;">
  <iframe src="menu.php" id="iframePausa" frameborder="0"
    style="width:100%; height:100vh; border:none;"></iframe>
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
    const menuPausa = document.getElementById("menuPausa");


    class proyectil
    {
        constructor (x, y, ancho, altura,id, imagen, imagen_fin, orientado, velocidady,altura_hitbox)
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
            this.altura_hitbox = altura_hitbox;
            this.imagen = imagen;
            this.imagen_fin = imagen_fin;
        }
    }
    class laser
    {
        constructor (x, y, ancho, altura,id, imagen, imagen_fin, orientado, velocidady, habilitado, velocidadx,altura_hitbox)
        {
            this.velocidadx = 0;//velocidadx;
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
            this.ancho = 3;
            this.altura = altura;
            this.id = id;
            this.imagen = imagen;
            this.imagen_fin = imagen_fin;
            this.habilitado = habilitado;
            this.altura_hitbox = altura_hitbox;
        }
        mover () 
        {
            this.ancho += 5*this.orientado;
        }
    }
    class magia
    {
        constructor (x, y, ancho, altura,id, imagen, imagen_fin, orientado, velocidady,altura_hitbox)
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
            this.altura_hitbox;
        }
    }
   
    ctx.fillStyle = 'red';
    ctx.fillRect(0,0,100,100);
    const musica = new Audio('cancion-rpg.wav');
    musica.volume = 0.5;
    const snd_salto = new Audio('sonidos/salto.wav');
    const snd_daño = new Audio('sonidos/daño.wav');
    const snd_golpe_guerrero = new Audio('sonidos/golpe-guerrero.wav');
    const snd_pocion = new Audio('sonidos/snd-curarse.mp3');

    canvas.width = 3000;
    canvas.height = 960;
    no_se_ve.width = canvas.width;
    no_se_ve.height = canvas.height;
    let clasee = "<?php echo $clase;?>";
    //console.log(clasee);
    let partida = <?php echo $partida;?>;
    //clasee = "Admin";
    let xinicio = <?php echo $xinicio?>;
    let yinicio = <?php echo $yinicio?>;
    let pausa = false;
    let rutaBase = 'sprites/clases/';          //Creo una constante con una parte de las rutas de las imagees
    let clases = ['Arquero', 'Golem', 'Guerrero', 'Mago', 'Ninja', 'Vampiro'];    
    let accion = { quieto: ' Quieto', caminando: " Caminando", daño: " Daño", salto: " Salto", ataque: " Ataque-Melee", especial: " Ataque-Especial"};   //  "personajes" y "accion" se usan en la asignacion dinamica de las rutas de las imagenes

    let barra_vida = new Image();
    barra_vida.src = "sprites/Barra de Vida.png";
    let barra_xp = new Image();
    barra_xp.src = "sprites/Barra de Experiencia.png";
    let vida_contador = 0;

    let objetos_nivel1 = ['Nenúfar_N1', 'Tabla_N1', 'NenúfarFlor_N1', 'ColeccionableAlma_N1', 'PócimaCuración'];
    let objetos_accion = {animacion: ' (animación)', quieto: ' (quieto)'};

   let xp_aux1 = 0; //guarda el xp necesario
   let xp_aux2 = 0; //contador
    
    
    let nivel1 = [];

    for (let i = 1; i < 8; i++)
    {
        const img = new Image();
        img.src = `sprites/nivel1-${i}.webp`;
        nivel1.push(img);
    }

    let fondo_ximagen = 0;
    let fondo_contador = 0;


    let nivel1_adelante = new Image();
    nivel1_adelante.src = "sprites/Nivel 1 Objetos por delante.png";

    const imagenes = { Guerrero: {}, Arquero: {}, Vampiro: {}, Ninja: {}, Mago: {}, Golem: {}, Esqueleto_Diabólico: {}, Nenúfar_N1: {}, Tabla_N1: {}, NenúfarFlor_N1: {}, ColeccionableAlma_N1: {}, PócimaCuración: {}, Pincho: {}, Pinchogrande: {}, PinchoAlt: {}, Faro: {}};   // creo el objeto donde guardare las imagenes
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

    let shuriken = new Image();
    shuriken.src = "sprites/clases/Shuriken (Ataque-Especial-Ninja).png";

    promesasCarga.push(new Promise(res => {
        shuriken.onload = res;
        shuriken.onerror = () => {
            console.error("aaaaaaaaaaaaaaaaaaaa");
            res();
        };
    }));

    let rayo_mago = new Image();
    rayo_mago.src = "sprites/clases/Rayo (Ataque-Especial-Mago).png";

    promesasCarga.push(new Promise(res => {
        rayo_mago.onload = res;
        rayo_mago.onerror = () => {
            console.error("aaaaaaaaaaaaaaaaaaaa");
            res();
        };
    }));

    let rayofin_mago = new Image();
    rayofin_mago.src = "sprites/clases/Rayo-Impacto (Ataque-Especial-Mago).png";

    promesasCarga.push(new Promise(res => {
        rayofin_mago.onload = res;
        rayofin_mago.onerror = () => {
            console.error("aaaaaaaaaaaaaaaaaaaa");
            res();
        };
    }));

    let rayo_golem = new Image();
    rayo_golem.src = "sprites/clases/Rayo (Ataque-Especial-Golem).png";

    promesasCarga.push(new Promise(res => {
        rayo_golem.onload = res;
        rayo_golem.onerror = () => {
            console.error("aaaaaaaaaaaaaaaaaaaa");
            res();
        };
    }));

    let rayofin_Golem = new Image();
    rayofin_Golem.src = "sprites/clases/Rayo-Impacto (Ataque-Especial-Gólem).png";

    promesasCarga.push(new Promise(res => {
        rayofin_Golem.onload = res;
        rayofin_Golem.onerror = () => {
            console.error("aaaaaaaaaaaaaaaaaaaa");
            res();
        };
    }));

    let shuriken_fin = new Image();
    shuriken_fin.src = "sprites/clases/Shuriken-Impacto (Ataque-Especial-Ninja).png";

    promesasCarga.push(new Promise(res => {
        shuriken_fin.onload = res;
        shuriken_fin.onerror = () => {
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
            }
        ));
        });
    }

    imagenes.Admin = {};
    imagenes.Admin = imagenes.Vampiro;

    let pinchos_img = new Image();
    pinchos_img.src = "sprites/Pincho.png";
    imagenes.Pincho = pinchos_img;

    let pinchoss_img = new Image();
    pinchoss_img.src = "sprites/Pincho2.png";
    imagenes.PinchoAlt = pinchoss_img;

    let pinchosgr_img = new Image();
    pinchosgr_img.src = "sprites/Pinchogrande.png";
    imagenes.Pinchogrande = pinchosgr_img;

    let faro_img = new Image();
    faro_img.src = "sprites/Faro.png";
    imagenes.Faro = faro_img;

    function ataque_especial(personaje)
    {
        let x = jugador.x;
        if(clasee != "Guerrero" && personaje.orientado == -1)
        {
            x -= personaje.ancho;
        }
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
                personaje.velocidadx = 10 * personaje.orientado;
            break;

            case "Vampiro": 
            break;

            case "Arquero":
                let flecha = new proyectil(x, jugador.y + 20, 48, 48, jugador.id, flecha_exp, fin, jugador.orientado, 0, 20);
                proyectiles.push(flecha);
            break;


            case "Mago":
                let pium = new laser(x+48, jugador.y + 23, 0, 14, jugador.id, rayo_mago, rayofin_mago, jugador.orientado, 0, true, 5, 20);
                proyectiles.push(pium);
            break;

            case "Golem":
                let puuuum = new laser(x+48, jugador.y + 20+28, 48, 7, jugador.id, rayo_golem, rayofin_Golem, jugador.orientado, 0, true, 5, 20);
                proyectiles.push(puuuum);
            break;

            case "Ninja":
                let estrella = new proyectil(x, jugador.y + 20, 48, 48, jugador.id, shuriken, shuriken_fin, jugador.orientado, 0, 20);
                proyectiles.push(estrella);
            break;
        }
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
            defensa: 15,
            altura: 25
        },
        "Golem":
        {
            velocidadx_max: 1,
            velocidady_max: 10,
            vida: 30,
            ataque: 5,
            defensa: 20,
            altura: 38
        },
        "Mago":
        {
            velocidadx_max: 2,
            velocidady_max: 8,
            vida: 15,
            ataque: 20,
            defensa: 5,
            altura: 25
        },
        "Arquero":
        {
            velocidadx_max: 5,
            velocidady_max: 6,
            vida: 10,
            ataque: 15,
            defensa: 6,
            altura: 25
        },
        "Ninja":
        {
            velocidadx_max: 6,
            velocidady_max: 6,
            vida: 10,
            ataque: 15,
            defensa: 5,
            altura: 25
        },
        "Vampiro":
        {
            velocidadx_max: 6,
            velocidady_max: 3,
            vida: 10,
            ataque: 10,
            defensa: 5,
            altura: 25
        },
        "Admin":
        {
            velocidadx_max: 10,
            velocidady_max: 2,
            vida: 1000,
            ataque: 1000,
            defensa: 5,
            altura: 25
        }
    };

    //clasee = "Arquero";

    let jugador = { altura_hitbox: estadisticas[clasee].altura, contador_limite: 6,orientado:1,contador: 0, ximagen: 0, yimagen: 0, anchoimagen: 48, altoimagen: 48,parado: true, x: xinicio, y: yinicio, altura:78, ancho:48, imagen: imagenes[clasee], base: [], colicion: false, id: 1, velocidadx: 0,velocidady : 0, velocidadx_max: estadisticas[clasee].velocidadx_max, velocidady_max: estadisticas[clasee].velocidady_max, saltando : false, salto : 0, estado: "quieto", animacion_continua: true, contador_ataque: 0, vida: estadisticas[clasee].vida, daño_aux: 0, ataque: estadisticas[clasee].ataque, critico: 1, defensa: estadisticas[clasee].defensa, nivel: 1, xp: 0};
    let esqueletodiabolico1 = { altura_hitbox:25, vision: 200,contador_limite: 6,orientado:1,contador: 0, ximagen: 0, yimagen: 0, anchoimagen: 48, altoimagen: 48,parado: true, x: 500, y: 555, altura:78, ancho:48, imagen: imagenes.Esqueleto_Diabólico, base: [], colicion: false, id: 2, velocidadx: 0,velocidady : 0, velocidadx_max: 2, velocidady_max: 5, saltando : false, salto : 0, estado: "quieto", animacion_continua: true, contador_ataque: 0, vida: 7, daño_aux: 0, delay_ataque: 0, ataque: 5, defensa: 3, critico: 0, xp:2};
    let esqueletodiabolico2 = { altura_hitbox:25, vision: 200,contador_limite: 6,orientado:1,contador: 0, ximagen: 0, yimagen: 0, anchoimagen: 48, altoimagen: 48,parado: true, x: 1612, y: 555, altura:78, ancho:48, imagen: imagenes.Esqueleto_Diabólico, base: [], colicion: false, id: 2, velocidadx: 0,velocidady : 0, velocidadx_max: 2, velocidady_max: 5, saltando : false, salto : 0, estado: "quieto", animacion_continua: true, contador_ataque: 0, vida: 7, daño_aux: 0, delay_ataque: 0, ataque: 5, defensa: 3, critico: 0, xp:2};
    let esqueletodiabolico3 = { altura_hitbox:25, vision: 200,contador_limite: 6,orientado:1,contador: 0, ximagen: 0, yimagen: 0, anchoimagen: 48, altoimagen: 48,parado: true, x: 2800, y: 555, altura:78, ancho:48, imagen: imagenes.Esqueleto_Diabólico, base: [], colicion: false, id: 2, velocidadx: 0,velocidady : 0, velocidadx_max: 2, velocidady_max: 5, saltando : false, salto : 0, estado: "quieto", animacion_continua: true, contador_ataque: 0, vida: 7, daño_aux: 0, delay_ataque: 0, ataque: 5, defensa: 3, critico: 0, xp:2};
    let personajes = [jugador, esqueletodiabolico1, esqueletodiabolico2, esqueletodiabolico3];

    let piso = {x:0, y:637,altura:20, ancho:655};
    let piso2 = {x: 939, y: 637, altura: 20, ancho: canvas.width};
    let pisoagua = {x:671, y:640,altura:20, ancho:256};
    let antisuicidio1 = {x:655, y:632 ,altura:5, ancho:16};
    let antisuicidio2 = {x:927, y:632,altura:5, ancho:12};
    let piso3 = {x: 2426, y: 901, altura: 20, ancho: 772};
    let techo3 = {x: 2426, y: 792, altura: 20, ancho: 772};
    let pared3 = {x: 2426, y: 792, altura: 200, ancho: 10};
   /* let piso4 = {x: 640, y: canvas.height - 292, altura: 20, ancho: 20};
    let piso5 = {x: 684, y: canvas.height - 292, altura: 20, ancho: 214};
    let piso6 = {x: 939, y: canvas.height - 292, altura: 20, ancho: canvas.width};*/
    let agua = {x: 671, y: 638, altura: 2, ancho: 256};
    let plataforma1 = {x: 62, y: 594, altura: 8, ancho: 21};
    let plataforma2 = {x: 48, y: 534, altura: 10, ancho: 29};
    let plataforma3 = {x:105, y:565, altura: 8, ancho: 28};
    let plataforma4 = {x:2509,y:511, ancho: 66, altura:8};
    let caja1 = {ancho:40, y: 598, altura: 38, x: 2419};
    let caja2 = {ancho:57, y: 579, altura:57, x: 2462};
    let caja3 = {ancho:47, y: 610, altura: 26, x: 2524};
    let pared = {x: 2574, y: 450, ancho:56, altura: 187};
    let pared1 = {x: 0, y: 0, ancho:31, altura: canvas.height};
    let pared2 = {x: canvas.width - 10, y: 0, ancho:10, altura: canvas.height};
    let nenufar1 = {ximagen:0, yimagen:0,x: 699, y: 634, altura: 48, ancho: 48, altoimagen: 48, anchoimagen: 48, imagen: imagenes.NenúfarFlor_N1, estado: "quieto", contador: 0, contador_limite: 6, id: -1, animacion_continua: true};
    let tabla = {ximagen:0, yimagen:0,x: 765, y: 634, altura: 48, ancho: 48, altoimagen: 48, anchoimagen: 48, imagen: imagenes.Tabla_N1, estado: "quieto", contador: 0, contador_limite: 6, id: -1, animacion_continua: true};
    let nenufar2 = {ximagen:0, yimagen:0,x: 835, y: 634, altura: 48, ancho: 48, altoimagen: 48, anchoimagen: 48, imagen: imagenes.Nenúfar_N1, estado: "quieto", contador: 0, contador_limite: 6, id: -1, animacion_continua: true};
    let tabla2 = {ximagen:0, yimagen:0,x: 900, y: 634, altura: 48, ancho: 48, altoimagen: 48, anchoimagen: 48, imagen: imagenes.Tabla_N1, estado: "quieto", contador: 0, contador_limite: 6, id: -1, animacion_continua: true};
    let pocion1 = {ximagen:0, yimagen:0,x: 810, y: 600, altura: 20, ancho: 20, altoimagen: 20, anchoimagen: 20, imagen: imagenes.PócimaCuración, estado: "animacion", contador: 0, contador_limite: 6, id: -3, animacion_continua: true};
    let pocion2 = {ximagen:0, yimagen:0,x: 2448, y: 570, altura: 20, ancho: 20, altoimagen: 20, anchoimagen: 20, imagen: imagenes.PócimaCuración, estado: "animacion", contador: 0, contador_limite: 6, id: -3, animacion_continua: true};
    let pincho1 = {ximagen:0, yimagen:0,x: 1130, y: 607, altura: 32, ancho: 30, altoimagen: 50, anchoimagen: 30, imagen: imagenes.Pincho, contador: 0, contador_limite: 15, id: -2, animacion_continua: true};
    let pincho2 = {ximagen:0, yimagen:0,x: 1224, y: 607, altura: 32, ancho: 30, altoimagen: 50, anchoimagen: 30, imagen: imagenes.PinchoAlt, contador: 0, contador_limite: 15, id: -2, animacion_continua: true};
    let pincho3 = {ximagen:0, yimagen:0,x: 1313, y: 607, altura: 32, ancho: 30, altoimagen: 50, anchoimagen: 30, imagen: imagenes.Pincho, contador: 0, contador_limite: 15, id: -2, animacion_continua: true};
    let pinchogrande = {ximagen:0, yimagen:0,x: 1776, y: 568, altura: 32, ancho: 63, altoimagen: 90, anchoimagen: 63, imagen: imagenes.Pinchogrande, contador: 0, contador_limite: 15, id: -4, animacion_continua: true};
    let checkpoint1 = {ximagen:0, yimagen:0,x: 1567, y: 596, altura: 42, ancho: 10, altoimagen: 60, anchoimagen: 10, imagen: imagenes.Faro, contador: 0, contador_limite: 15, id: -5, animacion_continua: false, estado: "apagado"};
    let checkpoint2 = {ximagen:0, yimagen:0,x: 2980, y: 859, altura: 42, ancho: 10, altoimagen: 60, anchoimagen: 10, imagen: imagenes.Faro, contador: 0, contador_limite: 15, id: -5, animacion_continua: false, estado: "apagado"};
    let sacrificio = {ximagen:0, yimagen:0,x: 435, y: canvas.height - 20, altura: 10, ancho: 48, altoimagen: 48, anchoimagen: 48, imagen: imagenes.Nenúfar_N1, estado: "quieto", contador: 0, contador_limite: 6, id: -1, animacion_continua: true}; //este sacrificio es para que ande el resto de objetos
    let tp1 = {x: 2988, y: 613, ancho:10, altura: 20, id: 100};

    let proyectiles = [];

    let obstaculos = [pared1, pared2, antisuicidio1, antisuicidio2, piso, piso2, piso3, techo3, pared3, pisoagua, plataforma1, plataforma2, plataforma3, caja1, caja2, caja3, plataforma4, pared];
    let objetos = [nenufar1,tabla, nenufar2, tabla2, pocion1, pocion2, pincho1, pincho2, pincho3, pinchogrande/*, checkpoint1, checkpoint2*/];
    let obstaculos_daño = [agua];
    let tps = [tp1];

    let camaray_aux = jugador.y;
    let camarax_aux = jugador.x;

    //console.log(clasee);
    hitbox.fillStyle = "black";


    function cambiar_estado ()
    {
       if (jugador.vida > 0 && pausa == false)
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
        if (e.key == "Enter")
        {
            if (pausa == false)
            {
                pausa = true;
                
            }
            else
            {
                pausa = false;
            }
        }
        else
        {
        if (e.repeat == false)
        {
            cambiar_estado();
        }
        }     
    });


    window.addEventListener("message", (e) => 
    {
        if (e.data === "reanudar")
        {
            pausa = false;
            window.focus();
        }
        else if (e.data === "salir")
        {
            window.location.href = "index.php";
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
        if(jugador.estado == "especial" && jugador.contador == 3 && jugador.ximagen == 5)
        {
            ataque_especial(jugador);
        }

        else if(jugador.daño_aux < 0 && jugador.estado != "daño")
        {
            jugador.vida += 1;
            jugador.daño_aux += 1;
        }
        /* Habria que agregar un if que verifica que no tenga colisiones abajo para todo esto, por ahora le da gravedad todo el tiempo */
           

        if (jugador.xp >= 2)
        {
            jugador.nivel += 1;
            jugador.xp -= 2;
            jugador.ataque += 1;
            jugador.defensa += 1;
            //console.log("subio de nivel");
        }

        if (xp_aux2 > 0)
        {
            xp_aux2 -= 1;
            jugador.xp += xp_aux1 / 8;
        }

       


       


       
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
        fondo_contador += 1;
        if (fondo_contador > 10)
        {
            fondo_ximagen += 1;
            fondo_contador = 0;
            if (fondo_ximagen > 6)
            {
                fondo_ximagen = 0;
            }
        }
        
        ctx.drawImage(nivel1[fondo_ximagen], 0, 0, 3000, 960, 0, 0, canvas.width, canvas.height);
        for (let i = 0; i < personajes.length; i++)
        {
            if ((personajes[i].x >= camarax_aux - 300 && personajes[i].x <= (camarax_aux - 300) + jugador.ancho + 500) && (personajes[i].y >= camaray_aux - 210 && personajes[i].y <= (camaray_aux - 210) + jugador.altura + 300))
            {
                contexto.fillStyle = "#FF0000";
                dibujar_personaje(personajes[i],contexto);
                cambiar(personajes[i], 1);
            }           
        }
        for (let i = 0; i < obstaculos.length; i++)
        {
            contexto.fillStyle = "black";
            dibujar_obstaculo(obstaculos[i],contexto);
        }
        for (let i = 0; i < objetos.length; i++)
        {
            if ((objetos[i].x >= camarax_aux - 300 && objetos[i].x <= (camarax_aux - 300) + jugador.ancho + 500) && (objetos[i].y >= camaray_aux - 210 && objetos[i].y <= (camaray_aux - 210) + jugador.altura + 300))
            {
                hitbox.fillStyle = "black";
                dibujar_objeto(objetos[i]);
                if (objetos[i].id == -2 || objetos[i].id == -4 || objetos[i].id == -5)
                {
                    ctx.drawImage(objetos[i].imagen, objetos[i].ximagen * objetos[i].anchoimagen, 0, objetos[i].anchoimagen, objetos[i].altoimagen, objetos[i].x - 14, objetos[i].y - 18, objetos[i].anchoimagen, objetos[i].altoimagen);
                }
                else
                {
                    ctx.drawImage(objetos[i].imagen[objetos[i].estado], objetos[i].ximagen * objetos[i].anchoimagen, 0, objetos[i].anchoimagen, objetos[i].altoimagen, objetos[i].x - 14, objetos[i].y - 18, objetos[i].ancho, objetos[i].altura);              
                }
            }
        }
            
        for (let i = 0; i < proyectiles.length; i++)
        {
            if ((proyectiles[i].x >= camarax_aux - 300 && proyectiles[i].x <= (camarax_aux - 300) + jugador.ancho + 500) && (proyectiles[i].y >= camaray_aux - 210 && proyectiles[i].y <= (camaray_aux - 210) + jugador.altura + 300))
            {
                contexto.fillStyle = "black";
                dibujar_proyectil(proyectiles[i],contexto);
            }
            
        }
        
        ctx.drawImage(nivel1_adelante, canvas.width - 82, 571, 82, 66);
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
        hud_ctx.fillStyle = "#00d915";
        hud_ctx.font = '7px Arial';
        hud_ctx.fillText(jugador.nivel, 36, 46);
        hud_ctx.fillStyle = "#55b15e";
        hud_ctx.fillRect(19,40,((38 * jugador.xp) / 2),7);
        hud_ctx.drawImage(barra_xp, 10, 20, 48, 48);

        if (pausa == true)
        {
            menuPausa.style.display = "block";
            musica.volume = 0.2;
        }
        else
        {
            menuPausa.style.display = "none";
            musica.volume = 0.5;
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
        if ((a.x >= camarax_aux - 300 && a.x <= (camarax_aux - 300) + jugador.ancho + 500) && (a.y >= camaray_aux - 210 && a.y <= (camaray_aux - 210) + jugador.altura + 300))
        {
            contexto.save();
            if(a.orientado == -1)
            {
                contexto.scale(-1, 1); // Invierte horizontalmente
                contexto.translate(-a.ancho - a.x * 2, 0);
            }
            contexto.drawImage(a.imagen, a.ximagen * a.imagen.naturalHeight, 0, a.imagen.naturalHeight, a.imagen.naturalHeight, a.x, a.y, a.ancho, a.altura);
            //contexto.fillRect(500+a.ximagen * a.imagen.naturalHeigh, 500, 500+a.imagen.naturalHeigh, 500+a.imagen.naturalHeigh);
            //console.log(/*a.ximagen */ a.imagen.naturalHeight + " " +  0 + " " +  a.imagen.naturalHeight + " " +  a.imagen.naturalHeight + " " +  a.x + " " +  a.y + " " + a.ancho + " "+  a.altura);
            contexto.restore();
        }
        
        
    }
    function dibujar_objeto(a)
    {
        if (a.id == -3)
        {
            hitbox.fillStyle = "rgb(255,255,64)";
            hitbox.fillRect(a.x - 15,a.y - 10,20,a.altura);
        }
        else if (a.id == -1)
        {
            hitbox.fillStyle = "rgb(0,1,0)";
            hitbox.fillRect(a.x,a.y,20,a.altura);
        }
        else if (a.id == -2 || a.id == -4)
        {
            hitbox.fillStyle = "rgba(0,255,0,0.5)";
            hitbox.fillRect(a.x, 575 - a.altura,20,a.altura);
        }
        else if (a.id == -5)
        {
            hitbox.fillStyle = "rgb(0,0,255)";
            hitbox.fillRect(a.x,a.y,a.ancho,a.altura);
        }
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
        if ((a.x >= camarax_aux - 300 && a.x <= (camarax_aux - 300) + jugador.ancho + 500) && (a.y >= camaray_aux - 210 && a.y <= (camaray_aux - 210) + jugador.altura + 300))
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
        
    }

    function cambiar(a, b)
    {
        if (pausa == false)
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
                                xp_aux1 = a.xp / jugador.nivel;
                                xp_aux2 = 8;
                                personajes.splice(personajes.indexOf(a), 1);
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
                    if (a.id == -2)
                    {
                        switch (a.ximagen)
                        {
                            case 0:
                                a.altura = 22;
                                a.contador_limite = 2;
                            break;
                            case 1:
                                a.altura = 35;
                                a.contador_limite = 2;
                            break;
                            case 2:
                                a.altura = 50;
                                a.contador_limite = 2;
                            break;
                            case 3:
                                a.altura = 50;
                                a.contador_limite = 4;
                            break;
                            case 4:
                                a.altura = 50;
                                a.contador_limite = 4;
                            break;
                            case 5:
                                a.altura = 35;
                                a.contador_limite = 4;
                            break;
                            case 6:
                                a.altura = 22;
                                a.contador_limite = 2;
                            break;
                            case 7:
                                a.altura = 9;
                                a.contador_limite = 2;
                            break;
                            case 8:
                                a.altura = 0;
                                a.contador_limite = 15;
                            break;
                            case 9:
                                a.altura = 0;
                                a.contador_limite = 60;
                            break;
                        }
                        if(a.contador >= a.contador_limite)
                        {
                            a.contador = 0;
                            a.ximagen++;
                            if(a.ximagen >= a.imagen.naturalWidth / 30)
                            {
                                a.ximagen = 0;
                                //a.altura = ;
                            }
                        }
                        else
                        {
                            a.contador++;
                        }
                    }
                    else if (a.id == -4)
                    {
                        switch (a.ximagen)
                        {
                            case 0:
                                a.altura = 0;
                                a.contador_limite = 4;
                            break;
                            case 1:
                                a.altura = 39;
                                a.contador_limite = 4;
                            break;
                            case 2:
                                a.altura = 56;
                                a.contador_limite = 4;
                            break;
                            case 3:
                                a.altura = 88;
                                a.contador_limite = 7;
                            break;
                            case 4:
                                a.altura = 88;
                                a.contador_limite = 7;
                            break;
                            case 5:
                                a.altura = 88;
                                a.contador_limite = 7;
                            break;
                            case 6:
                                a.altura = 88;
                                a.contador_limite = 7;
                            break;
                            case 7:
                                a.altura = 88;
                                a.contador_limite = 7;
                            break;
                            case 8:
                                a.altura = 54;
                                a.contador_limite = 4;
                            break;
                            case 9:
                                a.altura = 38;
                                a.contador_limite = 4;
                            break;
                            case 10:
                                a.altura = 4;
                                a.contador_limite = 7;
                            break;
                            case 11:
                                a.altura = 0;
                                a.contador_limite = 15;
                            break;
                            case 12:
                                a.altura = 0;
                                a.contador_limite = 15;
                            break;
                            case 13:
                                a.altura = 0;
                                a.contador_limite = 90;
                            break;
                        }
                        if(a.contador >= a.contador_limite)
                        {
                            a.contador = 0;
                            a.ximagen++;
                            if(a.ximagen >= a.imagen.naturalWidth / 63)
                            {
                                a.ximagen = 0;
                                //a.altura = ;
                            }
                        }
                        else
                        {
                            a.contador++;
                        }
                    }
                    else if (a.id == -5)
                    {
                        if (a.estado == "animacion")
                        {
                            if(a.contador >= a.contador_limite)
                            {
                            a.contador = 0;
                            a.ximagen++;
                                if(a.ximagen >= a.imagen.naturalWidth / 10)
                                {
                                    a.ximagen = 4;
                                    a.estado = "prendido";
                                }
                            }
                        }
                        else if (a.estado == "apagado")
                        {
                            a.ximagen = 0;
                        }
                        else if (a.estado == "prendido")
                        {
                            a.ximagen = 4;
                        }
                    }
                    else
                    {
                        if(a.contador >= a.contador_limite)
                        {
                            a.contador=0;
                            a.ximagen++;
                            if(a.id> 0)
                            {
                                if(a.ximagen >= a.imagen.naturalWidth / a.imagen.naturalHeight && a.animacion_continua)
                                {
                                    a.ximagen = 0;
                                }
                            }
                            else
                            {
                                if(a.ximagen >= a.imagen[a.estado].naturalWidth / 48 && a.animacion_continua == true)
                                {
                                    a.ximagen = 0;
                                }
                                else if(a.ximagen >= a.imagen[a.estado].naturalWidth / 48 && a.animacion_continua == false)
                                {
                                    //console.log(a.estado);
                                    a.ximagen = 0;
                                    a.estado = "quieto";
                                    a.animacion_continua = true;
                                }
                            }
                            //if(clasee != "Mago" && clasee != "Golem"){
                                
                            //}
                            
                        }
                        else
                        {
                            a.contador++;
                        }
                        //console.log(!a.animacion_continua + " && " + a.ximagen + " >= " + a.imagen.naturalWidth + " / " + a.imagen.naturalHeight);
                        if(!a.animacion_continua && a.ximagen >= a.imagen.naturalWidth/a.imagen.naturalHeight/* && a.contador == a.contador_limite*/)
                        {
                            console.log("puuuuuuum");
                            proyectiles.splice(proyectiles.indexOf(a), 1);
                        }
                    }
                break;
            }
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
    function mover_proyectil(objeto)
    {
        let aux = objeto.velocidadx;
        objeto.velocidadx = 0;
        if(revisar_porcion(objeto).izquierda && revisar_porcion(objeto).derecha && !(Math.abs(objeto.ancho) > 400))
        {
            objeto.velocidadx = aux;
        }
        else if(Math.abs(objeto.ancho) > 400)
        {
            console.log(4);
            objeto.animacion_continua = false;
            ximagen=0;
        }
        else if(objeto.animacion_continua != false)
        {
            //objeto.x -= 100 * objeto.orientado;
            objeto.imagen = objeto.imagen_fin;
            //objeto.ancho -= 100 * objeto.orientado;
            objeto.animacion_continua = false;
            objeto.ximagen = 0;
            objeto.contador_limite = 2;
        }
        objeto.x +=objeto.velocidadx * objeto.orientado;
        console.log(objeto.ancho);
        cambiar(objeto, -1);

        if(clasee == "Mago" || clasee == "Golem")
        {
            objeto.mover();
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
        for (let i = 0; i < tps.length; i++)
        {
            switch (tps[i].id)
            {
                case 100:
                    hitbox.fillStyle = "rgba(100,100,100,0.8)";
                break;
                case 101:
                    hitbox.fillStyle = "rgb(100,100,101)";
                break;
            }
            
            dibujar_obstaculo(tps[i],hitbox);
        }
        let pixeles = hitbox.getImageData(porcion.x, porcion.y, porcion.ancho, porcion.altura).data;
        porcion.x -= porcion.velocidadx;
        porcion.y -= porcion.velocidady;
        let colisiones = {abajo: true, arriba: true, izquierda: true, derecha: true};
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
                    else if (pixeles[i] == 0 && pixeles[i+1] == 1 && pixeles[i+2] == 0)
                    {
                        colisiones.abajo = false;  
                        for (let j = 0; j < objetos.length; j++)
                        {
                            if (objetos[j].id == -1)
                            {
                                if (((jugador.orientado == 1 && objetos[j].x >= jugador.x && objetos[j].x <= jugador.x + jugador.ancho) || (jugador.orientado == -1 && objetos[j].x <= jugador.x && objetos[j].x >= jugador.x - jugador.ancho)) || objetos[j].y >= jugador.y + jugador.altura)
                                {
                                    objetos[j].estado = "animacion";
                                    objetos[j].animacion_continua = false;
                                    break;
                                }
                            }  
                        }
                    }
                }
                if (y < porcion.altura - 3)
                {
                    if (x < porcion.ancho / 2 && y < porcion.altura -2)
                    {
                        if (pixeles[i] == 0 && pixeles[i+1] == 0 && pixeles[i+2] == 0)
                        {
                            colisiones.izquierda = false;
                        }
                        else if (pixeles[i] == 100 && pixeles[i+1] == 100 && pixeles[i+2] == 100 && pixeles[i+3] == 204)
                        {
                            if (porcion.id == 1)
                            {
                                jugador.x = piso3.x;
                                jugador.y = piso3.y - jugador.altura;
                                camaray_aux += 20;
                            }
                        }
                        else if(/*pixeles[i] == 0 && pixeles[i+1] == 255 && pixeles[i+2] == 0 */pixeles[i+3] == 26 && porcion.estado != "daño")
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
                            //console.log(porcion.daño_aux);
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
                            if (jugador.daño_aux >= 0)
                            {
                                if (jugador.vida + 7 <= estadisticas[clasee].vida)
                                {
                                    jugador.daño_aux = -7;
                                }
                                else
                                {
                                    jugador.daño_aux = (estadisticas[clasee].vida - jugador.vida) * -1;
                                }

                                snd_pocion.play();

                                for (let j = 0; j < objetos.length; j++)
                                {
                                    if (objetos[j].id == -3)
                                    {
                                        if (((jugador.orientado == 1 && objetos[j].x >= jugador.x && objetos[j].x <= jugador.x + jugador.ancho) || (jugador.orientado == -1 && objetos[j].x <= jugador.x && objetos[j].x >= jugador.x - jugador.ancho)) || objetos[j].y >= jugador.y + jugador.altura)
                                        {
                                            objetos.splice(objetos.indexOf(objetos[j]), 1);
                                            break;
                                        }
                                    }  
                                }
                                
                            }                  
                        }
                        else if (pixeles[i] == 0 && pixeles[i+1] == 0 && pixeles[i+2] == 255)
                        {
                            for (let j = 0; j < objetos.length; j++)
                                {
                                    if (objetos[j].id == -5)
                                    {
                                        if (((jugador.orientado == 1 && objetos[j].x >= jugador.x && objetos[j].x <= jugador.x + jugador.ancho) || (jugador.orientado == -1 && objetos[j].x <= jugador.x && objetos[j].x >= jugador.x - jugador.ancho)) || objetos[j].y >= jugador.y + jugador.altura)
                                        {
                                            if (objetos[j].estado == "apagado" && porcion.id == 1)
                                            {
                                                objetos[j].estado = "animacion";
                                                xinicio = objetos[j].x;
                                                yinicio = objetos[j].y;
                                                //console.log("se cambiooo");
                                                console.log(xinicio, " ", yinicio)
                                            }
                                            
                                            break;
                                        }
                                    }  
                                }
                        }
                    }
                    else if (x > porcion.ancho / 2 && y < porcion.altura -2)
                    {
                        if (pixeles[i] == 0 && pixeles[i+1] == 0 && pixeles[i+2] == 0)
                        {
                            colisiones.derecha = false;
                        }
                        else if (pixeles[i] == 100 && pixeles[i+1] == 100 && pixeles[i+2] == 100 && pixeles[i+3] == 204)
                        {
                            if (porcion.id == 1)
                            {
                                jugador.x = piso3.x;
                                jugador.y = piso3.y - jugador.altura;
                                camaray_aux = piso3.y - 300;
                            }
                        }
                        else if(/*pixeles[i] == 0 && pixeles[i+1] == 255 && pixeles[i+2] == 0 */pixeles[i+3] == 128 && porcion.estado != "daño")
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
                            //console.log(porcion.daño_aux);
                            porcion.velocidadx = 0;
                            porcion.contador_limite = 5;
                            porcion.contador = 0;
                            porcion.animacion_continua = false;
                            porcion.estado = "daño";
                            porcion.ximagen = 0;
                            //porcion.velocidadx -= 5;
                        }
                        else if (pixeles[i] == 255 && pixeles[i+1] == 255 && pixeles[i+2] == 64)
                        {
                            if (jugador.daño_aux >= 0)
                            {                                  
                                if (jugador.vida + 7 <= estadisticas[clasee].vida)
                                {
                                    jugador.daño_aux = -7;
                                }
                                else
                                {
                                    jugador.daño_aux = (estadisticas[clasee].vida - jugador.vida) * -1;
                                }

                                snd_pocion.play();

                                for (let j = 0; i < objetos.length; j++)
                                {
                                    if (objetos[j].id == -3)
                                    {
                                        if (((jugador.orientado == 1 && objetos[j].x >= jugador.x && objetos[j].x <= jugador.x + jugador.ancho) || (jugador.orientado == -1 && objetos[j].x <= jugador.x && objetos[j].x >= jugador.x - jugador.ancho)) || objetos[j].y >= jugador.y + jugador.altura)
                                        {
                                            objetos.splice(objetos.indexOf(objetos[j]), 1);
                                            break;
                                        }
                                    }  
                                }
                            }  
                        }
                        else if (pixeles[i] == 0 && pixeles[i+1] == 0 && pixeles[i+2] == 255)
                        {
                            for (let j = 0; j < objetos.length; j++)
                            {
                                if (objetos[j].id == -5)
                                {
                                    if (((jugador.orientado == 1 && objetos[j].x >= jugador.x && objetos[j].x <= jugador.x + jugador.ancho) || (jugador.orientado == -1 && objetos[j].x <= jugador.x && objetos[j].x >= jugador.x - jugador.ancho)) || objetos[j].y >= jugador.y + jugador.altura)
                                    {
                                        if (objetos[j].estado == "apagado" && porcion.id == 1)
                                        {
                                            objetos[j].estado = "animacion";
                                            xinicio = objetos[j].x;
                                            yinicio = objetos[j].y;
                                            // console.log("se cambiooo");
                                            console.log(xinicio, " ", yinicio)
                                        }
                                        
                                        break;
                                    }
                                }  
                            }
                        }      
                    }
                                    
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

    

    function loop()
    {
        if (pausa == true)
        {
            //musica.play();
            hitbox.clearRect (0,0,canvas.width, canvas.height);
            ctx.clearRect (0,0,canvas.width, canvas.height);
            hud_ctx.clearRect(0,0,screen.width,screen.height);

            if (jugador.y + 200 >= canvas.height)
            {
                //console.log("en teoria esta entrando aca");
                camaray_aux = canvas.height - 200;
            }
            else if (jugador.y - 200 <= 0)
            {
                //console.log("en teoria esta entrando aca");
                camaray_aux = 200;
            }
            else if (revisar_porcion(jugador).abajo == false && Math.abs(jugador.y - camaray_aux )>= 30)
            {
                if (camaray_aux > jugador.y)
                {
                    camaray_aux -= 2;
                }
                else
                {
                    camaray_aux += 2;
                }
                
            }
            else if(revisar_porcion(jugador).abajo && Math.abs(jugador.y - camaray_aux) >= 60)
            {
                camaray_aux += jugador.velocidady;
            }

            if (jugador.x - 300 <= 0)
            {
                camarax_aux = 300;
            }
            else if (jugador.x + 250 < canvas.width -250)
            {
                camarax_aux = jugador.x;
            }
            else if (jugador.x + 250 >= canvas.width - 250)
            {
                camarax_aux = canvas.width - 250;
            }
        
            //console.log(jugador.y);
           
            dibujar(ctx);
            //hitbox.clearRect (0,0,canvas.width, canvas.height);
            //console.log(camaray_aux);
            //console.log(jugador.velocidady);
        

            hitbox.drawImage(canvas, camarax_aux - 300, camaray_aux - 210, jugador.ancho + 500, jugador.altura + 300, 0,0,canvas.width, canvas.height);
            //console.log(camarax_aux - 300, " ", camaray_aux - 300, " ", jugador.ancho + 500, " ", jugador.altura + 300);
            ctx.clearRect (0,0,canvas.width, canvas.height);
            ctx.drawImage(no_se_ve, 0,0,canvas.width, canvas.height);
            hitbox.clearRect(0,0,canvas.width, canvas.height);
        } 
        else if (jugador.vida <= 0)
        {
            const form = document.createElement("form");
            form.method = "POST";
            form.action = "juego.php";

            const input = document.createElement("input");
            input.type = "hidden";
            input.name = "personaje";
            input.value = clasee;

            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        }
        else
        {
            //console.log(jugador.estado);
            
            hitbox.clearRect (0,0,canvas.width, canvas.height);
            ctx.clearRect (0,0,canvas.width, canvas.height);
            hud_ctx.clearRect(0,0,screen.width,screen.height);

            if (jugador.y + 200 >= canvas.height)
            {
                //console.log("en teoria esta entrando aca");
                camaray_aux = canvas.height - 200;
            }
            else if (jugador.y - 200 <= 0)
            {
                //console.log("en teoria esta entrando aca");
                camaray_aux = 200;
            }
            else if (revisar_porcion(jugador).abajo == false && Math.abs(jugador.y - camaray_aux )>= 30)
            {
                if (camaray_aux > jugador.y)
                {
                    camaray_aux -= 2;
                }
                else
                {
                    camaray_aux += 2;
                }
                
            }
            else if(revisar_porcion(jugador).abajo && Math.abs(jugador.y - camaray_aux) >= 60)
            {
                camaray_aux += jugador.velocidady;
            }

            if (jugador.x - 300 <= 0)
            {
                camarax_aux = 300;
            }
            else if (jugador.x + 250 < canvas.width -250)
            {
                camarax_aux = jugador.x;
            }
            else if (jugador.x + 250 >= canvas.width - 250)
            {
                camarax_aux = canvas.width - 250;
            }
        
            //console.log(jugador.y);
            moverJugador();
            for (let i = 1; i < personajes.length; i++)
            {
                if ((personajes[i].x >= camarax_aux - 300 && personajes[i].x <= (camarax_aux - 300) + jugador.ancho + 500) && (personajes[i].y >= camaray_aux - 210 && personajes[i].y <= (camaray_aux - 210) + jugador.altura + 300))
                {
                    mover_enemigos(personajes[i]);
                }
                
            }
            for (let i = 0; i < objetos.length; i++)
            {
                if ((objetos[i].x >= camarax_aux - 300 && objetos[i].x <= (camarax_aux - 300) + jugador.ancho + 500) && (objetos[i].y >= camaray_aux - 210 && objetos[i].y <= (camaray_aux - 210) + jugador.altura + 300))
                {
                    cambiar(objetos[i], -1);
                }
                
            }
            for (let i = 0; i < proyectiles.length; i++)
            {
                if ((proyectiles[i].x >= camarax_aux - 300 && proyectiles[i].x <= (camarax_aux - 300) + jugador.ancho + 500) && (proyectiles[i].y >= camaray_aux - 210 && proyectiles[i].y <= (camaray_aux - 210) + jugador.altura + 300))
                {
                    mover_proyectil(proyectiles[i]);
                }
                else
                {
                    proyectiles.splice(proyectiles.indexOf(proyectiles[i]), 1);
                }
                
            }
            dibujar(ctx);
            //hitbox.clearRect (0,0,canvas.width, canvas.height);
            //console.log(camaray_aux);
            //console.log(jugador.velocidady);
        

            hitbox.drawImage(canvas, camarax_aux - 300, camaray_aux - 210, jugador.ancho + 500, jugador.altura + 300, 0,0,canvas.width, canvas.height);
            //console.log(camarax_aux - 300, " ", camaray_aux - 300, " ", jugador.ancho + 500, " ", jugador.altura + 300);
            ctx.clearRect (0,0,canvas.width, canvas.height);
            ctx.drawImage(no_se_ve, 0,0,canvas.width, canvas.height);
            hitbox.clearRect(0,0,canvas.width, canvas.height);
            //console.log(jugador.ximagen + " " + jugador.contador_limite + " " + jugador.contador);
            // console.log(personajes[0].vida);
        }
       
        musica.play();


        requestAnimationFrame(loop);
       
    }
    Promise.all(promesasCarga).then(() => {
        loop();
    });

</script>