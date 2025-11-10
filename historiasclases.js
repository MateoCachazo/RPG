

const storyText = document.getElementById("story-text");
const continueHint = document.getElementById("continue-hint");

const typeSound = new Audio("../sonidos/tecleo.wav");//sonido de tecleo

//verificar si hay clase elegida
const playerClass = (localStorage.getItem("playerClass") || "").toLowerCase();
const username = <?php echo $username ?>; 

if (!playerClass) {
  window.location.href = "selec.php";
}

//historias personalizadas
const historias = {
  guerrero: `[Nombre de Jugador] fue un valiente y reconocido guerrero del ejército real.
 Amado por su pueblo y respetado por sus camaradas, servía con honor y humildad.
 No buscaba gloria ni reconocimiento… solo cumplía órdenes, como todo buen soldado.

Pero más allá del acero y la disciplina, [nombre] también tenía un corazón.
 Un corazón que, sin querer, se rindió ante la belleza de la princesa Varyntha Valenroth, hija del gran Rey Tygor.
 Pese a su noble alma y su valentía, [Nombre de Jugador] sabía que jamás podría aspirar a ella.
 Su linaje no era digno de una dama de sangre real.

Aun así, nunca dejó de soñar.
 Y cuando las noticias del regreso del Aterrador Khaos sacudieron el reino, [nombre] vio en ello una oportunidad:
 no de huir, sino de probar su valía… de hacer algo tan grande que ni la realeza pudiera ignorarlo.

Así, vistió su armadura una vez más, empuñó su espada con determinación y juró enfrentarse al mismísimo Khaos.
 No por el reino.
 No por la gloria.
 Sino por amor.
`,
    //**************************************************************************** */
  mago: `[Nombre de Jugador] era un poderoso y respetado mago en tiempos antiguos. 
  Durante años, su sabiduría y dominio de la magia lo convirtieron en una leyenda viviente.

Sin embargo, el paso del tiempo lo llevó a abandonar su bastón y a vivir en paz como un hombre común, 
dedicado a su familia y hogar.

En sus días de gloria, había escuchado incontables relatos sobre el Todopoderoso y Temible Khaos, 
un ser cuyo poder desafiaba toda lógica. Cuando las noticias de su regreso estremecieron la Tierra, 
[Nombre de Jugador] sintió cómo la llama de su magia volvía a encenderse en su interior.

Ahora, decidido a enfrentar su destino una última vez, [Nombre de Jugador] deja atrás su retiro 
para vivir su última aventura: Frustrar los planes de Khaos y asegurarse de que su nombre sea recordado 
por toda la eternidad…
`,
  //**************************************************************************** */
  arquero: `[Nombre de Jugador] fue alguna vez el mejor arquero del ejército real.
 Sus flechas nunca erraban, y su fama se extendía por todo el reino. Pero el talento sin 
 humildad se convierte en un arma de doble filo: la soberbia y la indisciplina de [nombre] 
 le costaron su lugar en la armada.

Humillado y sin propósito, [Nombre de Jugador] se perdió entre los vicios, olvidando el arco 
que alguna vez lo hizo leyenda.
 Hasta que un día, los rumores sobre el regreso del Tenebroso Khaos resonaron en las tabernas 
 y campos de batalla.

Por primera vez en años, sintió que su corazón volvía a latir con fuerza.
 Sabía que esta era su oportunidad para redimirse, para demostrar que aún quedaba fuego en su 
 mirada y precisión en sus manos.

Con el amanecer como testigo, [Nombre de Jugador] tomó su arco, llenó su carcaj y partió hacia 
el horizonte… en busca de la cabeza de Khaos.`,
   //**************************************************************************** */
  vampiro: `[Nombre de Jugador] es un vampiro solitario, condenado a vivir en las lejanías del mundo.
 No por elección… sino por miedo.
 Las leyendas, los rumores y los relatos de terror lo convirtieron en un monstruo ante los ojos de todos. Ninguna aldea lo aceptaba. Ningún alma se atrevía a pronunciar su nombre sin temblar.

Solo sus fieles compañeros, los murciélagos que habitaban su castillo, permanecían a su lado.
 Una noche, entre susurros y aleteos, le hablaron del regreso de una criatura aún más temida:
 Khaos.

Por primera vez en siglos, [Nombre de Jugador] vio una oportunidad de redimirse.
 No para conquistar… sino para proteger.

 Quizás, si lograba derrotar al temible Khaos, el mundo dejaría de verlo como una sombra,
 y comenzaría a recordarlo como un héroe.

Así fue como [Nombre de Jugador] dejó atrás su castillo,
 guiado por la esperanza de obtener el amor y el respeto que siempre mereció.
`,
    //**************************************************************************** */
  golem: `[Nombre de Jugador] es el último Gólem de la Tierra, una criatura tan misteriosa como poderosa.
 Durante siglos vagó sin rumbo por los bosques oscuros de Lo Lejano, sin propósito, sin esperanza… 
 una reliquia de un mundo que ya no existe.

Una noche, mientras atormentaba a unos cazadores que habían osado invadir su territorio, escuchó algo 
que heló su corazón de piedra.
 Ellos hablaban de un ser antiguo, un nombre que hacía temblar hasta a los más valientes…

Khaos.

Ese nombre resonó en su mente una y otra vez.
 Era él. El responsable de su soledad. El asesino de su familia. El destructor de su especie.

Entonces, por primera vez en siglos, [Nombre de Jugador] sintió algo más que dolor: odio puro.
 Tomó su cuerpo de roca, endurecido por el tiempo, y emprendió su marcha.
 No por gloria. No por redención.
 Sino por venganza.

Khaos debía reencontrarse con todas las almas que había sepultado.
`,
  //**************************************************************************** */
  ninja: `
  
  [Nombre de Jugador] fue alguna vez un joven aprendiz en un pequeño dojo perdido entre las montañas.

 Sus maestros lo consideraban torpe, sin talento ni disciplina, y tras años de fracasos, 
 fue expulsado sin piedad. Aquel día, algo cambió dentro de [Nombre de Jugador].

 Mientras otros habrían abandonado, él decidió forjar su propio camino. Pasó los años entrenando 
 en secreto, perfeccionando sus técnicas a escondidas, mientras trabajaba largas noches en una taberna 
 para sobrevivir.

Cuando los rumores sobre el regreso del Temible Khaos se convirtieron en una amenaza real, 
[Nombre de Jugador] vio su oportunidad.
 No buscaba gloria ni venganza… solo demostrar que todos estaban equivocados acerca de él.

Con su vieja katana al hombro y una determinación inquebrantable en el alma, [Nombre de Jugador] dejó su hogar con un único propósito:
 derrotar a Khaos y reclamar su lugar como un verdadero ninja.`

};

//elegir historia según clase
let story = historias[playerClass] || "El destino aún no ha elegido a su héroe...";

//reemplazar el nombre en todo el texto
story = story
  .replaceAll("[Nombre de Jugador]", username)
  .replaceAll("[nombre]", username);

//efecto de tipeo
let i = 0;
const baseSpeed = 70;//ms por letra

function playTypeSound() {
  const click = typeSound.cloneNode();
  click.volume = 0.3;
  click.play();
}

//btón de saltar (como skip intro)
document.getElementById("skip-btn").addEventListener("click", skipStory);
function skipStory() {
  i = story.length;
  storyText.innerHTML = story;
  continueHint.classList.remove("hidden");
  document.addEventListener("keydown", handleKey);
}

function typeWriter() {
  if (i < story.length) {
    const currentChar = story.charAt(i);
    storyText.innerHTML += currentChar;

    storyText.scrollTop = storyText.scrollHeight;

    let delay = baseSpeed;

    if (".?!…".includes(currentChar)) delay = 500;
    else if (",;".includes(currentChar)) delay = 150;
    else if (currentChar !== " " && currentChar !== "\n") playTypeSound();

    i++;
    setTimeout(typeWriter, delay);
  } else {
    continueHint.classList.remove("hidden");
    document.addEventListener("keydown", handleKey);
  }
}

function handleKey(e) {
  if (e.key === "Enter") 
  {
    const form = document.createElement("form");
    form.method = "POST";
    form.action = "juego.php";

    const input = document.createElement("input");
    input.type = "hidden";
    input.name = "personaje";
    input.value = playerClass;

    form.appendChild(input);
    document.body.appendChild(form);
    form.submit();
  }
}

window.onload = typeWriter;