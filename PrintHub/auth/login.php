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
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // 1. Buscar usuario
    $user = findUserByUsername($username);

    // 2. Verificar contraseña
    if ($user && password_verify($password, $user['contrasenya'])) {
        // 3. Crear sesión segura
        session_regenerate_id(true); // Prevención de Session Fixation
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['nom_usuari'];

        // 4. Crear cookie (Opcional)
        // Expira en 1 hora (3600s)
        setcookie('user_id', $user['id'], time() + 3600, "/");

        header("Location: profile.php");
        exit;
    } else {
        $error = "Usuario o contraseña incorrectos.";
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
  </head>
<body>
<button class="alternar-menu">☰</button>

<aside class="barra-lateral">
  <div class="barra-lateral-cabecera">
    <h1 class="logo-texto">Print<span class="resaltado">Hub</span></h1>
    <div class="logo">
      <img src="public/logoPrintHub.jpeg" alt="Logo de PrintHub" />
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
    <?php if($error): ?><p class="error"><?= $error ?></p><?php endif; ?>
    
    <form action="login.php" method="POST">
        <input type="text" name="username" placeholder="Nombre de usuario" required>
        <input type="password" name="password" placeholder="Contraseña" required>
        <button type="submit">Entrar</button>
    </form>

    <p>¿No tienes cuenta? <a href="register.php">Regístrate</a></p>
</div>

</body>
<script src="../src/js/script.js"></script>
</html>
