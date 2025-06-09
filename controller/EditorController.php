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
        $this->model->render("editor");
    }

}