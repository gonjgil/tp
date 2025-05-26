<?php

class AdminController{

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
        if (!isset($_SESSION['user']) || $_SESSION['user']['user_type'] !== 'administrador') {
            header("Location: /tp/login");
            exit;
        }
        $this->model->render("admin");
    }
}
