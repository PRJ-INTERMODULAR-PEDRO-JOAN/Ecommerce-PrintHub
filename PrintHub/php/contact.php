<?php
// ---------------------------
// VALIDACIÓN SERVIDOR PHP
// ---------------------------
$name = $email = $message = "";
$errors = [];
$success = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $name = trim($_POST["name"] ?? "");
  $email = trim($_POST["email"] ?? "");
  $message = trim($_POST["message"] ?? "");
  $terms = isset($_POST["terms"]);
  $compro = isset($_POST["compro"]);

  // Solo valida si no está marcada la comprobación cliente
  
    if (strlen($name) < 2) $errors[] = "El nombre debe tener al menos 2 caracteres.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Por favor, ingresa un correo electrónico válido.";
    if (strlen($message) < 10) $errors[] = "El mensaje debe tener al menos 10 caracteres.";
    if (!$terms) $errors[] = "Debes aceptar los términos y condiciones.";
  

  if (empty($errors)) {
    $success = true;
  }
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Formulario Contacto - PrintHub</title>
  <link rel="stylesheet" href="../src/css/formstyle.css" />
  <link rel="icon" type="image/x-icon" href="/public/logoPrintHubIcon.ico">
</head>
<body>

  <!-- Botón menú lateral -->
  <button class="menu-toggle">☰</button>

  <!-- Barra lateral -->
  <aside class="sidebar">
    <br><br><br>
    <center><h1>Print<span class="highlight">Hub</span></h1></center>
    <br>
    <div class="logo">
      <img src="/public/logoPrintHub.jpeg" alt="Logo de la empresa" />
    </div>
    <p>Menú</p>
    <nav>
      <ul>
        <li><a href="/">Inicio</a></li>

        <li class="dropdown">
          <a href="#" class="dropbtn">Maquetas Personalizadas ▾</a>
          <ul class="dropdown-content">
            <li><a href="#">Videojuegos</a></li>
            <li><a href="#">Arquitectura</a></li>
            <li><a href="#">Automóviles</a></li>
          </ul>
        </li>

        <li><a href="#">Diseñar Maquetas</a></li>
        <li><a href="#">Galería de Proyectos</a></li>
        <li><a href="contact.php" class="active">Formulario Contacto</a></li>
      </ul>
    </nav>
  </aside>

  <!-- Contenido principal -->
  <main class="form-container">
    <section class="contact-section">
      <h1>Contacta con <span class="highlight">PrintHub</span></h1>
      <p class="subtitle">Envíanos tu mensaje y te responderemos lo antes posible.</p>

      <!-- Mensajes del servidor -->
      <?php if (!empty($errors)): ?>
        <div id="formMessage" class="error">
          <?php foreach ($errors as $err) echo "• " . htmlspecialchars($err) . "<br>"; ?>
        </div>
      <?php elseif ($success): ?>
        <div id="formMessage" class="success">
          ¡Formulario enviado correctamente!
        </div>
      <?php else: ?>
        <div id="formMessage"></div>
      <?php endif; ?>

      <!-- Formulario -->
      <form id="contactForm" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="name">Nombre:</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" placeholder="Tu nombre">

        <label for="email">Correo Electrónico:</label>
        <input type="text" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" placeholder="tu@email.com">

        <label for="message">Mensaje:</label>
        <textarea id="message" name="message" placeholder="Escribe tu mensaje aquí..."><?php echo htmlspecialchars($message); ?></textarea>

        <div class="checkbox-container">
          <label>
            <input type="checkbox" id="compro" name="compro" <?php if(isset($_POST["compro"])) echo "checked"; ?>>
            Quitar Comprobación Cliente
          </label>
        </div>

        <div class="checkbox-container">
          <label>
            <input type="checkbox" id="terms" name="terms" <?php if(isset($_POST["terms"])) echo "checked"; ?>>
            Acepto los <a href="/terminos.html" target="_blank">términos y condiciones</a> y la 
            <a href="/privacidad.html" target="_blank">política de privacidad</a>.
          </label>
        </div>

        <button type="submit" class="btn">Enviar</button>
      </form>
    </section>
  </main>

  <!-- Footer -->
  <footer class="footer">
    <div class="footer-container">
      <div class="footer-left">
        <h2 class="logo">Print<span class="highlight">Hub</span></h2><br>
        <div>
          <a class="social-icons" href="#"><img src="/public/facebook.jpg" alt="Facebook"></a>
          <a class="social-icons" href="#"><img src="/public/linkedIn.png" alt="LinkedIn"></a>
          <a class="youtube-icons" href="#"><img src="/public/youtube.png" alt="YouTube"></a>
          <a class="social-icons" href="#"><img src="/public/insta.jpg" alt="Instagram"></a>
        </div>
      </div>

      <div class="footer-center">
        <img src="/public/fotterImage.png" alt="Imagen global 3D">
      </div>

      <div class="footer-right">
        <div class="services">
          <h3>Servicios y productos</h3>
          <ul>
            <li><a href="#">Fabricación aditiva 3D</a></li>
            <li><a href="#">Servicio de Impresión 3D</a></li>
            <li><a href="#">Prototipado 3D</a></li>
            <li><a href="#">Ingeniería y diseño</a></li>
          </ul>
        </div>
        <div class="contact">
          <h3>Contacto</h3>
          <ul>
            <li>📞 (+34) 951 753 852</li>
            <li>📧 <a href="mailto:printhub@contact.me">printhub@contact.me</a></li>
            <li>📍 Partida Cotes Altes, 27, 03804 Alcoi, Alicante</li>
            <li>🕒 Lunes – Viernes: 8:00 – 13:30</li>
          </ul>
        </div>
      </div>
    </div>
  </footer>

  <!-- Script -->
  <script>
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

    if (!comproChecked) {
      if (name.length < 2) errors.push('El nombre debe tener al menos 2 caracteres.');
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/; if (!emailRegex.test(email)) { errors.push('Por favor, ingresa un correo electrónico válido.'); }
      if (message.length < 10) errors.push('El mensaje debe tener al menos 10 caracteres.');
      if (!termsAccepted) errors.push('Debes aceptar los términos y condiciones.');
    }

    return errors;
  };

  // Validación al enviar
  form.addEventListener('submit', function (event) {
    event.preventDefault();
    const errors = validateForm();

    if (errors.length > 0) {
      formMessage.innerHTML = errors.map(err => `• ${err}`).join('<br>');
      formMessage.className = "error";
      return;
    }

    formMessage.textContent = 'Formulario válido. Enviando...';
    formMessage.className = "success";
    form.submit();
  });

  // Limpia errores al escribir
  [nameInput, emailInput, messageInput, termsInput].forEach(el => {
    el.addEventListener('input', () => {
      const errors = validateForm();
      if (errors.length === 0) {
        formMessage.textContent = '';
        formMessage.className = '';
      }
    });
  });
  </script>
</body>
</html>
