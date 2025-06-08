<?php

class CrearPreguntaController {
    private $model;
    private $view;

    public function __construct($model, $view) {
        $this->model = $model;
        $this->view = $view;
    }
    public function index() {
        echo $this->view->render("crearPregunta", ["mensaje" => "Hola, esto es una prueba de vista."]);
    }
    public function guardar() {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $pregunta = $_POST['pregunta'];
            $opciones = $_POST['opciones'];
            $respuestaCorrecta = $_POST['respuesta_correcta'];
            $categoriaId = $_POST['categoria_id'];

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
