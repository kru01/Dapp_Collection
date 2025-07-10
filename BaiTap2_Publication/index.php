<?php
session_start();

$controller = $_GET['controller'] ?? 'home';
$action = $_GET['action'] ?? 'index';

$controllerFile = "controllers/{$controller}_controller.php";

if (file_exists($controllerFile)) {
    require_once $controllerFile;

    $className = ucfirst($controller) . 'Controller';
    $controllerInstance = new $className();

    if (method_exists($controllerInstance, $action)) {
        $controllerInstance->$action();
    } else {
        echo "Action $action not found!";
    }
} else {
    echo "Controller $controller not found!";
}
