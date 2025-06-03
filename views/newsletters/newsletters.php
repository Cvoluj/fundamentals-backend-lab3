<?php
require_once __DIR__ . '/../../crud/newsletter.php';
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int) $_GET['id'];
    $item = get_newsletter($id);

    if (!$item) {
        header("HTTP/1.0 404 Not Found");
        echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Not Found</title></head><body>";
        echo "<h2>Newsletter #{$id} not found</h2>";
        echo "<p><a href='/lab3/newsletters'>Back to List</a></p>";
        echo "</body></html>";
        exit;
    }

    ?>
    <!DOCTYPE html>
    <html lang="uk">
    <head>
      <meta charset="UTF-8">
      <link rel="stylesheet" href="/lab3/css/style.css">
      <title>View Newsletter #<?= htmlspecialchars($item['id']) ?></title>
    </head>
    <body>
      <h2>Newsletter #<?= htmlspecialchars($item['id']) ?></h2>
      <p><strong>Subject:</strong> <?= htmlspecialchars($item['subject']) ?></p>
      <p><strong>Content:</strong><br>
        <?= nl2br(htmlspecialchars($item['content'])) ?>
      </p>
      <p><strong>Date Sent:</strong> <?= htmlspecialchars($item['sent_at']) ?></p>
      <p><a href="/lab3/newsletters">← Back to List</a></p>
    </body>
    </html>
    <?php
    exit;
}

$allowed   = ['subject', 'sent_at'];
$sort_by   = in_array($_GET['sort_by'] ?? '', $allowed) ? $_GET['sort_by'] : 'id';
$order_in  = strtolower($_GET['order'] ?? 'asc');
$order     = $order_in === 'desc' ? 'DESC' : 'ASC';

$toggle = $order === 'ASC' ? 'desc' : 'asc';

$sql  = "SELECT id, subject, content, sent_at 
         FROM newsletters 
         ORDER BY {$sort_by} {$order}";
$all  = fetch_all_newsletters($sql);
?>

<!DOCTYPE html>
<html lang="uk">
<head>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="/lab3/css/style.css">
  <title>List of Newsletters</title>
</head>
<body>
  <h2>List of Newsletters</h2>

  <div style="margin-bottom:20px;">
    <a href="/lab3/newsletters/add">Add Newsletter</a> |
    <a href="/lab3/newsletters?sort_by=subject&order=<?= $toggle ?>">Sort by Subject <?= $order === 'ASC' ? '↑' : '↓' ?></a> |
    <a href="/lab3/newsletters?sort_by=sent_at&order=<?= $toggle ?>">Sort by Date <?= $order === 'ASC' ? '↑' : '↓' ?></a>
  </div>

  <table border="1" cellpadding="5" cellspacing="0">
    <thead>
      <tr>
        <th><a href="/lab3/newsletters?sort_by=id&order=<?= $toggle ?>">ID</a></th>
        <th>Subject</th>
        <th>Content</th>
        <th>Date</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($all as $row): ?>
      <tr>
        <td><?= htmlspecialchars($row['id']) ?></td>
        <td><?= htmlspecialchars($row['subject']) ?></td>
        <td><?= nl2br(htmlspecialchars($row['content'])) ?></td>
        <td><?= htmlspecialchars($row['sent_at']) ?></td>
        <td>
          <a href="/lab3/newsletters/edit/<?= urlencode($row['id']) ?>">Edit</a> |
          <a href="/lab3/newsletters/delete/<?= urlencode($row['id']) ?>"
             onclick="return confirm('Delete Newsletter?')">Delete</a>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>

  <p><a href="/lab3/">Back to Main</a></p>
</body>
</html>
