<?php
session_start();
require_once("Configuration.php");

$controller = $_GET["controller"] ?? "home";
$method = $_GET["method"] ?? "index";

$configuration = new Configuration();
$router = $configuration->getRouter();

$jsonString = file_get_contents('configuration/rols.json');
$rolsConfig = json_decode($jsonString, true);

function checkAccess($controller, $method, $rolsConfig) {
    $controller = strtolower($controller);
    $method = strtolower($method);

    if ($controller === 'login') {
        return true;
    }
    
    foreach ($rolsConfig['public'] ?? [] as $publicController => $methodsAllowed) {
        if (strtolower($publicController) === $controller) {
            if (empty($methodsAllowed) || in_array($method, array_map('strtolower', $methodsAllowed))) {
                return true;
            }
        }
    }
    
    if (!isset($_SESSION['user'])) {
        return false;
    }
    
    $userRole = strtolower($_SESSION['user']['user_type'] ?? '');

    foreach ($rolsConfig[$userRole] ?? [] as $roleController => $methodsAllowed) {
        if (strtolower($roleController) === $controller) {
            if (empty($methodsAllowed) || in_array($method, array_map('strtolower', $methodsAllowed))) {
                return true;
            }
        }
    }

    return false;
}

if (!checkAccess($controller, $method, $rolsConfig)) {
    header("Location: /login");
    exit();
}

$router->go($controller, $method);