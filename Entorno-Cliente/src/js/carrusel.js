// === CARRUSEL FUNCIONAL ===
(function initCarousel() {
  const carousel = document.querySelector('.carousel');
  if (!carousel) return;

  const viewport = carousel.querySelector('.carousel-viewport');
  const track = carousel.querySelector('.carousel-track');
  const slides = Array.from(track.querySelectorAll('.slide'));
  const prevBtn = carousel.querySelector('.carousel-btn.prev');
  const nextBtn = carousel.querySelector('.carousel-btn.next');
  const dotsContainer = carousel.querySelector('.dots');

  if (!viewport || !track || slides.length === 0 || !dotsContainer) return;

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

  if (nextBtn) nextBtn.addEventListener('click', () => { goTo(current + 1); restartAutoplay(); });
  if (prevBtn) prevBtn.addEventListener('click', () => { goTo(current - 1); restartAutoplay(); });
  dots.forEach((dot, i) => dot.addEventListener('click', () => { goTo(i); restartAutoplay(); }));

  window.addEventListener('resize', () => updatePosition(false));

  // Autoplay
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

  carousel.addEventListener('mouseenter', stopAutoplay);
  carousel.addEventListener('mouseleave', startAutoplay);

  updatePosition(false);
  startAutoplay();
})();
