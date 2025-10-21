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

// Elementos del formulario
const nameInput = form.name;
const emailInput = form.email;
const messageInput = form.message;
const termsInput = form.terms;
const comproInput = form.compro;

// Validación que muestra todos los errores a la vez
const validateForm = () => {
  const errors = [];

  const name = nameInput.value.trim();
  const email = emailInput.value.trim();
  const message = messageInput.value.trim();
  const termsAccepted = termsInput.checked;
  const comproChecked = comproInput.checked;

  // Solo se validan los demás campos si NO se han aceptado los términos
  if (!comproChecked) {
    if (name.length < 2) {
      errors.push('El nombre debe tener al menos 2 caracteres.');
    }

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
      errors.push('Por favor, ingresa un correo electrónico válido.');
    }

    if (message.length < 10) {
      errors.push('El mensaje debe tener al menos 10 caracteres.');
    }

    if (!termsAccepted) {
      errors.push('Debes aceptar los términos y condiciones.');
    }

  }

  return errors;
};

// Validación al enviar
form.addEventListener('submit', function (event) {
  event.preventDefault(); // Evita envío por defecto

  const termsAccepted = termsInput.checked;

  // Si no se aceptaron los términos, se validan todos los campos
  const errors = validateForm();

  if (errors.length > 0) {
    // Muestra todos los errores en una lista
    formMessage.innerHTML = errors.map(err => `• ${err}`).join('<br>');
    formMessage.className = "error";
    return;
  }

  // Si todo está correcto
  formMessage.textContent = 'Formulario válido. Enviando...';
  formMessage.className = "success";
  form.submit();
});

// Limpia los errores si todo va bien mientras se escribe
[nameInput, emailInput, messageInput, termsInput].forEach(el => {
  el.addEventListener('input', () => {
    const errors = validateForm();
    if (errors.length === 0) {
      formMessage.textContent = '';
      formMessage.className = '';
    }
  });
});
