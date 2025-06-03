<?php
require_once __DIR__ . '/../db.php';
$result = $mysql_connection->query("SELECT COUNT(*) AS cnt FROM newsletters");
$row = $result->fetch_assoc();
$cntNewsletters = $row['cnt'] ?? 0;
$result = $mysql_connection->query("SELECT COUNT(*) AS cnt FROM subscribers");
$row = $result->fetch_assoc();
$cntSubscribers = $row['cnt'] ?? 0;
$result = $mysql_connection->query("SELECT COUNT(*) AS cnt FROM newsletters WHERE sent_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)");
$row = $result->fetch_assoc();
$cntNewslettersLastMonth = $row['cnt'] ?? 0;
$result = $mysql_connection->query("SELECT COUNT(*) AS cnt FROM subscribers WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)");
$row = $result->fetch_assoc();
$cntSubscribersLastMonth = $row['cnt'] ?? 0;
$result = $mysql_connection->query("SELECT * FROM newsletters ORDER BY sent_at DESC LIMIT 1");
$lastNewsletter = $result->fetch_assoc();
$result = $mysql_connection->query("
    SELECT n.*, COUNT(s.subscriber_id) AS related_count
      FROM newsletters AS n
 LEFT JOIN subscriptions AS s
        ON s.newsletter_id = n.id
  GROUP BY n.id
  ORDER BY related_count DESC
  LIMIT 1
");
$mostSubscribed = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Statistics</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
    <h1 class="mb-4">Site Statistics</h1>
    <div class="row mb-3">
        <div class="col-md-6">
            <div class="card border-primary">
                <div class="card-body">
                    <h5 class="card-title">Total Records</h5>
                    <p class="card-text">In <code>newsletters</code>: <strong><?=htmlspecialchars($cntNewsletters)?></strong></p>
                    <p class="card-text">In <code>subscribers</code>: <strong><?=htmlspecialchars($cntSubscribers)?></strong></p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-success">
                <div class="card-body">
                    <h5 class="card-title">Last 30 Days</h5>
                    <p class="card-text">In <code>newsletters</code>: <strong><?=htmlspecialchars($cntNewslettersLastMonth)?></strong></p>
                    <p class="card-text">In <code>subscribers</code>: <strong><?=htmlspecialchars($cntSubscribersLastMonth)?></strong></p>
                </div>
            </div>
        </div>
    </div>
    <?php if ($lastNewsletter): ?>
        <div class="row mb-3">
            <div class="col-12">
                <div class="card border-info">
                    <div class="card-body">
                        <h5 class="card-title">Last Newsletter Entry</h5>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($lastNewsletter as $field => $value): ?>
                                <li class="list-group-item">
                                    <strong><?=htmlspecialchars($field)?>:</strong>
                                    <?=htmlspecialchars((string)$value)?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <?php if ($mostSubscribed): ?>
        <div class="row">
            <div class="col-12">
                <div class="card border-warning">
                    <div class="card-body">
                        <h5 class="card-title">Newsletter with Most Subscribers</h5>
                        <p>Subscriber Count: <strong><?=htmlspecialchars($mostSubscribed['related_count'])?></strong></p>
                        <ul class="list-group list-group-flush">
                            <?php unset($mostSubscribed['related_count']); ?>
                            <?php foreach ($mostSubscribed as $field => $value): ?>
                                <li class="list-group-item">
                                    <strong><?=htmlspecialchars($field)?>:</strong>
                                    <?=htmlspecialchars((string)$value)?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
