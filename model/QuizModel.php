<?php
class QuizModel {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    public function startGame($userId) {
        $sql  = "INSERT INTO games (user_id) VALUES (?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $insertId = $stmt->insert_id;
        return (int)$insertId;
    }

    public function getRandomQuestion($excludeSession, $forceReset = false) {
        $userId = $_SESSION['user']['id'];
        $userDifficulty = $this->getUserDifficulty($userId);
        list($minDiff, $maxDiff) = $this->getDifficultyRangeForUser($userDifficulty);

        if ($forceReset) {
            $this->clearUserQuestionHistory($userId);
            $_SESSION['asked_questions'] = [];
            $excludeIds = [];
        } else {
            $excludeDb  = $this->getQuestionsAlreadyAnsweredByUser($userId);
            $excludeIds = array_unique(array_merge($excludeSession, $excludeDb));
        }

        $question = $this->findQuestionByDifficultyAndExclusion($minDiff, $maxDiff, $excludeIds);

        if (!$question) {
            $question = $this->findQuestionByDifficultyAndExclusion(0, 100, $excludeIds);
        }

        return $question;
    }

    private function getQuestionsAlreadyAnsweredByUser($userId) {
        $sql = "SELECT DISTINCT q.question_id
                FROM game_questions q
                JOIN games g ON q.id_game = g.id_game
                WHERE g.user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        return array_column($result, 'question_id');
    }

    public function clearUserQuestionHistory($userId) {
        $sql = "DELETE q FROM game_questions q
                JOIN games g ON q.id_game = g.id_game
                WHERE g.user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
    }

    public function saveQuestionToGame($gameId, $questionId) {
        $sql = "INSERT IGNORE INTO game_questions (id_game, question_id) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $gameId, $questionId);
        $stmt->execute();
    }

    public function getUserDifficulty($userId) {
        $sql = "SELECT difficulty FROM users WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result ? (float)$result['difficulty'] : 0.2;
    }

    public function getDifficultyRangeForUser($userDifficulty) {
        if ($userDifficulty <= 0.3) {
            return [70, 100];
        } else {
            return [0, 30];
        }
    }

    public function findQuestionByDifficultyAndExclusion($minDiff, $maxDiff, $excludeIds = array()) {
        $sql = "SELECT q.*, c.name AS category_name
                FROM questions q
                JOIN categories c ON q.category_id = c.id
                WHERE q.difficulty BETWEEN ? AND ?";

        $params = array($minDiff, $maxDiff);
        $types = "ii";

        if (count($excludeIds)) {
            $placeholders = implode(',', array_fill(0, count($excludeIds), '?'));
            $sql .= " AND q.id NOT IN ($placeholders)";
            $types .= str_repeat('i', count($excludeIds));
            $params = array_merge($params, $excludeIds);
        }

        $sql .= " ORDER BY RAND() LIMIT 1";
        $stmt = $this->db->prepare($sql);
        call_user_func_array(array($stmt, 'bind_param'), $this->refValues(array_merge(array($types), $params)));
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc() ?: null;
    }

    public function getOptionsByQuestion($qid) {
        $sql = "SELECT * FROM answers WHERE question_id = ? ORDER BY RAND()";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $qid);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function checkCorrect($optionId) {
        $sql = "SELECT is_correct FROM answers WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $optionId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return (bool)$row['is_correct'];
    }

    public function incrementScore($gameId) {
        $sql = "UPDATE games SET correct_answers = correct_answers + 1 WHERE id_game = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $gameId);
        $stmt->execute();
    }

    public function getScore($gameId) {
        $sql = "SELECT correct_answers FROM games WHERE id_game = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $gameId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return $row ? (int)$row['correct_answers'] : 0;
    }

    public function getTotalCorrectByUser($userId) {
        $sql = "SELECT COALESCE(SUM(correct_answers),0) AS total FROM games WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return $row ? (int)$row['total'] : 0;
    }

    public function incrementTimesAnsweredQuestions($questionId) {
        $sql = "UPDATE questions SET times_answered = times_answered + 1 WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $questionId);
        $stmt->execute();
    }

    public function incrementTimesIncorrectQuestions($questionId) {
        $sql = "UPDATE questions SET times_incorrect = times_incorrect + 1 WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $questionId);
        $stmt->execute();
    }

    public function updateDifficultyQuestions($questionId) {
        $sql = "UPDATE questions SET difficulty = CASE WHEN times_answered > 0 THEN (100 * times_incorrect / times_answered) ELSE 100 END WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $questionId);
        $stmt->execute();
    }

    public function incrementTotalAnswersUser($userId) {
        $sql = "UPDATE users SET total_answers = total_answers + 1 WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
    }

    public function incrementCorrectAnswersUser($userId) {
        $sql = "UPDATE users SET correct_answers = correct_answers + 1 WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
    }

    public function updateUserDifficulty($userId) {
        $sql = "UPDATE users SET difficulty = CASE WHEN total_answers >= 6 THEN (1 - (correct_answers / total_answers)) ELSE difficulty END WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
    }

    public function incrementTotalQuestions($gameId) {
        $sql = "UPDATE games SET total_questions = total_questions + 1 WHERE id_game = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $gameId);
        $stmt->execute();
    }

    public function endGame($gameId) {
        $sql = "UPDATE games SET end_time = NOW() WHERE id_game = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $gameId);
        $stmt->execute();
    }

    public function getQuestionById($id) {
        $sql = "SELECT q.*, c.name AS category_name FROM questions q JOIN categories c ON q.category_id = c.id WHERE q.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    private function refValues($arr) {
        if (strnatcmp(phpversion(),'5.3') >= 0) {
            $refs = array();
            foreach($arr as $key => $value) {
                $refs[$key] = &$arr[$key];
            }
            return $refs;
        }
        return $arr;
    }




}