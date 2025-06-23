<?php

use Dompdf\Dompdf;
use Dompdf\Options;

class AdminController {

    private $view;
    private $model;

    public function __construct($view, $model)
    {
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
        $defaultFrom = '2025-01-01';
        $today = date('Y-m-d');
        $filters = [
            'from'       => $_GET['from']       ?? $defaultFrom,
            'to'         => $_GET['to']         ?? $today,
            'creator_id' => $_GET['creator_id'] ?? 'all',
        ];
        $front = '/index.php';

        $creators     = $this->model->getQuestionCreators();
        $selCreatorId = $filters['creator_id'];
        $creators = array_map(function($c) use ($selCreatorId) {
            $c['isSelected'] = ((string)$c['creator_id'] === (string)$selCreatorId);
            return $c;
        }, $creators);
        $isSelectedAll = ($selCreatorId === 'all');

        // URLs de los graficos
        //    1) grafico de Categorias
        $chartUrl       = $front
            . '?controller=graphs'
            . '&method=questionsByCategory&'
            . http_build_query($filters);

        //    2) grafico Volumen diario
        $chartDayUrl    = $front
            . '?controller=graphs'
            . '&method=questionsByDay&'
            . http_build_query($filters);

        //    3) grafico Dificultad
        $categories     = $this->model->getCategories();
        $selCat         = $_GET['category_id'] ?? $categories[0]['id'];
        $chartDiffUrl   = $front
            . '?controller=graphs'
            . '&method=questionsByDifficulty&'
            . http_build_query(['category_id' => $selCat]);

        //    4) grafico Resumen Jugadores
        $filter         = $_GET['filter'] ?? 'gender';
        $chartPlayersUrl= $front
            . '?controller=graphs'
            . '&method=playersSummary&'
            . http_build_query(['filter' => $filter]);

        //  Renderiza la vista con todos los datos
        $this->view->render('adminDashboard', [
            // para los selects de fecha
            'from'            => $filters['from'],
            'to'              => $filters['to'],

            // para el dropdown de creadores en el grafico 1
            'creators'        => $creators,
            'isSelectedAll'   => $isSelectedAll,

            // dropdown de categorias en el grafico 2
            'categories'      => array_map(function($c) use($selCat) {
                $c['isSelected'] = ($c['id'] == $selCat);
                return $c;
            }, $categories),

            // dropdown de genero/pais en el grafico 4
            'filter'          => $filter,
            'filter_is_gender'=> $filter === 'gender',
            'filter_is_country'=> $filter === 'country',

            // URLs a cada grafico
            'chartUrl'        => $chartUrl,
            'chartDayUrl'     => $chartDayUrl,
            'chartDiffUrl'    => $chartDiffUrl,
            'chartPlayersUrl' => $chartPlayersUrl,
        ]);
    }



    public function exportarPDF() {
        $filters = [
            'from'    => $_GET['from']   ?? null,
            'to'      => $_GET['to']     ?? null,
            'filter'  => $_GET['filter'] ?? 'gender',
            'section' => $_GET['section']?? 'all'
        ];

        // Host y esquema para URLs absolutas
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS']!=='off') ? 'https' : 'http';
        $host   = $_SERVER['HTTP_HOST'];

        // Query strings
        $qsDates   = http_build_query(['from'=>$filters['from'],'to'=>$filters['to']]);
        $qsCatId   = http_build_query(['category_id'=> $_GET['category_id'] ?? '']);
        $qsFilter  = http_build_query(['filter'=>$filters['filter']]);
        // Nuevas rutas MVC (sin .php)
        $base      = "{$scheme}://{$host}";
        $chartUrl      = "{$base}/graphs/questionsByCategory?{$qsDates}";
        $chartDayUrl   = "{$base}/graphs/questionsByDay?{$qsDates}";
        $chartDiffUrl  = "{$base}/graphs/questionsByDifficulty?{$qsCatId}";
        $chartPlayers  = "{$base}/graphs/playersSummary?{$qsFilter}";

        $htmlSections = [];

        if ($filters['section']==='all' || $filters['section']==='categories') {
            $htmlSections[] = "
        <div class=\"chart-container\">
          <h2>Preguntas por Categoría</h2>
          <img src=\"{$chartUrl}\" alt=\"Preguntas por Categoría\">
        </div>";
        }
        if ($filters['section']==='all' || $filters['section']==='daily') {
            $htmlSections[] = "
        <div class=\"chart-container\">
          <h2>Volumen diario de preguntas</h2>
          <img src=\"{$chartDayUrl}\" alt=\"Volumen diario\">
        </div>";
        }
        if ($filters['section']==='all' || $filters['section']==='daily') {
            // Aseguramos pasar también category_id en la sección diaria si aplica
            // Si quieres incluir dificultad aquí, cámbialo a section==='difficulty'
        }
        if ($filters['section']==='all' || $filters['section']==='players') {
            $htmlSections[] = "
        <div class=\"chart-container\">
          <h2>Resumen de Jugadores por " . ucfirst($filters['filter']) . "</h2>
          <img src=\"{$chartPlayers}\" alt=\"Resumen de Jugadores\">
        </div>";
        }

        $html = "
    <!DOCTYPE html>
    <html>
    <head><style>
      body{font-family:sans-serif;}
      .chart-container{page-break-inside:avoid; margin-top:30px;}
      img{max-width:100%;height:auto;}
    </style></head>
    <body>
      <h1>Reporte de Actividad</h1>
      <p><strong>Periodo:</strong> " . ($filters['from']??'Inicio') .
            " <strong>al:</strong> " . ($filters['to']??'Final') . "</p>
      " . implode("\n", $htmlSections) . "
    </body>
    </html>";

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4','portrait');
        $dompdf->render();
        $dompdf->stream("reporte-{$filters['section']}-".date('Y-m-d').".pdf",
            ['Attachment'=>false]);
        exit;
    }


}
