<?php
session_start();
require_once '../includes/json_connect.php';

$error = '';
$success = "";

// Detectar si se edita perfil o se registra
$isEditing = isset($_SESSION['user_id']);
$currentUser = null;

if ($isEditing) {
    $currentUser = findUserById($_SESSION['user_id']);
    if (!$currentUser) {
        header("Location: logout.php");
        exit;
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Datos recibidos
    $nom = trim($_POST['nom'] ?? "");
    $cognoms = trim($_POST['cognoms'] ?? "");
    $email = trim($_POST['email'] ?? "");
    $password = trim($_POST['password'] ?? "");
    $username = $isEditing ? $currentUser['nom_usuari'] : trim($_POST['username'] ?? "");

    // VALIDACIÓN SERVIDOR
    $errors = [];

    if (strlen($nom) < 2) $errors[] = "El nombre debe tener al menos 2 caracteres.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Por favor, ingresa un correo electrónico válido.";
    if (!$isEditing && strlen($username) < 3) $errors[] = "El nombre de usuario debe tener al menos 3 caracteres.";

    if (!$isEditing && strlen($password) < 6)
        $errors[] = "La contraseña debe tener al menos 6 caracteres.";

    if ($isEditing && !empty($password) && strlen($password) < 6)
        $errors[] = "Si cambias la contraseña, debe tener al menos 6 caracteres.";

    // Verificar usuario ya registrado
    if (!$isEditing) {
        $existingUser = findUserByUsername($username);
        if ($existingUser) {
            $errors[] = "Este nombre de usuario ya está registrado.";
        }
    }

    if (empty($errors)) {

        if ($isEditing) {
            // --- ACTUALIZACIÓN ---
            $updateData = [
                "nom" => $nom,
                "cognoms" => $cognoms,
                "email" => $email
            ];

            if (!empty($password)) {
                $updateData["contrasenya"] = password_hash($password, PASSWORD_DEFAULT);
            }

            $result = updateUser($_SESSION['user_id'], $updateData);

            if ($result) {
                $success = "Perfil actualizado correctamente. <a href='profile.php'>Volver</a>";
                $currentUser = array_merge($currentUser, $updateData);
            } else {
                $errors[] = "Error al guardar los datos.";
            }

        } else {
            // --- REGISTRO ---
            $newUser = [
                "nom" => $nom,
                "cognoms" => $cognoms,
                "email" => $email,
                "nom_usuari" => $username,
                "contrasenya" => password_hash($password, PASSWORD_DEFAULT),
                "data_registre" => date('c')
            ];

            $result = createUser($newUser);

            if ($result) {
                $success = "Registro completado con éxito. <a href='login.php'>Iniciar sesión</a>";
            } else {
                $errors[] = "Error al comunicarse con el servidor.";
            }
        }
    }

    if (!empty($errors)) {
        $error = implode("<br>", $errors);
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $isEditing ? "Editar Perfil" : "Registro" ?> - PrintHub</title>
    <link rel="stylesheet" href="../src/css/registerStyle.css">
    <link rel="stylesheet" href="../src/css/aside.css" />
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
    <h2><?= $isEditing ? "✏️ Editar Perfil" : "📝 Registro de Usuario" ?></h2>

    <div id="formMessage">
        <?php if ($error): ?>
            <div class="error"><?= $error ?></div>
        <?php elseif ($success): ?>
            <div class="success"><?= $success ?></div>
        <?php endif; ?>
    </div>

    <?php if (!$success || $isEditing): ?>
        <form id="registerForm" method="POST" novalidate>

<label>Nombre</label>
<input type="text" name="nom" value="<?= $isEditing ? htmlspecialchars($currentUser['nom']) : '' ?>">

<label>Apellidos</label>
<input type="text" name="cognoms" value="<?= $isEditing ? htmlspecialchars($currentUser['cognoms']) : '' ?>">

<label>Correo electrónico</label>
<input type="email" name="email" value="<?= $isEditing ? htmlspecialchars($currentUser['email']) : '' ?>">

<label>Nombre de usuario</label>
<input type="text" name="username"
       value="<?= $isEditing ? htmlspecialchars($currentUser['nom_usuari']) : '' ?>"
       <?= $isEditing ? 'readonly style="background:#eee;"' : '' ?>>

<label><?= $isEditing ? "Nueva contraseña (opcional)" : "Contraseña" ?></label>
<input type="password" name="password">

<!-- RECAPTCHA -->
<?php if (!$isEditing): ?>
<div class="g-recaptcha" data-sitekey="6LfmChosAAAAAO1KhMNCFkQiKGDuwLH6Ss4kc5Ns"></div>
<?php endif; ?>

<button type="submit"><?= $isEditing ? "Guardar cambios" : "Registrarse" ?></button>
</form>


    <?php if ($isEditing): ?>
        <p><a href="profile.php">🔙 Volver al perfil</a></p>
    <?php else: ?>
        <p>¿Ya tienes cuenta? <a href="login.php">Iniciar sesión</a></p>
    <?php endif; ?>

    <?php endif; ?>
</div>

<script src="../src/js/barra-lateral.js"></script>
<script src="../src/js/register.js"></script>

</body>
</html>
