<?php

class EditorController{
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
        if (!isset($_SESSION['user']) || $_SESSION['user']['user_type'] !== 'editor') {
            header("Location: /tp/login");
            exit();
        }
        $this->model->render("editor");
    }

}