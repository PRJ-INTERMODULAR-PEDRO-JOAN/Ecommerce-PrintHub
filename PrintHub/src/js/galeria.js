// === CARRUSEL FUNCIONAL (PARA MÚLTIPLES INSTANCIAS) ===

(function initAllCarousels() {
  
  // 1. Busca TODOS los carruseles en la página
  const carousels = document.querySelectorAll('.carousel');
  
  if (!carousels.length) return; // Si no hay, no hagas nada

  // 2. Itera sobre cada carrusel encontrado
  carousels.forEach(carousel => {
    
    // 3. Ejecuta la lógica original para CADA carrusel
    //    Usando 'carousel.querySelector' para buscar solo DENTRO de este carrusel
    
    const viewport = carousel.querySelector('.carousel-viewport');
    const track = carousel.querySelector('.carousel-track');
    const slides = Array.from(track.querySelectorAll('.slide'));
    const prevBtn = carousel.querySelector('.carousel-btn.prev');
    const nextBtn = carousel.querySelector('.carousel-btn.next');
    const dotsContainer = carousel.querySelector('.dots');

    if (!viewport || !track || slides.length === 0 || !dotsContainer) return;

    // Estas variables ahora son locales para CADA carrusel
    let current = 0;
    let autoplayTimer = null;
    const AUTOPLAY_DELAY = 3500;

    // Crear dots
    slides.forEach((_, i) => {
      const dot = document.createElement('button');
      dot.className = 'dot';
      if (i === 0) dot.classList.add('active');
      dotsContainer.appendChild(dot);
    });
    const dots = Array.from(dotsContainer.children);

    function updatePosition(animate = true) {
      slides.forEach((s, i) => s.classList.toggle('active', i === current));
      dots.forEach((d, i) => d.classList.toggle('active', i === current));

      const currentSlide = slides[current];
      const slideCenter = currentSlide.offsetLeft + currentSlide.offsetWidth / 2;
      const viewportCenter = viewport.offsetWidth / 2;
      const translateX = viewportCenter - slideCenter;

      if (!animate) {
        track.style.transition = 'none';
        track.style.transform = `translateX(${translateX}px)`;
        void track.offsetWidth;
        track.style.transition = '';
      } else {
        track.style.transform = `translateX(${translateX}px)`;
      }
    }

    function goTo(index) {
      current = ((index % slides.length) + slides.length) % slides.length;
      updatePosition(true);
    }
    
    // Autoplay (funciones también locales)
    function startAutoplay() {
      stopAutoplay();
      autoplayTimer = setInterval(() => goTo(current + 1), AUTOPLAY_DELAY);
    }
    function stopAutoplay() {
      if (autoplayTimer) {
        clearInterval(autoplayTimer);
        autoplayTimer = null;
      }
    }
    function restartAutoplay() {
      stopAutoplay();
      startAutoplay();
    }

    // --- Asignar Eventos ---
    if (nextBtn) nextBtn.addEventListener('click', () => { goTo(current + 1); restartAutoplay(); });
    if (prevBtn) prevBtn.addEventListener('click', () => { goTo(current - 1); restartAutoplay(); });
    dots.forEach((dot, i) => dot.addEventListener('click', () => { goTo(i); restartAutoplay(); }));

    window.addEventListener('resize', () => updatePosition(false));

    carousel.addEventListener('mouseenter', stopAutoplay);
    carousel.addEventListener('mouseleave', startAutoplay);

    updatePosition(false);
    startAutoplay();

  });
  
})();

// Selección de elementos
const barraLateral = document.querySelector(".barra-lateral");
const botonAlternar = document.querySelector(".alternar-menu");

// Toggle del menú lateral (abrir/cerrar con el mismo botón)
if (botonAlternar && barraLateral) {
  botonAlternar.addEventListener("click", () => {
    barraLateral.classList.toggle("activa");
  });
}

// Dropdown del menú lateral
const botonDesplegable = document.querySelector(".desplegable"); // Selecciona el <li>

if (botonDesplegable) {
  botonDesplegable.addEventListener("click", (e) => {
    
    // Solo previene la navegación si se hace clic en el enlace (<a>)
    if (e.target.tagName === 'A') {
        e.preventDefault(); 
    }

    // Busca el contenido desplegable DENTRO del <li>
    const contenidoDesplegable = botonDesplegable.querySelector(".contenido-desplegable"); 
    
    if (contenidoDesplegable) {
      contenidoDesplegable.classList.toggle("mostrar");
    }
  });
}

// Mensaje botones productos
document.querySelectorAll('.boton').forEach(button => {
  button.addEventListener('click', () => {
    alert('Estàs veient més informació del producte!');
  });
});

// --- SECCIÓN DEL CARRUSEL ELIMINADA ---