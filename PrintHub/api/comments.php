<?php
session_start();
header('Content-Type: application/json');

// Configuración
$apiUrl = 'http://jsonserver3:3002/comments'; 
$usersFile = '../data/users.json';

// Helper API
function callApi($method, $url, $data = false) {
    $curl = curl_init();
    switch ($method) {
        case "POST":
            curl_setopt($curl, CURLOPT_POST, 1);
            if ($data) {
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            }
            break;
        case "PUT":
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
            if ($data) {
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            }
            break;
        case "DELETE":
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
            break;
    }
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($curl);
    curl_close($curl);
    return json_decode($result, true);
}

// Helper Admin (ID 2 o usuario 'admin')
function isAdmin() {
    if (!isset($_SESSION['user_id'])) return false;
    if ($_SESSION['user_id'] == 2) return true;
    if (isset($_SESSION['nom_usuari']) && $_SESSION['nom_usuari'] === 'admin') return true;
    return false;
}

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

// --- 1. LEER (GET) ---
if ($method === 'GET') {
    $productId = $_GET['product_id'] ?? null;
    if (!$productId) { echo json_encode([]); exit; }

    $comments = callApi('GET', $apiUrl . '?product_id=' . $productId);
    
    // Leer usuarios
    $usersData = file_get_contents($usersFile);
    $json = json_decode($usersData, true);
    $usersList = $json['usuaris'] ?? []; 

    $result = [];
    $totalRating = 0;
    $count = 0;

    if (is_array($comments)) {
        foreach ($comments as $c) {
            // Asociar perfil
            $authorName = 'Usuario Anónimo';
            $authorId = $c['user_id'];
            
            foreach ($usersList as $u) {
                if ((string)$u['id'] === (string)$authorId) {
                    $authorName = $u['nom_usuari'] ?? $u['nom'] ?? 'Usuario';
                    break;
                }
            }

            // PERMISOS
            $isOwner = (isset($_SESSION['user_id']) && (string)$_SESSION['user_id'] === (string)$authorId);
            $isAdmin = isAdmin();

            $c['author'] = htmlspecialchars($authorName);
            
            // AQUI ESTÁ EL CAMBIO: Admin también puede editar
            $c['can_edit'] = $isOwner || $isAdmin;   
            $c['can_delete'] = $isOwner || $isAdmin;
            
            $result[] = $c;
            $totalRating += (int)$c['rating'];
            $count++;
        }
    }

    $avg = ($count > 0) ? round($totalRating / $count, 1) : 0;
    usort($result, fn($a, $b) => strtotime($b['date']) - strtotime($a['date']));

    echo json_encode(['comments' => $result, 'avg_rating' => $avg]);
    exit;
}

// --- 2. AÑADIR (POST) ---
if ($method === 'POST' && $action === 'add') {
    if (!isset($_SESSION['user_id'])) { echo json_encode(['error' => 'No autenticado']); exit; }

    $input = json_decode(file_get_contents('php://input'), true);
    $newComment = [
        'product_id' => $input['product_id'],
        'user_id'    => $_SESSION['user_id'],
        'text'       => htmlspecialchars($input['text']),
        'rating'     => (int)$input['rating'],
        'date'       => date('Y-m-d H:i:s')
    ];
    callApi('POST', $apiUrl, $newComment);
    echo json_encode(['success' => true]);
    exit;
}

// --- 3. EDITAR (POST) ---
if ($method === 'POST' && $action === 'edit') {
    if (!isset($_SESSION['user_id'])) { echo json_encode(['error' => 'No autenticado']); exit; }

    $input = json_decode(file_get_contents('php://input'), true);
    $id = $input['comment_id'];
    
    $comment = callApi('GET', $apiUrl . '/' . $id);

    // PERMISO: Dueño O Admin
    if ($comment && ($comment['user_id'] == $_SESSION['user_id'] || isAdmin())) {
        $comment['text'] = htmlspecialchars($input['text']);
        $comment['rating'] = (int)$input['rating'];
        callApi('PUT', $apiUrl . '/' . $id, $comment);
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'No tienes permiso']);
    }
    exit;
}

// --- 4. BORRAR (POST) ---
if ($method === 'POST' && $action === 'delete') {
    if (!isset($_SESSION['user_id'])) { echo json_encode(['error' => 'No autenticado']); exit; }
    
    $input = json_decode(file_get_contents('php://input'), true);
    $id = $input['comment_id'];
    $comment = callApi('GET', $apiUrl . '/' . $id);

    // PERMISO: Dueño O Admin
    if ($comment && ($comment['user_id'] == $_SESSION['user_id'] || isAdmin())) {
        callApi('DELETE', $apiUrl . '/' . $id);
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'No tienes permiso']);
    }
    exit;
}