<?php
class RankingModel {

    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    public function getRanking() {
        $query = "SELECT id, username, total_answers, correct_answers, difficulty 
              FROM users 
              WHERE id_rol = 3 AND is_active = 1 
              ORDER BY correct_answers DESC, difficulty DESC";
        return $this->db->query($query);
    }

    public function getPlayerById($id) {
        $query = "SELECT username, name, last_name, profile_picture, total_answers, correct_answers, difficulty, city, country, lat, lng 
              FROM users 
              WHERE id = ? AND id_rol = 3";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }


}