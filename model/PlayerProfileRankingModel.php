<?php

class PlayerProfileRankingModel {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    public function getPlayerData($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_object();
    }


    public function getGamesByUser($id) {
        $query = "SELECT id_game, correct_answers, total_questions, start_time, end_time 
              FROM games WHERE user_id = ? ORDER BY start_time DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $result = [];
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            $result[] = $row;
        }

        return $result;
    }

}