<?php



class QuizController
{
    private $model, $view;

    public function __construct($model, $view)
    {
        $this->model = $model;
        $this->view = $view;
    }

    // metodo princiapales

    public function newGame()
    {
        $gameId = $this->model->startGame((int)$_SESSION['user']['id']);
        $_SESSION['current_game'] = (int)$gameId;
        $_SESSION['asked_questions'] = [];
        $this->clearQuestionState();
        $this->clearFeedbackState();
        $this->clearReportState();
        header("Location: /quiz/next");
        exit();
    }

    public function next()
    {
        if ($this->isTimedOut()) {
            $this->handleTimeout();
        } elseif (isset($_SESSION['current_question_id'])) {
            $this->renderCurrentQuestion();
        } else {
            $question = $this->getNextAvailableQuestion();

            if ($question) {
                $this->renderNewQuestion($question);
            } else {
                $this->view->render('error', ['message' => 'No hay preguntas disponibles.']);
            }
        }
    }

    public function answer()
    {
        if (!$this->isAnswerRequestValid()) {
            $this->endAsCheat();
        } elseif (!$this->isSignatureValid()) {
            $this->endAsCheat();
        } elseif ($this->isAnswerTimeout()) {
            $_SESSION['finish_reason'] = 'timeout';
            $this->clearQuestionState();
            header("Location: /quiz/finish");
            exit();
        } elseif (!$this->isValidOption((int)$_POST['answer'])) {
            $this->endAsCheat();
        } else {
            $this->processAnswer();
            header("Location: /quiz/feedback");
            exit();
        }
    }

//    public function feedback()
//    {
//        if (!isset($_SESSION['last_answer_correct'], $_SESSION['last_question_id'], $_SESSION['current_options'])) {
//            header("Location: /quiz/next");
//            exit();
//        }
//
//        $correct = $_SESSION['last_answer_correct'];
//        $questionId = (int)$_SESSION['last_question_id'];
//        $opts = $_SESSION['current_options'];
//        $q = $this->model->getQuestionById($questionId);
//        $q['category_class'] = $this->getCategoryClass($q['category_name']);
//        $q['question_start_time'] = $_SESSION['question_start_time'];
//        $score = $this->model->getScore((int)$_SESSION['current_game']);
//
//        $correctOptionId = $this->model->getCorrectOptionId($questionId);
//        $selectedOptionId = $_SESSION['selected_option_id'];
//
//        foreach ($opts as &$opt) {
//            $opt['isSelected'] = ($opt['id'] == $selectedOptionId);
//            $opt['isCorrectAnswer'] = ($opt['id'] == $correctOptionId);
//            $opt['panelClass'] = $opt['isSelected']
//                ? ($opt['isCorrectAnswer'] ? 'w3-border w3-border-green w3-topbar w3-bottombar w3-leftbar w3-rightbar'
//                    : 'w3-border w3-border-red w3-topbar w3-bottombar w3-leftbar w3-rightbar')
//                : '';
//        }
//        unset($opt);
//
//        $this->view->render('question', [
//            'question' => $q,
//            'options' => $opts,
//            'score' => $score,
//            'feedback' => ['isCorrect' => $correct]
//        ]);
//
//        $this->clearFeedbackState();
//    }

    public function feedback()
    {
        if (!$this->hasFeedbackSessionData()) {
            header("Location: /quiz/next");
            exit();
        }

        $questionId = (int)$_SESSION['last_question_id'];
        $selectedOptionId = $_SESSION['selected_option_id'];
        $correct = $_SESSION['last_answer_correct'];
        $options = $_SESSION['current_options'];
        $score = $this->model->getScore((int)$_SESSION['current_game']);

        $question = $this->prepareQuestionData($questionId);
        $correctOptionId = $this->model->getCorrectOptionId($questionId);
        $preparedOptions = $this->prepareOptionsFeedback($options, $selectedOptionId, $correctOptionId);

        $this->view->render('question', [
            'question' => $question,
            'options' => $preparedOptions,
            'score' => $score,
            'feedback' => ['isCorrect' => $correct]
        ]);

        $this->clearFeedbackState();
    }


    public function finish()
    {
        $score = $this->model->getScore((int)$_SESSION['current_game']);
        $total = $this->model->getTotalCorrectByUser((int)$_SESSION['user']['id']);
        $reason = $_SESSION['finish_reason'] ?? null;
        unset($_SESSION['finish_reason']);

        $reasonText = match ($reason) {
            'wrong' => 'Respuesta incorrecta',
            'timeout' => 'Su tiempo se terminó',
            'cheat' => 'Partida finalizada por trampa',
            default => 'Partida finalizada'
        };

        $this->view->render('quizSummary', [
            'score' => $score,
            'total' => $total,
            'reason' => $reasonText,
        ]);
    }

    public function report()
    {
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

    // metodos aux

    private function isTimedOut()
    {
        return isset($_SESSION['question_start_time']) && (time() - (int)$_SESSION['question_start_time']) > 15;
    }

    private function handleTimeout()
    {
        $_SESSION['finish_reason'] = 'timeout';
        header("Location: /quiz/finish");
        exit();
    }

    private function renderCurrentQuestion()
    {
        $questionId = (int)$_SESSION['current_question_id'];
        $q = $this->model->getQuestionById($questionId);

        if (!$q) {
            $this->view->render('error', ['message' => 'Pregunta no encontrada.']);
        } else {
            $opts = $_SESSION['current_options'] ?? $this->model->getOptionsByQuestion($questionId);
            $_SESSION['current_options'] = $opts;

            $q['category_class'] = $this->getCategoryClass($q['category_name']);
            $q['question_start_time'] = $_SESSION['question_start_time'];
            $score = $this->model->getScore((int)$_SESSION['current_game']);

            $this->view->render('question', [
                'question' => $q,
                'options' => $opts,
                'score' => $score
            ]);
        }
    }

    private function getNextAvailableQuestion()
    {
        $asked = $_SESSION['asked_questions'] ?? [];
        $q = $this->model->getRandomQuestion($asked);

        if (!$q) {
            $this->model->clearUserQuestionHistory((int)$_SESSION['user']['id']);
            $_SESSION['asked_questions'] = [];
            $q = $this->model->getRandomQuestion([]);
        }

        return $q;
    }

    private function renderNewQuestion(array $q)
    {
        $qId = (int)$q['id'];
        $_SESSION['asked_questions'][] = $qId;
        $_SESSION['current_question_id'] = $qId;
        $_SESSION['question_start_time'] = time();
        $_SESSION['signature'] = $this->generateSignature($qId, $_SESSION['question_start_time']);

        $this->model->incrementTimesAnsweredQuestions($qId);

        $opts = $this->model->getOptionsByQuestion($qId);
        $_SESSION['current_options'] = $opts;

        $q['category_class'] = $this->getCategoryClass($q['category_name']);
        $q['question_start_time'] = $_SESSION['question_start_time'];
        $score = $this->model->getScore((int)$_SESSION['current_game']);

        $this->view->render('question', [
            'question' => $q,
            'options' => $opts,
            'score' => $score
        ]);
    }

    private function isAnswerRequestValid()
    {
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

    private function isSignatureValid()
    {
        $questionId = (int)$_POST['question_id'];
        $startTime = (int)$_POST['question_start_time'];
        return $_SESSION['signature'] === $this->generateSignature($questionId, $startTime);
    }

    private function isAnswerTimeout()
    {
        $startTime = (int)$_POST['question_start_time'];
        return (time() - $startTime) > 15;
    }

    private function isValidOption(int $optionId)
    {
        return in_array($optionId, array_column($_SESSION['current_options'], 'id'));
    }

    private function processAnswer()
    {
        $questionId = (int)$_POST['question_id'];
        $optionId = (int)$_POST['answer'];
        $userId = $_SESSION['user']['id'];
        $gameId = $_SESSION['current_game'];
        $isCorrect = $this->model->checkCorrect($optionId);

        $this->model->saveQuestionToGame($gameId, $questionId);
        $this->model->incrementTotalAnswersUser($userId);
        $this->model->incrementTotalQuestions($gameId);

        if ($isCorrect) {
            $this->handleCorrectAnswer($gameId, $userId);
        } else {
            $this->handleIncorrectAnswer($questionId, $userId);
        }

        $this->model->updateDifficultyQuestions($questionId);
        $this->model->updateUserDifficulty($userId);

        $_SESSION['selected_option_id'] = $optionId;
        $_SESSION['last_question_id'] = $questionId;

        $this->clearQuestionState();
    }

    private function handleCorrectAnswer(int $gameId, int $userId)
    {
        $this->model->incrementScore($gameId);
        $this->model->incrementCorrectAnswersUser($userId);
        $_SESSION['last_answer_correct'] = true;
    }

    private function handleIncorrectAnswer(int $questionId, int $userId)
    {
        $this->model->incrementTimesIncorrectQuestions($questionId);
        $_SESSION['last_answer_correct'] = false;
        $_SESSION['finish_reason'] = 'wrong';
    }

    private function endAsCheat()
    {
        $_SESSION['finish_reason'] = 'cheat';
        $this->model->endGame($_SESSION['current_game'] ?? null);
        header("Location: /quiz/finish");
        exit();
    }

    private function generateSignature($questionId, $startTime)
    {
        return hash('sha256', $questionId . '|' . $_SESSION['user']['id'] . '|' . $startTime);
    }

    private function getCategoryClass(string $categoryName): string
    {
        return match (strtolower($categoryName)) {
            'cultura general' => 'w3-red',
            'ciencia' => 'w3-blue',
            'historia' => 'w3-purple',
            default => 'w3-grey'
        };
    }

    private function clearQuestionState()
    {
        unset($_SESSION['question_start_time'], $_SESSION['current_question_id'], $_SESSION['signature']);
    }

    private function clearFeedbackState()
    {
        unset($_SESSION['last_question_id'], $_SESSION['current_options'], $_SESSION['selected_option_id']);
    }

    private function clearReportState()
    {
        unset($_SESSION['last_answer_correct']);
    }






    private function hasFeedbackSessionData()
    {
        return isset(
            $_SESSION['last_answer_correct'],
            $_SESSION['last_question_id'],
            $_SESSION['current_options']
        );
    }

    private function prepareQuestionData(int $questionId)
    {
        $question = $this->model->getQuestionById($questionId);
        $question['category_class'] = $this->getCategoryClass($question['category_name']);
        $question['question_start_time'] = $_SESSION['question_start_time'];
        return $question;
    }

    private function prepareOptionsFeedback(array $options, int $selectedId, int $correctId)
    {
        foreach ($options as &$option) {
            $option['isSelected'] = ($option['id'] == $selectedId);
            $option['isCorrectAnswer'] = ($option['id'] == $correctId);
            if ($option['isSelected']) {
                $option['panelClass'] = $option['isCorrectAnswer']
                    ? 'w3-border w3-border-green w3-topbar w3-bottombar w3-leftbar w3-rightbar'
                    : 'w3-border w3-border-red w3-topbar w3-bottombar w3-leftbar w3-rightbar';
            } else {
                $option['panelClass'] = '';
            }
        }
        unset($option);
        return $options;
    }

}

