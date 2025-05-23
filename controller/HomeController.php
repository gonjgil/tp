<?php

class HomeController
{
    private $view;

    public function __construct($view)
    {
        $this->view = $view;
    }

//    public function index()
//    {
//        $this->listar();
//    } //ver xq en algunas pc funciona y en otras no

    public function show()
    {
        $this->view->render("band");
    }
}