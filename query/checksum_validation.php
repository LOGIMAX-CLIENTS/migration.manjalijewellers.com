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
if ($old_conn->connect_error) die("Old DB connection failed: " . $old_conn->connect_error);

// Connect new DB
$new_conn = new mysqli($new_db['host'], $new_db['username'], $new_db['password'], $new_db['database']);
if ($new_conn->connect_error) die("New DB connection failed: " . $new_conn->connect_error);

// Get common tables
$old_tables = [];
$new_tables = [];

$res = $old_conn->query("SHOW TABLES");
while ($row = $res->fetch_row()) $old_tables[] = $row[0];
$res = $new_conn->query("SHOW TABLES");
while ($row = $res->fetch_row()) $new_tables[] = $row[0];

$common_tables = array_intersect($old_tables, $new_tables);

// Function: table checksum
function getTableChecksum($conn, $table) {
    $checksum = null;
    $res = $conn->query("CHECKSUM TABLE `$table`");
    if ($res && $row = $res->fetch_assoc()) {
        $checksum = $row['Checksum'];
    }
    return $checksum;
}

// Function: row-level checksum comparison
function getRowChecksums($conn, $table) {
    $rows = [];
    $res = $conn->query("SHOW COLUMNS FROM `$table`");
    $cols = [];
    while ($col = $res->fetch_assoc()) $cols[] = "`".$col['Field']."`";

    if (!$cols) return [];

    $concatCols = "CONCAT_WS('|', " . implode(",", $cols) . ")";
    $query = "SELECT MD5($concatCols) as row_hash, $concatCols as raw_data FROM `$table`";
    $res = $conn->query($query);

    while ($row = $res->fetch_assoc()) {
        $rows[] = $row;
    }
    return $rows;
}

// Run checksum validation
$results = [];
foreach ($common_tables as $table) {
    $old_sum = getTableChecksum($old_conn, $table);
    $new_sum = getTableChecksum($new_conn, $table);

    $mismatch = ($old_sum !== $new_sum);

    $row_diffs = [];
    if ($mismatch) {
        // Deep check row hashes
        $old_rows = getRowChecksums($old_conn, $table);
        $new_rows = getRowChecksums($new_conn, $table);

        $old_hashes = array_column($old_rows, null, "row_hash");
        $new_hashes = array_column($new_rows, null, "row_hash");

        // Missing in new
        foreach ($old_hashes as $hash => $data) {
            if (!isset($new_hashes[$hash])) {
                $row_diffs[] = ["status"=>"Missing in NEW DB", "data"=>$data['raw_data']];
            }
        }
        // Missing in old
        foreach ($new_hashes as $hash => $data) {
            if (!isset($old_hashes[$hash])) {
                $row_diffs[] = ["status"=>"Extra in NEW DB", "data"=>$data['raw_data']];
            }
        }
    }

    $results[$table] = [
        "old_checksum" => $old_sum,
        "new_checksum" => $new_sum,
        "match" => !$mismatch,
        "row_diffs" => $row_diffs
    ];
}

$old_conn->close();
$new_conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checksum Validation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        pre { background: #f1f1f1; padding: .5em; border-radius: 6px; }
        .match { color: green; }
        .mismatch { color: red; }
    </style>
</head>
<body>
<div class="container mt-4">
    <div class="mb-3">
        <button class="btn btn-secondary" onclick="window.location.href='config.php'">🔙 Back to Home</button>
    </div>

    <h3>🔑 Checksum Validation (Data Integrity)</h3>
    <?php if (empty($common_tables)): ?>
        <div class="alert alert-warning">No common tables between databases.</div>
    <?php else: ?>
        <?php foreach ($results as $table => $info): ?>
            <div class="card mb-3 shadow-sm">
                <div class="card-header <?= $info['match'] ? 'bg-success' : 'bg-danger' ?> text-white">
                    Table: <?= htmlspecialchars($table) ?>
                </div>
                <div class="card-body">
                    <p>
                        Old Checksum: <b><?= $info['old_checksum'] ?></b><br>
                        New Checksum: <b><?= $info['new_checksum'] ?></b><br>
                        Status: <?= $info['match'] ? '<span class="match">✔ Match</span>' : '<span class="mismatch">❌ Mismatch</span>' ?>
                    </p>

                    <?php if (!$info['match']): ?>
                        <h6>Row Differences:</h6>
                        <?php if (empty($info['row_diffs'])): ?>
                            <p>No row-level differences detected, but checksums differ (possible index/storage difference).</p>
                        <?php else: ?>
                            <pre><?= htmlspecialchars(json_encode($info['row_diffs'], JSON_PRETTY_PRINT)) ?></pre>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
</body>
</html>
