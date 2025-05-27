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

    public function isEmailTaken($email) {
        $stmt = $this->database->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }

    public function createUser($name, $lastName, $birthDate, $gender, $country, $city, $email, $username, $hashedPassword, $profilePicture, $userType = 'jugador') {
        $stmt = $this->database->prepare("INSERT INTO users (name, last_name, birth_date, gender, country, city, email, username, password, profile_picture, user_type)
                                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssssss", $name, $lastName, $birthDate, $gender, $country, $city, $email, $username, $hashedPassword, $profilePicture, $userType);
        return $stmt->execute();
    }



}
