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
            exit;
        }

        $id = $_SESSION['user']['id'];
        $user = $this->model->getUserById((int)$id);

        if (!$user) {
            die("No existe usuario con ID $id.");
        }

        echo "<pre>";
        print_r($user);
        echo "</pre>";
        exit;

        $this->view->render('perfilUsuario', ['user' => $user]);
    }


}
