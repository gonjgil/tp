<?php

class RegisterModel {

    private $database;

    public function __construct($database) {
        $this->database = $database;
    }

    public function isUsernameTaken($username) {
        $stmt = $this->database->prepare("SELECT id FROM usuarios WHERE nombre_usuario = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;   
    }

    public function createUser($nombre, $apellido, $fecha_nacimiento, $genero, $pais, $ciudad, $email, $nombre_usuario, $contrasenia, $foto_perfil) {
        $stmt = $this->database->prepare("INSERT INTO usuarios (nombre, apellido, fecha_nacimiento, genero, pais, ciudad, email, nombre_usuario, contrasenia, foto_perfil)
                                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssssss", $nombre, $apellido, $fecha_nacimiento, $genero, $pais, $ciudad, $email, $nombre_usuario, $contrasenia, $foto_perfil);
        return $stmt->execute();
    }


}
