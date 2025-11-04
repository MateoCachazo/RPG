const storyText = document.getElementById("story-text");
const continueHint = document.getElementById("continue-hint");

const typeSound = new Audio("../sonidos/tecleo.wav");//sonido de tecleo
//historia
const story = `
Tensos años azotan al mundo.
Las tierras, antes fértiles, se marchitan bajo un cielo sin piedad.
Los cultivos mueren, el hambre se extiende, y las enfermedades se llevan a los más débiles sin dejar rastro ni explicación.
Los niños ya no juegan, los animales no comen, y los pájaros… han olvidado cómo cantar.


Dicen los rumores que hay un origen para tanta desdicha
No un hombre.
No un dios.
Sino algo más antiguo, más profundo… una idea.
Una representación del odio, del caos y del mal puro que ha habitado el mundo desde el principio de los tiempos.


Nadie lo ha visto, al menos no con vida para contarlo.
Pero los relatos coinciden en su forma:
una sombra colosal, tan densa y poderosa que incluso el más valiente de los guerreros ha rehusado buscar su cabeza.

Y así, el mundo continúa sin rumbo, sobreviviendo apenas a las noches cada vez más largas y a los inviernos aún más fríos.

Los rumores se vuelven relatos, y los relatos, advertencias.
Criaturas extrañas emergen de la tierra o aparecen como invocadas por artes prohibidas.
El miedo se propaga, y la esperanza se apaga poco a poco.

Ya no hay duda alguna…
Todo esto es obra de él.
De Khaos.

Pero una pregunta permanece, resonando en la oscuridad del mundo:
¿Quién será lo suficientemente poderoso… y lo bastante valiente…
para desafiar su terror absoluto?
`;

let i = 0;
const baseSpeed = 70;//ms por letra

function playTypeSound() {
  //clona el audio para que pueda reproducirse varias veces seguidas sin cortar
  const click = typeSound.cloneNode();
  click.volume = 0.3;//más suave
  click.play();
}
//Botón de saltar
document.getElementById("skip-btn").addEventListener("click", skipStory);
function skipStory() {
  //Detener el efecto de escritura
  i = story.length;
  storyText.innerHTML = story;
  continueHint.classList.remove("hidden");
  document.addEventListener("keydown", handleKey);
}

function typeWriter() {
  if (i < story.length) {
    const currentChar = story.charAt(i);
    storyText.innerHTML += currentChar;

    storyText.scrollTop = storyText.scrollHeight;//scrolea solo la historia

    let delay = baseSpeed;

    //pausas naturales
    if (".?!…".includes(currentChar)) {
      delay = 500;//pausa con . ? ! ...
    } else if (",;".includes(currentChar)) {
      delay = 150;//pausa mas corta con ,
    } else if (currentChar !== " " && currentChar !== "\n") {
      playTypeSound();//solo suena si es una letra visible
    }

    i++;
    setTimeout(typeWriter, delay);
  } else {
    continueHint.classList.remove("hidden");
    document.addEventListener("keydown", handleKey);
  }
}

function handleKey(e) {
  if (e.key === "Enter") {
    window.location.href = "selec.php";
  }
}

window.onload = typeWriter;