<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 1) Localiza raíz del proyecto
$root = dirname(__DIR__, 2);

// 2) Incluye sólo lo que hace falta
require_once $root . '/core/Database.php';
require_once $root . '/model/AdminModel.php';
require_once $root . '/vendor/jpgraph-4.4.2/src/jpgraph.php';
require_once $root . '/vendor/jpgraph-4.4.2/src/jpgraph_pie.php';

// 3) Recupera la conexión desde Database.php
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

// 5) Trae los datos
$data = $model->getQuestionsByCategory($filters);

// 6) Prepara los arrays para JPGraph
$labels = array_column($data, 'category');
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

// 7) Genera y envía la imagen
header('Content-Type: image/png');
$graph = new PieGraph(700, 400);
$graph->title->Set('Preguntas por Categoría');
$pie = new PiePlot($values);
$pie->SetLegends($labels);
$graph->Add($pie);
$graph->Stroke();
exit;
