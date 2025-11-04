const storyText = document.getElementById("story-text");
const continueHint = document.getElementById("continue-hint");

const typeSound = new Audio("../sonidos/tecleo.wav"); // sonido de tecleo

// Verificar si hay clase elegida
const playerClass = (localStorage.getItem("playerClass") || "").toLowerCase();
const username = localStorage.getItem("username") || "El Héroe"; // <-- tu variable

if (!playerClass) {
  window.location.href = "selec.php";
}

// Historias personalizadas
const historias = {
  guerrero: `
[Nombre de Jugador] fue un valiente y reconocido guerrero del ejército real.
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

  // ... el resto de historias (mago, arquero, etc.)
};

// Elegir historia según clase
let story = historias[playerClass] || "El destino aún no ha elegido a su héroe...";

// 🔹 Reemplazar el nombre en todo el texto
story = story
  .replaceAll("[Nombre de Jugador]", username)
  .replaceAll("[nombre]", username);

// --- efecto de tipeo ---
let i = 0;
const baseSpeed = 70; // ms por letra

function playTypeSound() {
  const click = typeSound.cloneNode();
  click.volume = 0.3;
  click.play();
}

// Botón de saltar
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
  if (e.key === "Enter") {
    window.location.href = "juego.php";
  }
}

window.onload = typeWriter;
