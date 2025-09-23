<?php

class Task {
    private $pdo;

    public function __construct() {
        $conn = require_once __DIR__ . "/../../config/database.php";
        // connexio a la bbdd
    }

    public function getTasksByUser($userId) {
       // recuperar les tasques d'un usuari
    }

    public function addTask($userId, $title) {
        // afegir tasca
    }

    public function toggleTask($taskId, $completed) {
        // possar una tasca com a completada
    }

    public function deleteTask($taskId) {
       // esborrar tasca
    }
}
?>