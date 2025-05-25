<?php

class LoginModel {
    private $database;

    public function __construct($database) {
        $this->database = $database;
    }

    public function findUserByUsername($username) {
        $stmt = $this->database->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}
