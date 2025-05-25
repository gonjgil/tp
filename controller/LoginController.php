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

        if (!password_verify($password, $user['contrasenia'])) {
            $this->view->render('login', ['error' => "Contraseña incorrecta"]);
            return;
        }

        $profilePicturePath = 'uploads/default.jpg';
        
        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['nombre'],
            'username' => $user['nombre_usuario'],
            'profile_picture' => !empty($user['foto_perfil'])
                ? (strpos($user['foto_perfil'], 'uploads/') === 0
                    ? $user['foto_perfil']
                    : 'uploads/' . $user['foto_perfil'])
                : 'uploads/default.jpg'
        ];


        header("Location: index.php?controller=home&method=index");
        exit();
    }

    public function logout()
    {
        session_destroy();
        header("Location: /tp/home/index");
        exit();
    }
}
