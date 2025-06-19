<?php
class AdminController {

    private $view;   // MustachePresenter
    private $model;  // AdminModel

    public function __construct($view, $model)
    {
        // Asegúrate de asignar bien:
        $this->view  = $view;
        $this->model = $model;
    }

    public function index()
    {
        $this->panel();
    }

    public function panel()
    {
        // Renderiza la vista sin datos
        $this->view->render("admin");
    }

    public function dashboard() {
        $filters = ['from'=>$_GET['from']??null,'to'=>$_GET['to']??null];

        //Datos para grafico categorias
        $stats   = $this->model->getQuestionsByCategory($filters);
        $chartUrl= '/public/graphs/questionsByCategory.php?'.http_build_query($filters);

        //Datos para grafico preguntas por dia
        $chartDayUrl   = '/public/graphs/questionsByDay.php?' . http_build_query($filters);

        $this->view->render('adminDashboard', [
            'from'=>$filters['from'],
            'to'=>$filters['to'],
            'stats'=>$stats,
            'chartUrl'=>$chartUrl,
            'chartByDayUrl'   => $chartDayUrl,
        ]);
    }
}
