<?php

class RecordController
{
    private $view;
    private $model;

    public function __construct($view, $recordModel)
    {
        $this->view = $view;
        $this->model = $recordModel;
    }

    public function index()
    {
        $userId = $_SESSION['user']['id'];
        $username = $_SESSION['user']['username'];
        $games = $this->model->getUserGames($userId);

        $this->view->render('record', [
            'username' => $username,
            'games' => $games
        ]);
    }

}
