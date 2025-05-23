<?php
class UserModel {
    private $database;

    public function __construct($database) {
        $this->database = $database;
    }

    public function findUserByEmail($email) {
        $stmt = $this->database->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email); // 's' indica que es un string
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc(); // Devuelve un solo resultado como array asociativo
    }



    public function createUser($name, $email, $hashedPassword) {
        $sql = "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$hashedPassword')";
        $this->database->execute($sql);
    }


}


