<?php

require_once __DIR__ . '/../vendor/jpgraph-4.4.2/src/jpgraph.php';
require_once __DIR__ . '/../vendor/jpgraph-4.4.2/src/jpgraph_pie.php';
require_once __DIR__ . '/../vendor/jpgraph-4.4.2/src/jpgraph_line.php';
require_once __DIR__ . '/../vendor/jpgraph-4.4.2/src/jpgraph_bar.php';

class GraphsController
{
    private $view;
    private $model;

    public function __construct($view, $model)
    {
        $this->view  = $view;
        $this->model = $model;
    }

    public function questionsByCategory()
    {
        $from       = $_GET['from']       ?? '1970-01-01';
        $to         = $_GET['to']         ?? date('Y-m-d');
        $creator_id = $_GET['creator_id'] ?? 'all';

        $filters = [
            'from'       => $from,
            'to'         => $to,
            'creator_id' => $creator_id,
        ];

        $data   = $this->model->getQuestionsByCategory($filters);
        $labels = array_column($data, 'category');
        $values = array_map('intval', array_column($data, 'total'));

        if (array_sum($values) === 0) {
            $this->renderNoData(700, 200, 'No hay preguntas registradas aún');
            return;
        }

        header('Content-Type: image/png');
        $graph = new PieGraph(700, 400);
        $graph->title->Set('Preguntas por Creador');
        $pie = new PiePlot($values);
        $pie->SetLegends($labels);
        $graph->Add($pie);
        $graph->Stroke();
    }

    public function questionsByDay()
    {
        $from = $_GET['from'] ?? date('Y-m-d');
        $to   = $_GET['to']   ?? date('Y-m-d');
        $filters = ['from'=>$from,'to'=>$to];

        $data   = $this->model->getQuestionsPerDay($filters);
        $labels = array_column($data, 'fecha');
        $values = array_map('intval', array_column($data, 'total'));

        header('Content-Type: image/png');

        $graph = new Graph(700, 400);
        $graph->SetScale('textlin');
        $graph->SetMargin(60,20,40,60);                          // márgenes más amplios
        $graph->title->Set('Volumen Diario de Preguntas');

        $graph->ygrid->SetFill(true, '#EFEFEF@0.4', '#FFFFFF@0.4');
        $graph->ygrid->Show();
        $graph->xaxis->SetTickLabels($labels);
        $graph->xaxis->SetLabelAngle(50);

        $line = new LinePlot($values);
        $line->SetColor('#0077CC');
        $line->SetWeight(3);
        $line->mark->SetType(MARK_FILLEDCIRCLE);
        $line->mark->SetFillColor('#FFFFFF');
        $line->mark->SetWidth(6);

        $graph->Add($line);
        $graph->Stroke();
    }


    public function questionsByDifficulty()
    {
        $category_id = $_GET['category_id'] ?? 0;
        $data        = $this->model->getQuestionsByDifficulty((int)$category_id);

        if (empty($data)) {
            $this->renderNoData(400, 100, 'No hay datos para esta categoría');
            return;
        }

        $ticks  = array_column($data, 'difficulty');
        $values = array_map('intval', array_column($data, 'total'));

        header('Content-Type: image/png');
        $graph = new Graph(700, 400);
        $graph->SetScale('textlin');
        $graph->title->Set('Preguntas por Dificultad');
        $graph->xaxis->SetTickLabels($ticks);
        $graph->xaxis->SetLabelAngle(45);
        $barplot = new BarPlot($values);
        $barplot->value->Show();
        $barplot->value->SetFormat('%d');
        $graph->Add($barplot);
        $graph->Stroke();
    }

    public function playersSummary()
    {
        $filter = $_GET['filter'] ?? 'gender';

        switch ($filter) {
            case 'country':
                $data   = $this->model->getPlayersByCountry();
                $title  = 'Jugadores por País';
                $labels = array_column($data, 'pais');
                break;

            case 'gender':
            default:
                $data   = $this->model->getPlayersByGender();
                $title  = 'Jugadores por Género';
                $labels = array_column($data, 'genero');
                break;
        }

        $values = array_map('intval', array_column($data, 'total'));

        header('Content-Type: image/png');
        $graph = new Graph(700, 400);
        $graph->SetScale('textlin');
        $graph->title->Set($title);
        $graph->xaxis->SetTickLabels($labels);
        $graph->xaxis->SetLabelAngle(45);
        $bar = new BarPlot($values);
        $bar->SetFillColor('orange');
        $bar->value->Show();
        $graph->Add($bar);
        $graph->Stroke();
    }

    private function renderNoData($width, $height, $message)
    {
        header('Content-Type: image/png');
        $im = imagecreatetruecolor($width, $height);
        $bg = imagecolorallocate($im, 255, 255, 255);
        imagefilledrectangle($im, 0, 0, $width, $height, $bg);
        $textcolor = imagecolorallocate($im, 255, 0, 0);
        imagestring($im, 5, 10, $height/2 - 10, $message, $textcolor);
        imagepng($im);
        imagedestroy($im);
    }
}
