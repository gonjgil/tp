<?php

class EditorController {
    private $model;
    private $view;

    public function __construct($view, $model) {
        $this->view  = $view;
        $this->model = $model;
    }


    public function panel() {
        echo $this->view->render('editor');
    }

    public function show() {
        $this->panel();
    }

    public function all() {
        $questions = $this->model->getAllQuestions();
        echo $this->view->render('editorList', [
            'questions' => $questions,
            'mode'      => 'all'
        ]);
    }

    public function reported() {
        $reports = $this->model->getReportedQuestionsWithDetails();
        echo $this->view->render('editorReportedList', [
            'reports' => $reports
        ]);

    }
    public function deleteReport($reportId) {
        $this->model->deleteReport((int)$reportId);
        header('Location: /editor/reported');
        exit;
    }

    public function delete($id) {
        $this->model->deleteQuestion((int)$id);
        header('Location: /editor/all');
        exit;
    }

    public function toggle($id) {

        $data = $this->model->getQuestionById((int)$id);
        if (! $data || ! isset($data['question']['approved'])) {
            header('Location: /editor/all');
            exit;
        }

        $current = (int)$data['question']['approved'];

        $newStatus = ($current === 1) ? 0 : 1;

        $this->model->toggleApproved((int)$id, $newStatus);

        header('Location: /editor/all');
        exit;
    }

    public function edit($id) {
        $data = $this->model->getQuestionById((int)$id);
        if (!$data) {
            header('Location: /editor/all');
            exit;
        }
        foreach ($data['answers'] as $i => &$answer) {
            $answer['position'] = $i + 1;
        }
        unset($answer);
        echo $this->view->render('editorEdit', [
            'question' => $data['question'],
            'answers'  => $data['answers']
        ]);
    }

    public function editSubmit() {
    $qid  = (int)$_POST['question_id'];
    $text = trim($_POST['question_text']);
    $this->model->updateQuestion($qid, $text);

    $correctId = isset($_POST['correct_answer'])
        ? (int)$_POST['correct_answer']
        : null;

    foreach ($_POST['answers'] as $ans) {
        $aid   = (int)$ans['id'];
        $atext = trim($ans['text']);
        // se marca correcta solo si coincide con $correctId
        $isCorrect = ($aid === $correctId);

        $this->model->updateAnswer($aid, $atext, $isCorrect);
    }

    header('Location: /editor/all');
    exit;
}

    public function suggested() {
        $list = $this->model->getSuggestedQuestions();
        echo $this->view->render('editorSuggestedList', [
            'questions' => $list
        ]);
    }

    public function viewSuggestion($id) {
        $data = $this->model->getSuggestionById((int)$id);
        if (! $data) {
            header('Location: /editor/suggested');
            exit;
        }
        echo $this->view->render('editorSuggested', [
            'question' => $data['question'],
            'answers'  => $data['answers']
        ]);
    }

    public function acceptSuggestion($id) {
        $this->model->acceptSuggestion((int)$id);
        header('Location: /editor/suggested');
        exit;
    }

    public function rejectSuggestion($id) {
        $this->model->rejectSuggestion((int)$id);
        header('Location: /editor/suggested');
        exit;
    }


}