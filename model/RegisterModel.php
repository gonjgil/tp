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



    public function createUser($name, $lastName, $birthDate, $gender, $country, $city, $email, $username, $password, $profilePicture, $idRol, $isActive, $lat, $lng) {
        $idGender = intval($gender);

        $stmt = $this->database->prepare("
        INSERT INTO users 
        (id, id_gender, id_rol, email, username, password, profile_picture, birth_date, name, last_name, is_active, country, city, lat, lng)
        VALUES (
            (SELECT IFNULL(MAX(id), 0) + 1 FROM users u2), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
        )
    ");

        $stmt->bind_param("iisssssssssidd", $idGender, $idRol, $email, $username, $password, $profilePicture, $birthDate, $name, $lastName, $isActive, $country, $city, $lat, $lng);
        return $stmt->execute();
    }


    private function getGenderId($gender) {
        switch (strtolower($gender)) {
            case 'masculino':
                return 1;
            case 'femenino':
                return 2;
            default:
                return 3;
        }
    }




}
