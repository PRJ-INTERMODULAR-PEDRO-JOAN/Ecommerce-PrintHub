<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $name = trim($_POST["name"] ?? "");
  $email = trim($_POST["email"] ?? "");
  $message = trim($_POST["message"] ?? "");

  $errors = [];

  if (strlen($name) < 2) {
    $errors[] = "El nombre debe tener al menos 2 caracteres.";
  }

  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Correo electrónico no válido.";
  }

  if (strlen($message) < 10) {
    $errors[] = "El mensaje debe tener al menos 10 caracteres.";
  }

  if (!empty($errors)) {
    // Aquí puedes mostrar errores o enviarlos de vuelta al formulario
    foreach ($errors as $error) {
      echo "<p style='color: red;'>$error</p>";
    }
    echo '<p><a href="javascript:history.back()">Volver al formulario</a></p>';
    exit;
  }

  // Aquí procesas el formulario (por ejemplo, enviar email, guardar en BD, etc)
  echo "<p style='color: green;'>Formulario recibido correctamente. ¡Gracias!</p>";
} else {
  // Si se accede sin enviar el form, redirige o muestra error
  header("Location: index.html");
  exit;
}
?>
