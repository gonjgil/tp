<?php
class PerfilUsuarioModel{
    private $database;

    public function __construct($database){
        $this->database = $database;
    }

    public function getUserById($id)
    {
        $stmt = $this->database->prepare("
        SELECT u.id, u.name, u.last_name, u.birth_date, g.type AS gender, 
               u.country, u.city, u.email, u.username, u.profile_picture, 
               r.type AS user_type, u.created_at, u.lat, u.lng
        FROM users u
        JOIN gender g ON u.id_gender = g.id
        JOIN rol r ON u.id_rol = r.id
        WHERE u.id = ?
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result ? $result->fetch_assoc() : null;
    }
}
