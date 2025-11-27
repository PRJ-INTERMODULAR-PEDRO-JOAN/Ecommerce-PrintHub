<?php
// productDetail.php

// --- Configuración reCAPTCHA v2 ---
$siteKey = "6LfmChosAAAAAO1KhMNCFkQiKGDuwLH6Ss4kc5Ns";
$secretKey = "6LfmChosAAAAAHaTmvsOsHr5ml9SEN6EzIZstsXZ";

// Variables para mensajes
$captchaError = "";
$commentSuccess = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating = $_POST['inputRating'] ?? '';
    $text = $_POST['inputText'] ?? '';
    $captchaResponse = $_POST['g-recaptcha-response'] ?? '';

    // --- Validación reCAPTCHA ---
    if (!$captchaResponse) {
        $captchaError = "Por favor verifica el captcha.";
    } else {
        $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secretKey}&response={$captchaResponse}");
        $captchaResult = json_decode($verify);

        if (!$captchaResult->success) {
            $captchaError = "Captcha incorrecto, intenta nuevamente.";
        } else {
            // Guardar comentario en JSON
            $jsonFile = __DIR__ . '/comments.json';
            $comments = file_exists($jsonFile) ? json_decode(file_get_contents($jsonFile), true) : [];

            $comments[] = [
                "id" => count($comments) + 1,
                "user_id" => $_COOKIE['user_id'] ?? 0,
                "rating" => (int)$rating,
                "text" => htmlspecialchars($text, ENT_QUOTES),
                "date" => date("Y-m-d H:i:s")
            ];

            file_put_contents($jsonFile, json_encode($comments, JSON_PRETTY_PRINT));
            $commentSuccess = "Comentario enviado correctamente.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Detalles del Producto - PrintHub</title>
<link rel="icon" href="../public/logoPrintHubIcon.ico" type="image/x-icon">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&family=Roboto+Mono:wght@700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="css/productDetail.css">
<link rel="stylesheet" href="css/footer.css">
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>

<header class="header-detalle">
    <div class="contenedor-header">
        <a href="../index.html" class="logo-texto">Print<span class="resaltado">Hub</span></a>
        <a href="../index.html" class="btn-volver">Volver</a>
    </div>
</header>

<section class="detalle-seccion">
    <div class="detalle-contenedor" id="detalle-contenedor">
        <p class="mensaje-centro">Cargando detalles del producto...</p>
    </div>
</section>

<section class="container mt-5 mb-5">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-bottom">
            <div class="d-flex justify-content-between align-items-center py-2">
                <h3 class="h4 mb-0 text-dark">Opiniones y Valoraciones</h3>
                <button id="btnLikeProduct" class="btn btn-outline-danger rounded-pill px-4">
                    <i class="far fa-heart me-2"></i> <span id="likeCount" class="fw-bold">0</span> Me gusta
                </button>
            </div>
        </div>
        <div class="card-body p-4">

            <div id="commentFormContainer" class="mb-5 p-4 bg-light rounded-3 border">
                <h5 class="mb-3 fw-bold">Deja tu opinión</h5>
                <form id="formComment" method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Puntuación:</label>
                        <select id="inputRating" name="inputRating" class="form-select w-auto shadow-sm" required>
                            <option value="">Selecciona</option>
                            <option value="5">⭐⭐⭐⭐⭐ Excelente</option>
                            <option value="4">⭐⭐⭐⭐ Muy bueno</option>
                            <option value="3">⭐⭐⭐ Correcto</option>
                            <option value="2">⭐⭐ Regular</option>
                            <option value="1">⭐ Malo</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Comentario:</label>
                        <textarea id="inputText" name="inputText" class="form-control shadow-sm" rows="3" placeholder="¿Qué te ha parecido el producto?" required></textarea>
                    </div>
                    <div class="mb-3">
                        <div class="g-recaptcha" data-sitekey="<?php echo $siteKey; ?>"></div>
                        <span id="captchaMessage" class="text-danger small">
                            <?php echo $captchaError; ?>
                        </span>
                        <span id="successMessage" class="text-success small">
                            <?php echo $commentSuccess; ?>
                        </span>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary px-4 fw-bold">Publicar Opinión</button>
                    </div>
                </form>
            </div>

            <hr class="my-4 opacity-25">

            <h5 class="mb-4 fw-bold text-secondary">Comentarios de usuarios</h5>
            <div id="commentsList">
                <?php
                // Mostrar comentarios existentes
                $jsonFile = __DIR__ . '/comments.json';
                $comments = file_exists($jsonFile) ? json_decode(file_get_contents($jsonFile), true) : [];
                if (count($comments) === 0) {
                    echo '<div class="text-center p-4 text-muted"><p>Aún no hay opiniones.</p></div>';
                } else {
                    foreach ($comments as $c) {
                        echo '<div class="card mb-3 border-0 shadow-sm"><div class="card-body">';
                        echo '<div class="d-flex justify-content-between"><strong>Usuario</strong><small>'.$c["date"].'</small></div>';
                        echo '<div class="mb-2 text-warning">'.str_repeat('⭐', $c["rating"]).'</div>';
                        echo '<p>'.$c["text"].'</p>';
                        echo '</div></div>';
                    }
                }
                ?>
            </div>

        </div>
    </div>
</section>

<footer class="pie-pagina">
        <div class="pie-pagina-contenedor">
          <div class="pie-pagina-izquierda">
            <h2 class="logo">Print<span class="resaltado">Hub</span></h2>
            <br />
            <div>
              <a class="iconos-sociales" href="#"
                ><img src="../public/facebook.svg" alt="Facebook"
              /></a>
              <a class="iconos-sociales" href="#"
                ><img src="../public/linkedin.svg" alt="LinkedIn"
              /></a>
              <a class="iconos-sociales" href="#"
                ><img src="../public/youtube.svg" alt="YouTube"
              /></a>
              <a class="iconos-sociales" href="#"
                ><img src="../public/insta.svg" alt="Instagram"
              /></a>
            </div>
          </div>

          <div class="pie-pagina-centro">
            <img src="../public/fotterImage.jpg" alt="Imagen global 3D" />
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
                <li>
                  📧
                  <a href="mailto:printhub@contact.me">printhub@contact.me</a>
                </li>
                <li>📍 Partida Cotes Altes, 27, 03804 Alcoi, Alicante</li>
                <li>🕒 Lunes – Viernes: 8:00 – 13:30</li>
              </ul>
            </div>
          </div>
        </div>
      </footer>

<script type="module" src="js/productDetail.js"></script>
</body>
</html>
