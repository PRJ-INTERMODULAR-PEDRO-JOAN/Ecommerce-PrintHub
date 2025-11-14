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

  <button class="alternar-menu">☰</button>

  <aside class="barra-lateral">
    <div class="barra-lateral-cabecera">
      <h1 class="logo-texto">Print<span class="resaltado">Hub</span></h1>
      <div class="logo">
        <img src="../public/logoPrintHub.jpeg" alt="Logo de PrintHub" />
      </div>
    </div>

    <ul class="iconos-utilidad">
      <li><a href="#" aria-label="Carrito">🛒</a></li>
      <li><a href="#" aria-label="Iniciar Sesión">👤</a></li>
    </ul>

    <h3 class="etiqueta-menu">Menú</h3>
    <nav>
      <ul>
        <li><a href="../index.html">Inicio</a></li>
        <li class="desplegable">
          <a href="#">Maquetas Personalizadas ▾</a>
          <ul class="contenido-desplegable">
            <li><a href="#">Videojuegos</a></li>
            <li><a href="#">Arquitectura</a></li>
            <li><a href="#">Automóviles</a></li>
          </ul>
        </li>
        <li><a href="#como-funciona">Diseñar Maquetas</a></li>
        <li><a href="../src/galeria.html">Galería de Proyectos</a></li>
        <li><a href="#impresoras">Impresoras 3D</a></li>
        <li><a href="./contact.php">Formulario Contacto</a></li>
        <li><a href="../src/admin_importar.html">Importar Información</a></li>
      </ul>
    </nav>
  </aside>


  <main class="form-container">
    <section class="contact-section">
      <h1>Contacta con <span class="highlight">PrintHub</span></h1>
      <p class="subtitle">Envíanos tu mensaje y te responderemos lo antes posible.</p>

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

  <footer class="pie-pagina">
      <div class="pie-pagina-contenedor">
        
        <div class="pie-pagina-izquierda">
          <h2 class="logo">
            Print<span class="resaltado">Hub</span>
          </h2>
          <br>
          <div>
            <a class="iconos-sociales" href="#"><img src="../public/facebook.jpg" alt="Facebook"></a>
            <a class="iconos-sociales" href="#"><img src="../public/linkedIn.png" alt="LinkedIn"></a>
            <a class="iconos-youtube" href="#"><img src="../public/youtube.png" alt="YouTube"></a>
            <a class="iconos-sociales" href="#"><img src="../public/insta.jpg" alt="Instagram"></a>
          </div>
        </div>
  
        <div class="pie-pagina-centro">
          <img src="../public/fotterImage.jpg" alt="Imagen global 3D">
        </div>
  
        <div class="pie-pagina-derecha">
          <div class="servicios">
            <h3>Servicios y productos</h3>
            <ul>
              <li><a href="#">Fabricación aditiva 3D</a></li>
              <li><a href="#">Servicio de Impresión 3D</a></li>
              <li><a href="#">Prototipado 3D</a></li>
              <li><a href="#">Ingeniería y diseño</a></li>
            </ul>
          </div>
          <div class="contacto">
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

  <script>
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

  // === INICIO DE LA CORRECCIÓN JS ===
  // Faltaba definir la variable 'form' seleccionando el formulario por su ID
  const form = document.getElementById('contactForm');
  // === FIN DE LA CORRECCIÓN JS ===

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