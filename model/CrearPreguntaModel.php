<?php

class CrearPreguntaModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function guardarPregunta($pregunta, $opciones, $respuestaCorrecta, $categoriaId, $creatorId) {
        $query = $this->db->prepare("INSERT INTO questions (question_text, category_id, creator_id, approved, reported, difficulty, times_answered, times_incorrect)
                                 VALUES (?, ?, ?, 0, 0, 100, 0, 0)");
        $query->execute([$pregunta, $categoriaId, $creatorId]);

        $questionId = $this->db->lastInsertId();

        foreach ($opciones as $letra => $texto) {
            $esCorrecta = ($letra === $respuestaCorrecta) ? 1 : 0;
            $stmt = $this->db->prepare("INSERT INTO answers (question_id, answer_text, is_correct)
                                    VALUES (?, ?, ?)");
            $stmt->execute([$questionId, $texto, $esCorrecta]);
        }
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
