

const storyText = document.getElementById("story-text");
const continueHint = document.getElementById("continue-hint");

const typeSound = new Audio("../sonidos/tecleo.wav");//sonido de tecleo

//verificar si hay clase elegida
const playerClass = (localStorage.getItem("playerClass") || "").toLowerCase();
const username = localStorage.getItem('username') ?? "El Héroe"; 
console.log (username);

if (!playerClass) {
  window.location.href = "selec.php";
}

//historias personalizadas
const historias = {
  guerrero: `Specter – El Devorador de Almas

Un espectro maligno que vaga entre los límites del mundo de los vivos y los muertos.
Specter se alimenta de las almas errantes que se extravían en su camino hacia el más allá, condenándolas a un tormento eterno.

En su infinita hambre, fue tentado por el poder del Maligno Khaos, quien le prometió un banquete eterno de almas a cambio de su lealtad.
Ahora, Specter sirve como su sombra, un peón sin voluntad propia en el tablero del caos.

Aquel que cruce su dominio deberá enfrentarse no solo a su furia espectral…
sino también al peso de las almas que lo acompañan en su condena.
`};

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