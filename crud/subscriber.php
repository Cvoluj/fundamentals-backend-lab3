<?php
require_once __DIR__ . '/../db.php';

function get_subscriber($id) {
    global $mysql_connection;
    $stmt = $mysql_connection->prepare("SELECT id, name, email, address, password FROM subscribers WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function create_subscriber($name, $email, $address, $password) {
    global $mysql_connection;
    $hash = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $mysql_connection->prepare(
        "INSERT INTO subscribers (name, email, address, password) VALUES (?, ?, ?, ?)"
    );
    $stmt->bind_param("ssss", $name, $email, $address, $hash);
    return $stmt->execute();
}

function update_subscriber($id, $name, $email, $address, $password = null) {
    global $mysql_connection;
    if ($password) {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $sql = "UPDATE subscribers SET name = ?, email = ?, address = ?, password = ? WHERE id = ?";
        $stmt = $mysql_connection->prepare($sql);
        $stmt->bind_param("ssssi", $name, $email, $address, $hash, $id);
    } else {
        $sql = "UPDATE subscribers SET name = ?, email = ?, address = ? WHERE id = ?";
        $stmt = $mysql_connection->prepare($sql);
        $stmt->bind_param("sssi", $name, $email, $address, $id);
    }
    return $stmt->execute();
}

function delete_subscriber($id) {
    global $mysql_connection;
    $stmt = $mysql_connection->prepare("DELETE FROM subscribers WHERE id = ?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

function fetch_all_subscribers($sql) {
    global $mysql_connection;
    $res = $mysql_connection->query($sql);
    return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
}

function get_subscriptions($subscriber_id) {
    global $mysql_connection;
    $stmt = $mysql_connection->prepare(
        "SELECT n.id, n.subject, s.subscribed_at
         FROM subscriptions s
         JOIN newsletters n ON s.newsletter_id = n.id
         WHERE s.subscriber_id = ?"
    );
    $stmt->bind_param("i", $subscriber_id);
    $stmt->execute();
    $res = $stmt->get_result();
    return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
}

function remove_subscription(int $subscriber_id, int $newsletter_id): bool {
    global $mysql_connection;
    $sql  = "DELETE FROM subscriptions
             WHERE subscriber_id = ? 
               AND newsletter_id = ?";
    $stmt = $mysql_connection->prepare($sql);
    return $stmt->execute([$subscriber_id, $newsletter_id]);
}

