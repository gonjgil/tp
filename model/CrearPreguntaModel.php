<?php

class CrearPreguntaModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function guardarPregunta($pregunta, $a, $b, $c, $d, $correcta, $categoria) {
        $query = $this->db->prepare("INSERT INTO preguntas (pregunta, opcion_a, opcion_b, opcion_c, opcion_d, respuesta_correcta, categoria)
                                     VALUES (?, ?, ?, ?, ?, ?, ?)");
        $query->execute([$pregunta, $a, $b, $c, $d, $correcta, $categoria]);
    }
}
