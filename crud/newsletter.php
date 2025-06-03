<?php

require_once __DIR__ . '/../db.php';

function get_newsletter(int $id): array|null {
    global $mysql_connection;
    $stmt = $mysql_connection->prepare(
        "SELECT id, subject, content, sent_at 
         FROM newsletters 
         WHERE id = ?"
    );
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc() ?: null;
}

function create_newsletter(string $subject, string $content): bool {
    global $mysql_connection;
    $stmt = $mysql_connection->prepare(
        "INSERT INTO newsletters (subject, content) VALUES (?, ?)"
    );
    $stmt->bind_param("ss", $subject, $content);
    return $stmt->execute();
}

function update_newsletter(int $id, string $subject, string $content): bool {
    global $mysql_connection;
    $stmt = $mysql_connection->prepare(
        "UPDATE newsletters SET subject = ?, content = ? WHERE id = ?"
    );
    $stmt->bind_param("ssi", $subject, $content, $id);
    return $stmt->execute();
}

function delete_newsletter(int $id): bool {
    global $mysql_connection;
    $stmt = $mysql_connection->prepare("DELETE FROM subscriptions WHERE newsletter_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    $stmt = $mysql_connection->prepare("DELETE FROM newsletters WHERE id = ?");
    $stmt->bind_param("i", $id);
    $res = $stmt->execute();
    $stmt->close();
    return $res;
}

function fetch_all_newsletters(string $sql): array {
    global $mysql_connection;
    $res = $mysql_connection->query($sql);
    return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
}
