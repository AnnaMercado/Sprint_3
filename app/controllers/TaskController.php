<?php
include __DIR__ . '/../../lib/base/Controller.php';

class TaskController extends Controller {
    //private $jsonFile = BASE_PATH . '/data/tasks.json';
    private $jsonFile = __DIR__ . '/../../data/tasks.json';
 

    public function indexAction() {
        $tasks = $this->getAllTasks();
    }


    public function createAction() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->createNewTask($_POST);
            header('Location: /tasks');
            exit;
        }
    }


    public function updateAction($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->updateTask($id, $_POST);
            header('Location: /tasks');
            exit;
        }
        $task = $this->getTaskById($id);
        $this->view->task = $task;
        $this->view->render('scripts/TaskForms/EditFormTask');
    }


    public function deleteAction($id) {
        $this->deleteTask($id);
        header('Location: /tasks');
        exit;
    }


    private function getAllTasks() {
        $jsonData = file_get_contents($this->jsonFile);
        $data = json_decode($jsonData, true);
        return $data['tasks'] ?? [];
    }


    private function createNewTask($data) {
        $tasks = $this->getAllTasks();
        $newTask = [
            'id' => uniqid(),
            'name' => $data['name'],
            'task' => $data['task'],
            'created_at' => date('Y-m-d H:i:s'),
            'status' => $data['status'],
            'end_time' => $data['end_time'] ?? null,
            'email' => $data['email'] ?? ''
        ];
        $tasks[] = $newTask;
        $this->saveTasks($tasks);
    }


    private function updateTask($id, $data) {
        $tasks = $this->getAllTasks();
        foreach ($tasks as &$task) {
            if ($task['id'] == $id) {
                $task['name'] = $data['name'];
                $task['task'] = $data['task'];
                $task['status'] = $data['status'];
                break;
            }
        }
        $this->saveTasks($tasks);
    }

    private function deleteTask($id) {
        $tasks = $this->getAllTasks();
        $tasks = array_filter($tasks, function($task) use ($id) {
            return $task['id'] != $id;
        });
        $this->saveTasks(array_values($tasks));
    }

    private function getTaskById($id) {
        $tasks = $this->getAllTasks();
        foreach ($tasks as $task) {
            if ($task['id'] == $id) {
                return $task;
            }
        }
        return null;
    }

    private function saveTasks($tasks) {
        file_put_contents($this->jsonFile, json_encode(['tasks' => $tasks], JSON_PRETTY_PRINT));
    }


}
