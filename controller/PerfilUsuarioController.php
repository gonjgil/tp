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

    /**
     * show($id = null):
     *  - Si $id es null, muestra el perfil del usuario logueado.
     *  - Si $id tiene un valor, muestra el perfil de ese ID.
     */
    public function show($id = null)
    {

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }


        if (!isset($_SESSION['user'])) {
            header("Location: /tp/login");
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
