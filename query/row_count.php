<?php
session_start();

// Load DB configs from session
$old_db = $_SESSION['old_db'] ?? null;
$new_db = $_SESSION['new_db'] ?? null;

if (!$old_db || !$new_db) {
    die("❌ Please configure databases first in config.php");
}

// Connect to old DB
$old_conn = new mysqli($old_db['host'], $old_db['username'], $old_db['password'], $old_db['database']);
if ($old_conn->connect_error) {
    die("Connection failed to old database: " . $old_conn->connect_error);
}

// Connect to new DB
$new_conn = new mysqli($new_db['host'], $new_db['username'], $new_db['password'], $new_db['database']);
if ($new_conn->connect_error) {
    die("Connection failed to new database: " . $new_conn->connect_error);
}

// Fetch table list
$old_tables = [];
$new_tables = [];

$res = $old_conn->query("SHOW TABLES");
while ($row = $res->fetch_row()) {
    $old_tables[] = $row[0];
}
$res = $new_conn->query("SHOW TABLES");
while ($row = $res->fetch_row()) {
    $new_tables[] = $row[0];
}

// Compare only tables that exist in BOTH DBs
$common_tables = array_intersect($old_tables, $new_tables);

// Prepare results
$results = [];

foreach ($common_tables as $table) {
    $old_count = 0;
    $new_count = 0;

    $res_old = $old_conn->query("SELECT COUNT(*) FROM `$table`");
    if ($res_old) {
        $old_count = $res_old->fetch_row()[0];
    }

    $res_new = $new_conn->query("SELECT COUNT(*) FROM `$table`");
    if ($res_new) {
        $new_count = $res_new->fetch_row()[0];
    }

    $results[] = [
        'table' => $table,
        'old_count' => $old_count,
        'new_count' => $new_count,
        'status' => ($old_count == $new_count) ? 'Match' : 'Mismatch'
    ];
}

$old_conn->close();
$new_conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Row Count Comparison</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .table-success { background-color: #d4edda !important; }
        .table-danger { background-color: #f8d7da !important; }
        .filter-btn { margin-right: 10px; }
    </style>
</head>
<body class="bg-light">
<div class="container mt-4">

    <div class="mb-3">
        <button class="btn btn-secondary" onclick="window.location.href='config.php'">🔙 Back to Home</button>
    </div>

    <h3>Row Count Comparison</h3>
    <p>
        <strong>Old DB:</strong> <?= htmlspecialchars($old_db['database']) ?><br>
        <strong>New DB:</strong> <?= htmlspecialchars($new_db['database']) ?>
    </p>

    <?php if (empty($common_tables)): ?>
        <div class="alert alert-warning">No records to compare.</div>
    <?php else: ?>
        <!-- Filter Buttons -->
        <div class="mb-3">
            <button class="btn btn-outline-primary filter-btn" onclick="filterRows('all')">All</button>
            <button class="btn btn-outline-success filter-btn" onclick="filterRows('Match')">Matched</button>
            <button class="btn btn-outline-danger filter-btn" onclick="filterRows('Mismatch')">Mismatched</button>
        </div>

        <table class="table table-bordered table-hover bg-white shadow-sm" id="comparisonTable">
            <thead class="table-dark">
                <tr>
                    <th>Table</th>
                    <th>Old DB Count</th>
                    <th>New DB Count</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $row): ?>
                    <tr class="<?= $row['status'] == 'Match' ? 'table-success' : 'table-danger' ?>" data-status="<?= $row['status'] ?>">
                        <td><?= htmlspecialchars($row['table']) ?></td>
                        <td><?= $row['old_count'] ?></td>
                        <td><?= $row['new_count'] ?></td>
                        <td><?= $row['status'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

</div>

<script>
function filterRows(status) {
    const rows = document.querySelectorAll('#comparisonTable tbody tr');
    rows.forEach(row => {
        if (status === 'all') {
            row.style.display = '';
        } else if (row.dataset.status !== status) {
            row.style.display = 'none';
        } else {
            row.style.display = '';
        }
    });
}
</script>
</body>
</html>
