<?php
session_start();

// 1. Destruir variables de sessió
$_SESSION = array();

// 2. Destruir la cookie de sessió si existeix
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 3. Eliminar la nostra cookie personalitzada 'user_id'
if (isset($_COOKIE['user_id'])) {
    setcookie('user_id', '', time() - 3600, "/");
}

// 4. Destruir la sessió
session_destroy();

// 5. Redirigir
header("Location: login.php");
exit;
?>  