<?php
// PUBLIC/GRAPHS/QUESTIONSBYDAY.PHP
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 1) Localiza la raíz del proyecto (C:/xampp/htdocs)
$root = dirname(__DIR__, 2);

// 2) Includes mínimos
require_once $root . '/core/Database.php';
require_once $root . '/model/AdminModel.php';
require_once $root . '/vendor/jpgraph-4.4.2/src/jpgraph.php';
require_once $root . '/vendor/jpgraph-4.4.2/src/jpgraph_line.php';

// 3) Conexion a bdd
$dbConfig   = new Database(
    'localhost',
    'root',
    'trivia',
    ''
);
$model      = new AdminModel($dbConfig);

// 4) Parametros del filtro
$filters = [
    'from' => $_GET['from'] ?? null,
    'to'   => $_GET['to']   ?? null,
];

// 5) Recupera series: fecha / total de preguntas
$data = $model->getQuestionsPerDay($filters);
// espera un array de filas con ['fecha' => 'YYYY-MM-DD', 'total' => int]

// 6) Prepara los arrays para JPGraph
$labels = array_column($data, 'fecha');
$values = array_map('intval', array_column($data, 'total'));

// NUEVO: si no hay al menos dos puntos, salimos con imagen de aviso
if (count($labels) < 2) {
    $im    = imagecreatetruecolor(700, 200);
    $white = imagecolorallocate($im, 255, 255, 255);
    $black = imagecolorallocate($im,   0,   0,   0);
    imagefilledrectangle($im, 0, 0, 700, 200, $white);
    imagestring($im, 5, 180,  80,
        "Selecciona un rango de al menos 2 dias.",
        $black
    );
    header('Content-Type: image/png');
    imagepng($im);
    imagedestroy($im);
    exit;
}

// 7) Si tenemos >=2 puntos, hacemos línea normal
header('Content-Type: image/png');
$graph = new Graph(700, 400);
$graph->SetScale('textlin');
$graph->title->Set('Preguntas por Día');
$graph->xaxis->SetTickLabels($labels);
$graph->xaxis->SetLabelAngle(50);
$lineplot = new LinePlot($values);
$graph->Add($lineplot);
$graph->Stroke();
exit;
