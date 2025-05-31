<?php
class QuizModel {
    private $db;
    public function __construct($database) { $this->db = $database; }

    public function startGame(int $userId): int {
        $sql  = "INSERT INTO games (user_id) VALUES (?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $insertId = $stmt->insert_id;
        return (int)$insertId;
    }

    public function getRandomQuestion(array $excludeIds) {
        if (count($excludeIds)) {
            $placeholders = implode(',', array_fill(0, count($excludeIds), '?'));
            $sql = "SELECT * FROM questions
              WHERE id NOT IN ($placeholders)
              ORDER BY RAND() LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $types = str_repeat('i', count($excludeIds));
            $stmt->bind_param($types, ...$excludeIds);
        } else {
            $sql = "SELECT * FROM questions ORDER BY RAND() LIMIT 1";
            $stmt = $this->db->prepare($sql);
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    public function getOptionsByQuestion(int $qid): array {
        $sql = "SELECT * FROM answers WHERE question_id = ? ORDER BY RAND()";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $qid);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function checkCorrect(int $optionId): bool {
        $sql = "SELECT is_correct FROM answers WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $optionId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return (bool)$row['is_correct'];
    }

    public function incrementScore(int $gameId) {
        $sql = "UPDATE games
              SET correct_answers = correct_answers + 1
            WHERE id_game = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $gameId);
        $stmt->execute();
    }

    public function getScore(int $gameId): int {
        $sql = "SELECT correct_answers FROM games WHERE id_game = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $gameId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return $row ? (int)$row['correct_answers'] : 0;
    }

    public function getTotalCorrectByUser(int $userId): int {
        $sql = "SELECT COALESCE(SUM(correct_answers),0) AS total
            FROM games
            WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return $row ? (int)$row['total'] : 0;
    }

    public function incrementTimesAnsweredQuestions(int $questionId){
        $sql = "UPDATE questions SET times_answered = times_answered + 1 WHERE id = ?";
        $this->db->prepare($sql)->execute([$questionId]);
    }

    public function incrementTimesIncorrectQuestions(int $questionId){
        $sql = "UPDATE questions SET times_incorrect = times_incorrect + 1 WHERE id = ?";
        $this->db->prepare($sql)->execute([$questionId]);
    }

    public function updateDifficultyQuestions(int $questionId){
        $sql = "UPDATE questions SET difficulty = CASE WHEN times_answered > 0 THEN (100 * times_incorrect / times_answered)
                ELSE 100 END WHERE id = ?";
        $this->db->prepare($sql)->execute([$questionId]);
    }


    public function incrementTotalAnswersUser(int $userId) {
        $sql = "UPDATE users SET total_answers = total_answers + 1 WHERE id = ?";
        $this->db->prepare($sql)->execute([$userId]);
    }


    public function incrementCorrectAnswersUser(int $userId){
        $sql = "UPDATE users SET correct_answers = correct_answers + 1 WHERE id = ?";
        $this->db->prepare($sql)->execute([$userId]);
    }


    public function updateUserDifficulty(int $userId){
        $sql = "UPDATE users SET difficulty = CASE WHEN total_answers > 0 THEN (1 - (correct_answers / total_answers))
                ELSE 1 END WHERE id = ?";
        $this->db->prepare($sql)->execute([$userId]);
    }


}