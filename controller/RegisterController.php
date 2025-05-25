<?php

class RegisterController
{
    private $view;
    private $model;

    public function __construct($view, $registerModel)
    {
        $this->view = $view;
        $this->model = $registerModel;
    }

    public function register()
    {
        $this->view->render('register');
    }


    public function handleRegister() {
        $data = $_POST;
        $username = $data['username'];

        $errors = $this->validateRegistrationData($data);
        $profilePicture = $this->ProfilePictureUpload($_FILES['profile_picture'], $username);

        if (empty($errors)) {
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
            $this->model->createUser(
                $data['name'],
                $data['last_name'],
                $data['birth_date'],
                $data['gender'],
                $data['country'],
                $data['city'],
                $data['email'],
                $data['username'],
                $hashedPassword,
                $profilePicture
            );
            header("Location: index.php?controller=login&method=login");
            exit();
        } else {
            $this->view->render("register", ['errors' => $errors]);
        }
    }



    private function validateRegistrationData($data) {
        $errors = [];

        if (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/", $data['name'])) {
            $errors[] = "El nombre solo puede contener letras y espacios";
        }

        if (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/", $data['last_name'])) {
            $errors[] = "El apellido solo puede contener letras y espacios";
        }

        if (strtotime($data['birth_date']) > time()) {
            $errors[] = "La fecha de nacimiento no puede ser en el futuro";
        }

        if ($data['password'] !== $data['repeat_password']) {
            $errors[] = "Las contraseñas no coinciden";
        }

        if ($this->model->isUsernameTaken($data['username'])) {
            $errors[] = "El nombre de usuario ya existe";
        }

        return $errors;
    }

    private function ProfilePictureUpload($file, $username) {
        if ($file && $file['error'] === UPLOAD_ERR_OK) {
            $uploadDir = "uploads/";

            $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);

            $safeUsername = preg_replace("/[^a-zA-Z0-9_-]/", "", $username);

            $filename = $safeUsername . '.' . $fileExtension;
            $path = $uploadDir . $filename;

            move_uploaded_file($file['tmp_name'], $path);
            return $path;
        }

        return null;
    }



}
