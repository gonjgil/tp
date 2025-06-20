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

// 3) Conexión a la BDD
$dbConfig   = new Database(
    'localhost',   // servidor
    'root',        // usuario
    'trivia',      // nombre de la base
    ''             // contraseña
);
$model      = new AdminModel($dbConfig);

// 4) Parámetros de filtro
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

if (empty($labels) || empty($values)) {
    // Mostrar una imagen con mensaje
    $im = imagecreatetruecolor(700, 200);
    $white = imagecolorallocate($im, 255, 255, 255);
    $black = imagecolorallocate($im, 0, 0, 0);
    imagefilledrectangle($im, 0, 0, 700, 200, $white);
    imagestring($im, 5, 180, 80, "No hay datos para las fechas seleccionadas.", $black);
    header('Content-Type: image/png');
    imagepng($im);
    imagedestroy($im);
    exit;
}

// 7) Dibuja el gráfico
header('Content-Type: image/png');
$graph = new Graph(700,400);
$graph->SetScale('textlin');
$graph->title->Set('Preguntas por Día');
$graph->xaxis->SetTickLabels($labels);
$graph->xaxis->SetLabelAngle(50);
$lineplot = new LinePlot($values);
$graph->Add($lineplot);
$graph->Stroke();
exit;
