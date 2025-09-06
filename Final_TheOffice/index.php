<?php

$controller = $_GET['controller'] ?? 'auth';
$action = $_GET['action'] ?? 'login';

$controllerFile = __DIR__ . '/controllers/' . ucfirst($controller) . 'Controller.php';

if (file_exists($controllerFile)) {
    require_once $controllerFile;

    $controllerClass = ucfirst($controller) . 'Controller';
    $ctrl = new $controllerClass();

    if (method_exists($ctrl, $action)) {
        $ctrl->$action();
    } else {
        echo "Action not found!";
    }
} else {
    echo "Controller not found!";
}
