<?php

class DatabaseConnection
{

    // private $user = "admindatabase";
    // private $password = "password";
    private $server = "localhost";
    private $user = "root";
    private $password = "";
    private $database = "bus_reserve";

    private static $instance = null;

    private $conn;
    private function __construct()
    {
        $this->conn = new PDO("mysql:host=" . $this->server . ";dbname=" . $this->database, $this->user, $this->password);
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new DatabaseConnection();
        }

        return self::$instance;
    }

    public function getConnection()
    {
        return $this->conn;
    }
}

?>