<?php
class QuizController {
    private $model, $view;
    public function __construct($model, $view) {
        $this->model = $model;
        $this->view  = $view;
    }

    public function newGame() {
        $gameId = $this->model->startGame($_SESSION['user']['id']);
        $_SESSION['current_game'] = $gameId;
        $_SESSION['asked_questions'] = [];
        unset($_SESSION['question_start_time']);
        header("Location: /tp/quiz/next");
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
        if (
            !isset($_SESSION['question_start_time']) ||
            !isset($_SESSION['current_question_id']) ||
            !isset($_SESSION['signature']) ||
            !isset($_SESSION['user']['id'])
        ) {
            $_SESSION['finish_reason'] = 'cheat';
            $this->model->endGame($_SESSION['current_game'] ?? null);
            header("Location: /tp/quiz/finish");
            exit();
        }

        $expectedSignature = hash('sha256', $_SESSION['current_question_id'] . '|' . $_SESSION['user']['id'] . '|' . $_SESSION['question_start_time']);
        if ($_SESSION['signature'] !== $expectedSignature) {
            $_SESSION['finish_reason'] = 'cheat';
            $this->model->endGame($_SESSION['current_game'] ?? null);
            header("Location: /tp/quiz/finish");
            exit();
        }

        $elapsed = time() - $_SESSION['question_start_time'];
        if ($elapsed > 15) {
            unset($_SESSION['question_start_time']);
            unset($_SESSION['current_question_id']);

            $_SESSION['finish_reason'] = 'timeout';

            header("Location: /tp/quiz/finish");
            exit();
        }

        $gameId     = $_SESSION['current_game'];
        $questionId = (int)$_POST['question_id'];
        $optionId   = (int)$_POST['answer'];
        $correct    = $this->model->checkCorrect($optionId);
        $userId     = $_SESSION['user']['id'];

        $_SESSION['asked_questions'][] = $questionId;

        $this->model->saveQuestionToGame($gameId, $questionId); // registrar pregunta

        $this->model->incrementTotalAnswersUser($userId);
        $this->model->incrementTotalQuestions($gameId);

        if ($correct) {
            $this->model->incrementScore($gameId);
            $this->model->incrementCorrectAnswersUser($userId);
        } else {
            $this->model->incrementTimesIncorrectQuestions($questionId);

            $_SESSION['finish_reason'] = 'wrong';
        }

        $this->model->updateDifficultyQuestions($questionId);
        $this->model->updateUserDifficulty($userId);

        unset($_SESSION['question_start_time']);
        unset($_SESSION['current_question_id']);
        unset($_SESSION['signature']);

        if ($correct) {
            header("Location: /tp/quiz/next");
        } else {
            $this->model->endGame($gameId);
            header("Location: /tp/quiz/finish");
        }
        exit();
    }



    private function handleQuestionTimeout(){
        if (isset($_SESSION['question_start_time'])) {
            $elapsed = time() - $_SESSION['question_start_time'];
            if ($elapsed > 15) {
                unset($_SESSION['question_start_time']);
                unset($_SESSION['current_question_id']);
                header("Location: /tp/quiz/finish");
                exit();
            }
        }
        return false;
    }

    private function renderCurrentQuestionIfExists(){
        if (isset($_SESSION['current_question_id'])) {
            $questionId = $_SESSION['current_question_id'];
            $q = $this->model->getQuestionById($questionId);
            if (!$q) {
                $this->view->render('error', ['message' => 'Pregunta no encontrada.']);
                return true;
            }

            $opts = $this->model->getOptionsByQuestion($questionId);
            $gameId = $_SESSION['current_game'];
            $score = $this->model->getScore($gameId);
            $q['category_class'] = $this->getCategoryClass($q['category_name']);

            $this->view->render('question', [
                'question' => $q,
                'options'  => $opts,
                'score'    => $score,
                'question_start_time' => $_SESSION['question_start_time'],
            ]);
            return true;
        }

        return false;
    }

    private function getNextAvailableQuestion(){
        $asked = $_SESSION['asked_questions'] ?? [];
        $q = $this->model->getRandomQuestion($asked);

        if (!$q) {
            $this->model->clearUserQuestionHistory($_SESSION['user']['id']);
            $_SESSION['asked_questions'] = [];
            $q = $this->model->getRandomQuestion([]);
        }

        return $q;
    }

    private function renderNewQuestion(array $q){
        $_SESSION['asked_questions'][] = $q['id'];

        $_SESSION['current_question_id'] = $q['id'];
        $_SESSION['question_start_time'] = time();

        $_SESSION['signature'] = hash('sha256',
            $_SESSION['current_question_id'] . '|' .
            $_SESSION['user']['id'] . '|' .
            $_SESSION['question_start_time']
        );

        $this->model->incrementTimesAnsweredQuestions($q['id']);

        $opts  = $this->model->getOptionsByQuestion($q['id']);
        $score = $this->model->getScore($_SESSION['current_game']);
        $q['category_class'] = $this->getCategoryClass($q['category_name']);

        $this->view->render('question', [
            'question' => $q,
            'options'  => $opts,
            'score'    => $score,
            'question_start_time' => $_SESSION['question_start_time'],
        ]);
    }



    private function getCategoryClass(string $categoryName){
        switch (strtolower($categoryName)) {
            case 'cultura general':
                return 'w3-red';
            case 'ciencia':
                return 'w3-blue';
            case 'historia':
                return 'w3-purple';
            default:
                return 'w3-grey';
        }
    }


    public function finish() {
        $score = $this->model->getScore($_SESSION['current_game']);
        $total = $this->model->getTotalCorrectByUser($_SESSION['user']['id']);

        $reason = isset($_SESSION['finish_reason']) ? $_SESSION['finish_reason'] : null;
        unset($_SESSION['finish_reason']); // limpia tiempo

        $reasonText = '';
        switch ($reason) {
            case 'wrong':
                $reasonText = 'Respuesta incorrecta';
                break;
            case 'timeout':
                $reasonText = 'Su tiempo se terminó';
                break;
            case 'cheat':
                $reasonText = 'Partida finalizada por trampa';
                break;
            default:
                $reasonText = 'Partida finalizada';
                break;
        }

        $this->view->render('quizsummary', [
            'score'  => $score,
            'total'  => $total,
            'reason' => $reasonText,
        ]);
    }



}