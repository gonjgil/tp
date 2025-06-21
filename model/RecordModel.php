<?php

class RecordModel {

    private $database;

    public function __construct($database) {
        $this->database = $database;
    }

    public function getUserGames($userId) {
        $stmt = $this->database->prepare("
        SELECT id_game, correct_answers, start_time
        FROM games
        WHERE user_id = ?
        ORDER BY start_time DESC
    ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        $games = [];
        $totalPoints = 0;
        $counter = 1;

        while ($row = $result->fetch_assoc()) {
            $totalPoints += (int)$row['correct_answers'];
            $games[] = [
                'number' => $counter++, // partida 1, 2, 3...
                'start_time' => date("d/m/Y H:i", strtotime($row['start_time'])),
                'points' => (int)$row['correct_answers'],
                'total' => $totalPoints
            ];
        }

        return $games;
    }


}
