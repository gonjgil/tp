<?php

use Dompdf\Dompdf;
use Dompdf\Options;

class AdminController
{
  private $view;
  private $model;

  public function __construct($view, $model)
  {
    $this->view = $view;
    $this->model = $model;
  }

  public function index()
  {
    $this->panel();
  }

  public function panel()
  {
    // Renderiza la vista sin datos
    $this->view->render('admin');
  }
  public function dashboard()
  {
    $defaultFrom = '2025-01-01';
    $today = date('Y-m-d');
    $filters = [
      'from' => $_GET['from'] ?? $defaultFrom,
      'to' => $_GET['to'] ?? $today,
      'category_id' => $_GET['category_id'] ?? 'all',
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
        $questionsByCreatorData = $this->model->getQuestionsByCategory($filters);

        // URLs de los graficos
        //    1) grafico de Categorias
        $chartUrl       = $front
            . '?controller=graphs'
            . '&method=questionsByCategory&'
            . http_build_query($filters + ['creator_id' => $_GET['creator_id'] ?? 'all']);

        // preguntas por Día (line/step) — usa rango from/to
        $questionsPerDayData = $this->model->getQuestionsPerDay($filters);
        $chartDayUrl = $front
            . '?controller=graphs'
            . '&method=questionsByDay&'
            . http_build_query($filters);

        //preguntas por Dificultad

        $categories = $this->model->getCategories();
        $selCat     = $_GET['category_id'] ?? $categories[0]['id'];
        $questionsByDifficultyData = $this->model->getQuestionsByDifficulty((int)$selCat);
        $chartDiffUrl = $front
            . '?controller=graphs'
            . '&method=questionsByDifficulty&'
            . http_build_query(['category_id' => $selCat]);


        // resumen de jugador
        $filter = $_GET['filter'] ?? 'gender';
        if ($filter === 'country') {
            $rawPlayers = $this->model->getPlayersByCountry();
            $playersLabel = 'País';
            $playersSummaryData = array_map(fn($r) => [
                'label' => $r['pais'],
                'total' => $r['total']
            ], $rawPlayers);
        } else {
            $rawPlayers = $this->model->getPlayersByGender();
            $playersLabel = 'Género';
            $playersSummaryData = array_map(fn($r) => [
                'label' => $r['genero'],
                'total' => $r['total']
            ], $rawPlayers);
        }
        $chartPlayersUrl = $front
            . '?controller=graphs'
            . '&method=playersSummary&'
            . http_build_query(['filter' => $filter]);

        // render:
        $this->view->render('adminDashboard', [
            'from'    => $filters['from'],
            'to'      => $filters['to'],

            // ** datos en crudo para tablas **
            'questionsByCreatorData'    => $questionsByCreatorData,
            'questionsPerDayData'       => $questionsPerDayData,
            'questionsByDifficultyData' => $questionsByDifficultyData,
            'playersSummaryData'        => $playersSummaryData,
            'playersLabel'              => $playersLabel,

            // ** URLs de los gráficos **
            'chartUrl'       => $chartUrl,
            'chartDayUrl'    => $chartDayUrl,
            'chartDiffUrl'   => $chartDiffUrl,
            'chartPlayersUrl'=> $chartPlayersUrl,

            // dropdown de categorias en el grafico 2
            // MODIFICADO POR EL DE ABAJO EN EL MERGE
            // 'categories'      => array_map(function($c) use($selCat) {
            //     $c['isSelected'] = ($c['id'] == $selCat);
            //     return $c;
            // }, $categories),

            // ** selectores existentes **
            'categories'           => array_map(fn($c) => [
                'id'=>$c['id'],
                'name'=>$c['name'],
                'isSelected'=>$c['id']==$selCat
                ], $categories),

            // dropdown de genero/pais en el grafico 4
            'filter'          => $filter,
            'filter_is_gender'=> $filter === 'gender',
            'filter_is_country'=> $filter === 'country',

            'creators'             => $this->model->getQuestionCreators(), // debe devolver id/username
            'isAllSelectedCreator' => ($_GET['creator_id'] ?? 'all')==='all',
            'selectedCreatorId'    => $_GET['creator_id'] ?? 'all',
        ]);
    }

 public function exportarPDF()
    {
        $defaultFrom = '2025-01-01';
        $today = date('Y-m-d');

        $filters = [
            'from'        => $_GET['from']        ?? $defaultFrom,
            'to'          => $_GET['to']          ?? $today,
            'filter'      => $_GET['filter']      ?? 'gender',
            'section'     => $_GET['section']     ?? 'all',
            'category_id' => $_GET['category_id'] ?? '',
            'creator_id'  => $_GET['creator_id']  ?? 'all',
        ];

        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host   = $_SERVER['HTTP_HOST'];
        $base   = "{$scheme}://{$host}";

        $qsCatChart = http_build_query([
            'from'       => $filters['from'],
            'to'         => $filters['to'],
            'creator_id' => $filters['creator_id'],
        ]);
        $chartUrl      = "{$base}/graphs/questionsByCategory?{$qsCatChart}";

        $qsDayChart = http_build_query([
            'from' => $filters['from'],
            'to'   => $filters['to'],
        ]);
        $chartDayUrl   = "{$base}/graphs/questionsByDay?{$qsDayChart}";

        $qsDiffChart = http_build_query([
            'category_id' => $filters['category_id'],
        ]);
        $chartDiffUrl  = "{$base}/graphs/questionsByDifficulty?{$qsDiffChart}";

        $qsPlayersChart = http_build_query([
            'filter' => $filters['filter'],
        ]);
        $chartPlayers  = "{$base}/graphs/playersSummary?{$qsPlayersChart}";

        $htmlSections = [];

        $selectedCategoryName = '';
        if (!empty($filters['category_id'])) {
            $categories = $this->model->getCategories();
            foreach ($categories as $cat) {
                if ((string)$cat['id'] === (string)$filters['category_id']) {
                    $selectedCategoryName = htmlspecialchars($cat['name']);
                    break;
                }
            }
            if ($selectedCategoryName === '' && $filters['category_id'] !== '') {
                $selectedCategoryName = 'ID ' . htmlspecialchars($filters['category_id']) . ' (no encontrada)';
            }
        } else {
            $selectedCategoryName = 'Todas';
        }


        if ($filters['section'] === 'all' || $filters['section'] === 'categories') {
            $htmlSections[] = "
                <div class=\"chart-container\">
                  <h2>Preguntas por Creador</h2>
                  <img src=\"{$chartUrl}\" alt=\"Preguntas por Creador\" class=\"chart-img\">
                </div>";
        }
        if ($filters['section'] === 'all' || $filters['section'] === 'difficulty') {
            $htmlSections[] = "
                <div class=\"chart-container\">
                  <h2>Preguntas por Dificultad (Categoría: {$selectedCategoryName})</h2>
                  <img src=\"{$chartDiffUrl}\" alt=\"Preguntas por Dificultad\" class=\"chart-img\">
                </div>";
        }
        if ($filters['section'] === 'all' || $filters['section'] === 'daily') {
            $htmlSections[] = "
                <div class=\"chart-container\">
                  <h2>Volumen Diario de Preguntas</h2>
                  <img src=\"{$chartDayUrl}\" alt=\"Volumen diario\" class=\"chart-img\">
                </div>";
        }
        if ($filters['section'] === 'all' || $filters['section'] === 'players') {
            $htmlSections[] = "
                <div class=\"chart-container\">
                  <h2>Resumen de Jugadores por " .
                ucfirst($filters['filter']) .
                "</h2>
                  <img src=\"{$chartPlayers}\" alt=\"Resumen de Jugadores\" class=\"chart-img\">
                </div>";
        }

        $reportDate = date('d/m/Y H:i');
        $periodFrom = date('d/m/Y', strtotime($filters['from']));
        $periodTo   = date('d/m/Y', strtotime($filters['to']));
        $sectionExported =
            isset($filters['section']) && $filters['section'] !== 'all'
                ? ucfirst($filters['section'])
                : 'Todos los gráficos';

        $logoUrl = "{$base}/uploads/logo.svg";

        $templatePath = __DIR__ . '/../view/reports/template.html';

        if (!file_exists($templatePath)) {
            die('Error: PDF template file not found at ' . $templatePath);
        }
        $htmlTemplate = file_get_contents($templatePath);

        $replacements = [
            '{{reportDate}}'           => $reportDate,
            '{{periodFrom}}'           => $periodFrom,
            '{{periodTo}}'             => $periodTo,
            '{{sectionExported}}'      => $sectionExported,
            '{{htmlSections}}'         => implode("\n", $htmlSections),
            '{{currentYear}}'          => date('Y'),
            '{{logoUrl}}'              => $logoUrl,
        ];

        $html = str_replace(
            array_keys($replacements),
            array_values($replacements),
            $htmlTemplate
        );

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Helvetica');
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $canvas = $dompdf->getCanvas();
        $font = $dompdf->getFontMetrics()->get_font('Helvetica', 'normal');
        $text = 'Página {PAGE_NUM} de {PAGE_COUNT}';
        $width = $dompdf->getFontMetrics()->get_text_width($text, $font, 8);
        $canvas->page_text(
            590 - $width,
            810,
            $text,
            $font,
            8,
            [0, 0, 0]
        );

        $dompdf->stream(
            "reporte-{$filters['section']}-" . date('Y-m-d') . '.pdf',
            ['Attachment' => false]
        );
        exit();
    }
}