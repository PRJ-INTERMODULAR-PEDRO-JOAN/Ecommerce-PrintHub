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

  ?>
  <!DOCTYPE html>
  <html lang="es">
  <head>
    <meta charset="UTF-8">
    <title>Resultado del formulario</title>
    <style>
      body {
        font-family: Arial, sans-serif;
        background: linear-gradient(135deg, #74ebd5 0%, #ACB6E5 100%);
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
      }
      .container {
        background: #fff;
        padding: 25px 35px;
        border-radius: 15px;
        box-shadow: 0 6px 18px rgba(0,0,0,0.2);
        max-width: 450px;
        width: 100%;
        text-align: center;
        animation: fadeIn 0.8s ease-in-out;
      }
      @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
      }
      .error {
        color: #b00020;
        background: #fdecea;
        padding: 10px;
        border-radius: 8px;
        margin-bottom: 10px;
      }
      .success {
        color: #0a7b39;
        background: #e6f9ed;
        padding: 10px;
        border-radius: 8px;
        margin-bottom: 10px;
      }
      .btn {
        display: inline-block;
        margin-top: 15px;
        padding: 10px 18px;
        background: #007BFF;
        color: #fff;
        font-weight: bold;
        border-radius: 8px;
        text-decoration: none;
        transition: 0.3s;
      }
      .btn:hover {
        background: #0056b3;
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
        echo '<br><a href="http://localhost:5173" class="btn">🏠 Página inicial</a>';
        exit;
      }

      echo "<div class='success'>Formulario recibido correctamente. ¡Gracias!</div>";
      echo '<a href="http://localhost:5173" class="btn">🏠 Ir a la página inicial</a>';
      ?>
    </div>
  </body>
  </html>
  <?php
} else {
  header("Location: index.html");
  exit;
}
?>
