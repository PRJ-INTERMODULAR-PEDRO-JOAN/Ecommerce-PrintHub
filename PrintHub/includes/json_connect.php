<?php
// includes/json_connect.php

// Definimos la ruta al archivo JSON.
define('JSON_FILE', __DIR__ . '/../data/users.json');

/**
 * LEER: Obtiene la lista de usuarios dentro de la clave 'usuaris'
 */
function getAllUsers() {
    if (!file_exists(JSON_FILE)) {
        return []; 
    }
    $jsonContent = file_get_contents(JSON_FILE);
    $data = json_decode($jsonContent, true);

    if (isset($data['usuaris']) && is_array($data['usuaris'])) {
        return $data['usuaris'];
    }

    return [];
}

/**
 * GUARDAR: Guarda el array dentro de la estructura {"usuaris": [...]}
 */
function saveAllUsers($usersList) {
    $structure = [
        "usuaris" => $usersList
    ];
    // JSON_PRETTY_PRINT para que sea legible
    return file_put_contents(JSON_FILE, json_encode($structure, JSON_PRETTY_PRINT));
}

/**
 * Buscar por Username
 */
function findUserByUsername($username) {
    $users = getAllUsers();
    foreach ($users as $user) {
        if (isset($user['nom_usuari']) && $user['nom_usuari'] === $username) {
            return $user;
        }
    }
    return null;
}

/**
 * Buscar por ID
 */
function findUserById($id) {
    $users = getAllUsers();
    foreach ($users as $user) {
        if (isset($user['id']) && $user['id'] == $id) {
            return $user;
        }
    }
    return null;
}

/**
 * Crear Usuario (MODIFICADA: LOGICA DE HUECOS LIBRES)
 */
function createUser($userData) {
    $users = getAllUsers();
    
    // 1. Obtenemos todos los IDs que existen actualmente
    $existingIds = [];
    if (!empty($users)) {
        $existingIds = array_column($users, 'id');
    }

    // 2. Buscamos el primer ID libre empezando desde el 1
    // Ejemplo: Si existen [1, 4], el bucle hará:
    // - ¿Existe el 1? Sí. $newId pasa a 2.
    // - ¿Existe el 2? No. Bucle termina. Se asigna el 2.
    $newId = 1;
    while (in_array($newId, $existingIds)) {
        $newId++;
    }
    
    $userData['id'] = $newId;
    $users[] = $userData;

    // 3. (Opcional pero recomendado) Ordenamos el array por ID para que el JSON quede limpio
    usort($users, function($a, $b) {
        return $a['id'] - $b['id'];
    });

    if (saveAllUsers($users)) {
        return $userData;
    } else {
        return false;
    }
}

/**
 * Actualizar Usuario
 */
function updateUser($id, $updateData) {
    $users = getAllUsers();
    $userFound = false;

    foreach ($users as $key => $user) {
        if ($user['id'] == $id) {
            $users[$key] = array_merge($user, $updateData);
            $userFound = true;
            break;
        }
    }

    if ($userFound) {
        return saveAllUsers($users);
    }

    return false;
}

/**
 * Eliminar Usuario
 */
function deleteUser($id) {
    $users = getAllUsers();
    
    // Filtramos el array
    $newUsersList = array_filter($users, function($user) use ($id) {
        return $user['id'] != $id;
    });

    // NOTA: En esta versión NO reindexamos con array_values() ciegamente si queremos
    // mantener la consistencia estricta de IDs vs lógica, pero como estamos
    // usando JSON simple, array_values está bien para quitar claves numéricas del array de PHP,
    // los IDs internos 'id': X se mantienen.
    $newUsersList = array_values($newUsersList);

    return saveAllUsers($newUsersList);
}
?>