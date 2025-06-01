<?php

class LoginController
{
    private $view;
    private $model;

    public function __construct($view, $loginModel)
    {
        $this->view = $view;
        $this->model = $loginModel;
    }

    public function index()
    {
        $this->view->render('login');
    }

    public function handleLogin() {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $user = $this->model->findUserByUsername($username);

        if (!$user) {
            $this->view->render('login', ['error' => "Usuario no encontrado"]);
            return;
        }

        if ($password !== $user['password']) {
            $this->view->render('login', ['error' => "Contraseña incorrecta"]);
            return;
        }


        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'username' => $user['username'],
            'user_type' => $user['user_type'],
            'profile_picture' => !empty($user['profile_picture'])
                ? (strpos($user['profile_picture'], 'uploads/') === 0
                    ? $user['profile_picture']
                    : 'uploads/' . $user['profile_picture'])
                : 'uploads/default.jpg'
        ];

        switch ($user['user_type']) {
            case 'jugador':
                header("Location: /tp/player/panel");
                break;
            case 'editor':
                header("Location: /tp/editor/panel");
                break;
            case 'administrador':
                header("Location: /tp/admin/panel");
                break;
            default:
                header("Location: /tp/login");
                break;
        }
    }


    public function logout()
    {
        session_destroy();
        header("Location: /tp");
    }
}
