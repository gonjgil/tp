<?php
class RegisterController
{
    private $view;
    private $model;

    public function __construct($view, $registerModel)
    {
        $this->view = $view;
        $this->model = $registerModel;
        $this->emailSender = new emailSender();
    }

    public function index()
    {
        $this->view->render('register');
    }

    public function handleRegister()
    {
        $data = $_POST;
        $username = $data['username'];

        $errors = $this->validateRegistrationData($data);
        $profilePicture = $this->ProfilePictureUpload($_FILES['profile_picture'], $username);

        if (empty($errors)) {
            $rawPassword = $data['password'];
            $lat = isset($data['lat']) ? floatval($data['lat']) : null;
            $lng = isset($data['lng']) ? floatval($data['lng']) : null;
            $token = $this->generateSecureToken(16);

            $newUser = $this->model->createUser(
                $data['name'],
                $data['last_name'],
                $data['birth_date'],
                $data['gender'],
                $data['country'],
                $data['city'],
                $data['email'],
                $data['username'],
                $rawPassword,
                $profilePicture,
                3, // id_rol por defecto = jugador
                0, // is_active = false, hasta que valide el mail
                $token,
                $lat,
                $lng
            );

            $body = $this->generateEmailBodyFor($newUser['name'], $newUser['last_name'], $newUser['id'], $token);
            $this->emailSender->send($newUser['email'], $body);

            $this->view->render('registerSuccess', ['message' => $body]);
        } else {
            $this->view->render('register', ['errors' => $errors]);
        }
    }

    private function validateRegistrationData($data)
    {
        $errors = [];

        if (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/", $data['name'])) {
            $errors[] = 'El nombre solo puede contener letras y espacios';
        }

        if (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/", $data['last_name'])) {
            $errors[] = 'El apellido solo puede contener letras y espacios';
        }

        if (strtotime($data['birth_date']) > time()) {
            $errors[] = 'La fecha de nacimiento no puede ser en el futuro';
        }

        if ($data['password'] !== $data['repeat_password']) {
            $errors[] = 'Las contraseñas no coinciden';
        }

        if ($this->model->isEmailTaken($data['email'])) {
            $errors[] = 'El email ya esta en uso';
        }

        if ($this->model->isUsernameTaken($data['username'])) {
            $errors[] = 'El nombre de usuario ya existe';
        }

        return $errors;
    }

    private function ProfilePictureUpload($file, $username)
    {
        if ($file && $file['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/';

            $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);

            $safeUsername = preg_replace('/[^a-zA-Z0-9_-]/', '', $username);

            $filename = $safeUsername . '.' . $fileExtension;
            $path = $uploadDir . $filename;

            move_uploaded_file($file['tmp_name'], $path);
            return $path;
        }

        return null;
    }

    public function generateSecureToken($length)
    {
        $bytes = random_bytes($length);
        return bin2hex($bytes);
    }

    public function validateMail()
    {
        $iduser = $_GET['iduser'];
        $idverificador = $_GET['idverificador'];
        $storedToken = $this->model->getToken($iduser);

        if ($idverificador === $storedToken) {
            $this->model->activateUser($iduser);
        }

        header('Location: /login');
        exit();
    }

    public function generateEmailBodyFor($name, $last_name, $iduser, $token)
    {
        return "<body>Hola $name " .
            strtoupper($last_name) .
            ($message = "
                <p class='w3-center w3-large w3-text-black'>
                    Creaste con éxito tu cuenta.
                </p>

                <p class='w3-center w3-large w3-text-black'>
                    Para validar tu nueva cuenta haz click en:
                </p>

                <p class='w3-center w3-large'>
                    <a href='/register/validateMail?iduser=$iduser&idverificador=$token' class='w3-button w3-win8-blue w3-round-large w3-large'>
                        Validar cuenta
                    </a>
                </p>
                ");
    }
}

class emailSender
{
    function send($email, $body)
    {
    }
}
