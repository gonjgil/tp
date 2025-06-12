<?php
class QuizController {
    private $model, $view;

    public function __construct($model, $view) {
        $this->model = $model;
        $this->view  = $view;
    }

    public function newGame() {
        $gameId = $this->model->startGame((int)$_SESSION['user']['id']);
        $_SESSION['current_game'] = (int)$gameId;
        $_SESSION['asked_questions'] = [];
        $this->clearQuestionState();
        $this->clearFeedbackState();
        $this->clearReportState();
        header("Location: /quiz/next");
        exit();
    }

    public function next() {
        $this->handleQuestionTimeout();

        if ($this->renderCurrentQuestionIfExists()) {
            return;
        }

        $q = $this->getNextAvailableQuestion();

        if (!$q) {
            $this->view->render('error', ['message' => 'No hay preguntas disponibles.']);
            return;
        }

        $this->renderNewQuestion($q);
    }

    public function answer() {
        if (!$this->isAnswerRequestValid()) {
            $this->endAsCheat();
        }

        $questionId = (int)$_POST['question_id'];
        $optionId = (int)$_POST['answer'];
        $submittedStartTime = (int)$_POST['question_start_time'];

        if ($questionId !== $_SESSION['current_question_id'] || $submittedStartTime !== $_SESSION['question_start_time']) {
            $this->endAsCheat();
        }

        $expectedSignature = $this->generateSignature($questionId, $submittedStartTime);
        if ($_SESSION['signature'] !== $expectedSignature) {
            $this->endAsCheat();
        }

        $elapsed = time() - $_SESSION['question_start_time'];
        if ($elapsed > 15) {
            $this->clearQuestionState();
            $_SESSION['finish_reason'] = 'timeout';
            header("Location: /quiz/finish");
            exit();
        }

        if (!in_array($optionId, array_column($_SESSION['current_options'], 'id'))) {
            $this->endAsCheat();
        }

        $correct = $this->model->checkCorrect($optionId);
        $userId  = $_SESSION['user']['id'];
        $gameId  = $_SESSION['current_game'];

        $this->model->saveQuestionToGame($gameId, $questionId);
        $this->model->incrementTotalAnswersUser($userId);
        $this->model->incrementTotalQuestions($gameId);

        if ($correct) {
            $this->model->incrementScore($gameId);
            $this->model->incrementCorrectAnswersUser($userId);
            $_SESSION['last_answer_correct'] = true;
        } else {
            $this->model->incrementTimesIncorrectQuestions($questionId);
            $_SESSION['last_answer_correct'] = false;
            $_SESSION['finish_reason'] = 'wrong';
        }

        $this->model->updateDifficultyQuestions($questionId);
        $this->model->updateUserDifficulty($userId);

        $_SESSION['selected_option_id'] = $optionId;
        $_SESSION['last_question_id'] = $questionId;

        $this->clearQuestionState();

        header("Location: /quiz/feedback");
        exit();
    }

    public function feedback() {
        if (!isset($_SESSION['last_answer_correct'], $_SESSION['last_question_id'], $_SESSION['current_options'])) {
            header("Location: /quiz/next");
            exit();
        }

        $correct = $_SESSION['last_answer_correct'];
        $questionId = (int)$_SESSION['last_question_id'];

        $q = $this->model->getQuestionById($questionId);
        $opts = $_SESSION['current_options'];
        $score = $this->model->getScore((int)$_SESSION['current_game']);
        $q['category_class'] = $this->getCategoryClass($q['category_name']);

        $correctOptionId = $this->model->getCorrectOptionId($questionId);
        $selectedOptionId = $_SESSION['selected_option_id'];

        foreach ($opts as &$opt) {
            $opt['isSelected'] = ($opt['id'] == $selectedOptionId);
            $opt['isCorrectAnswer'] = ($opt['id'] == $correctOptionId);
            $opt['panelClass'] = '';

            if ($opt['isSelected']) {
                if ($opt['isCorrectAnswer']) {
                    $opt['panelClass'] = 'w3-border w3-border-green w3-topbar w3-bottombar w3-leftbar and w3-rightbar';
                } else {
                    $opt['panelClass'] = 'w3-border w3-border-red w3-topbar w3-bottombar w3-leftbar and w3-rightbar';
                }
            }
        }
        unset($opt);

        $this->view->render('question', [
            'question' => $q,
            'options'  => $opts,
            'score'    => $score,
            'feedback' => ['isCorrect' => $correct]
        ]);

        $this->clearFeedbackState();
    }

    public function finish() {
        $score = $this->model->getScore((int)$_SESSION['current_game']);
        $total = $this->model->getTotalCorrectByUser((int)$_SESSION['user']['id']);

        $reason = $_SESSION['finish_reason'] ?? null;
        unset($_SESSION['finish_reason']);

        $reasonText = match($reason) {
            'wrong'   => 'Respuesta incorrecta',
            'timeout' => 'Su tiempo se terminó',
            'cheat'   => 'Partida finalizada por trampa',
            default   => 'Partida finalizada'
        };

        $this->view->render('quizSummary', [
            'score'  => $score,
            'total'  => $total,
            'reason' => $reasonText,
        ]);
    }

    private function handleQuestionTimeout() {
        if (isset($_SESSION['question_start_time'])) {
            $elapsed = time() - (int)$_SESSION['question_start_time'];
            if ($elapsed > 15) {
                $this->clearQuestionState();
                $_SESSION['finish_reason'] = 'timeout';
                header("Location: /quiz/finish");
                exit();
            }
        }
    }

    private function renderCurrentQuestionIfExists() {
        if (isset($_SESSION['current_question_id'])) {
            $questionId = (int)$_SESSION['current_question_id'];
            $q = $this->model->getQuestionById($questionId);
            if (!$q) {
                $this->view->render('error', ['message' => 'Pregunta no encontrada.']);
                return true;
            }

            $opts = $_SESSION['current_options'] ?? $this->model->getOptionsByQuestion($questionId);
            $_SESSION['current_options'] = $opts;

            $score = $this->model->getScore((int)$_SESSION['current_game']);
            $q['category_class'] = $this->getCategoryClass($q['category_name']);
            $q['question_start_time'] = $_SESSION['question_start_time'];

            $this->view->render('question', [
                'question' => $q,
                'options'  => $opts,
                'score'    => $score
            ]);
            return true;
        }
        return false;
    }

    private function getNextAvailableQuestion() {
        $asked = $_SESSION['asked_questions'] ?? [];
        $q = $this->model->getRandomQuestion($asked);

        if (!$q) {
            $this->model->clearUserQuestionHistory((int)$_SESSION['user']['id']);
            $_SESSION['asked_questions'] = [];
            $q = $this->model->getRandomQuestion([]);
        }

        return $q;
    }

    private function renderNewQuestion(array $q) {
        $_SESSION['asked_questions'][] = (int)$q['id'];
        $_SESSION['current_question_id'] = (int)$q['id'];
        $_SESSION['question_start_time'] = time();

        $_SESSION['signature'] = $this->generateSignature((int)$q['id'], $_SESSION['question_start_time']);

        $this->model->incrementTimesAnsweredQuestions((int)$q['id']);

        $opts  = $this->model->getOptionsByQuestion((int)$q['id']);
        $_SESSION['current_options'] = $opts;

        $score = $this->model->getScore((int)$_SESSION['current_game']);
        $q['category_class'] = $this->getCategoryClass($q['category_name']);
        $q['question_start_time'] = $_SESSION['question_start_time'];

        $this->view->render('question', [
            'question' => $q,
            'options'  => $opts,
            'score'    => $score
        ]);
    }

    private function getCategoryClass(string $categoryName) {
        return match(strtolower($categoryName)) {
            'cultura general' => 'w3-red',
            'ciencia'         => 'w3-blue',
            'historia'        => 'w3-purple',
            default           => 'w3-grey'
        };
    }

    private function endAsCheat() {
        $_SESSION['finish_reason'] = 'cheat';
        $this->model->endGame($_SESSION['current_game'] ?? null);
        header("Location: /quiz/finish");
        exit();
    }

    private function clearQuestionState() {
        unset($_SESSION['question_start_time'], $_SESSION['current_question_id'], $_SESSION['signature']);
    }

    private function clearFeedbackState() {
        unset($_SESSION['last_question_id'], $_SESSION['current_options'], $_SESSION['selected_option_id']);
    }

    private function clearReportState() {
        unset($_SESSION['last_answer_correct']);
    }

    private function isAnswerRequestValid() {
        return isset(
            $_SESSION['question_start_time'],
            $_SESSION['current_question_id'],
            $_SESSION['signature'],
            $_SESSION['user']['id'],
            $_POST['question_id'],
            $_POST['answer'],
            $_POST['question_start_time']
        );
    }

    private function generateSignature($questionId, $startTime) {
        return hash('sha256', $questionId . '|' . $_SESSION['user']['id'] . '|' . $startTime);
    }

    public function report() {
        $questionId = (int)$_GET['question_id'];

        $q = $this->model->getQuestionById($questionId);
        $options = $this->model->getOptionsByQuestion($questionId);
        $correctOptionId = $this->model->getCorrectOptionId($questionId);

        foreach ($options as &$opt) {
            $opt['isCorrect'] = ($opt['id'] == $correctOptionId);
        }

        $q['category_class'] = $this->getCategoryClass($q['category_name']);
               
        $this->view->render('report', [
            'question' => $q,
            'options' => $options
        ]);
    }
}
