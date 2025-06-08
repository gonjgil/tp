<?php
class RankingModel {

    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    public function getRanking() {
        $query = " SELECT u.id, u.username, u.profile_picture,COUNT(g.id_game) AS games_played,u.correct_answers, u.total_answers,u.difficulty
        FROM users u LEFT JOIN games g ON g.user_id = u.id 
        WHERE u.id_rol = 3 AND u.is_active = 1 GROUP BY u.id ORDER BY u.correct_answers DESC, u.difficulty DESC";
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