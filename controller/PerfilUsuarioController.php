<?php

class PerfilUsuarioController
{
    private $view;
    private $model;

    public function __construct($view, $userModel)
    {
        $this->view = $view;
        $this->model = $userModel;
    }

    public function show($id = null)
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user'])) {
            header("Location: /login");
            exit();
        }

        if ($id === null) {
            $id = $_SESSION['user']['id'];
        }

        $user = $this->model->getUserById($id);
        if (!$user) {
            die("User with ID = $id not found.");
        }

        $user['is_own_profile'] = ($_SESSION['user']['id'] == $id);
        $this->view->render("perfilUsuario", $user);
    }

    public function edit()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user'])) {
            header("Location: /login");
            exit();
        }

        $userId = $_SESSION['user']['id'];
        $user = $this->model->getUserById($userId);

        if (!$user) {
            die("User not found.");
        }

        $user['gender_male']   = $user['gender'] === 'Masculino';
        $user['gender_female'] = $user['gender'] === 'Femenino';
        $user['gender_other']  = $user['gender'] === 'Otro';

        $this->view->render('editProfile', $user);
    }

    public function save()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user'])) {
            header("Location: /login");
            exit();
        }

        $userId = $_SESSION['user']['id'];
        $data = $_POST;
        $errors = [];

        // Validación de contraseña
        if (!empty($data['current_password']) || !empty($data['new_password']) || !empty($data['repeat_password'])) {
            $currentPassword = $data['current_password'] ?? '';
            $newPassword     = $data['new_password'] ?? '';
            $repeatPassword  = $data['repeat_password'] ?? '';

            $user = $this->model->getUserRawById($userId);

            if ($currentPassword !== $user['password']) {
                $errors[] = "La contraseña actual es incorrecta.";
            } elseif ($newPassword !== $repeatPassword) {
                $errors[] = "Las nuevas contraseñas no coinciden.";
            } elseif (strlen($newPassword) < 5) {
                $errors[] = "La nueva contraseña debe tener al menos 5 caracteres.";
            } else {
                $data['password'] = $newPassword;
            }
        }

        // Subida de imagen
        $filename = $this->handleProfilePicture($_FILES['profile_picture'], $_SESSION['user']['username']);
        if ($filename) {
            $data['profile_picture'] = $filename;
        }

        if (!empty($errors)) {
            $user = $this->model->getUserById($userId);
            $user['gender_male']   = $user['gender'] === 'Masculino';
            $user['gender_female'] = $user['gender'] === 'Femenino';
            $user['gender_other']  = $user['gender'] === 'Otro';
            $user['errors'] = $errors;
            $user['lat'] = $data['lat'];
            $user['lng'] = $data['lng'];
            $this->view->render('editProfile', $user);
            return;
        }

        $success = $this->model->updateUser($userId, $data);

        if ($success) {
            //  ACTUALIZAR SESIÓN
            $updatedUser = $this->model->getUserById($userId);
            $_SESSION['user']['name'] = $updatedUser['name'];
            $_SESSION['user']['profile_picture'] = $updatedUser['profile_picture'];
            $_SESSION['user']['username'] = $updatedUser['username'];

            header("Location: /perfilUsuario");
            exit();
        } else {
            die("Error al guardar los cambios.");
        }
    }




    private function handleProfilePicture($file, $username)
    {
        if ($file['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $safeUsername = preg_replace('/[^a-zA-Z0-9_-]/', '', $username);
        $filename = $safeUsername . '.' . $ext;

        $uploadDir = "uploads/";
        $fullPath = $uploadDir . $filename;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if (move_uploaded_file($file['tmp_name'], $fullPath)) {
            return $fullPath;
        }

        return null;
    }


}