<?php
class UserModel
{
    private $database;

    public function __construct($database)
    {
        $this->database = $database;
    }

    public function getUserById($id)
    {
        $stmt = $this->database->prepare(
            "SELECT
            id,
            name AS nombre,
            last_name AS apellido,
            birth_date AS fecha_nacimiento,
            gender AS genero,
            country AS pais,
            city AS ciudad,
            email,
            username AS nombre_usuario,
            profile_picture AS foto_perfil
         FROM users
         WHERE id = ?"
        );

        $stmt->bind_param("i", $id);
        $stmt->execute();

        $result = $stmt->get_result();
        return $result ? $result->fetch_assoc() : null;
    }

}

