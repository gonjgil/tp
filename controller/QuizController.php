<?php

class QuizController {
    private $model, $view;

    public function __construct($model, $view) {
        $this->model = $model;
        $this->view  = $view;
    }

    //------ metodos principales ---------------//
    public function newGame() {
        $userId = $_SESSION['user']['id'];
        $gameId = $this->model->startGame($userId);


        $_SESSION['current_game'] = $gameId;
        $_SESSION['asked_questions'] = array();
        unset($_SESSION['question_start_time']);
        unset($_SESSION['current_question_id']);
        unset($_SESSION['signature']);
        unset($_SESSION['finish_reason']);

        header("Location: /quiz/next");
        exit();
    }

    public function next() {
        if ($this->isTimeout()) {
            $this->endGame('timeout');

        } else if ($this->renderCurrentQuestion()) {

        } else {
            $question = $this->fetchNextQuestion();

            if ($question) {
                $this->renderNewQuestion($question);
            } else {
                $this->view->render('error', array('message' => 'No hay preguntas disponibles.'));
            }
        }
    }


    public function answer() {
        if (!$this->isValidAnswerRequest()) {
            $this->endGame('cheat');
        } else if ($this->hasTimedOut()) {
            $this->endGame('timeout');
        } else {
            $this->processAnswer();
        }
    }


    public function finish() {
        $score  = $this->model->getScore($_SESSION['current_game']);
        $total  = $this->model->getTotalCorrectByUser($_SESSION['user']['id']);
        $reason = isset($_SESSION['finish_reason']) ? $_SESSION['finish_reason'] : null;

        unset($_SESSION['finish_reason']);

        $reasonText = 'Partida finalizada';
        if ($reason === 'wrong') {
            $reasonText = 'Respuesta incorrecta';
        } elseif ($reason === 'timeout') {
            $reasonText = 'Su tiempo se terminó';
        } elseif ($reason === 'cheat') {
            $reasonText = 'Partida finalizada por trampa';
        }

        $this->view->render('quizsummary', array(
            'score'  => $score,
            'total'  => $total,
            'reason' => $reasonText,
        ));
    }

    // ---------------- metodos llamados  ---------------------//

    private function isTimeout() {
        if (isset($_SESSION['question_start_time'])) {
            $elapsed = time() - $_SESSION['question_start_time'];
            return $elapsed > 15;
        }
        return false;
    }

    private function renderCurrentQuestion() {
        if (!isset($_SESSION['current_question_id'])) {
            return false;
        }

        $questionId = $_SESSION['current_question_id'];
        $question = $this->model->getQuestionById($questionId);

        if (!$question) {
            $this->view->render('error', array('message' => 'Pregunta no encontrada.'));
            return true;
        }

        $options = $this->model->getOptionsByQuestion($questionId);

        if (!$options || count($options) === 0) {
            $this->view->render('error', array('message' => 'No se encontraron opciones para esta pregunta.'));
            return true;
        }

        $gameId = $_SESSION['current_game'];
        $score = $this->model->getScore($gameId);
        $question['category_class'] = $this->getCategoryClass($question['category_name']);

        $this->view->render('question', array(
            'question' => $question,
            'options' => $options,
            'score' => $score,
            'question_start_time' => $_SESSION['question_start_time']
        ));

        return true;
    }


    private function fetchNextQuestion() {
        $asked = isset($_SESSION['asked_questions']) ? $_SESSION['asked_questions'] : array();
        $q = $this->model->getRandomQuestion($asked);

        if (!$q) {
            $this->model->clearUserQuestionHistory($_SESSION['user']['id']);
            $_SESSION['asked_questions'] = array();
            $q = $this->model->getRandomQuestion(array());
        }

        return $q;
    }

    private function renderNewQuestion($q) {
        $_SESSION['current_question_id'] = $q['id'];
        $_SESSION['question_start_time'] = time();
        $_SESSION['signature'] = hash('sha256', $q['id'] . '|' . $_SESSION['user']['id'] . '|' . $_SESSION['question_start_time']);
        $_SESSION['asked_questions'][] = $q['id'];

        $this->model->incrementTimesAnsweredQuestions($q['id']);
        $opts = $this->model->getOptionsByQuestion($q['id']);
        $q['category_class'] = $this->getCategoryClass($q['category_name']);
        $score = $this->model->getScore($_SESSION['current_game']);

        $this->view->render('question', array(
            'question' => $q,
            'options'  => $opts,
            'score'    => $score,
            'question_start_time' => $_SESSION['question_start_time'],
        ));
    }

    private function endGame($reason) {
        $_SESSION['finish_reason'] = $reason;
        $this->model->endGame(isset($_SESSION['current_game']) ? $_SESSION['current_game'] : null);
        header("Location: /quiz/finish");
        exit();
    }

    private function isValidAnswerRequest() {
        if (
            !isset($_SESSION['question_start_time']) ||
            !isset($_SESSION['current_question_id']) ||
            !isset($_SESSION['signature']) ||
            !isset($_SESSION['user']['id'])
        ) {
            return false;
        }

        $expected = hash('sha256',
            $_SESSION['current_question_id'] . '|' .
            $_SESSION['user']['id'] . '|' .
            $_SESSION['question_start_time']
        );

        return $_SESSION['signature'] === $expected;
    }

    private function getCategoryClass($name) {
        $name = strtolower($name);
        if ($name === 'cultura general') return 'w3-red';
        if ($name === 'ciencia')         return 'w3-blue';
        if ($name === 'historia')        return 'w3-purple';
        return 'w3-grey';
    }

    private function hasTimedOut() {
        $elapsed = time() - $_SESSION['question_start_time'];
        return $elapsed > 15;
    }

    private function processAnswer() {
        $gameId     = $_SESSION['current_game'];
        $questionId = (int)$_POST['question_id'];
        $optionId   = (int)$_POST['answer'];
        $userId     = $_SESSION['user']['id'];
        $isCorrect  = $this->model->checkCorrect($optionId);

        $_SESSION['asked_questions'][] = $questionId;

        $this->model->saveQuestionToGame($gameId, $questionId);
        $this->model->incrementTotalAnswersUser($userId);
        $this->model->incrementTotalQuestions($gameId);
        $this->model->updateDifficultyQuestions($questionId);
        $this->model->updateUserDifficulty($userId);

        if ($isCorrect) {
            $this->model->incrementScore($gameId);
            $this->model->incrementCorrectAnswersUser($userId);
            $this->goToNextQuestion();
        } else {
            $this->model->incrementTimesIncorrectQuestions($questionId);
            $this->endGame('wrong');
        }
    }

    private function goToNextQuestion() {
        unset($_SESSION['question_start_time'], $_SESSION['current_question_id'], $_SESSION['signature']);
        header("Location: /quiz/next");
        exit();
    }

}
