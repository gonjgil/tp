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
        FROM users u JOIN gender g ON u.id_gender = g.id
        JOIN rol r ON u.id_rol = r.id WHERE u.id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result ? $result->fetch_assoc() : null;
    }

    public function getUserRawById($id)
    {
        $stmt = $this->database->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result ? $result->fetch_assoc() : null;
    }

    public function updateUser($id, $data)
    {
        $fields = [
            "name = ?",
            "last_name = ?",
            "birth_date = ?",
            "id_gender = ?",
            "country = ?",
            "city = ?",
            "email = ?"
        ];
        $params = [
            $data['name'],
            $data['last_name'],
            $data['birth_date'],
            $data['gender'],
            $data['country'],
            $data['city'],
            $data['email']
        ];
        $types = "sssssss";

        if (isset($data['profile_picture'])) {
            $fields[] = "profile_picture = ?";
            $params[] = $data['profile_picture'];
            $types .= "s";
        }

        if (isset($data['password'])) {
            $fields[] = "password = ?";
            $params[] = $data['password'];
            $types .= "s";
        }

        if (isset($data['lat']) && isset($data['lng'])) {
            $fields[] = "lat = ?";
            $fields[] = "lng = ?";
            $params[] = $data['lat'];
            $params[] = $data['lng'];
            $types .= "dd";
        }

        $params[] = $id;
        $types .= "i";

        $sql = "UPDATE users SET " . implode(", ", $fields) . " WHERE id = ?";
        $stmt = $this->database->prepare($sql);
        if (!$stmt) {
            die("Prepare error: " . $this->database->error);
        }

        $stmt->bind_param($types, ...$params);
        return $stmt->execute();
    }


}
