<?php
include 'userClass.php';

$file_path = 'users.json';
$error_message = "";

class UserController extends Controller {

    function validateValuesAction($name, $email) {
        if (empty($name) || empty($email)) {
            return "Por favor, complete todos los campos.";
        }
        return "";
    }

    function validateEmailAction($email, $file_path) {
        $users = file_exists($file_path) ? json_decode(file_get_contents($file_path), true) : [];
        foreach ($users as $user) {
            if ($user['email'] == $email) {
                return "Este correo ya está registrado. Por favor, usa otro.";
            }
        }
        return "";
    }

    function createUserAction($name, $email, $file_path) {
        $new_user = new User($name, $email);       
        return $new_user->saveUserAction($file_path);
        

    }
}



if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = filter_var(trim($_POST['name']), FILTER_SANITIZE_STRING);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);

    $error_message = validateValuesAction($name, $email);

    if (empty($error_message)) {
        $error_message = validateEmailAction($email, $file_path);
    }

    if (empty($error_message)) {
        if (createUserAction($name, $email, $file_path)) {
            header("Location: userList.phtml");
            exit(); // Asegurarse de que el script termine después de la redirección
        } else {
            echo "Hubo un error al crear el usuario.";
        }
    }
    else{
        echo $error_message ;
    }
}
        
?>