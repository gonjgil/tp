<?php

class RankingController {
    private $model, $view;
    public function __construct($model, $view) {
        $this->model = $model;
        $this->view  = $view;
    }

    public function index() {
        $this->showRanking();
    }


    public function showRanking() {
        $ranking = $this->model->getRanking();
        $this->view->render("ranking", ["ranking" => $ranking]);

    }

    public function showProfile($id) {
        $player = $this->model->getPlayerById($id);
        if ($player) {
            $this->view->render("playerProfile", ["player" => $player]);
        } else {
            $this->redirect("/tp/ranking");
        }
    }

    public function profile($id) {
        $this->showProfile($id);
    }




}
