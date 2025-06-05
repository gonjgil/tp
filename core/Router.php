<?php

class Router
{
    private $defaultController;
    private $defaultMethod;
    private $configuration;

    public function __construct($defaultController, $defaultMethod, $configuration)
    {
        $this->defaultController = $defaultController;
        $this->defaultMethod = $defaultMethod;
        $this->configuration = $configuration;
    }

    public function go($controllerName, $methodName)
    {

        $controller = $this->getControllerFrom($controllerName);


        $uri      = trim($_SERVER['REQUEST_URI'], '/');
        $segments = explode('/', $uri);
        //     segments[0] = "tp"
        //     segments[1] = "perfilUsuario"
        //     segments[2] = "show"
        //     segments[3] = "4"


        if (isset($segments[3]) && is_numeric($segments[3])) {

            call_user_func([$controller, $methodName], $segments[3]);
        }
        else {

            $this->executeMethodFromController($controller, $methodName);
        }
    }

    private function getControllerFrom($controllerName)
    {
        $controllerName = 'get' . ucfirst($controllerName) . 'Controller';
        $validController = method_exists($this->configuration, $controllerName) ? $controllerName : $this->defaultController;
        return call_user_func(array($this->configuration, $validController));
    }

    private function executeMethodFromController($controller, $method)
    {
        $validMethod = method_exists($controller, $method) ? $method : $this->defaultMethod;
        call_user_func(array($controller, $validMethod));
    }
}