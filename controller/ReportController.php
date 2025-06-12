<?php
class ReportController {
    private $quizModel;
    private $reportModel;

    public function __construct(ReportModel $reportModel, QuizModel $quizModel) {
        $this->quizModel = $quizModel;
        $this->reportModel = $reportModel;
    }

    public function index() {
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?action=login");
            exit();
        }

        $userId = $_SESSION['user']['id'];
        $games = $this->reportModel->getAllGamesByUser($userId);

        foreach ($games as &$game) {
            $questionIds = $this->reportModel->getQuestionsByGame($game['id_game']);
            $game['questions'] = [];

            foreach ($questionIds as $qid) {
                $question = $this->quizModel->getQuestionById($qid);
                $game['questions'][] = $question;
            }
        }

        require 'views/report/index.php';
    }

public function submitReport() {
    if (!isset($_POST['question_id'], $_POST['report'])) {
        header("Location: /quiz/next");
        exit();
    }

    $questionId = (int)$_POST['question_id'];
    $reportText = $_POST['report'];

    $this->reportModel->saveReport($questionId, $reportText);
    $this->reportModel->markQuestionReported($questionId);

    $wasCorrect = $_SESSION['last_answer_correct'] ?? false;
    unset($_SESSION['last_answer_correct']);

    if ($wasCorrect) {
        header("Location: /quiz/next");
    } else {
        header("Location: /quiz/finish");
    }
    exit();
}

}
