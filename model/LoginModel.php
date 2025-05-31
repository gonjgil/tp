<?php
class LoginModel {
    private $database;

    public function __construct($database) {
        $this->database = $database;
    }

    public function findUserByUsername($username) {
        $sql = "
      SELECT u.*, LOWER(r.type) AS user_type
      FROM users u
      JOIN rol r ON u.id_rol = r.id
      WHERE u.username = ?
    ";
        $stmt = $this->database->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}
