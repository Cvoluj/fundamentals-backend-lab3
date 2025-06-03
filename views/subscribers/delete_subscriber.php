<?php
require_once __DIR__ . '/../../db.php';
require_once __DIR__ . '/../../crud/subscriber.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id > 0) {
    delete_subscriber($id);
}

header('Location: /lab3/subscribers');
exit;
