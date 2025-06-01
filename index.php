<?php
require_once("Configuration.php");
session_start();

$controller = $_GET["controller"] ?? "home";
$method = $_GET["method"] ?? "index";

$configuration = new Configuration();
$router = $configuration->getRouter();

$router->go($controller, $method);