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

// Fetch common tables
$old_tables = [];
$new_tables = [];

$res = $old_conn->query("SHOW TABLES");
while ($row = $res->fetch_row()) $old_tables[] = $row[0];

$res = $new_conn->query("SHOW TABLES");
while ($row = $res->fetch_row()) $new_tables[] = $row[0];

$common_tables = array_intersect($old_tables, $new_tables);

// Function to detect duplicates
function findDuplicates($conn, $table) {
    $duplicates = [];
    $res = $conn->query("SHOW KEYS FROM `$table` WHERE Key_name = 'PRIMARY'");
    $pk_cols = [];
    while ($row = $res->fetch_assoc()) $pk_cols[] = $row['Column_name'];
    if (!$pk_cols) return $duplicates; // Skip if no primary key
    $pk_list = implode(", ", array_map(fn($c) => "`$c`", $pk_cols));
    $query = "SELECT $pk_list, COUNT(*) as cnt 
              FROM `$table` 
              GROUP BY $pk_list 
              HAVING cnt > 1";
    $res = $conn->query($query);
    if ($res) {
        while ($row = $res->fetch_assoc()) $duplicates[] = $row;
    }
    return $duplicates;
}

// Check duplicates in old and new DB
$results = [];
foreach ($common_tables as $table) {
    $old_dups = findDuplicates($old_conn, $table);
    $new_dups = findDuplicates($new_conn, $table);
    $results[$table] = [
        'old_count' => count($old_dups),
        'new_count' => count($new_dups),
        'old_duplicates' => $old_dups,
        'new_duplicates' => $new_dups
    ];
}

$old_conn->close();
$new_conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Duplicate Data Check</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    body { background: #f8f9fa; }
    .filter-btn { margin-right: 5px; }
    .has-dups { color: red; font-weight: bold; }
    .no-dups { color: green; font-weight: bold; }
    pre { background: #f1f1f1; padding: 0.8em; border-radius: 6px; overflow-x: auto; }
</style>
</head>
<body>
<div class="container mt-4">

    <div class="d-flex justify-content-between mb-3">
        <h3>Duplicate Data Detection</h3>
        <button class="btn btn-secondary" onclick="window.location.href='config.php'">🔙 Back to Home</button>
    </div>

    <?php if (empty($common_tables)): ?>
        <div class="alert alert-warning">No common tables to check.</div>
    <?php else: ?>

    <div class="mb-3">
        <button class="btn btn-outline-primary filter-btn" onclick="filterTables('all')">All Tables</button>
        <button class="btn btn-outline-danger filter-btn" onclick="filterTables('duplicates')">Tables With Duplicates</button>
        <button class="btn btn-outline-success filter-btn" onclick="filterTables('clean')">Tables Without Duplicates</button>
    </div>

    <table class="table table-bordered table-hover bg-white">
        <thead class="table-dark">
            <tr>
                <th>Table Name</th>
                <th>Old DB Duplicates</th>
                <th>New DB Duplicates</th>
                <th>Status</th>
                <th>Details</th>
            </tr>
        </thead>
        <tbody id="table-body">
        <?php foreach ($results as $table => $data): 
            $status = ($data['old_count'] > 0 || $data['new_count'] > 0) ? 'duplicates' : 'clean';
        ?>
            <tr data-status="<?= $status ?>">
                <td><?= htmlspecialchars($table) ?></td>
                <td><?= $data['old_count'] ?></td>
                <td><?= $data['new_count'] ?></td>
                <td class="<?= $status == 'duplicates' ? 'has-dups' : 'no-dups' ?>">
                    <?= $status == 'duplicates' ? '⚠ Duplicates Found' : '✅ No Duplicates' ?>
                </td>
                <td>
                    <?php if ($status == 'duplicates'): ?>
                        <button class="btn btn-sm btn-info" type="button" data-bs-toggle="collapse" data-bs-target="#details_<?= $table ?>">View</button>
                        <div class="collapse mt-2" id="details_<?= $table ?>">
                            <strong>Old DB:</strong>
                            <pre><?= htmlspecialchars(json_encode($data['old_duplicates'], JSON_PRETTY_PRINT)) ?></pre>
                            <strong>New DB:</strong>
                            <pre><?= htmlspecialchars(json_encode($data['new_duplicates'], JSON_PRETTY_PRINT)) ?></pre>
                        </div>
                    <?php else: ?>
                        <span class="text-muted">No duplicates</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function filterTables(type) {
    const rows = document.querySelectorAll('#table-body tr');
    rows.forEach(row => {
        if (type === 'all') {
            row.style.display = '';
        } else if (type === 'duplicates' && row.dataset.status === 'duplicates') {
            row.style.display = '';
        } else if (type === 'clean' && row.dataset.status === 'clean') {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}
</script>
</body>
</html>
