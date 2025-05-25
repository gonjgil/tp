<?php

class RegisterModel {

    private $database;

    public function __construct($database) {
        $this->database = $database;
    }

    public function isUsernameTaken($username) {
        $stmt = $this->database->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    public function createUser($name, $lastName, $birthDate, $gender, $country, $city, $email, $username, $hashedPassword, $profilePicture) {
        $stmt = $this->database->prepare("INSERT INTO users (name, last_name, birth_date, gender, country, city, email, username, password, profile_picture)
                                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssisssssss", $name, $lastName, $birthDate, $gender, $country, $city, $email, $username, $hashedPassword, $profilePicture);
        return $stmt->execute();
    }


}
