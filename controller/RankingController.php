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
        $data = $this->model->getRanking();

        $rawRows = $data['rows'];
        $maxGames = $data['maxGames'] ?: 1;
        $maxTotalAnswers = $data['maxTotalAnswers'] ?: 1;

        $rankingData = [];

        foreach ($rawRows as $row) {
            $total = (int)$row['total_answers'];
            $correct = (int)$row['correct_answers'];
            $gamesPlayed = (int)$row['games_played'];

            $precision = $total > 0 ? ($correct / $total) : 0;
            $gamesNormalized = $gamesPlayed / $maxGames;
            $answersNormalized = $total / $maxTotalAnswers;


            $scoreNormalized = 0.5 * $precision + 0.3 * $gamesNormalized + 0.2 * $answersNormalized;
            $scorePoints = round($scoreNormalized * 1000);

            if ($precision <= 0.40) {
                $label = 'Novato';
                $cssClass = 'w3-win8-green';
            } elseif ($precision <= 0.70) {
                $label = 'Intermedio';
                $cssClass = 'w3-win8-amber';
            } else {
                $label = 'Pro';
                $cssClass = 'w3-win8-crimson';
            }

            $rankingData[] = [
                'id'               => $row['id'],
                'username'         => $row['username'],
                'profile_picture'  => $row['profile_picture'] ?? 'default.png',
                'games_played'     => $gamesPlayed,
                'correct_answers'  => $correct,
                'total_answers'    => $total,
                'accuracy'         => round($precision * 100),
                'type_label'       => $label,
                'type_class'       => $cssClass,
                'score'            => $scorePoints
            ];
        }

        // orden
        usort($rankingData, function($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        // posicion
        $position = 1;
        foreach ($rankingData as &$item) {
            $item['position'] = $position++;
        }
        unset($item);

        $this->view->render('ranking', [
            'ranking' => $rankingData
        ]);
    }


}