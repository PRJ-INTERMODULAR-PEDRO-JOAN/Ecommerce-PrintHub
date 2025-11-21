<?php
session_start();
header('Content-Type: application/json');

$likesApi = 'http://jsonserver4:3003/likes';

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $productId = $input['product_id'];
    $action = $input['action'] ?? '';

    // 1. OBTENER CONTADOR Y ESTADO
    if ($action === 'get') {
        $likes = callApi('GET', $likesApi . '?product_id=' . $productId);
        if (!is_array($likes)) $likes = [];

        $likedByUser = false;
        if (isset($_SESSION['user_id'])) {
            foreach ($likes as $l) {
                if ($l['user_id'] == $_SESSION['user_id']) {
                    $likedByUser = true;
                    break;
                }
            }
        }
        echo json_encode(['count' => count($likes), 'liked' => $likedByUser]);
        exit;
    }

    // 2. DAR / QUITAR LIKE (TOGGLE)
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['error' => 'Unauthenticated']);
        exit;
    }

    $userId = $_SESSION['user_id'];
    
    // Consultamos si ya existe el like para este usuario y producto
    $query = $likesApi . '?product_id=' . $productId . '&user_id=' . $userId;
    $existingLike = callApi('GET', $query);

    if (!empty($existingLike) && is_array($existingLike)) {
        // YA EXISTE -> LO BORRAMOS (Dislike)
        $likeId = $existingLike[0]['id']; // json-server devuelve un array
        callApi('DELETE', $likesApi . '/' . $likeId);
        echo json_encode(['success' => true, 'status' => 'unliked']);
    } else {
        // NO EXISTE -> LO CREAMOS (Like)
        $newLike = ['product_id' => $productId, 'user_id' => $userId];
        callApi('POST', $likesApi, $newLike);
        echo json_encode(['success' => true, 'status' => 'liked']);
    }
}