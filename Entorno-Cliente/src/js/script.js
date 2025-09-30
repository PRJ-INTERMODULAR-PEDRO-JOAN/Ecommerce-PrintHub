// Selección de elementos
const sidebar = document.querySelector(".sidebar");
const toggleBtn = document.querySelector(".menu-toggle");

// Toggle del menú lateral (abrir/cerrar con el mismo botón)
toggleBtn.addEventListener("click", () => {
  sidebar.classList.toggle("active");
});

// Dropdown del menú lateral
const dropdownBtn = document.querySelector(".dropbtn");
dropdownBtn.addEventListener("click", (e) => {
  e.preventDefault(); // Evita que el enlace navegue
  const dropdownContent = dropdownBtn.nextElementSibling;
  dropdownContent.classList.toggle("show");
});

// Missatge botons productes
document.querySelectorAll('.btn').forEach(button => {
  button.addEventListener('click', () => {
    alert('Estàs veient més informació del producte!');
  });
});

// Selección del carrusel
const track = document.querySelector('.carousel-track');
const slides = Array.from(track.children);
const nextButton = document.querySelector('.carousel-btn.next');
const prevButton = document.querySelector('.carousel-btn.prev');

let currentSlide = 0;

// Función para actualizar la posición
function updateSlide() {
  const slideWidth = slides[0].getBoundingClientRect().width;
  track.style.transform = `translateX(-${slideWidth * currentSlide}px)`;
}

// Botón siguiente
nextButton.addEventListener('click', () => {
  currentSlide = (currentSlide + 1) % slides.length;
  updateSlide();
});

// Botón anterior
prevButton.addEventListener('click', () => {
  currentSlide = (currentSlide - 1 + slides.length) % slides.length;
  updateSlide();
});
