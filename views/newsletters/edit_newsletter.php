<?php
require_once __DIR__ . '/../../crud/newsletter.php';

$id = (int)($_GET['id'] ?? 0);
$newsletter = get_newsletter($id);
if (!$newsletter) {
    die('Newsletter not found.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = $_POST['subject'] ?? '';
    $content = $_POST['content'] ?? '';
    if (update_newsletter($id, $subject, $content)) {
        header('Location: /lab3/newsletters');
        exit;
    } else {
        $error = 'Cannot update newsletter';
    }
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="/lab3/css/style.css">
  <title>Edit Newsletter</title>
</head>
<body>
  <h2>Edit Newsletter</h2>
  <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
  <form method="POST">
    <div>
      <label>Subject</label><br>
      <input type="text" name="subject" value="<?= htmlspecialchars($newsletter['subject']) ?>" required>
    </div>
    <div>
      <label>Content</label><br>
      <textarea name="content" rows="6" required><?= htmlspecialchars($newsletter['content']) ?></textarea>
    </div>
    <button type="submit">Save</button>
  </form>
  <a href="/lab3/newsletters">Back to List</a>
</body>
</html>
