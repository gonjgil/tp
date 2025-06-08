<?php

class PlayerProfileRankingController {
    private $model;
    private $view;

    public function __construct($model, $view) {
        $this->model = $model;
        $this->view = $view;
    }

    public function show($id) {
        $player = $this->model->getPlayerData($id);

        if (!$player) {
            header("Location: /tp/ranking");
            exit;
        }

        $games = $this->model->getGamesByUser($id);

        // porcentaje
        $totalAnswers = $player->total_answers ?? 0;
        $correctAnswers = $player->correct_answers ?? 0;
        $accuracy = ($totalAnswers > 0) ? round(($correctAnswers / $totalAnswers) * 100, 2) : 0;

        $player->accuracy = $accuracy;

        $this->view->render("playerProfileRanking", [
            "player" => $player,
            "games" => $games
        ]);
    }

}
