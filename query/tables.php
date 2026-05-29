<?php
session_start();

$old_db = $_SESSION['old_db'] ?? ['host' => '', 'username' => '', 'password' => '', 'database' => ''];
$new_db = $_SESSION['new_db'] ?? ['host' => '', 'username' => '', 'password' => '', 'database' => ''];

// Now use $old_db and $new_db to connect...
$old_db_connection = new mysqli($old_db['host'], $old_db['username'], $old_db['password'], $old_db['database']);
if ($old_db_connection->connect_error) {
    die("Connection failed to old database: " . $old_db_connection->connect_error);
}

$new_db_connection = new mysqli($new_db['host'], $new_db['username'], $new_db['password'], $new_db['database']);
if ($new_db_connection->connect_error) {
    die("Connection failed to new database: " . $new_db_connection->connect_error);
}

// Fetch tables
$old_tables = [];
$new_tables = [];

$old_result = $old_db_connection->query("SHOW TABLES");
if ($old_result) {
    while ($row = $old_result->fetch_row()) {
        $old_tables[] = $row[0];
    }
} else {
    die("Error fetching tables from old database: " . $old_db_connection->error);
}

$new_result = $new_db_connection->query("SHOW TABLES");
if ($new_result) {
    while ($row = $new_result->fetch_row()) {
        $new_tables[] = $row[0];
    }
} else {
    die("Error fetching tables from new database: " . $new_db_connection->error);
}

// Differences
$missing_in_old_db = array_diff($new_tables, $old_tables);
$missing_in_new_db = array_diff($old_tables, $new_tables);

// Get CREATE TABLE statements
$create_queries = [];
$combined_sql = '';

foreach ($missing_in_old_db as $table) {
    $res = $new_db_connection->query("SHOW CREATE TABLE `$table`");
    if ($res && $row = $res->fetch_assoc()) {
        $create_queries[$table] = $row['Create Table'] . ';';
        $combined_sql .= $row['Create Table'] . ";\n\n";
    }
}

foreach ($missing_in_new_db as $table) {
    $res = $old_db_connection->query("SHOW CREATE TABLE `$table`");
    if ($res && $row = $res->fetch_assoc()) {
        $create_queries[$table] = $row['Create Table'] . ';';
        $combined_sql .= $row['Create Table'] . ";\n\n";
    }
}

$old_db_connection->close();
$new_db_connection->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <title>Compare Database Tables</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        pre {
            background: #f8f9fa;
            padding: 1em;
            border: 1px solid #ccc;
            border-radius: 6px;
            overflow-x: auto;
        }
        .table-name {
            cursor: pointer;
            color: #0d6efd;
            text-decoration: underline;
        }
    </style>
</head>
<body>
            <div class="btn btn-primary" style="width:100%" onclick="window.location.href='config.php'">
        Home
    </div>
<div class="container mt-5">
    <h3>Comparing Tables Between:</h3>
    <p>
        <strong>Old Database:</strong> <?= htmlspecialchars($old_db['database']) ?><br>
        <strong>New Database:</strong> <?= htmlspecialchars($new_db['database']) ?>
    </p>

    <div class="mb-3">
        <strong>Summary:</strong><br>
        ✅ Total Tables in Old DB: <?= count($old_tables) ?><br>
        ✅ Total Tables in New DB: <?= count($new_tables) ?><br>
        ❌ Tables in New DB but Missing in Old DB: <?= count($missing_in_old_db) ?><br>
        ❌ Tables in Old DB but Missing in New DB: <?= count($missing_in_new_db) ?>
    </div>

    <button class="btn btn-success mb-4" onclick="copyAllSQL()">📋 Copy All SQL</button>
    <textarea id="all_sql" style="display:none;"><?= htmlspecialchars($combined_sql) ?></textarea>

    <!-- New DB tables missing in old DB -->
    <div class="alert alert-info">
        <strong>Tables in new database but missing in old database:</strong>
        <ul>
            <?php foreach ($missing_in_old_db as $table): ?>
                <li style="text-decoration:none">
                    <span class="table-name" onclick="toggleQuery('query_<?= $table ?>')"><?= htmlspecialchars($table) ?></span>
                    <div id="query_<?= $table ?>" style="display:none;" class="mt-2">
                        <pre id="code_<?= $table ?>"><?= htmlspecialchars($create_queries[$table]) ?></pre>
                        <button class="btn btn-sm btn-secondary" onclick="copyToClipboard('code_<?= $table ?>')">📋 Copy SQL</button>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <!-- Old DB tables missing in new DB -->
    <div class="alert alert-warning">
        <strong>Tables in old database but missing in new database:</strong>
        <ul>
            <?php foreach ($missing_in_new_db as $table): ?>
                <li>
                    <span class="table-name" onclick="toggleQuery('query_<?= $table ?>')"><?= htmlspecialchars($table) ?></span>
                    <div id="query_<?= $table ?>" style="display:none;" class="mt-2">
                        <pre id="code_<?= $table ?>"><?= htmlspecialchars($create_queries[$table]) ?></pre>
                        <button class="btn btn-sm btn-secondary" onclick="copyToClipboard('code_<?= $table ?>')">📋 Copy SQL</button>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<script>
    function toggleQuery(id) {
        const el = document.getElementById(id);
        el.style.display = (el.style.display === "none") ? "block" : "none";
    }

    function copyToClipboard(id) {
        const text = document.getElementById(id).innerText;
        navigator.clipboard.writeText(text).then(() => {
            alert("SQL copied to clipboard!");
        }).catch(err => {
            alert("Failed to copy: " + err);
        });
    }

    function copyAllSQL() {
        const text = document.getElementById('all_sql').value;
        navigator.clipboard.writeText(text).then(() => {
            alert("All SQL queries copied!");
        }).catch(err => {
            alert("Copy failed: " + err);
        });
    }
</script>
</body>
</html>
