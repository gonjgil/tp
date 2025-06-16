<?php

class RegisterModel
{
    private $database;

    public function __construct($database)
    {
        $this->database = $database;
    }

    public function isUsernameTaken($username)
    {
        $stmt = $this->database->prepare('SELECT id FROM users WHERE username = ?');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    public function isEmailTaken($email)
    {
        $stmt = $this->database->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }

    public function createUser(
        $name,
        $lastName,
        $birthDate,
        $gender,
        $country,
        $city,
        $email,
        $username,
        $password,
        $profilePicture,
        $idRol,
        $isActive,
        $token,
        $lat,
        $lng
    ) {
        $idGender = intval($gender);

        $stmt = $this->database->prepare("
        INSERT INTO users 
        (id_gender, id_rol, email, username, password, profile_picture, birth_date, name, last_name, is_active, token, country, city, lat, lng)
        VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
        )
    ");

        $stmt->bind_param(
            'iissssssssssidd',
            $idGender,
            $idRol,
            $email,
            $username,
            $password,
            $profilePicture,
            $birthDate,
            $name,
            $lastName,
            $isActive,
            $token,
            $country,
            $city,
            $lat,
            $lng
        );
        $stmt->execute();
        $newUserId = $this->database->getConnection()->insert_id;
        $stmt->close();

        return [
            'id' => $newUserId,
            'name' => $name,
            'last_name' => $lastName,
            'email' => $email,
        ];
    }

    private function getGenderId($gender)
    {
        switch (strtolower($gender)) {
            case 'masculino':
                return 1;
            case 'femenino':
                return 2;
            default:
                return 3;
        }
    }

    public function getToken($iduser)
    {
        $stmt = $this->database->prepare('SELECT token FROM users WHERE id = ?');
        $stmt->bind_param('i', $iduser);
        $stmt->execute();
        $result = $stmt->get_result();
        $token = null;

        if ($row = $result->fetch_assoc()) {
            $token = $row['token'];
        }

        $stmt->close();
        return $token;
    }

    public function activateUser($iduser)
    {
        $stmt = $this->database->prepare('UPDATE users SET is_active = 1, validation_date = NOW() WHERE id = ?');
        $stmt->bind_param('i', $iduser);
        $stmt->execute();
        $stmt->close();
    }
}
