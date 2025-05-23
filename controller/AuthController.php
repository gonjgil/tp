<?php

class AuthController
{
    private $view;
    private $model;

    public function __construct($view, $userModel)
    {
        $this->view = $view;
        $this->model = $userModel;
    }

    public function login()
    {
        $this->view->render('login');
    }

    public function handleLogin()
    {
        $email = $_POST['email'];
        $password = $_POST['password'];
        $user = $this->model->findUserByEmail($email);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user;
            header("Location: index.php?controller=home&method=index");
        } else {
            echo "Login incorrecto";
        }
    }

    public function register()
    {
        $this->view->render('register');
    }

    public function handleRegister()
    {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $this->model->createUser($name, $email, $password);
        header("Location: index.php?controller=auth&method=login");
    }

    public function logout()
    {
        session_destroy();
        header("Location: /tp/home/index");
    }

}
