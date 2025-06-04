<?php

class RecordModel {

    private $database;

    public function __construct($database) {
        $this->database = $database;
    }

    public function getUserGames(int $userId): array {
        $stmt = $this->database->prepare("
        SELECT id_game, correct_answers, total_questions, start_time, end_time
        FROM games
        WHERE user_id = ?
        ORDER BY start_time DESC
    ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

}
