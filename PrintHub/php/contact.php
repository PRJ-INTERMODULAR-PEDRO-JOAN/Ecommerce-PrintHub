<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $name = trim($_POST["name"] ?? "");
  $email = trim($_POST["email"] ?? "");
  $message = trim($_POST["message"] ?? "");
  $terms = trim($_POST["terms"] ?? "");

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

  if ($terms !== "on") {
    $errors[] = "Debe aceptar los términos y condiciones.";
  }
  ?>
  <!DOCTYPE html>
  <html lang="es">
  <head>
    <meta charset="UTF-8">
    <title>Resultado del formulario</title>
    <link rel="icon" type="image/x-icon" href="/public/logoPrintHubIcon.ico">
    <style>
      /* --- Reset y body --- */
      * { margin:0; padding:0; box-sizing:border-box; }
      body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(135deg, #f0f4ff, #e6ecff);
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
        padding: 1rem;
      }

      /* --- Contenedor --- */
      .container {
        background: #fff;
        padding: 30px 40px;
        border-radius: 16px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        max-width: 450px;
        width: 100%;
        text-align: center;
        animation: fadeIn 0.8s ease-in-out;
      }

      @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
      }

      /* --- Mensajes --- */
      .error {
        color: #b00020;
        background: #fdecea;
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 12px;
      }

      .success {
        color: #0a7b39;
        background: #e6f9ed;
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 12px;
      }

      /* --- Botones --- */
      .btn {
        display: inline-block;
        margin-top: 15px;
        margin-right: 10px;
        padding: 10px 18px;
        background: #666;
        color: #fff;
        font-weight: 600;
        border-radius: 8px;
        text-decoration: none;
        transition: background 0.3s ease, transform 0.2s ease;
      }

      .btn:hover {
        background: #555;
        transform: translateY(-2px);
      }
    </style>
  </head>
  <body>
    <div class="container">
      <?php
      if (!empty($errors)) {
        foreach ($errors as $error) {
          echo "<div class='error'>$error</div>";
        }
        echo '<a href="javascript:history.back()" class="btn">← Volver al formulario</a>';
        echo '<a href="http://localhost" class="btn">🏠 Página inicial</a>';
        exit;
      }

      echo "<div class='success'>Formulario recibido correctamente. ¡Gracias!</div>";
      echo '<a href="http://localhost" class="btn">🏠 Ir a la página inicial</a>';
      ?>
    </div>
  </body>
  </html>
  <?php
} else {
  header("Location: http://localhost");
  exit;
}
?>
