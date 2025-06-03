<?php

class PerfilUsuarioController
{
    private $view;
    private $model;

    public function __construct($view, $userModel)
    {
        $this->view = $view;
        $this->model = $userModel;
    }

    public function show()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user'])) {
            header("Location: /tp/login");
            exit();
        }

        $id = $_SESSION['user']['id'];
        $user = $this->model->getUserById($id);

        if (!$user) {
            die("No se encontró el usuario.");
        }

        $this->view->render("perfilUsuario", $user);
    }
}
