<?php
// PUBLIC/GRAPHS/questionsByDifficulty.php
ini_set('display_errors',1);
error_reporting(E_ALL);

// 1) Raiz del proyecto
$root = dirname(__DIR__, 2);

// 2) Includes minimos
require_once $root . '/core/Database.php';
require_once $root . '/model/AdminModel.php';
require_once $root . '/vendor/jpgraph-4.4.2/src/jpgraph.php';
require_once $root . '/vendor/jpgraph-4.4.2/src/jpgraph_bar.php';


// 3) Conexion a bdd
$dbConfig = new Database('localhost','root','trivia','');
$model    = new AdminModel($dbConfig);


// 4) Filtro por categoria
$catId = isset($_GET['category_id'])
    ? intval($_GET['category_id'])
    : 0;


// 5) Datos
$data   = $model->getQuestionsByDifficulty($catId);

if (empty($data)) {
    $im = imagecreate(400, 100);
    $bg = imagecolorallocate($im, 255, 255, 255);
    $textcolor = imagecolorallocate($im, 255, 0, 0);
    imagestring($im, 5, 10, 45, 'No hay datos para esta categoría', $textcolor);
    header('Content-Type: image/png');
    imagepng($im);
    imagedestroy($im);
    exit;
}

$labels = array_column($data,'difficulty');
$values = array_column($data,'total');

$ticks = array_map(function($d) {
    $d = (float)$d;
    if ($d <= 20)  return 'Muy fácil';
    if ($d <= 40)  return 'Fácil';
    if ($d <= 60)  return 'Media';
    if ($d <= 80)  return 'Difícil';
    return 'Muy difícil';
}, $labels);

// 7) Dibujo el grafico
header('Content-Type: image/png');
$graph = new Graph(700,400);
$graph->SetScale('textlin');
$graph->title->Set('Preguntas por Dificultad');
$graph->xaxis->SetTickLabels($ticks);
$graph->xaxis->SetLabelAngle(45);

$barplot = new BarPlot($values);
$barplot->value->Show();
$barplot->value->SetFormat('%d');
$graph->Add($barplot);
$graph->Stroke();
exit;