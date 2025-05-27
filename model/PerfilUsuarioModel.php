<?php
class PerfilUsuarioModel{
    private $database;

    public function __construct($database){
        $this->database = $database;
    }

    public function getUserById($id)
    {
        $stmt = $this->database->prepare("SELECT id,name,last_name,birth_date,gender,country,city,email,username,profile_picture,user_type,created_at
        FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result ? $result->fetch_assoc() : null;
    }
}
