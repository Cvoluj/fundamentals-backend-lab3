<?php
require_once __DIR__ . '/../db.php';

$q = trim($_GET['q'] ?? '');
$strict = isset($_GET['strict']);
$pattern = trim($_GET['pattern'] ?? '');
$from = trim($_GET['from'] ?? '');
$to   = trim($_GET['to'] ?? '');

$conditions = [];
$params = [];
$types = '';

if ($q !== '') {
    if ($strict) {
        $conditions[] = 'subject = ?';
        $types .= 's';
        $params[] = $q;
    } else {
        $conditions[] = 'subject LIKE ?';
        $types .= 's';
        $params[] = '%' . $q . '%';
    }
}

if ($pattern !== '') {
    $conditions[] = 'content LIKE ?';
    $types .= 's';
    $params[] = $pattern;
}

if ($from !== '' && $to !== '') {
    $conditions[] = 'sent_at BETWEEN ? AND ?';
    $types .= 'ss';
    $params[] = $from . ' 00:00:00';
    $params[] = $to   . ' 23:59:59';
}

$where = '';
if (!empty($conditions)) {
    $where = ' WHERE ' . implode(' AND ', $conditions);
}

$sql = 'SELECT * FROM newsletters' . $where;
$stmt = $mysql_connection->prepare($sql);
if ($types !== '') {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$res = $stmt->get_result();
$results = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search Newsletters</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
    <h1 class="mb-4">Search Newsletters</h1>
    <form method="get" class="row g-3 mb-4">
        <div class="col-md-3">
            <label for="q" class="form-label">Keyword (Subject)</label>
            <input type="text" name="q" id="q" class="form-control" value="<?=htmlspecialchars($q)?>">
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" id="strict" name="strict" <?= $strict ? 'checked' : '' ?>>
                <label class="form-check-label" for="strict">Strict</label>
            </div>
        </div>
        <div class="col-md-3">
            <label for="pattern" class="form-label">Pattern (Content LIKE)</label>
            <input type="text" name="pattern" id="pattern" class="form-control" value="<?=htmlspecialchars($pattern)?>" placeholder="%example%">
        </div>
        <div class="col-md-2">
            <label for="from" class="form-label">From Date</label>
            <input type="date" name="from" id="from" class="form-control" value="<?=htmlspecialchars($from)?>">
        </div>
        <div class="col-md-2">
            <label for="to" class="form-label">To Date</label>
            <input type="date" name="to" id="to" class="form-control" value="<?=htmlspecialchars($to)?>">
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary">Search</button>
        </div>
    </form>

    <?php if (count($results) > 0): ?>
        <table class="table table-striped table-bordered">
            <thead class="table-light">
                <tr>
                    <?php foreach (array_keys($results[0]) as $colName): ?>
                        <th><?=htmlspecialchars($colName)?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $row): ?>
                    <tr>
                        <?php foreach ($row as $value): ?>
                            <td><?=htmlspecialchars((string)$value)?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php elseif ($q !== '' || $pattern !== '' || ($from !== '' && $to !== '')): ?>
        <div class="alert alert-warning">No records found.</div>
    <?php endif; ?>
    <p><a href="/lab3/">Back to Main</a></p>
</div>
</body>
</html>
