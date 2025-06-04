<?php

require_once 'model/CrearPreguntaModel.php';

class CrearPreguntaController {
    private $model;
    private $view;

    public function __construct($model, $view) {
        $this->model = $model;
        $this->view = $view;
    }

    // Este se llama con method=index
    public function index() {
        echo $this->view->render("crearPreguntaView");
    }

    // Este se llama con method=guardar
    public function guardar() {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $pregunta = $_POST['pregunta'];
            $opcionA = $_POST['opcion_a'];
            $opcionB = $_POST['opcion_b'];
            $opcionC = $_POST['opcion_c'];
            $opcionD = $_POST['opcion_d'];
            $respuestaCorrecta = $_POST['respuesta_correcta'];
            $categoria = $_POST['categoria'];

            $this->model->guardarPregunta($pregunta, $opcionA, $opcionB, $opcionC, $opcionD, $respuestaCorrecta, $categoria);

            echo "<p>Pregunta guardada correctamente.</p><a href='index.php?controller=crearPregunta&method=index'>Crear otra</a>";
        }
    }
}
