<?php

class HomeController
{
    private $view;

    public function __construct($view)
    {
        $this->view = $view;
    }

    public function show() {
    $data = [];
    $this->view->render('home', $data);
}
}