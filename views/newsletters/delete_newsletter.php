<?php
require_once __DIR__ . '/../../crud/newsletter.php';

$id = (int)($_GET['id'] ?? 0);
if ($id && delete_newsletter($id)) {
    header('Location: /lab3/newsletters');
    exit;
} else {
    die('Cannot delete newsletter');
}
