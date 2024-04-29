<?php
// Load Composer's autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__, 'config.env');
$dotenv->load();

// Enhancements for session security BEFORE starting the session
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1); // Enable this if using HTTPS
ini_set('session.use_only_cookies', 1);

// Now start the session
session_start();

date_default_timezone_set("Asia/Kolkata");

// Use environment variables for database credentials
$host = $_ENV['DB_HOST'];
$user = $_ENV['DB_USER'];
$password = $_ENV['DB_PASSWORD'];
$database = $_ENV['DB_NAME'];

$con = mysqli_connect($host, $user, $password, $database);

if (mysqli_connect_errno()) {
    // Log error instead of displaying it
    error_log("Failed to connect to MySQL: " . mysqli_connect_error());
    exit;
}
?>
