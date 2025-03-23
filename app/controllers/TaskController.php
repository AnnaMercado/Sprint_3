<?php
include __DIR__ . '/../../lib/base/Controller.php';
include __DIR__ . '/../models/TaskClass.php';

class TaskController extends Controller {
   private String $jsonFile = __DIR__ . '/../data/tasks.json';
   
   //c-read-ud
    public function indexAction() {
        $tasks = $this->getAllTasks();

        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $searchQuery = filter_var(trim($_GET['search']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            // Filter tasks by name or email
            $tasks = array_filter($tasks, function($task) use ($searchQuery) {
                return stripos($task['name'], $searchQuery) !== false || stripos($task['email'], $searchQuery) !== false;
            });
        }

        // Capturing the view output manually
    require_once ROOT_PATH . '/app/views/scripts/task/index.phtml';

    $this->view->disableView(); 
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
    public function updateAction() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            die("Error: Direct access to update page is not allowed.");
        }
    
        if (!isset($_POST['id']) || empty($_POST['id'])) {
            die("Error: No task identifier provided.");
        }
    
        $id = $_POST['id'];
        
        $tasks = $this->getAllTasks();
        $taskToUpdate = null;
    
        // Find the task by ID
        foreach ($tasks as $task) {
            if ($task['id'] === $id) {
                $taskToUpdate = $task;
                break;
            }
        }
    
        if (!$taskToUpdate) {
            die("Error: Task not found.");
        }
    
        // If form is submitted, update the task
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
            $taskToUpdate['name'] = $_POST['name'] ?? $taskToUpdate['name'];
            $taskToUpdate['task'] = $_POST['task'] ?? $taskToUpdate['task'];
            $taskToUpdate['created_at'] = $_POST['created_at'] ?? $taskToUpdate['created_at'];
            $taskToUpdate['status'] = $_POST['status'] ?? $taskToUpdate['status'];
    
            // Save changes to tasks array
            foreach ($tasks as &$task) {
                if ($task['id'] === $taskToUpdate['id']) {
                    $task = $taskToUpdate;
                    break;
                }
            }
    
            $this->saveTasks($tasks);
    
            // Redirect right after update
            header("Location: tasks");
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

     //validating zone
    public function validateValues($email) {
        if (empty($email)) {
            return "Please fill in all the fields.";
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "Please enter a valid email address.";
        }

        //red zone
        $tasks = $this->getAllTasks();
        foreach ($tasks as $task) {
            if ($task['email'] == $email) {
                return "This email address is already registered. Please use another one.";
            }
        }
        return "";
    } 

}

?>