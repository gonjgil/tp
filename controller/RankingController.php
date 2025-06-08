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
        $position = 1;

        foreach ($rawRows as $row) {
            $total = (int)$row['total_answers'];
            $correct = (int)$row['correct_answers'];
            $percentage = $total > 0 ? round(($correct / $total) * 100) : 0;

            switch (true) {
                case ($percentage <= 25):
                    $label = 'Novato';
                    $cssClass = 'w3-win8-green';
                    break;
                case ($percentage <= 69):
                    $label = 'Intermedio';
                    $cssClass = 'w3-win8-amber';
                    break;
                default:
                    $label = 'Pro';
                    $cssClass = 'w3-win8-crimson';
                    break;
            }

            $rankingData[] = [
                'position'         => $position++,
                'id'               => $row['id'],
                'username'         => $row['username'],
                'profile_picture'  => $row['profile_picture'] ?? 'default.png',
                'games_played'     => $row['games_played'],
                'correct_answers'  => $correct,
                'total_answers'    => $total,
                'accuracy'         => $percentage,
                'type_label'       => $label,
                'type_class'       => $cssClass,
            ];
        }

        $this->view->render('ranking', [
            'ranking' => $rankingData
        ]);
    }

}