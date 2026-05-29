<?php
session_start();

if (!isset($_SESSION['old_db'])) {
    die("No old DB credentials found in session. Please configure the database first.");
}

$old_db = $_SESSION['old_db'];
$mysqli = new mysqli($old_db['host'], $old_db['username'], $old_db['password'], $old_db['database']);
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$database = $mysqli->real_escape_string($old_db['database']);
$tablesResult = $mysqli->query("SHOW TABLES FROM `$database`");

$results = [];
$rowCounter = 1;

while ($row = $tablesResult->fetch_array()) {
    $table = $row[0];
    $query = "
        SELECT COLUMN_NAME, COLUMN_TYPE
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = '$database'
          AND TABLE_NAME = '$table'
          AND COLUMN_DEFAULT IS NULL
          AND IS_NULLABLE = 'NO'
          AND COLUMN_NAME NOT IN (
              SELECT COLUMN_NAME
              FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
              WHERE TABLE_SCHEMA = '$database'
                AND TABLE_NAME = '$table'
                AND CONSTRAINT_NAME = 'PRIMARY'
          )
    ";

    $columnsResult = $mysqli->query($query);
    if ($columnsResult && $columnsResult->num_rows > 0) {
        while ($col = $columnsResult->fetch_assoc()) {
            $results[] = [
                'id' => $rowCounter++,
                'table' => $table,
                'column' => $col['COLUMN_NAME']
            ];
        }
    }
}
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Set Default Values</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script>
        function updateQuery(rowId, table, column) {
            const input = document.getElementById(`default-${rowId}`);
            const nullableCheckbox = document.getElementById(`nullable-${rowId}`);
            const queryText = document.getElementById(`query-text-${rowId}`);
            const copyBtn = document.getElementById(`copy-btn-${rowId}`);
            const value = input.value.trim();

            if (nullableCheckbox.checked) {
                input.value = "";
                const query = `ALTER TABLE \`${table}\` ALTER COLUMN \`${column}\` SET DEFAULT NULL;`;
                queryText.textContent = query;
                copyBtn.disabled = false;
            } else if (value !== "") {
                const safeValue = value.replace(/'/g, "\\'");
                const query = `ALTER TABLE \`${table}\` ALTER COLUMN \`${column}\` SET DEFAULT '${safeValue}';`;
                queryText.textContent = query;
                copyBtn.disabled = false;
            } else {
                queryText.textContent = "";
                copyBtn.disabled = true;
            }
        }

        function onCheckboxClick(rowId, table, column) {
            const checkbox = document.getElementById(`nullable-${rowId}`);
            const input = document.getElementById(`default-${rowId}`);
            if (checkbox.checked) {
                input.value = "";
            }
            updateQuery(rowId, table, column);
        }

        function copyToClipboard(query) {
            if (query.trim() === "") return;
            navigator.clipboard.writeText(query).then(() => alert("Query copied to clipboard!"));
        }

        function copyAllQueries() {
            let queries = [];
            document.querySelectorAll("[id^='query-text-']").forEach(el => {
                const query = el.textContent.trim();
                if (query !== "") {
                    queries.push(query);
                }
            });

            if (queries.length === 0) {
                alert("No queries to copy.");
                return;
            }

            navigator.clipboard.writeText(queries.join("\n")).then(() => alert("All queries copied!"));
        }

        function exportToExcel() {
            const wb = XLSX.utils.book_new();
            const ws_data = [["Table", "Column", "Default Value", "Generated Query"]];

            document.querySelectorAll("tr[data-row-id]").forEach(row => {
                const rowId = row.getAttribute("data-row-id");
                const table = row.getAttribute("data-table");
                const column = row.getAttribute("data-column");
                const value = document.getElementById(`default-${rowId}`).value.trim();
                const query = document.getElementById(`query-text-${rowId}`).textContent.trim();

                if (query !== "") {
                    ws_data.push([table, column, value || "NULL", query]);
                }
            });

            if (ws_data.length === 1) {
                alert("No queries to export.");
                return;
            }

            const ws = XLSX.utils.aoa_to_sheet(ws_data);
            XLSX.utils.book_append_sheet(wb, ws, "Queries");
            XLSX.writeFile(wb, "default_value_queries.xlsx");
        }
        function filterTable() {
        const input = document.getElementById("searchInput").value.toLowerCase();
        const rows = document.querySelectorAll("tbody tr");

        rows.forEach(row => {
            const table = row.getAttribute("data-table").toLowerCase();
            const column = row.getAttribute("data-column").toLowerCase();

            if (table.includes(input) || column.includes(input)) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        });
    }
    </script>
</head>
<body class="p-4">
          <div class="btn btn-primary" style="width:100%" onclick="window.location.href='config.php'">
        Home
    </div>
<div class="container">
    <h3 class="mb-4">Set Default Values for Columns (NULL Default, NOT NULL, Not Primary)</h3>

    <div class="mb-3">
        <button class="btn btn-success me-2" onclick="copyAllQueries()">Copy All Queries</button>
        <button class="btn btn-info" onclick="exportToExcel()">Export to Excel</button>
    </div>
<div class="mb-3">
    <input type="text" id="searchInput" class="form-control" placeholder="Search by table or column name..." onkeyup="filterTable()">
</div>

    <table class="table table-bordered align-middle">
        <thead class="table-dark">
        <tr>
            <th>Table</th>
            <th>Column</th>
            <th>Default Value</th>
            <th>Is Nullable</th>
            <th>Generated Query</th>
            <th>Copy</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($results as $row): ?>
            <tr data-row-id="<?= $row['id'] ?>" data-table="<?= $row['table'] ?>" data-column="<?= $row['column'] ?>">
                <td><?= $row['table'] ?></td>
                <td><?= $row['column'] ?></td>
                <td>
                    <input type="text" id="default-<?= $row['id'] ?>" class="form-control"
                           oninput="updateQuery('<?= $row['id'] ?>', '<?= $row['table'] ?>', '<?= $row['column'] ?>')">
                </td>
                <td class="text-center">
                    <input type="checkbox" id="nullable-<?= $row['id'] ?>"
                           onclick="onCheckboxClick('<?= $row['id'] ?>', '<?= $row['table'] ?>', '<?= $row['column'] ?>')">
                </td>
                <td><code id="query-text-<?= $row['id'] ?>"></code></td>
                <td>
                    <button id="copy-btn-<?= $row['id'] ?>" class="btn btn-sm btn-primary" disabled
                            onclick="copyToClipboard(document.getElementById('query-text-<?= $row['id'] ?>').textContent)">
                        Copy
                    </button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
