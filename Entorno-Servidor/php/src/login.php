<?php
require_once './app/controllers/AuthController.php';
$authController = new AuthController();
$error = $authController->login();

require './views/auth/login.php';