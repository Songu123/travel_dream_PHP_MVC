<?php
// Database configuration for XAMPP
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "du_lich";
$port = 3306; // Default MySQL port in XAMPP

// Test connection first (without database)
$test_conn = new mysqli($servername, $username, $password, "", $port);
if ($test_conn->connect_error) {
    die("XAMPP MySQL connection failed. Please check:<br>
        1. XAMPP is running<br>
        2. MySQL service is started<br>
        3. Port 3306 is available<br>
        Error: " . $test_conn->connect_error);
}

// Check if database exists
$db_check = $test_conn->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$dbname'");
if ($db_check->num_rows == 0) {
    die("Database '$dbname' not found. Please create it first:<br>
        1. Open phpMyAdmin (http://localhost/phpmyadmin)<br>
        2. Create database named '$dbname'<br>
        3. Set collation to utf8mb4_unicode_ci");
}
$test_conn->close();

// Create connection to specific database
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Set charset to UTF8 for Vietnamese support
$conn->set_charset("utf8mb4");

// Success message for testing (remove in production)
// echo "✓ Connected to database '$dbname' successfully!<br>";

// Optional: Database class for OOP approach
class Database {
    private $host = "localhost";
    private $user = "root";
    private $pass = "";
    private $dbname = "du_lich";
    private $conn;

    public function __construct() {
        $this->connect();
    }

    private function connect() {
        $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->dbname);

        if ($this->conn->connect_error) {
            die("Database connection failed: " . $this->conn->connect_error);
        }

        // Set charset UTF8 for Vietnamese support
        $this->conn->set_charset("utf8");
    }

    public function getConnection() {
        return $this->conn;
    }

    public function close() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}
