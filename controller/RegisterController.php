<?php
require_once 'api/emailSender.php';

class RegisterController
{
  private $view;
  private $model;
  private $emailSender;

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
    $profilePicture = $this->ProfilePictureUpload(
      $_FILES['profile_picture'],
      $username
    );

    if (empty($errors)) {
      $rawPassword = $data['password'];
      $hashedPassword = password_hash($rawPassword, PASSWORD_DEFAULT);
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
        $hashedPassword,
        $profilePicture,
        3,
        0,
        $token,
        $lat,
        $lng
      );

      $body = $this->generateEmailBodyFor(
        $newUser['name'],
        $newUser['last_name'],
        $newUser['id'],
        $token
      );

      $this->emailSender->send($newUser['email'], $body);
      $this->view->render('registerSuccess');
    } else {
      $this->view->render('register', [
        'errors' => $errors,
        'old' => $data,
      ]);
    }
  }

  private function isValidName($name)
  {
    return preg_match('/^[a-zA-Z\s]+$/', $name);
  }

  private function isFutureDate($dateString)
  {
    $date = new DateTime($dateString);
    $now = new DateTime();
    return $date > $now;
  }

  private function validateRegistrationData($data)
  {
    $errors = [];

    $validations = [
      [
        'condition' => !$this->isValidName($data['name']),
        'message' => 'El nombre solo puede contener letras y espacios',
      ],
      [
        'condition' => !$this->isValidName($data['last_name']),
        'message' => 'El apellido solo puede contener letras y espacios',
      ],
      [
        'condition' => $this->isFutureDate($data['birth_date']),
        'message' => 'La fecha de nacimiento no puede ser en el futuro',
      ],
      [
        'condition' => $data['password'] !== $data['repeat_password'],
        'message' => 'Las contraseñas no coinciden',
      ],
      [
        'condition' => $this->model->isEmailTaken($data['email']),
        'message' => 'El email ya está en uso',
      ],
      [
        'condition' => $this->model->isUsernameTaken($data['username']),
        'message' => 'El nombre de usuario ya existe',
      ],
    ];

    foreach ($validations as $validation) {
      if ($validation['condition']) {
        $errors[] = $validation['message'];
      }
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
    $link = "http://localhost/register/validateMail?iduser=$iduser&idverificador=$token";

    return "
        <body>
            <p>Hola $name <strong>" .
      strtoupper($last_name) .
      "</strong>,</p>
            <p>Creaste con éxito tu cuenta en Codigo Trivia.</p>
            <p>Para validar tu nueva cuenta, dale click al boton de abajo</p>
            <p>
               <a href='$link'
                  style='padding: 10px 20px; background-color: #337ab7; color: white; border-radius: 5px; text-decoration: none;'>
                   Validar cuenta
               </a>
           </p>
        </body>
    ";
  }
}
