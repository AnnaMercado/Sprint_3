<?php

class Task {
    private $id;
    private $name;
    private $task;
    private $created_at;
    private $status;
    private $endTime;
    private $email;

    public function __construct($id, $name, $task,$created_at, $status,  $endTime, $email) {
        $this->id = $id;
        $this->name = $name;
        $this->task = $task;
        $this->created_at = $created_at;
        $this->status = $status;
        $this->endTime = $endTime;
        $this->email = $email;
    }

    // Getters and setters for each property minus id
    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    public function setName($name) { $this->name = $name; }
    public function getTask() { return $this->task; }
    public function setTask($task) { $this->task = $task; }
    public function getStatus() { return $this->status; }
    public function setStatus($status) { $this->status = $status; }
    public function getCreatedAt() { return $this->created_at; }
    public function setCreatedAt($created_at) { $this->created_at = $created_at; }
    public function getEndTime() { return $this->endTime; }
    public function setEndTime($endTime) { $this->endTime = $endTime; }
    public function getEmail() { return $this->email; }
    public function setEmail($email) { $this->email = $email; }
    
    public function toArray() {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'task'        => $this->task,         
            'created_at'  => $this->created_at,
            'status'      => $this->status,
            'endTime'     => $this->endTime,
            'email'       => $this->email,
        ];
    }

   
}

?>