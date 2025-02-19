<?php
include 'userClass.php';

$file_path = 'users.json';

class UserController extends Controller {

    function logInUser(){
        // Logic for logging in users (not implemented here)
    }

    function signUpUser(){
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            include 'signUp.phtml';
        } else {
            $name = filter_var(trim($_POST['name']), FILTER_SANITIZE_STRING);
            $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);

            $error_message = $this->validateValues($name, $email, $file_path);

            if (empty($error_message)) {
                $new_user = new User($name, $email);
                $new_user->saveUserAction($file_path);
                header("Location: userList.phtml");
                exit();
            } else {
                echo $error_message;
            }
        }
    }

    function validateValues($name, $email, $file_path) {
        if (empty($name) || empty($email)) {
            return "Por favor, complete todos los campos.";
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "Por favor, ingrese un correo electrónico válido.";
        }

        $users = file_exists($file_path) ? json_decode(file_get_contents($file_path), true) : [];
        foreach ($users as $user) {
            if ($user['email'] == $email) {
                return "Este correo ya está registrado. Por favor, usa otro.";
            }
        }
        return "";
    }
}
?>
