<html lang="en">
    <head>
    <link rel="stylesheet" href="/lab3/css/style.css">
    
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <title>Lab1</title>
</head>
<body>
    <h4>Main Page</h4>
    
    <?php
require 'vendor/autoload.php';

use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$mysql_connection = mysqli_connect($_ENV['DB_HOST'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD'], port: $_ENV['DB_PORT']);
if (!$mysql_connection) {
    echo "<div>Connection error: " . mysqli_connect_error() . "</div>";
    exit;
} else {
    echo "<div>Connected successfully</div>";
}

$dbname = "newsletter_system";
$query = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($mysql_connection->query($query) === TRUE) {
    echo "<div>Database '$dbname' is ready</div>";
} else {
    echo "<div>Error creating database: " . $mysql_connection->error . "</div>";
}

$select = mysqli_select_db($mysql_connection, $dbname);
if ($select) {
    echo "<div>Database selected! 😊</div>";
} else {
    echo "<div>Database not selected</div>";
}

// subscribers
$query_subscribers = "CREATE TABLE IF NOT EXISTS subscribers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    address VARCHAR(255),
    password VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)";
if ($mysql_connection->query($query_subscribers) === TRUE) {
    echo "<div>Table 'subscribers' created successfully</div>";
} else {
    echo "<div>Error creating table 'subscribers': " . $mysql_connection->error . "</div>";
}

// newsletters
$query_newsletters = "CREATE TABLE IF NOT EXISTS newsletters (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subject VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    sent_at DATETIME DEFAULT CURRENT_TIMESTAMP
)";
if ($mysql_connection->query($query_newsletters) === TRUE) {
    echo "<div>Table 'newsletters' created successfully</div>";
} else {
    echo "<div>Error creating table 'newsletters': " . $mysql_connection->error . "</div>";
}

// subscriptions
$query_subscriptions = "CREATE TABLE IF NOT EXISTS subscriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subscriber_id INT NOT NULL,
    newsletter_id INT NOT NULL,
    subscribed_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (subscriber_id) REFERENCES subscribers(id),
    FOREIGN KEY (newsletter_id) REFERENCES newsletters(id)
)";
if ($mysql_connection->query($query_subscriptions) === TRUE) {
    echo "<div>Table 'subscriptions' created successfully</div>";
} else {
    echo "<div>Error creating table 'subscriptions': " . $mysql_connection->error . "</div>";
}

$mysql_connection->close();
?>

<div>
  <h3>Actions:</h3>
  <a href="/lab3/subscribers">List Subscribers</a> |
  <a href="/lab3/newsletters">List Newsletters</a>
</div>

</body>
</html>
