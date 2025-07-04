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

    public function handleLogin()
    {
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
        if ($user['is_active'] == 0) {
            $this->view->render('login', ['error' => "Tenes que activar tu usuario"]);
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
            case 'player':
                header("Location: /player/panel");
                exit();
            case 'editor':
                header("Location: /editor/panel");
                exit();
            case 'admin':
                header("Location: /admin/panel");
                exit();
            default:
                header("Location: /login");
                exit();
        }
    }

    public function logout()
    {
        session_destroy();
        header("Location: /");
        exit();
    }
}