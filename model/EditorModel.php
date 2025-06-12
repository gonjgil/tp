<?php
class EditorModel {
    private $db;
    private $wrapper;

    public function __construct($dbOrWrapper) {
        if ($dbOrWrapper instanceof Database) {
            $this->wrapper = $dbOrWrapper;
            $this->db      = $dbOrWrapper->getConnection();
        } elseif ($dbOrWrapper instanceof \mysqli) {
            // Esta rama ya no se usará, pero la dejamos por compatibilidad
            $this->db = $dbOrWrapper;
        } else {
            throw new InvalidArgumentException(
                'EditorModel needs Database or mysqli'
            );
        }
    }
    public function getAllQuestions(): array {
        $sql = "SELECT q.id, q.question_text, q.approved, q.reported
                FROM questions q
                ORDER BY q.id DESC";
        $res = $this->db->query($sql);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getReportedQuestions(): array {
        $sql = "SELECT q.id, q.question_text, q.approved, q.reported
                FROM questions q
                WHERE q.reported = 1
                ORDER BY q.id DESC";
        $res = $this->db->query($sql);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }
    public function getReportedQuestionsWithDetails(): array {
        $sql = "
      SELECT
        q.id            AS question_id,
        q.question_text,
        r.id            AS report_id,
        r.report,
        r.reported_at,
        r.reported_by,
        u.username
      FROM questions q
      JOIN reports   r ON r.id_question   = q.id
      JOIN users     u ON u.id            = r.reported_by
      WHERE q.reported = 1
      ORDER BY r.reported_at DESC
    ";

        $result = $this->db->query($sql);
        if (! $result) {
            return [];
        }

        $rows = $result->fetch_all(MYSQLI_ASSOC);

        $result->free();

        return $rows;
    }

    public function getQuestionById(int $questionId): ?array {
        $sqlQ = "SELECT id, question_text, approved, reported
                 FROM questions
                 WHERE id = ?";
        $stmt = $this->db->prepare($sqlQ);
        $stmt->bind_param("i", $questionId);
        $stmt->execute();
        $resQ = $stmt->get_result();
        $question = $resQ->fetch_assoc();
        $stmt->close();
        if (!$question) {
            return null;
        }

        $sqlA = "SELECT id, answer_text, is_correct
                 FROM answers
                 WHERE question_id = ?
                 ORDER BY id ASC";
        $stmt = $this->db->prepare($sqlA);
        $stmt->bind_param("i", $questionId);
        $stmt->execute();
        $resA = $stmt->get_result();
        $answers = $resA->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return [
            'question' => $question,
            'answers'  => $answers
        ];
    }

    public function toggleApproved(int $questionId, int $newStatus): void {
        $sql = "UPDATE questions
               SET approved = ?
             WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $newStatus, $questionId);
        $stmt->execute();
        $stmt->close();
    }

    public function deleteQuestion(int $questionId): void {

        $sql0 = "DELETE FROM reports WHERE id_question = ?";
        $stmt = $this->db->prepare($sql0);
        $stmt->bind_param("i", $questionId);
        $stmt->execute();
        $stmt->close();

        $sql1 = "DELETE FROM answers WHERE question_id = ?";
        $stmt = $this->db->prepare($sql1);
        $stmt->bind_param("i", $questionId);
        $stmt->execute();
        $stmt->close();

        $sql2 = "DELETE FROM questions WHERE id = ?";
        $stmt = $this->db->prepare($sql2);
        $stmt->bind_param("i", $questionId);
        $stmt->execute();
        $stmt->close();
    }

    public function deleteReport(int $reportId): void {
        $sql = "DELETE FROM reports WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $reportId);
        $stmt->execute();
        $stmt->close();
    }
    public function updateQuestion(int $questionId, string $text): void {
        $sql = "UPDATE questions SET question_text = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("si", $text, $questionId);
        $stmt->execute();
        $stmt->close();
    }

    public function updateAnswer(int $answerId, string $text, bool $isCorrect): void {
        $sql = "UPDATE answers SET answer_text = ?, is_correct = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $i = $isCorrectInt = ($isCorrect ? 1 : 0);
        $stmt->bind_param("sii", $text, $i, $answerId);
        $stmt->execute();
        $stmt->close();
    }
}