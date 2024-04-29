<?php
require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__, '../config/config.env');
$dotenv->load();

echo "DB Host: " . $_ENV['DB_HOST'] . "<br>";
echo "DB User: " . $_ENV['DB_USER'] . "<br>";
echo "DB Password: " . (empty($_ENV['DB_PASSWORD']) ? 'None set' : 'Set but hidden') . "<br>";
echo "DB Name: " . $_ENV['DB_NAME'] . "<br>";
?>
