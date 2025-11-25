<?php
session_start();
require_once '../includes/json_connect.php';

// 1. Control de acceso
if (!isset($_SESSION['user_id'])) {
    if (isset($_COOKIE['user_id'])) {
        $_SESSION['user_id'] = $_COOKIE['user_id'];
    } else {
        header("Location: login.php");
        exit;
    }
}

$userId = $_SESSION['user_id'];

// --- NUEVO: Lógica de eliminación ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_account'])) {
    if (deleteUser($userId)) {
        // 1. Destruir la sesión
        session_unset();
        session_destroy();
        
        // 2. Eliminar la cookie si existe
        if (isset($_COOKIE['user_id'])) {
            setcookie('user_id', '', time() - 3600, "/");
        }

        // 3. Redirigir al login o index
        header("Location: login.php?msg=deleted");
        exit;
    } else {
        echo "<script>alert('Error al eliminar el usuario.');</script>";
    }
}

// 2. Obtener datos actuales del usuario
$user = findUserById($userId);

if (!$user) {
    // Si el usuario no existe (por si acaso)
    session_destroy();
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - PrintHub</title>
    <link rel="stylesheet" href="../src/css/perfilStyle.css">
    <link rel="stylesheet" href="../src/css/aside.css" />
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
        <h2>👤 Mi Perfil</h2>
        
        <div class="profile-info">
            <p><strong>Nombre de usuario:</strong> <?= htmlspecialchars($user['nom_usuari']) ?></p>
            <p><strong>Nombre:</strong> <?= htmlspecialchars($user['nom']) ?></p>
            <p><strong>Apellidos:</strong> <?= htmlspecialchars($user['cognoms']) ?></p>
            <p><strong>Correo electrónico:</strong> <?= htmlspecialchars($user['email']) ?></p>
            <p><strong>Miembro desde:</strong> <?= date('d/m/Y', strtotime($user['data_registre'])) ?></p>
        </div>

        <a href="register.php" class="btn-edit">✏️ Editar Datos</a>

        <form method="POST" action="profile.php" onsubmit="return confirm('⚠️ ATENCIÓN:\n\n¿Estás seguro de que deseas eliminar tu cuenta permanentemente?\n\nEsta acción no se puede deshacer.');">
            <button type="submit" name="delete_account" class="btn-delete">🗑️ Eliminar Cuenta</button>
        </form>

        <hr>

        <a href="../index.html" class="link-home">🏠 Volver al Inicio</a>
        <a href="logout.php" class="link-logout">🚪 Cerrar Sesión</a>
    </div>
</body>
<script src="../src/js/barra-lateral.js"></script>

</html>
