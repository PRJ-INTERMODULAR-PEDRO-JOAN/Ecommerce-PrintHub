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

  // Validación en tiempo real (opcional)
  const nameInput = form.name;
  const emailInput = form.email;
  const messageInput = form.message;
  const termsInput = form.terms;

  const validateForm = () => {
    const name = nameInput.value.trim();
    const email = emailInput.value.trim();
    const message = messageInput.value.trim();
    const termsAccepted = termsInput.checked;

    if (name.length < 2) {
      return 'El nombre debe tener al menos 2 caracteres.';
    }

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
      return 'Por favor, ingresa un correo electrónico válido.';
    }

    if (message.length < 10) {
      return 'El mensaje debe tener al menos 10 caracteres.';
    }

    if (!termsAccepted) {
      return 'Debes aceptar los términos y condiciones para continuar.';
    }

    return ''; // Todo correcto
  };

  // Validación al escribir
  [nameInput, emailInput, messageInput, termsInput].forEach(el => {
    el.addEventListener('input', () => {
      const error = validateForm();
      if (error === '') {
        formMessage.textContent = '';
        formMessage.className = '';
      }
    });
    el.addEventListener('change', () => {
      const error = validateForm();
      if (error === '') {
        formMessage.textContent = '';
        formMessage.className = '';
      }
    });
  });

  // Validación al enviar
  form.addEventListener('submit', function(event) {
    event.preventDefault(); // Evita envío hasta que todo sea correcto

    const error = validateForm();

    if (error !== '') {
      formMessage.textContent = error;
      formMessage.className = "error";
      return;
    }

    formMessage.textContent = 'Formulario válido. Enviando...';
    formMessage.className = "success";

    form.submit(); // Solo se envía si todo es correcto
  });
});
