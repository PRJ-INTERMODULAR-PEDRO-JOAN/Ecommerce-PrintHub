<?php
session_start();
header('Content-Type: application/json');

// URLs de los contenedores Docker
$commentsApi = 'http://jsonserver3:3002/comments';
$usersApi    = 'http://jsonserver2:3001/users'; // Asumiendo que jsonserver2 tiene tus usuarios

// Función auxiliar para llamar a las APIs internas
function callApi($method, $url, $data = false) {
    $curl = curl_init();
    switch ($method) {
        case "POST":
            curl_setopt($curl, CURLOPT_POST, 1);
            if ($data) curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
            break;
        case "DELETE":
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
            break;
    }
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($curl);
    curl_close($curl);
    return json_decode($result, true);
}

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

// --- 1. OBTENER COMENTARIOS (GET) ---
if ($method === 'GET') {
    $productId = $_GET['product_id'] ?? null;
    if (!$productId) {
        echo json_encode([]);
        exit;
    }

    // 1. Pedimos los comentarios filtrados por producto al json-server
    $comments = callApi('GET', $commentsApi . '?product_id=' . $productId);
    
    // 2. Pedimos la lista de usuarios para saber sus nombres
    $users = callApi('GET', $usersApi);
    
    $result = [];
    $totalRating = 0;
    $count = 0;

    if (is_array($comments)) {
        foreach ($comments as $c) {
            // Buscar nombre del autor
            $authorName = 'Usuario Anónimo';
            if (is_array($users)) {
                foreach ($users as $u) {
                    // Ajusta 'id' según tu users.json (puede ser string o int)
                    if (isset($u['id']) && $u['id'] == $c['user_id']) {
                        // Ajusta 'nombre' o 'username' según tu users.json
                        $authorName = $u['nombre'] ?? $u['username'] ?? 'Usuario';
                        break;
                    }
                }
            }

            $c['author'] = htmlspecialchars($authorName);
            // Determinar si el usuario actual puede borrar este comentario
            $c['is_owner'] = (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $c['user_id']);
            $c['is_admin'] = (isset($_SESSION['role']) && $_SESSION['role'] === 'admin');
            
            $result[] = $c;
            $totalRating += (int)$c['rating'];
            $count++;
        }
    }

    // Calcular media
    $avgRating = ($count > 0) ? ($totalRating / $count) : 0;

    echo json_encode(['comments' => $result, 'avg_rating' => $avgRating]);
    exit;
}

// --- 2. AÑADIR COMENTARIO (POST) ---
if ($method === 'POST' && $action === 'add') {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['error' => 'No autenticado']);
        exit;
    }

    $input = json_decode(file_get_contents('php://input'), true);

    $newComment = [
        'product_id' => $input['product_id'],
        'user_id'    => $_SESSION['user_id'], // ID de la sesión PHP
        'text'       => htmlspecialchars($input['text']),
        'rating'     => (int)$input['rating'],
        'date'       => date('Y-m-d H:i:s')
    ];

    // Enviamos a json-server (él genera el ID automáticamente)
    callApi('POST', $commentsApi, $newComment);
    echo json_encode(['success' => true]);
    exit;
}

// --- 3. BORRAR COMENTARIO (POST action=delete) ---
if ($method === 'POST' && $action === 'delete') {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['error' => 'No autenticado']);
        exit;
    }
    
    $input = json_decode(file_get_contents('php://input'), true);
    $commentId = $input['comment_id'];

    // Verificar permiso: Obtenemos el comentario primero
    $comment = callApi('GET', $commentsApi . '/' . $commentId);
    
    if (isset($comment['user_id'])) {
        $isAdmin = (isset($_SESSION['role']) && $_SESSION['role'] === 'admin');
        if ($comment['user_id'] == $_SESSION['user_id'] || $isAdmin) {
            callApi('DELETE', $commentsApi . '/' . $commentId);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['error' => 'Sin permiso']);
        }
    } else {
        echo json_encode(['error' => 'No encontrado']);
    }
    exit;
}