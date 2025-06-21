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
        $this->requireLogin();

        $id = $id ?? $_SESSION['user']['id'];
        $user = $this->model->getUserById($id);

        if (!$user) {
            die("Usuario con ID = $id no encontrado.");
        }

        $user['is_own_profile'] = ($_SESSION['user']['id'] == $id);
        $this->view->render("perfilUsuario", $user);
    }

    public function edit()
    {
        $this->requireLogin();

        $userId = $_SESSION['user']['id'];
        $user = $this->model->getUserById($userId);

        if (!$user) {
            die("Usuario no encontrado.");
        }

        $this->prepareGenderFlags($user);
        $this->view->render('editProfile', $user);
    }

    public function save()
    {
        $this->requireLogin();

        $userId = $_SESSION['user']['id'];
        $data = $_POST;
        $errors = $this->validatePasswordChange($userId, $data);

        $filename = $this->handleProfilePicture($_FILES['profile_picture'], $_SESSION['user']['username']);
        if ($filename) {
            $data['profile_picture'] = $filename;
        }

        if (!empty($errors)) {
            $user = $this->model->getUserById($userId);
            $this->prepareGenderFlags($user);

            $user['errors'] = $errors;
            $user['lat'] = $data['lat'];
            $user['lng'] = $data['lng'];
            $this->view->render('editProfile', $user);
        } else {
            $this->updateUserAndSession($userId, $data);
            header("Location: /perfilUsuario");
            exit();
        }
    }

    // --------------------- metedos invocados -----------------------

    private function requireLogin()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user'])) {
            header("Location: /login");
            exit();
        }
    }

    private function validatePasswordChange($userId, &$data)
    {
        $errors = [];

        $current = trim($data['current_password'] ?? '');
        $new = trim($data['new_password'] ?? '');
        $repeat = trim($data['repeat_password'] ?? '');

        if ($current || $new || $repeat) {
            $user = $this->model->getUserRawById($userId);

            if ($current !== $user['password']) {
                $errors[] = "La contraseña actual es incorrecta.";
            } elseif ($new !== $repeat) {
                $errors[] = "Las nuevas contraseñas no coinciden.";
            } elseif (strlen($new) < 5) {
                $errors[] = "La nueva contraseña debe tener al menos 5 caracteres.";
            } else {
                $data['password'] = $new;
            }
        }

        return $errors;
    }

    private function handleProfilePicture($file, $username)
    {
        if ($file['error'] === UPLOAD_ERR_NO_FILE || $file['error'] !== UPLOAD_ERR_OK) {
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

        return move_uploaded_file($file['tmp_name'], $fullPath) ? $fullPath : null;
    }

    private function prepareGenderFlags(&$user)
    {
        $user['gender_male'] = $user['gender'] === 'Masculino';
        $user['gender_female'] = $user['gender'] === 'Femenino';
        $user['gender_other'] = $user['gender'] === 'Otro';
    }

    private function updateUserAndSession($userId, $data)
    {
        $success = $this->model->updateUser($userId, $data);

        if (!$success) {
            die("Error al guardar los cambios.");
        }

        $updatedUser = $this->model->getUserById($userId);
        $_SESSION['user']['name'] = $updatedUser['name'];
        $_SESSION['user']['profile_picture'] = $updatedUser['profile_picture'];
        $_SESSION['user']['username'] = $updatedUser['username'];
    }
}
