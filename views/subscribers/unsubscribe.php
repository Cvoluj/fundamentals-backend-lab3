<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../db.php';
require_once __DIR__ . '/../../crud/subscriber.php';

$sid = $_POST['subscriber_id']  ?? null;
$nid = $_POST['newsletter_id'] ?? null;

if (! $sid || ! $nid) {
    echo json_encode(['status'=>'error','message'=>'Incorrect data']);
    exit;
}

if (remove_subscription((int)$sid, (int)$nid)) {
    echo json_encode(['status'=>'ok']);
} else {
    echo json_encode(['status'=>'error','message'=>'Doesn\'t made subscribe']);
}
