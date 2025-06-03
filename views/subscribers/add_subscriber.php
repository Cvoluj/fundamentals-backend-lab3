<?php
require_once __DIR__ . '/../../crud/subscriber.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $address  = trim($_POST['address'] ?? '');
    $password = $_POST['password'] ?? '';

    if (create_subscriber($name, $email, $address, $password)) {
        header('Location: /lab3/subscribers');
        exit;
    } else {
        $error = 'Doesn\'t made subscribtion.';
    }
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="/lab3/css/style.css">
  <title>Add Subscriber</title>
</head>
<body>
  <h2>Add Subscriber</h2>

  <?php if (!empty($error)): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <form method="POST">
    <div>
      <label for="name">Name</label><br>
      <input type="text" id="name" name="name" required>
    </div>
    <div>
      <label for="email">Email</label><br>
      <input type="email" id="email" name="email" required>
    </div>
    <div>
      <label for="address">Address</label><br>
      <input type="text" id="address" name="address" required>
    </div>
    <div>
      <label for="password">Password</label><br>
      <input type="password" id="password" name="password" required>
    </div>
    <button type="submit">Submit</button>
  </form>

  <p><a href="/lab3/subscribers">← Back to Subscribers List</a></p>
</body>
</html>
