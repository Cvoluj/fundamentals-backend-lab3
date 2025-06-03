<?php
require_once __DIR__ . '/../../crud/newsletter.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = $_POST['subject'] ?? '';
    $content = $_POST['content'] ?? '';
    if (create_newsletter($subject, $content)) {
        header('Location: /lab3/newsletters');
        exit;
    } else {
        $error = 'Cannot add newsletter';
    }
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
  <link rel="stylesheet" href="/lab3/css/style.css">
  <meta charset="UTF-8">
  <title>Add Newsletter</title>
</head>
<body>
  <h2>Add Newsletter</h2>
  <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
  <form method="POST">
    <div>
      <label>Subject</label><br>
      <input type="text" name="subject" required>
    </div>
    <div>
      <label>Content</label><br>
      <textarea name="content" rows="6" required></textarea>
    </div>
    <button type="submit">Submit</button>
  </form>
  <a href="/lab3/newsletters">Back to List</a>
</body>
</html>
