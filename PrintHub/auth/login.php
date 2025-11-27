<?php
session_start();
require_once '../includes/json_connect.php';

// Si ya está logueado, redirigir al perfil
if (isset($_SESSION['user_id'])) {
    header("Location: profile.php");
    exit;
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    /* -------------------------------------------------
       1. VALIDAR reCAPTCHA con Google
    -------------------------------------------------- */

    $secretKey = "6LfmChosAAAAAHaTmvsOsHr5ml9SEN6EzIZstsXZ"; // ← PON TU SECRET KEY AQUÍ
    $captchaResponse = $_POST['g-recaptcha-response'];

    $verify = file_get_contents(
        "https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$captchaResponse"
    );

    $captchaSuccess = json_decode($verify);

    if (!$captchaSuccess->success) {
        $error = "Verifica el reCAPTCHA.";
    } else {

        /* -------------------------------------------------
           2. PROCESAR LOGIN SOLO SI EL CAPTCHA ES VÁLIDO
        -------------------------------------------------- */

        $username = trim($_POST['username']);
        $password = $_POST['password'];

        // Buscar usuario
        $user = findUserByUsername($username);

        // Verificar contraseña
        if ($user && password_verify($password, $user['contrasenya'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['nom_usuari'];

            setcookie('user_id', $user['id'], time() + 3600, "/");

            header("Location: profile.php");
            exit;
        } else {
            $error = "Usuario o contraseña incorrectos.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inicio de Sesión - PrintHub</title>
    <link rel="stylesheet" href="../src/css/loginStyle.css">
    <link rel="stylesheet" href="../src/css/aside.css" />

    <!-- SCRIPT NECESARIO PARA GOOGLE RECAPTCHA -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
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
    <li><a href="./profile.php" aria-label="Perfil">👤</a></li>
  </ul>

  <h3 class="etiqueta-menu">Menú</h3>
  <nav>
    <ul>
      <li><a href="/index.html">Inicio</a></li>
      <li class="desplegable">
        <a href="#">Maquetas Personalizadas ▾</a>
        <ul class="contenido-desplegable">
          <li><a href="#">Videojuegos</a></li>
          <li><a href="#">Arquitectura</a></li>
          <li><a href="#">Automóviles</a></li>
        </ul>
      </li>
      <li><a href="#como-funciona">Diseñar Maquetas</a></li>
      <li><a href="/src/galeria.html">Galería de Proyectos</a></li>
      <li><a href="#impresoras">Impresoras 3D</a></li>
      <li><a href="/php/contact.php">Formulario de Contacto</a></li>
      <li><a href="src/admin_importar.html">Importar Información</a></li>
    </ul>
  </nav>
</aside>

<div class="container">
    <h2>🔐 Iniciar Sesión</h2>

    <!-- Span para errores -->
    <span class="error" id="formError" style="display:none;"></span>

    <form id="loginForm" action="login.php" method="POST" novalidate>
        <input type="text" name="username" id="username" placeholder="Nombre de usuario" required>
        <input type="password" name="password" id="password" placeholder="Contraseña" required>

        <div class="g-recaptcha" data-sitekey="6LfmChosAAAAAO1KhMNCFkQiKGDuwLH6Ss4kc5Ns"></div>

        <button type="submit">Entrar</button>
    </form>

    <p>¿No tienes cuenta? <a href="register.php">Regístrate</a></p>
</div>



</body>
<script src="../src/js/login-validation.js"></script>
<script src="../src/js/barra-lateral.js"></script>
</html>
