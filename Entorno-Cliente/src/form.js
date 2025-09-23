document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('contactForm');
  const formMessage = document.getElementById('formMessage');

  form.addEventListener('submit', function(event) {
    event.preventDefault();

    const name = form.name.value.trim();
    const email = form.email.value.trim();
    const message = form.message.value.trim();

    if (name.length < 2) {
      formMessage.textContent = 'El nombre debe tener al menos 2 caracteres.';
      formMessage.style.color = 'red';
      return;
    }

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
      formMessage.textContent = 'Por favor, ingresa un correo electrónico válido.';
      formMessage.style.color = 'red';
      return;
    }

    if (message.length < 10) {
      formMessage.textContent = 'El mensaje debe tener al menos 10 caracteres.';
      formMessage.style.color = 'red';
      return;
    }

    formMessage.textContent = 'Formulario válido. Enviando...';
    formMessage.style.color = 'green';

    form.submit(); // Envía el formulario al servidor (contact.php)
  });
});
