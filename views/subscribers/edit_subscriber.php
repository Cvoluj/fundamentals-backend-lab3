<?php
// lab3/views/subscribers/edit_subscriber.php

require_once __DIR__ . '/../../db.php';
require_once __DIR__ . '/../../crud/subscriber.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    // Invalid ID — go back to list
    header('Location: /lab3/subscribers');
    exit;
}

$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']    ?? '');
    $email    = trim($_POST['email']   ?? '');
    $address  = trim($_POST['address'] ?? '');
    $password = $_POST['password']     ?? '';

    if (update_subscriber($id, $name, $email, $address, $password)) {
        header('Location: /lab3/subscribers');
        exit;
    } else {
        $error = 'Failed to update subscriber.';
    }
}

// Load existing subscriber data
$subscriber = get_subscriber($id);
if (! $subscriber) {
    header('Location: /lab3/subscribers');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <link rel="stylesheet" href="/lab3/css/style.css">
  <meta charset="UTF-8">
  <title>Edit Subscriber #<?= htmlspecialchars($subscriber['id']) ?></title>
</head>
<body>
  <h2>Edit Subscriber #<?= htmlspecialchars($subscriber['id']) ?></h2>

  <?php if ($error): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <form method="POST">
    <div>
      <label for="name">Name</label><br>
      <input
        type="text"
        id="name"
        name="name"
        value="<?= htmlspecialchars($subscriber['name']) ?>"
        required
      >
    </div>
    <div>
      <label for="email">Email</label><br>
      <input
        type="email"
        id="email"
        name="email"
        value="<?= htmlspecialchars($subscriber['email']) ?>"
        required
      >
    </div>
    <div>
      <label for="address">Address</label><br>
      <input
        type="text"
        id="address"
        name="address"
        value="<?= htmlspecialchars($subscriber['address']) ?>"
        required
      >
    </div>
    <div> 
      <label for="password">
        New Password <small>(leave blank to keep current)</small>
      </label><br>
      <input
        type="password"
        id="password"
        name="password"
        placeholder="••••••••"
      >
    </div>
    <button type="submit">Save Changes</button>
    <a href="/lab3/subscribers" style="margin-left:10px;">Cancel</a>
  </form>
</body>
</html>
