<?php
session_start();

// Load DB configs
$old_db = $_SESSION['old_db'] ?? null;
$new_db = $_SESSION['new_db'] ?? null;

if (!$old_db || !$new_db) {
    die("❌ Please configure databases first in config.php");
}

// Connect old DB
$old_conn = new mysqli($old_db['host'], $old_db['username'], $old_db['password'], $old_db['database']);
if ($old_conn->connect_error) {
    die("Connection failed to old database: " . $old_conn->connect_error);
}

// Connect new DB
$new_conn = new mysqli($new_db['host'], $new_db['username'], $new_db['password'], $new_db['database']);
if ($new_conn->connect_error) {
    die("Connection failed to new database: " . $new_conn->connect_error);
}

// Fetch indexes
function getIndexes($conn, $dbName) {
    $indexes = [];
    $res = $conn->query("SELECT TABLE_NAME, INDEX_NAME, NON_UNIQUE, SEQ_IN_INDEX, COLUMN_NAME, INDEX_TYPE
                         FROM INFORMATION_SCHEMA.STATISTICS
                         WHERE TABLE_SCHEMA = '{$dbName}'
                         ORDER BY TABLE_NAME, INDEX_NAME, SEQ_IN_INDEX");
    while ($row = $res->fetch_assoc()) {
        $indexes[$row['TABLE_NAME']][$row['INDEX_NAME']]['type'] = $row['INDEX_TYPE'];
        $indexes[$row['TABLE_NAME']][$row['INDEX_NAME']]['unique'] = ($row['NON_UNIQUE'] == 0);
        $indexes[$row['TABLE_NAME']][$row['INDEX_NAME']]['columns'][] = $row['COLUMN_NAME'];
    }
    return $indexes;
}

$old_indexes = getIndexes($old_conn, $old_db['database']);
$new_indexes = getIndexes($new_conn, $new_db['database']);

$results = [];
$summary = ["missing" => 0, "extra" => 0, "mismatch" => 0, "matched" => 0];
$all_tables = array_unique(array_merge(array_keys($old_indexes), array_keys($new_indexes)));

foreach ($all_tables as $table) {
    $old_tbl = $old_indexes[$table] ?? [];
    $new_tbl = $new_indexes[$table] ?? [];
    $all_indexes = array_unique(array_merge(array_keys($old_tbl), array_keys($new_tbl)));

    foreach ($all_indexes as $index) {
        $old_def = $old_tbl[$index] ?? null;
        $new_def = $new_tbl[$index] ?? null;

        if ($old_def && !$new_def) {
            $results[$table][] = [
                'index' => $index,
                'status' => 'Missing in New DB',
                'details' => $old_def,
                'sql' => "CREATE " . ($old_def['unique'] ? "UNIQUE " : "") . 
                         ($old_def['type'] === "FULLTEXT" ? "FULLTEXT " : "") .
                         "INDEX `$index` ON `$table` (" . implode(", ", array_map(fn($c) => "`$c`", $old_def['columns'])) . ");"
            ];
            $summary['missing']++;
        } elseif (!$old_def && $new_def) {
            $results[$table][] = [
                'index' => $index,
                'status' => 'Extra in New DB',
                'details' => $new_def,
                'sql' => "DROP INDEX `$index` ON `$table`;"
            ];
            $summary['extra']++;
        } elseif ($old_def && $new_def) {
            if ($old_def['type'] !== $new_def['type'] || 
                $old_def['unique'] !== $new_def['unique'] ||
                $old_def['columns'] !== $new_def['columns']) {
                $results[$table][] = [
                    'index' => $index,
                    'status' => 'Mismatch',
                    'details_old' => $old_def,
                    'details_new' => $new_def,
                    'sql' => "ALTER TABLE `$table` DROP INDEX `$index`, 
                              ADD " . ($old_def['unique'] ? "UNIQUE " : "") . 
                              ($old_def['type'] === "FULLTEXT" ? "FULLTEXT " : "") .
                              "INDEX `$index` (" . implode(", ", array_map(fn($c) => "`$c`", $old_def['columns'])) . ");"
                ];
                $summary['mismatch']++;
            } else {
                $results[$table][] = [
                    'index' => $index,
                    'status' => 'Matched',
                    'details' => $old_def
                ];
                $summary['matched']++;
            }
        }
    }
}

$old_conn->close();
$new_conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Indexes Comparison</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .badge-missing { background-color: #dc3545; }
        .badge-extra { background-color: #fd7e14; }
        .badge-mismatch { background-color: #0d6efd; }
        .badge-matched { background-color: #198754; }
        pre { background: #f8f9fa; padding: 0.75em; border-radius: 6px; }
    </style>
    <script>
        function filterResults(status) {
            document.querySelectorAll(".index-row").forEach(row => {
                if (status === "all" || row.dataset.status === status) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            });
        }
        function copySQL(id) {
            const text = document.getElementById(id).innerText;
            navigator.clipboard.writeText(text).then(() => {
                alert("SQL copied to clipboard!");
            });
        }
    </script>
</head>
<body class="bg-light">
<div class="container mt-4">

    <div class="mb-3 d-flex justify-content-between">
        <button class="btn btn-secondary" onclick="window.location.href='config.php'">🔙 Back to Home</button>
        <div>
            <select class="form-select" onchange="filterResults(this.value)">
                <option value="all">Show All</option>
                <option value="Missing in New DB">Missing in New DB</option>
                <option value="Extra in New DB">Extra in New DB</option>
                <option value="Mismatch">Mismatch</option>
                <option value="Matched">Matched</option>
            </select>
        </div>
    </div>

    <h3>📊 Indexes Comparison</h3>
    <p class="text-muted">Comparing Unique, Composite, Fulltext, and Normal indexes between <b><?= htmlspecialchars($old_db['database']) ?></b> and <b><?= htmlspecialchars($new_db['database']) ?></b>.</p>

    <!-- Summary -->
    <div class="alert alert-info">
        <strong>Summary:</strong><br>
        ✅ Matched: <?= $summary['matched'] ?><br>
        ❌ Missing in New DB: <?= $summary['missing'] ?><br>
        ⚠️ Extra in New DB: <?= $summary['extra'] ?><br>
        🔄 Mismatched: <?= $summary['mismatch'] ?>
    </div>

    <?php if (empty($results)): ?>
        <div class="alert alert-success">All indexes match perfectly 🎉</div>
    <?php else: ?>
        <?php foreach ($results as $table => $indexes): ?>
            <div class="card mb-3 shadow-sm">
                <div class="card-header bg-primary text-white">
                    Table: <?= htmlspecialchars($table) ?>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Index Name</th>
                                <th>Status</th>
                                <th>Columns</th>
                                <th>Type</th>
                                <th>Unique</th>
                                <th>Action (SQL Preview)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($indexes as $ix): ?>
                                <tr class="index-row" data-status="<?= htmlspecialchars($ix['status']) ?>">
                                    <td><?= htmlspecialchars($ix['index']) ?></td>
                                    <td>
                                        <span class="badge 
                                            <?= $ix['status']==='Missing in New DB' ? 'badge-missing' : 
                                                ($ix['status']==='Extra in New DB' ? 'badge-extra' : 
                                                ($ix['status']==='Mismatch' ? 'badge-mismatch' : 'badge-matched')) ?>">
                                            <?= htmlspecialchars($ix['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($ix['status']==='Mismatch'): ?>
                                            Old: <?= implode(", ", $ix['details_old']['columns']) ?><br>
                                            New: <?= implode(", ", $ix['details_new']['columns']) ?>
                                        <?php else: ?>
                                            <?= implode(", ", $ix['details']['columns']) ?>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $ix['status']==='Mismatch' ? "Old: {$ix['details_old']['type']} / New: {$ix['details_new']['type']}" : $ix['details']['type'] ?></td>
                                    <td><?= $ix['status']==='Mismatch' ? "Old: ".($ix['details_old']['unique']?'Yes':'No')." / New: ".($ix['details_new']['unique']?'Yes':'No') : ($ix['details']['unique']?'Yes':'No') ?></td>
                                    <td>
                                        <?php if ($ix['status']!=='Matched'): ?>
                                            <pre id="sql_<?= $table ?>_<?= $ix['index'] ?>"><?= htmlspecialchars($ix['sql']) ?></pre>
                                            <button class="btn btn-sm btn-outline-secondary" onclick="copySQL('sql_<?= $table ?>_<?= $ix['index'] ?>')">📋 Copy</button>
                                        <?php else: ?>
                                            ✅ No action needed
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

</div>
</body>
</html>
