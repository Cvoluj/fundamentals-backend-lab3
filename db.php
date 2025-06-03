<?php
require __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$mysql_connection = new mysqli($_ENV['DB_HOST'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD'], $_ENV['DB_NAME'], port:$_ENV['DB_PORT']);

if ($mysql_connection->connect_error) {
    die("Помилка підключення: " . $mysql_connection->connect_error);
}
?>
