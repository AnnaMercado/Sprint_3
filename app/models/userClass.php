<?php

class User {
    private $name;
    private $email;

    public function __construct($name, $email) {
        $this->name = $name;
        $this->email = $email;
    }

    public function getName() {
        return $this->name;
    }

    public function getEmail() {
        return $this->email;
    }

    public function toArray() {
        return [
            'name' => $this->name,
            'email' => $this->email
        ];
    }
    public function saveUserAccion($file_path) {
        $users = file_exists($file_path) ? json_decode(file_get_contents($file_path), true) : [];

        $users[] = $this->toArray();
        return file_put_contents($file_path, json_encode($users, JSON_PRETTY_PRINT));
    }
}

?>
