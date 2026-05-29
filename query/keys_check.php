<?php
session_start();

$old_db = $_SESSION['old_db'] ?? ['host' => '', 'username' => '', 'password' => '', 'database' => ''];
$new_db = $_SESSION['new_db'] ?? ['host' => '', 'username' => '', 'password' => '', 'database' => ''];

// Connect old
$old_conn = new mysqli($old_db['host'], $old_db['username'], $old_db['password'], $old_db['database']);
if ($old_conn->connect_error) {
    die("Connection failed to old DB: " . $old_conn->connect_error);
}

// Connect new
$new_conn = new mysqli($new_db['host'], $new_db['username'], $new_db['password'], $new_db['database']);
if ($new_conn->connect_error) {
    die("Connection failed to new DB: " . $new_conn->connect_error);
}

/**
 * Get primary + foreign keys
 */
function getKeys($conn, $dbName) {
    $keys = [
        'primary' => [],
        'foreign' => []
    ];

    // Primary Keys
    $pk_sql = "
        SELECT TABLE_NAME, COLUMN_NAME
        FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
        WHERE TABLE_SCHEMA = '{$dbName}' AND CONSTRAINT_NAME = 'PRIMARY'
        ORDER BY TABLE_NAME, ORDINAL_POSITION
    ";
    $res = $conn->query($pk_sql);
    while ($row = $res->fetch_assoc()) {
        $keys['primary'][$row['TABLE_NAME']][] = $row['COLUMN_NAME'];
    }

    // Foreign Keys
    $fk_sql = "
        SELECT kcu.TABLE_NAME, kcu.COLUMN_NAME, kcu.REFERENCED_TABLE_NAME, kcu.REFERENCED_COLUMN_NAME
        FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE kcu
        WHERE kcu.TABLE_SCHEMA = '{$dbName}' AND kcu.REFERENCED_TABLE_NAME IS NOT NULL
        ORDER BY kcu.TABLE_NAME
    ";
    $res = $conn->query($fk_sql);
    while ($row = $res->fetch_assoc()) {
        $keys['foreign'][$row['TABLE_NAME']][] = [
            'column' => $row['COLUMN_NAME'],
            'ref_table' => $row['REFERENCED_TABLE_NAME'],
            'ref_column' => $row['REFERENCED_COLUMN_NAME']
        ];
    }

    return $keys;
}

$old_keys = getKeys($old_conn, $old_db['database']);
$new_keys = getKeys($new_conn, $new_db['database']);

$old_conn->close();
$new_conn->close();

/**
 * Compare helper
 */
function compareKeys($old, $new) {
    $missing_in_new = [];
    $missing_in_old = [];

    foreach ($old as $table => $cols) {
        if (!isset($new[$table])) {
            $missing_in_new[$table] = $cols;
        } else {
            $diff = array_diff($cols, $new[$table]);
            if ($diff) $missing_in_new[$table] = $diff;
        }
    }

    foreach ($new as $table => $cols) {
        if (!isset($old[$table])) {
            $missing_in_old[$table] = $cols;
        } else {
            $diff = array_diff($cols, $old[$table]);
            if ($diff) $missing_in_old[$table] = $diff;
        }
    }

    return [$missing_in_new, $missing_in_old];
}

list($missing_pk_in_new, $missing_pk_in_old) = compareKeys($old_keys['primary'], $new_keys['primary']);

// Foreign key comparison (need to compare array of arrays, so string encode)
function normalizeFKs($fks) {
    $result = [];
    foreach ($fks as $table => $rows) {
        foreach ($rows as $row) {
            $result[$table][] = "{$row['column']} -> {$row['ref_table']}({$row['ref_column']})";
        }
    }
    return $result;
}

$old_fk_norm = normalizeFKs($old_keys['foreign']);
$new_fk_norm = normalizeFKs($new_keys['foreign']);

list($missing_fk_in_new, $missing_fk_in_old) = compareKeys($old_fk_norm, $new_fk_norm);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Compare Primary & Foreign Keys</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="btn btn-primary mb-3" style="width:100%" onclick="window.location.href='config.php'">Home</div>
    <h3>Primary & Foreign Keys Comparison</h3>
    <p>
        <strong>Old DB:</strong> <?= htmlspecialchars($old_db['database']) ?><br>
        <strong>New DB:</strong> <?= htmlspecialchars($new_db['database']) ?>
    </p>

    <h4 class="mt-4">Primary Keys Missing in New DB</h4>
    <?php if ($missing_pk_in_new): ?>
        <ul class="list-group">
            <?php foreach ($missing_pk_in_new as $table => $cols): ?>
                <li class="list-group-item">
                    <strong><?= $table ?>:</strong> <?= implode(', ', $cols) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <div class="alert alert-success">✅ No missing primary keys in new DB</div>
    <?php endif; ?>

    <h4 class="mt-4">Primary Keys Missing in Old DB</h4>
    <?php if ($missing_pk_in_old): ?>
        <ul class="list-group">
            <?php foreach ($missing_pk_in_old as $table => $cols): ?>
                <li class="list-group-item">
                    <strong><?= $table ?>:</strong> <?= implode(', ', $cols) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <div class="alert alert-success">✅ No missing primary keys in old DB</div>
    <?php endif; ?>

    <h4 class="mt-4">Foreign Keys Missing in New DB</h4>
    <?php if ($missing_fk_in_new): ?>
        <ul class="list-group">
            <?php foreach ($missing_fk_in_new as $table => $rules): ?>
                <li class="list-group-item">
                    <strong><?= $table ?>:</strong> <?= implode(', ', $rules) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <div class="alert alert-success">✅ No missing foreign keys in new DB</div>
    <?php endif; ?>

    <h4 class="mt-4">Foreign Keys Missing in Old DB</h4>
    <?php if ($missing_fk_in_old): ?>
        <ul class="list-group">
            <?php foreach ($missing_fk_in_old as $table => $rules): ?>
                <li class="list-group-item">
                    <strong><?= $table ?>:</strong> <?= implode(', ', $rules) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <div class="alert alert-success">✅ No missing foreign keys in old DB</div>
    <?php endif; ?>

</div>
</body>
</html>
