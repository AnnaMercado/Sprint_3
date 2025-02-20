<?php
include __DIR__ . '/../../lib/base/Controller.php';
include __DIR__ . '/../models/TaskClass.php';

class TaskController extends Controller {
   private String $jsonFile = __DIR__ . '/../data/tasks.json';
   
   //c-read-ud
    public function indexAction() {
        $tasks = $this->getAllTasks();
     
    }

    //create-rud
    public function createAction() {
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = filter_var(trim($_POST['name']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $email = isset($_POST['email']) ? trim($_POST['email']) : "";               // Email may be missing on the createform page
            $task = filter_var(trim($_POST['task']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $created_at = filter_var(trim($_POST['created_at']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $status = filter_var(trim($_POST['status']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            $error_message = $this->validateValues($email);

            if (empty($error_message)) {
                $this->createNewTask([
                    'name'       => $name,
                    'email'      => $email,
                    'task'       => $task,
                    'created_at' => $created_at,
                    'status'     => $status
                ]);
                header("Location:  tasks"); 
            } else {
                echo $error_message;
            }
        }
    }

    //cr---update---d
   public function updateAction($id) {
    $data = json_decode(file_get_contents($this->jsonFile), true);
    $tasks = $data['tasks'] ?? [];

    $task = null;
    foreach ($tasks as &$t) {
        if ($t['id'] === $id) {
            $task = &$t;     
            break;
        }
    }
    if (!$task) {
        die("Error: Task not found.");
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $task['name'] = filter_var(trim($_POST['name']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $task['task'] = filter_var(trim($_POST['task']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $task['status'] = filter_var(trim($_POST['status']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        file_put_contents($this->jsonFile, json_encode(['tasks' => $tasks], JSON_PRETTY_PRINT));

        header("Location: /index");
        exit();
    }
    include __DIR__ . '/../views/scripts/task/update.phtml';
}


    //cru---delete
    public function deleteAction()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
    
            if ($id) {
                $tasks = $this->getAllTasks();
                $tasks = array_filter($tasks, function($task) use ($id) {
                    return $task['id'] !== $id; 
                });
    
                $this->saveTasks(array_values($tasks));
    
                header('Location: tasks');  
                exit();
            } else {
                echo json_encode(['status' => 'error', 'message' => 'No task ID provided']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
        }
    
        exit();
    }
    



   //helper functions for the crud
    public function getAllTasks() {
        if (!file_exists($this->jsonFile)) {
            return [];
        }
        $jsonData = file_get_contents($this->jsonFile);
        $data = json_decode($jsonData, true);
        return $data['tasks'] ?? [];
    }

    //saving tasks
    public function saveTasks($tasks) {
        file_put_contents($this->jsonFile, json_encode(['tasks' => $tasks], JSON_PRETTY_PRINT));
    }

    //create tasks
    public function createNewTask($data) {
        $tasks = $this->getAllTasks();
        $newTask = [
            'id'       => uniqid(),
            'name'      => $data['name'],
            'task'        => $data['task'],
            'created_at'  => date('Y-m-d H:i:s'),
            'status'    => $data['status'],
            'email'   => $data['email']
        ];
        $tasks[] = $newTask;
        $this->saveTasks($tasks);
    }

 
    public function validateValues($email) {
        if (empty($email)) {
            return "Por favor, complete todos los campos.";
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "Por favor, ingrese un correo electrónico válido.";
        }

        //red zone
        $tasks = $this->getAllTasks();
        foreach ($tasks as $task) {
            if ($task['email'] == $email) {
                return "Este correo ya está registrado. Por favor, usa otro.";
            }
        }
        return "";
    } 

}

?>