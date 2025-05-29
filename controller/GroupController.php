<?php

class GroupController
{
    private $model;
    private $view;

    public function __construct($model, $view)
    {
        $this->model = $model;
        $this->view = $view;
    }
    public function index()
    {
        $this->listar();
    }

    public function request()
    {
        $integrantes["integrantes"] = $this->model->getIntegrantes();
        $this->view->render("group", $integrantes);
    }

    public function add()
    {
        $nombre = $_POST["nombre"];
        $instrumento = $_POST["instrumento"];
        $this->model->add($nombre, $instrumento);
        $this->redirectTo("/tp/group/success");
    }

    public function success()
    {
        $this->view->render("groupSuccess");
    }

    private function redirectTo($str)
    {
        header("location:" . $str);
        exit();
    }


}