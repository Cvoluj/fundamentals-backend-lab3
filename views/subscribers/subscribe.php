<?php
ini_set('display_errors', '0');
ini_set('log_errors',     '1');

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../db.php';

$sid = $_POST['subscriber_id'] ?? null;
$nid = $_POST['newsletter_id']  ?? null;

if (! $sid || ! $nid) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Incorrect input data'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$sql  = "INSERT INTO subscriptions (subscriber_id, newsletter_id) VALUES (?, ?)";
$stmt = $mysql_connection->prepare($sql);
$stmt->bind_param("ii", $sid, $nid);

if ($stmt->execute()) {
    echo json_encode([
        'status'  => 'ok',
        'message' => 'Subscribed'
    ], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Doesn\' made subscribtion, try again later.'
    ], JSON_UNESCAPED_UNICODE);
}

$stmt->close();
$mysql_connection->close();
exit;
