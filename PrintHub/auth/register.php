<?php
session_start();
require_once '../includes/json_connect.php';

$error = '';
$success = '';

// Detectar si estamos editando (usuario logueado) o registrando
$isEditing = isset($_SESSION['user_id']);
$currentUser = null;

// Si estamos editando, cargamos los datos actuales
if ($isEditing) {
    $currentUser = findUserById($_SESSION['user_id']);
    if (!$currentUser) {
        // Si la sesión es inválida, forzamos logout
        header("Location: logout.php");
        exit;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = trim($_POST['nom']);
    $cognoms = trim($_POST['cognoms']);
    $email = trim($_POST['email']);
    $password = $_POST['password']; // Puede estar vacío en edición
    
    // Solo capturamos username si es registro nuevo
    $username = $isEditing ? $currentUser['nom_usuari'] : trim($_POST['username']);

    // 1. Validaciones
    if (empty($nom) || empty($username) || (!$isEditing && empty($password))) {
        $error = "Tots els camps obligatoris s'han d'omplir.";
    } else {
        
        if ($isEditing) {
            // --- LÓGICA DE ACTUALIZACIÓN (UPDATE) ---
            
            $updateData = [
                "nom" => $nom,
                "cognoms" => $cognoms,
                "email" => $email
            ];

            // Solo actualizamos contraseña si el usuario escribió algo
            if (!empty($password)) {
                $updateData["contrasenya"] = password_hash($password, PASSWORD_DEFAULT);
            }

            $result = updateUser($_SESSION['user_id'], $updateData);

            if ($result) {
                $success = "Perfil actualitzat correctament! <br> <a href='profile.php'>Tornar al perfil</a>";
                // Refrescamos datos para que se vean en el form
                $currentUser = array_merge($currentUser, $updateData);
            } else {
                $error = "Error al guardar les dades.";
            }

        } else {
            // --- LÓGICA DE REGISTRO (CREATE) ---
            
            $existingUser = findUserByUsername($username);
            
            if ($existingUser) {
                $error = "Aquest nom d'usuari ja està registrat.";
            } else {
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
                    $success = "Registre completat amb èxit! 
                                <div style='margin-top: 10px;'>
                                    <a href='login.php' style='font-weight:800;'>👉 Inicia sessió aquí</a>
                                </div>";
                } else {
                    $error = "Error en comunicar amb el servidor.";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title><?= $isEditing ? "Editar Perfil" : "Registre" ?> - PrintHub</title>
    <link rel="stylesheet" href="../src/css/registerStyle.css">
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
    <li><a href="auth/profile.php" aria-label="Iniciar Sesión">👤</a></li>
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
      <li><a href="/php/contact.php">Formulario Contacto</a></li>
      <li><a href="src/admin_importar.html">Importar Información</a></li>
    </ul>
  </nav>
</aside>
    <div class="container">
        <h2><?= $isEditing ? "✏️ Editar Perfil" : "📝 Registre d'Usuari" ?></h2>
        
        <?php if($error): ?><div class="error"><?= $error ?></div><?php endif; ?>
        <?php if($success): ?><div class="success"><?= $success ?></div><?php endif; ?>
        
        <?php if(!$success || $isEditing): ?>
        
        <form action="register.php" method="POST">
            
            <label style="text-align:left; display:block; font-size:0.8em; color:#666;">Nom</label>
            <input type="text" name="nom" placeholder="Nom" required 
                   value="<?= $isEditing ? htmlspecialchars($currentUser['nom']) : '' ?>">
            
            <label style="text-align:left; display:block; font-size:0.8em; color:#666;">Cognoms</label>
            <input type="text" name="cognoms" placeholder="Cognoms" 
                   value="<?= $isEditing ? htmlspecialchars($currentUser['cognoms']) : '' ?>">
            
            <label style="text-align:left; display:block; font-size:0.8em; color:#666;">Email</label>
            <input type="email" name="email" placeholder="Email" required 
                   value="<?= $isEditing ? htmlspecialchars($currentUser['email']) : '' ?>">
            
            <label style="text-align:left; display:block; font-size:0.8em; color:#666;">Nom d'usuari</label>
            <input type="text" name="username" placeholder="Nom d'usuari" required 
                   value="<?= $isEditing ? htmlspecialchars($currentUser['nom_usuari']) : '' ?>"
                   <?= $isEditing ? 'readonly style="background-color: #e9ecef;"' : '' ?>>
            
            <label style="text-align:left; display:block; font-size:0.8em; color:#666;">
                <?= $isEditing ? "Nova Contrasenya (deixar en blanc per no canviar)" : "Contrasenya" ?>
            </label>
            <input type="password" name="password" placeholder="Contrasenya" <?= $isEditing ? '' : 'required' ?>>
            
            <button type="submit"><?= $isEditing ? "Guardar Canvis" : "Registrar-se" ?></button>
        </form>

        <?php if($isEditing): ?>
            <p><a href="profile.php">🔙 Cancel·lar i tornar al perfil</a></p>
        <?php else: ?>
            <p>Ja tens compte? <a href="login.php">Inicia sessió</a></p>
        <?php endif; ?>

        <?php endif; ?>
    </div>
</body>
<script src="../src/js/script.js"></script>

</html>