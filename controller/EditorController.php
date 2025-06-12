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
        echo $this->view->render('editorEdit', [
            'question' => $data['question'],
            'answers'  => $data['answers']
        ]);
    }

    public function editSubmit() {
        $qid  = (int)$_POST['question_id'];
        $text = trim($_POST['question_text']);
        $this->model->updateQuestion($qid, $text);

        foreach ($_POST['answers'] as $ans) {
            $aid     = (int)$ans['id'];
            $atext   = trim($ans['text']);
            $correct = isset($ans['is_correct']) && $ans['is_correct'] === 'on';
            $this->model->updateAnswer($aid, $atext, $correct);
        }

        header('Location: /editor/all');
        exit;
    }
}