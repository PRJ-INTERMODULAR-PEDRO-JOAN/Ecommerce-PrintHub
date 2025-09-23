<?php
require_once __DIR__ . '/../models/Task.php';
session_start();

class TaskController {
    private $taskModel;

    public function __construct() {
        $this->taskModel = new Task();
    }

    public function listTasks() {
        // llistat de tasques
    }

    public function createTask() {
        // crear tasca
    }

    public function deleteTask() {
        // esborrar tasca
    }
}