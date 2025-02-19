<?php
include __DIR__ . '/../models/userClass.php';
include __DIR__ . '/../../lib/base/Controller.php';


class UserController extends Controller {
    function logInUserAction(){
        $file_path = __DIR__ . '/../data/users.json';
    
        if ($_SERVER['REQUEST_METHOD'] == 'POST') { 
            $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        
            if (file_exists($file_path) && filesize($file_path) > 0) {
                $users = json_decode(file_get_contents($file_path), true);
                
                $user_found = false;
                $user_name = ''; 
                foreach ($users as $user) {
                    if ($user['email'] === $email) {
                        $user_found = true;
                        $user_name = $user['name'];
                        break;
                    }
                }

                if ($user_found) {
                    session_start();
                    $_SESSION['user_name'] = $user_name;
                    header("Location: todolist.phtml");
                    exit();
                } else {
                    echo "No tienes cuenta o el correo está mal escrito. Intenta de nuevo.";
                }
            } else {
                echo "Error: No se encontraron usuarios registrados.";
            }
        }
    }

    function signUpUserAction(){
        $file_path = __DIR__ . '/../data/users.json';
        if ($_SERVER['REQUEST_METHOD'] == 'POST') { 
            $name = filter_var(trim($_POST['name']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    
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
