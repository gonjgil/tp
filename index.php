<?php
require_once("Configuration.php");
session_start();
$configuration = new Configuration();
$router = $configuration->getRouter();

$router->go(
);