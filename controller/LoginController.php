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

    public function login()
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

        if (!password_verify($password, $user['password'])) {
            $this->view->render('login', ['error' => "Contraseña incorrecta"]);
            return;
        }

        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'username' => $user['username'],
            'profile_picture' => !empty($user['profile_picture'])
                ? (strpos($user['profile_picture'], 'uploads/') === 0
                    ? $user['profile_picture']
                    : 'uploads/' . $user['profile_picture'])
                : 'uploads/default.jpg'
        ];


        header("Location: index.php?controller=home&method=index");
    }

    public function logout()
    {
        session_destroy();
        header("Location: /tp/home/index");
    }
}
