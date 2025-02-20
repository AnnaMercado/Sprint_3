<?php
include __DIR__ . '/../../lib/base/Controller.php';
include __DIR__ . '/../models/TaskClass.php';

class TaskController extends Controller {
    //private $jsonFile = BASE_PATH . '/data/tasks.json';
   private String $jsonFile = __DIR__ . '/../data/tasks.json';
   
   //c-read-ud
    public function indexAction() {
        $tasks = $this->getAllTasks();
        // include 'C:/xampp/htdocs/MyProject/Developer_Team/app/views/scripts/task/taskList.phtml';
        //require_once  __DIR__ . '/../../views/scripts/task/taskList.phtml';
        //include __DIR__ . '/../views/scripts/task/taskList.phtml';
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
                header("Location:  tasks"); // header("Location:  /tasks/taskList.phtml");
                exit();
            } else {
                echo $error_message;
            }
        }
    }

    //cr---update---d
   public function updateAction($id) {
    // Load tasks from JSON file
    $data = json_decode(file_get_contents($this->jsonFile), true);
    $tasks = $data['tasks'] ?? [];

    // Find the task with the given user ID
    $task = null;
    foreach ($tasks as &$t) {
        if ($t['id'] === $id) {
            $task = &$t;     // Reference the task
            break;
        }
    }

    // try If task is not found, stop and display the error
    if (!$task) {
        die("Error: Task not found.");
    }

    // If the form is submitted,then  update the task details 
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $task['name'] = filter_var(trim($_POST['name']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $task['task'] = filter_var(trim($_POST['task']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $task['status'] = filter_var(trim($_POST['status']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        // Save the updated tasks back into the JSON file
        file_put_contents($this->jsonFile, json_encode(['tasks' => $tasks], JSON_PRETTY_PRINT));

        // Redirect to the main task list page
        header("Location: /index");
        exit();
    }

    // Pass the task to the view for editing
    include __DIR__ . '/../views/scripts/task/update.phtml';
}



    // Handles the task deletion logic
    public function deleteAction()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get the task ID from the POST request
            $id = $_POST['id'] ?? null;
    
            if ($id) {
                // Fetch all tasks, filter out the one to be deleted, and save the updated list
                $tasks = $this->getAllTasks();
                $tasks = array_filter($tasks, function($task) use ($id) {
                    return $task['id'] !== $id;  // Remove task with matching ID
                });
    
                $this->saveTasks(array_values($tasks));
    
                header('Location: tasks');  // Ensure this URL matches your route
                exit();
            } else {
                // If no task ID provided, show an error
                echo json_encode(['status' => 'error', 'message' => 'No task ID provided']);
            }
        } else {
            // If the request method is not POST, return an error
            echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
        }
    
        exit();
    }
    



   //helper functions for the crud
    public function getAllTasks() {
        if (!file_exists($this->jsonFile)) {
            //file_put_contents($this->jsonFile, json_encode(['tasks' => []], JSON_PRETTY_PRINT));
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