<?php
class RankingModel {

    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    public function getRanking()
    {
        $query = "SELECT u.id, u.username, u.profile_picture, COUNT(g.id_game) AS games_played, u.correct_answers, 
                  u.total_answers FROM users u  LEFT JOIN games g ON g.user_id = u.id 
                  WHERE u.id_rol = 3 AND u.is_active = 1 GROUP BY u.id";

        $rows = $this->db->query($query);

        $gamesList = array_column($rows, 'games_played');
        $answersList = array_column($rows, 'total_answers');

        $maxGames = !empty($gamesList) ? max($gamesList) : 1;
        $maxTotalAnswers = !empty($answersList) ? max($answersList) : 1;

        return [
            'rows' => $rows,
            'maxGames' => $maxGames,
            'maxTotalAnswers' => $maxTotalAnswers
        ];
    }


    public function getPlayerById($id) {
        $query = "SELECT username, name, last_name, profile_picture, total_answers, correct_answers, difficulty, city, country, lat, lng 
              FROM users 
              WHERE id = ? AND id_rol = 3";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }


}