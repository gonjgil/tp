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

    public function createReport(int $questionId, int $userId, string $reportText): bool {
        $sql  = "INSERT INTO reports (id_question, report, reported_by) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("isi", $questionId, $reportText, $userId);
        return $stmt->execute();
    }

    public function getReportDetails(int $questionId) {
        $sql = "
      SELECT r.id AS report_id,
             r.id_question,
             r.report,
             r.reported_at,
             r.reported_by,
             u.username
        FROM reports r
        JOIN users  u ON u.id = r.reported_by
       WHERE r.id_question = ?
       ORDER BY r.reported_at DESC
       LIMIT 1
    ";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $questionId);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        return $res ?: null;
    }

    public function markQuestionReported($questionId) {
        $stmt = $this->db->prepare("UPDATE questions SET reported = 1 WHERE id = ?");
        $stmt->bind_param("i", $questionId);
        $stmt->execute();
    }
}