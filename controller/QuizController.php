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
        header("Location: /tp/quiz/next");
        exit();
    }

    public function next() {
        $asked = $_SESSION['asked_questions'] ?? [];
        $q = $this->model->getRandomQuestion($asked);

        if (!$q) {
            header("Location: /tp/quiz/finish");
            exit();
        }

        $questionId = $q['id'];
        $this->model->incrementTimesAnsweredQuestions($questionId);

        $opts   = $this->model->getOptionsByQuestion((int)$questionId);
        $gameId = $_SESSION['current_game'];
        $score  = $this->model->getScore($gameId);

        // dar color ver si funciona
        $categoryClass = $this->getCategoryClass($q['category_name']);
        $q['category_class'] = $categoryClass;

        $this->view->render('question', [
            'question' => $q,
            'options'  => $opts,
            'score'    => $score,
        ]);
    }


    public function answer() {
        $gameId     = $_SESSION['current_game'];
        $questionId = (int)$_POST['question_id'];
        $optionId   = (int)$_POST['answer'];
        $correct    = $this->model->checkCorrect($optionId);
        $userId     = $_SESSION['user']['id'];

        $_SESSION['asked_questions'][] = $questionId;

        $this->model->incrementTotalAnswersUser($userId);
        $this->model->incrementTotalQuestions($gameId);
        if ($correct) {
            $this->model->incrementScore($gameId);
            $this->model->incrementCorrectAnswersUser($userId);
        } else {
            $this->model->incrementTimesIncorrectQuestions($questionId);
        }

        $this->model->updateDifficultyQuestions($questionId);      // pregunta
        $this->model->updateUserDifficulty($userId);       // usuario

        if ($correct) {
            header("Location: /tp/quiz/next");
            exit();
        } else {
            header("Location: /tp/quiz/finish");
            exit();
        }
        exit;
    }


    private function getCategoryClass(string $categoryName): string {
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
        $gameId = $_SESSION['current_game'];
        $score  = $this->model->getScore($gameId);
        $total  = $this->model->getTotalCorrectByUser($_SESSION['user']['id']);
        $this->model->endGame($gameId);
        $this->view->render('quizSummary', compact('score','total'));
    }
}