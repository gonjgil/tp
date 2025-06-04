<?php


class RankingController
{
    private $model, $view;

    public function __construct($model, $view)
    {
        $this->model = $model;
        $this->view  = $view;
    }

    public function index()
    {

        $this->showRanking();
    }

    public function showRanking()
    {
        $rawRows = $this->model->getRanking();

        $rankingData = [];

        foreach ($rawRows as $row) {
            $score = (float)$row['difficulty']; // Ej: 0.625

            if ($score < 0.25) {
                $label    = 'Fácil';
                $cssClass = 'w3-win8-green';
            }
            elseif ($score < 0.75) {
                $label    = 'Media';
                $cssClass = 'w3-win8-amber';
            }
            else {
                $label    = 'Difícil';
                $cssClass = 'w3-win8-crimson';
            }

            $rankingData[] = [
                'id'               => $row['id'],
                'username'         => $row['username'],
                'correct_answers'  => $row['correct_answers'],
                'total_answers'    => $row['total_answers'],
                'difficulty_raw'   => $row['difficulty'],
                'difficulty_label' => $label,
                'difficulty_class' => $cssClass,
            ];
        }

        $this->view->render('ranking', [
            'ranking' => $rankingData
        ]);
    }

    public function profile($id)
    {
        $player = $this->model->getPlayerById($id);
        if ($player) {
            $this->view->render("playerProfile", ["player" => $player]);
        } else {
            header("Location: /tp/ranking");
            exit;
        }
    }
}
