<?php
require_once './app/controllers/TaskController.php';
$tasksContr= new TaskController();

require './views/tasks/index.php';