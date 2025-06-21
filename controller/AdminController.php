<?php

use Dompdf\Dompdf;
use Dompdf\Options;

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
        $today = date('Y-m-d');
        $filters = ['from'=>$_GET['from'] ?? $today ,'to'=>$_GET['to'] ?? $today];

        //Datos para grafico categorias
        $stats   = $this->model->getQuestionsByCategory($filters);
        $chartUrl= '/public/graphs/questionsByCategory.php?'.http_build_query($filters);

        //Datos para grafico preguntas por dia
        $chartDayUrl   = '/public/graphs/questionsByDay.php?' . http_build_query($filters);

        //Grafico por categoria
        $categories    = $this->model->getCategories();       // [{id,name},…]
        $selCat        = $_GET['category_id'] ?? $categories[0]['id'];
        $chartDiffUrl  = '/public/graphs/questionsByDifficulty.php?' . http_build_query(['category_id' => $selCat]);

        $this->view->render('adminDashboard', [
            'from'=>$filters['from'],
            'to'=>$filters['to'],
            'stats'=>$stats,
            'categories'     => array_map(function($c)use($selCat){
                $c['isSelected'] = $c['id']==$selCat;
                return $c;
            }, $categories),
            'chartUrl'=>$chartUrl,
            'chartDayUrl'   => $chartDayUrl,
            'chartDiffUrl'   => $chartDiffUrl,
        ]);
    }
    public function getQuestionsByDifficulty(array $filters) {
        $sql = "
      SELECT
        CASE
          WHEN q.difficulty < 34 THEN 'Fácil'
          WHEN q.difficulty < 67 THEN 'Medio'
          ELSE 'Difícil'
        END AS nivel,
        COUNT(*) AS total
      FROM questions q
    ";
        $params = []; $conds = [];

        // Filtro por categoria
        if (! empty($filters['category_id'])) {
            $conds[]   = "q.category_id = ?";
            $params[]  = $filters['category_id'];
        }

        if ($conds) {
            $sql .= " WHERE " . implode(" AND ", $conds);
        }
        $sql .= " GROUP BY nivel ORDER BY 
        CASE nivel 
          WHEN 'Fácil' THEN 1 
          WHEN 'Medio' THEN 2 
          WHEN 'Difícil' THEN 3 
        END";

        $stmt = $this->database->prepare($sql);
        if ($params) {
            // todos enteros
            $stmt->bind_param(str_repeat('i', count($params)), ...$params);
        }
        $stmt->execute();
        $res = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        return array_map(function($r){
            return [
                'nivel' => $r['nivel'],
                'total' => (int)$r['total']
            ];
        }, $res);
    }

    public function exportarPDF() {
        // 1. Obtener los filtros de la URL
        $filters = ['from' => $_GET['from'] ?? null, 'to' => $_GET['to'] ?? null];

        // 2. Construir las URLs ABSOLUTAS a los gráficos
        // Dompdf necesita una URL completa para acceder a las imágenes.
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
        $host = $_SERVER['HTTP_HOST'];
        $queryParams = http_build_query($filters);

        $chartUrl = "{$protocol}://{$host}/public/graphs/questionsByCategory.php?{$queryParams}";
        $chartDayUrl = "{$protocol}://{$host}/public/graphs/questionsByDay.php?{$queryParams}";

        // 3. Crear el contenido HTML para el PDF
        // Es un simple string de HTML. Puedes hacerlo tan complejo como quieras.
        $html = '
            <!DOCTYPE html>
            <html>
            <head>
                <style>
                    body { font-family: sans-serif; }
                    h1 { text-align: center; color: #333; }
                    .chart-container { 
                        margin-top: 40px; 
                        page-break-inside: avoid; /* Evita que la imagen se corte entre páginas */
                    }
                    img { max-width: 100%; height: auto; }
                </style>
            </head>
            <body>
                <h1>Reporte de Actividad</h1>
                <p><strong>Periodo del:</strong> ' . ($filters['from'] ?? 'Inicio') . ' <strong>al:</strong> ' . ($filters['to'] ?? 'Final') . '</p>
                
                <div class="chart-container">
                    <h2>Preguntas por Categoría</h2>
                    <img src="' . $chartUrl . '">
                </div>

                <div class="chart-container">
                    <h2>Volumen diario de preguntas</h2>
                    <img src="' . $chartDayUrl . '">
                </div>
            </body>
            </html>
        ';

        // 4. Configurar e instanciar Dompdf
        $options = new Options();
        // Habilitar 'isRemoteEnabled' es CRUCIAL para que Dompdf pueda cargar imágenes de URLs externas (incluso de tu propio servidor)
        $options->set('isRemoteEnabled', true);
        
        $dompdf = new Dompdf($options);
        
        // 5. Cargar el HTML y renderizar el PDF
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait'); // (Opcional) Definir tamaño y orientación
        $dompdf->render();

        // 6. Enviar el PDF al navegador
        // El nombre del archivo será "reporte-dashboard-FECHA.pdf"
        $dompdf->stream("reporte-dashboard-" . date("Y-m-d") . ".pdf", [
            "Attachment" => false // Pone 'true' para forzar la descarga, 'false' para mostrarlo en el navegador.
        ]);
        
        exit; // Detenemos la ejecución del script
    }
}
