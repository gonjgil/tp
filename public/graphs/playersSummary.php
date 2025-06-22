<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$root = dirname(__DIR__, 2);
require_once $root . '/core/Database.php';
require_once $root . '/model/AdminModel.php';
require_once $root . '/vendor/jpgraph-4.4.2/src/jpgraph.php';
require_once $root . '/vendor/jpgraph-4.4.2/src/jpgraph_bar.php';

$db = new Database('localhost', 'root', 'trivia', '');
$model = new AdminModel($db);

$filter = $_GET['filter'] ?? 'gender'; // default

switch ($filter) {
    case 'country':
        $data = $model->getPlayersByCountry();
        $title = 'Jugadores por País';
        $labels = array_column($data, 'pais');
        break;
    case 'gender':
    default:
        $data = $model->getPlayersByGender();
        $title = 'Jugadores por Género';
        $labels = array_column($data, 'genero');
        break;
}

$values = array_map('intval', array_column($data, 'total'));

$graph = new Graph(700, 400);
$graph->SetScale("textlin");
$graph->xaxis->SetTickLabels($labels);
$graph->xaxis->SetLabelAngle(45);
$graph->title->Set($title);

$bar = new BarPlot($values);
$bar->SetFillColor('orange');
$bar->value->Show(); // Mostrar los valores
$graph->Add($bar);

header('Content-Type: image/png');
$graph->Stroke();

