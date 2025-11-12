const storyText = document.getElementById("story-text");
const continueHint = document.getElementById("continue-hint");

const typeSound = new Audio("../sonidos/tecleo.wav");//sonido de tecleo
//historia
const story = `
¡Muchas Gracias Por Jugar!
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
  //detiene el efecto de escritura
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
    window.location.href = "index.php";
  }
}

window.onload = typeWriter;