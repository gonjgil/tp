<?php

class PerfilUsuarioController
{
    private $view;
    private $model;

    public function __construct($view, $userModel)
    {
        $this->view  = $view;
        $this->model = $userModel;
    }

    public function show($id = null)
    {

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }


        if (!isset($_SESSION['user'])) {
            header("Location: /login");
            exit();
        }

        if ($id === null) {
            $id = $_SESSION['user']['id'];
        }

        $user = $this->model->getUserById($id);
        if (!$user) {
            die("Usuario con ID = $id no encontrado.");
        }

        $this->view->render("perfilUsuario", $user);
    }
}