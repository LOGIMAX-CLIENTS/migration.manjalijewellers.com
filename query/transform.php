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

// Transformation rules
$rules = [
    "UPPERCASE" => "Convert text to UPPERCASE",
    "LOWERCASE" => "Convert text to lowercase",
    "TRIM" => "Trim spaces",
    "DATE_FORMAT(YYYY-MM-DD)" => "Convert date to YYYY-MM-DD format",
    "DATE_FORMAT(DD/MM/YYYY)" => "Convert date to DD/MM/YYYY format",
    "CAST_TO_INT" => "Convert to Integer",
    "CAST_TO_BIGINT" => "Convert to BigInt",
    "CAST_TO_TEXT" => "Convert to Text"
];

// Handle request
$preview_data = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected_table = $_POST['table'];
    $selected_column = $_POST['column'];
    $selected_rule = $_POST['rule'];

    $query = "SELECT `$selected_column` FROM `$selected_table` LIMIT 10";
    $res = $old_conn->query($query);

    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $original = $row[$selected_column];
            $transformed = applyRule($original, $selected_rule);
            $preview_data[] = [
                "original" => $original,
                "transformed" => $transformed
            ];
        }
    }
}

// Function to apply transformation rule
function applyRule($value, $rule) {
    if ($value === null) return null;

    switch ($rule) {
        case "UPPERCASE": return strtoupper($value);
        case "LOWERCASE": return strtolower($value);
        case "TRIM": return trim($value);
        case "DATE_FORMAT(YYYY-MM-DD)": return date("Y-m-d", strtotime($value));
        case "DATE_FORMAT(DD/MM/YYYY)": return date("d/m/Y", strtotime($value));
        case "CAST_TO_INT": return intval($value);
        case "CAST_TO_BIGINT": return (string) intval($value);
        case "CAST_TO_TEXT": return (string) $value;
        default: return $value;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Data Transformation</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    body { background: #f8f9fa; }
    pre { background: #f1f1f1; padding: 0.6em; border-radius: 6px; }
</style>
</head>
<body>
<div class="container mt-4">

    <div class="d-flex justify-content-between mb-3">
        <h3>Data Transformation Rules</h3>
        <button class="btn btn-secondary" onclick="window.location.href='config.php'">🔙 Back to Home</button>
    </div>

    <?php if (empty($common_tables)): ?>
        <div class="alert alert-warning">No common tables to apply transformations.</div>
    <?php else: ?>
    <form method="post" class="card p-3 shadow-sm mb-4">
        <div class="row">
            <div class="col-md-4">
                <label class="form-label">Select Table</label>
                <select name="table" class="form-select" required onchange="this.form.submit()">
                    <option value="">-- Choose Table --</option>
                    <?php foreach ($common_tables as $table): ?>
                        <option value="<?= $table ?>" <?= isset($selected_table) && $selected_table == $table ? 'selected' : '' ?>>
                            <?= $table ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php if (!empty($selected_table)): ?>
            <div class="col-md-4">
                <label class="form-label">Select Column</label>
                <select name="column" class="form-select" required>
                    <option value="">-- Choose Column --</option>
                    <?php
                        $res = $old_conn->query("SHOW COLUMNS FROM `$selected_table`");
                        while ($col = $res->fetch_assoc()):
                    ?>
                        <option value="<?= $col['Field'] ?>" <?= isset($selected_column) && $selected_column == $col['Field'] ? 'selected' : '' ?>>
                            <?= $col['Field'] ?> (<?= $col['Type'] ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Select Rule</label>
                <select name="rule" class="form-select" required>
                    <option value="">-- Choose Rule --</option>
                    <?php foreach ($rules as $key => $desc): ?>
                        <option value="<?= $key ?>" <?= isset($selected_rule) && $selected_rule == $key ? 'selected' : '' ?>>
                            <?= $desc ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>
        </div>
        <div class="mt-3">
            <button class="btn btn-primary" type="submit">Preview Transformation</button>
        </div>
    </form>

    <?php if (!empty($preview_data)): ?>
    <div class="card shadow-sm">
        <div class="card-header bg-info text-white">Preview (first 10 rows)</div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Original Value</th>
                        <th>Transformed Value (<?= htmlspecialchars($selected_rule) ?>)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($preview_data as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars((string)$row['original']) ?></td>
                            <td><?= htmlspecialchars((string)$row['transformed']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <?php endif; ?>
</div>
</body>
</html>
