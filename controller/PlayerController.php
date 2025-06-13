<?php

class PlayerController {
    private $model;
    private $view;

    public function __construct($model, $view)
    {
        $this->model = $model;
        $this->view = $view;
    }

    public function index()
    {
        $this->panel();
    }

    public function panel()
    {
        $mensaje = $_SESSION['mensaje'] ?? null;
        unset($_SESSION['mensaje']);

        $this->model->render("player", [
            'mensaje' => $mensaje
        ]);
    }
}
