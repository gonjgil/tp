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
            $categoriaId = $_POST['categoria_id'];
            $creatorId = $_SESSION['user']['id'] ?? null;

            $this->model->guardarPregunta($pregunta, $opciones, $respuestaCorrecta, $categoriaId, $creatorId);

            $_SESSION['mensaje'] = '✅ Pregunta guardada correctamente. En breve será revisada para su aprobación.';

            header("Location: /player/panel");
            exit();
        }
    }

}
