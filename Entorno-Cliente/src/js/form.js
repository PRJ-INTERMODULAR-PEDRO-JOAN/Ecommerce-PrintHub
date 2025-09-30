document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('contactForm');
  const formMessage = document.getElementById('formMessage');
  const sidebar = document.querySelector(".sidebar");
  const toggleBtn = document.querySelector(".menu-toggle");
  const dropdownBtn = document.querySelector(".dropbtn");

  // Toggle del menú lateral
  toggleBtn.addEventListener("click", () => {
    sidebar.classList.toggle("active");
  });

  // Toggle del dropdown
  dropdownBtn.addEventListener("click", (e) => {
    e.preventDefault();
    const dropdownContent = dropdownBtn.nextElementSibling;
    dropdownContent.classList.toggle("show");
  });

  // Validación formulario
  form.addEventListener('submit', function(event) {
    event.preventDefault();

    const name = form.name.value.trim();
    const email = form.email.value.trim();
    const message = form.message.value.trim();

    if (name.length < 2) {
      formMessage.textContent = 'El nombre debe tener al menos 2 caracteres.';
      formMessage.className = "error";
      return;
    }

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
      formMessage.textContent = 'Por favor, ingresa un correo electrónico válido.';
      formMessage.className = "error";
      return;
    }

    if (message.length < 10) {
      formMessage.textContent = 'El mensaje debe tener al menos 10 caracteres.';
      formMessage.className = "error";
      return;
    }

    formMessage.textContent = 'Formulario válido. Enviando...';
    formMessage.className = "success";

    form.submit();
  });
});