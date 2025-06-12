<?php
class ReportModel {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    public function getAllGamesByUser(int $userId) {
        $sql = "SELECT * FROM games WHERE user_id = ? ORDER BY start_time DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getQuestionsByGame(int $gameId) {
        $sql = "SELECT question_id FROM game_questions WHERE id_game = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $gameId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        return array_column($result, 'question_id');
    }

    public function saveReport($questionId, $reportText) {
        $stmt = $this->db->prepare("INSERT INTO reports (id_question, report) VALUES (?, ?)");
        $stmt->bind_param("is", $questionId, $reportText);
        return $stmt->execute();
    }
    
    public function markQuestionReported($questionId) {
        $stmt = $this->db->prepare("UPDATE questions SET reported = 1 WHERE id = ?");
        $stmt->bind_param("i", $questionId);
        $stmt->execute();
    }
}