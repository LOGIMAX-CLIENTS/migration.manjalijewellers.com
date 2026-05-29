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
function getTableColumns($conn, $dbName, $table)
{
    $sql = "
        SELECT COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, COLUMN_DEFAULT, EXTRA, COLUMN_COMMENT
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = '$dbName' AND TABLE_NAME = '$table'
    ";

    $result = $conn->query($sql);
    $columns = [];
    while ($row = $result->fetch_assoc()) {
        $columns[$row['COLUMN_NAME']] = $row;
    }
    return $columns;
}



$alterTableQueries = [];
$oldTables = [];
$res = $old_db_connection->query("SHOW TABLES");
while ($row = $res->fetch_array()) {
    $oldTables[] = $row[0];
}

foreach ($oldTables as $table) {
    $res = $new_db_connection->query("SHOW TABLES LIKE '$table'");
    if ($res->num_rows === 0) {
        continue;
    }

    $oldCols = getTableColumns($old_db_connection, $old_db['database'], $table);
    $newCols = getTableColumns($new_db_connection, $new_db['database'], $table);

    foreach ($oldCols as $col => $meta) {
        if (!isset($newCols[$col])) {
            $line = "ALTER TABLE `$table` ADD COLUMN `$col` {$meta['COLUMN_TYPE']}";
            $line .= ($meta['IS_NULLABLE'] === 'NO') ? " NOT NULL" : " NULL";

            if (!is_null($meta['COLUMN_DEFAULT'])) {
                $default = addslashes($meta['COLUMN_DEFAULT']);
                $line .= " DEFAULT '{$default}'";
            }

            if (!empty($meta['EXTRA'])) {
                $line .= " " . strtoupper($meta['EXTRA']);
            }

            if (!empty($meta['COLUMN_COMMENT'])) {
                $comment = addslashes($meta['COLUMN_COMMENT']);
                $line .= " COMMENT '{$comment}'";
            }

            $line .= ";";
            $alterTableQueries[$table][] = $line;
        }
    }
}

$old_db_connection->close();
$new_db_connection->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ALTER TABLE Generator</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .code-block {
            background-color: #f8f9fa;
            padding: 1rem;
            border-radius: 0.5rem;
            position: relative;
            font-family: monospace;
        }

        .copy-btn {
            position: absolute;
            top: 10px;
            right: 10px;
        }

        .table-link {
            cursor: pointer;
            font-weight: bold;
        }

        .table-link:hover {
            color: #0d6efd;
        }

        .query-wrapper {
            margin-top: 10px;
            display: none;
        }

        #all-queries-block {
            display: none;
        }
    </style>
</head>
<body class="bg-light">
        <div class="btn btn-primary" style="width:100%" onclick="window.location.href='config.php'">
        Home
    </div>
<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Missing Columns - ALTER TABLE Generator</h4>
        </div>
        <div class="card-body">
            <?php if (count($alterTableQueries) > 0): ?>
                <?php
                    $totalMissingColumns = 0;
                    foreach ($alterTableQueries as $queries) {
                        $totalMissingColumns += count($queries);
                    }
                ?>
                <div class="alert alert-info">
                    🧾 Total Missing Columns: <strong><?= $totalMissingColumns ?></strong> across <?= count($alterTableQueries) ?> tables.
                </div>
                <button class="btn btn-success mb-3" onclick="showAllAndCopy()">📋 Copy All Queries</button>
            <?php endif; ?>

            <?php if (count($alterTableQueries) === 0): ?>
                <div class="alert alert-success">✅ No missing columns. New DB is in sync.</div>
            <?php else: ?>
                <p class="mb-3">📋 Click a table name to show its missing column queries:</p>
                <ul class="list-group">
                    <?php foreach ($alterTableQueries as $table => $queries): ?>
                        <li class="list-group-item">
                            <div class="table-link" onclick="toggleQueries('<?= $table ?>')">
                                🔽 <?= $table ?> (<?= count($queries) ?> missing columns)
                            </div>
                            <div class="query-wrapper" id="queries-<?= $table ?>">
                                <?php foreach ($queries as $i => $query): ?>
                                    <div class="mb-3 position-relative">
                                        <pre class="code-block" id="code-<?= $table . '-' . $i ?>"><?= htmlspecialchars($query) ?></pre>
                                        <button class="btn btn-sm btn-outline-secondary copy-btn" onclick="copyToClipboard('code-<?= $table . '-' . $i ?>')">📋 Copy</button>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <div class="mt-4" id="all-queries-block">
                    <h5>📦 All Missing Column Queries</h5>
                    <pre class="code-block" id="all-queries-content"></pre>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function toggleQueries(tableId) {
    const el = document.getElementById('queries-' + tableId);
    el.style.display = (el.style.display === 'none' || el.style.display === '') ? 'block' : 'none';
}

function copyToClipboard(elementId) {
    const el = document.getElementById(elementId);
    const temp = document.createElement("textarea");
    temp.value = el.textContent;
    document.body.appendChild(temp);
    temp.select();
    document.execCommand("copy");
    document.body.removeChild(temp);
    alert("Query copied to clipboard!");
}

function showAllAndCopy() {
    const queries = document.querySelectorAll('.code-block');
    let allText = '';
    queries.forEach(q => {
        allText += q.textContent.trim() + "\n";
    });

    const block = document.getElementById('all-queries-block');
    const content = document.getElementById('all-queries-content');
    content.textContent = allText.trim();
    block.style.display = 'block';

    const temp = document.createElement("textarea");
    temp.value = allText.trim();
    document.body.appendChild(temp);
    temp.select();
    document.execCommand("copy");
    document.body.removeChild(temp);
    alert("All queries copied to clipboard!");
}
</script>

</body>
</html>
