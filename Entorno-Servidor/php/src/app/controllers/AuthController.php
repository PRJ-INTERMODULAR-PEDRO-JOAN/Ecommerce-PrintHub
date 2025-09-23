<?php
require_once __DIR__ . '/../models/User.php';
session_start();

class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function login() {
       // logueja usuari
    }

    public function register() {
       // registra usuari
    }

    public function logout() {
        // deslogueja a l'usuari
    }
}