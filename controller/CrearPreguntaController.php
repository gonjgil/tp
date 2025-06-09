<?php

class CrearPreguntaController {
    private $model;
    private $view;

    public function __construct($model, $view) {
        $this->model = $model;
        $this->view = $view;
    }

    public function index() {
        $categories = $this->model->getCategories();
        $data = ['categories' => $categories];
        
        echo $this->view->render("crearPregunta", $data);
    }


    public function guardar() {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $pregunta = $_POST['pregunta'];
            $opciones = $_POST['opciones'];
            $respuestaCorrecta = $_POST['respuesta_correcta'];
            $categoriaId = $_POST['categories'][name];
            $creatorId = $_SESSION['user']['id'] ?? null;

            if (!$creatorId) {
                echo "No se pudo otener el ID del usuario actual";
                return;
            }

            $this->model->guardarPregunta($pregunta, $opciones, $respuestaCorrecta, $categoriaId, $creatorId);

            echo "<p>Pregunta guardada correctamente.</p><a href='/tp/index.php?controller=crearPregunta&method=index'>Crear otra</a>";

        }
    }

}
