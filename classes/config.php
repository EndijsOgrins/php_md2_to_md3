<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Europe/Riga');

class DatabaseCon {
    private $mysqli;
    
    public function __construct() {
        $db_host = 'localhost';
        $db_port = '3306';
        $db_user = 'root';
        $db_pass = 'usbw';
        $db_name = 'rally';
        $charset = 'utf8mb4';

        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        
        try {
            $this->mysqli = new mysqli(
                $db_host, 
                $db_user, 
                $db_pass, 
                $db_name, 
                $db_port
            );
            $this->mysqli->set_charset($charset);
        } catch (mysqli_sql_exception $e) {
            $error_message = match ($e->getCode()) {
                1045 => "Access denied: Invalid username/password",
                1049 => "Database '{$db_name}' does not exist",
                2002 => "Connection refused: Check host availability",
                default => "Connection failed"
            };
            die("$error_message (Error {$e->getCode()})");
        }
    }

    public function getConnection() {
        return $this->mysqli;
    }
}

$database = new DatabaseCon();
$mysqli = $database->getConnection();
?>