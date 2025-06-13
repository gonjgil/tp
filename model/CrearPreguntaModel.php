<?php

class CrearPreguntaModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function guardarPregunta($pregunta, $opciones, $respuestaCorrecta, $categoriaId, $creatorId) {
        $query = "INSERT INTO questions (category_id, creator_id, question_text, approved, reported, suggested, times_answered, times_incorrect, difficulty)
                                VALUES (?, ?, ?, 0, 0, 1, 0, 0, 100)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("iis", $categoriaId, $creatorId, $pregunta);
        $stmt->execute();
        $questionId = $this->db->getConnection()->insert_id;
        $stmt->close();

        $query2 = "INSERT INTO answers (question_id, answer_text, is_correct) VALUES (?, ?, ?)";
        $stmt2 = $this->db->prepare($query2);
        foreach ($opciones as $opcion => $texto) {
            $esCorrecta = ($opcion === $respuestaCorrecta) ? 1 : 0;
            $stmt2->bind_param("isi", $questionId, $texto, $esCorrecta);
            $stmt2->execute();
        }
        $stmt2->close();
    }

    public function getCategories() {
        $data = [];
        $query = $this->db->prepare("SELECT id, name FROM categories ORDER BY name ASC");
        $query->execute();
        $result = $query->get_result();
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        $query->close();
        return $data;
    }
}
