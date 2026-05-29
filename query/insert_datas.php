<?php
session_start();

$old_db = $_SESSION['old_db'] ?? ['host' => '', 'username' => '', 'password' => '', 'database' => ''];
$new_db = $_SESSION['new_db'] ?? ['host' => '', 'username' => '', 'password' => '', 'database' => ''];

$old_db_connection = new mysqli($old_db['host'], $old_db['username'], $old_db['password'], $old_db['database']);
if ($old_db_connection->connect_error) {
    die("Connection failed to old database: " . $old_db_connection->connect_error);
}

$new_db_connection = new mysqli($new_db['host'], $new_db['username'], $new_db['password'], $new_db['database']);
if ($new_db_connection->connect_error) {
    die("Connection failed to new database: " . $new_db_connection->connect_error);
}

// ✅ only get base tables (skip views)
$tables = [];
$res = $old_db_connection->query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'");
while ($row = $res->fetch_array()) {
    $tables[] = $row[0];
}

$insert_queries = [];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Data Migration (Insert Select Queries)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const oldDb = "<?php echo $old_db['database']; ?>";
            const newDb = "<?php echo $new_db['database']; ?>";

            function regenerateQueries() {
                const shouldTruncate = document.getElementById('withTruncate').checked;
                const shouldIgnore = document.getElementById('withIgnore').checked;
                const includePrimary = document.getElementById('withPrimaryKey').checked;

                let allQueries = [];

                document.querySelectorAll('tr[data-query]').forEach(tr => {
                    const targetTable = tr.dataset.target;
                    let columns = JSON.parse(tr.dataset.columns);
                    let primaryKeys = JSON.parse(tr.dataset.primarykeys);
                    let tableName = tr.dataset.table;

                    if (!includePrimary) {
                        columns = columns.filter(c => !primaryKeys.includes(c));
                    }

                    const colList = "`" + columns.join("`, `") + "`";

                    let baseQuery = `INSERT INTO ${targetTable} (${colList}) SELECT ${colList} FROM \`${oldDb}\`.\`${tableName}\`;`;

                    if (shouldIgnore) {
                        baseQuery = baseQuery.replace(/^INSERT INTO/i, "INSERT IGNORE INTO");
                    }

                    const finalQuery = shouldTruncate
                        ? `TRUNCATE TABLE ${targetTable};\n\n${baseQuery}`
                        : baseQuery;

                    tr.querySelector('code').textContent = finalQuery;
                    tr.querySelector('.copy-btn').setAttribute('onclick', `copyToClipboard(${JSON.stringify(finalQuery)})`);
                    tr.setAttribute("data-finalquery", finalQuery);

                    allQueries.push(finalQuery);
                });

                document.getElementById('allQueries').value = allQueries.join("\n\n");
            }

            // ✅ Clipboard functions (work on HTTP & HTTPS)
            window.copyToClipboard = function(text) {
                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(text).then(() => {
                        alert("Query copied to clipboard!");
                    }).catch(err => {
                        console.error("Clipboard write failed:", err);
                        fallbackCopy(text);
                    });
                } else {
                    fallbackCopy(text);
                }
            };

            function fallbackCopy(text) {
                const textarea = document.createElement("textarea");
                textarea.value = text;
                textarea.style.position = "fixed";
                textarea.style.left = "-9999px";
                textarea.style.top = "0";
                document.body.appendChild(textarea);
                textarea.focus();
                textarea.select();
                try {
                    const successful = document.execCommand("copy");
                    if (successful) {
                        alert("Query copied to clipboard!");
                    } else {
                        alert("Press Ctrl+C to copy manually.");
                    }
                } catch (err) {
                    console.error("Fallback copy failed:", err);
                    alert("Press Ctrl+C to copy manually.");
                }
                document.body.removeChild(textarea);
            }

            // ✅ Copy all queries
            window.copyAllQueries = function() {
                const allQueries = document.getElementById("allQueries").value;
                copyToClipboard(allQueries);
            };

            // ✅ Copy selected queries
            window.copySelectedQueries = function() {
                let selected = [];
                document.querySelectorAll('.table-checkbox:checked').forEach(cb => {
                    const tr = cb.closest("tr");
                    if (tr && tr.dataset.finalquery) {
                        selected.push(tr.dataset.finalquery);
                    }
                });

                if (selected.length === 0) {
                    alert("No tables selected!");
                    return;
                }

                copyToClipboard(selected.join("\n\n"));
            };

            // Event listeners
            document.getElementById('withTruncate').addEventListener('change', regenerateQueries);
            document.getElementById('withIgnore').addEventListener('change', regenerateQueries);
            document.getElementById('withPrimaryKey').addEventListener('change', regenerateQueries);

            // run once at load
            regenerateQueries();
        });
    </script>
</head>

<body class="p-4">
    <div class="btn btn-primary mb-3" onclick="window.location.href='config.php'" style="width:100%;">
        Home
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Data Migration</h3>
        <div class="d-flex gap-3">
            <!-- Truncate -->
            <div class="btn btn-danger d-flex align-items-center">
                <input class="form-check-input me-2" type="checkbox" id="withTruncate">
                <label class="form-check-label mb-0" for="withTruncate">With Truncate?</label>
            </div>

            <!-- Ignore duplicates -->
            <div class="btn btn-warning d-flex align-items-center">
                <input class="form-check-input me-2" type="checkbox" id="withIgnore">
                <label class="form-check-label mb-0" for="withIgnore">Ignore Duplicates?</label>
            </div>

            <!-- Include Primary Key -->
            <div class="btn btn-info d-flex align-items-center">
                <input class="form-check-input me-2" type="checkbox" id="withPrimaryKey">
                <label class="form-check-label mb-0" for="withPrimaryKey">Include Primary Key?</label>
            </div>
        </div>
    </div>

    <div class="mb-3">
        <input type="text" id="tableSearch" class="form-control" placeholder="Search table name...">
    </div>

    <!-- Top Copy buttons -->
    <div class="mb-3 d-flex gap-3">
        <button class="btn btn-success" onclick="copyAllQueries()">Copy All Queries</button>
        <button class="btn btn-primary" onclick="copySelectedQueries()">Copy Selected Queries</button>
    </div>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th><input type="checkbox" id="selectAll"></th>
                <th>Table</th>
                <th>Insert Select Query</th>
                <th>Copy</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($tables as $table) {
                $res_old = $old_db_connection->query("SHOW COLUMNS FROM `$table`");
                if (!$res_old) continue; // skip views or broken tables

                $columns = [];
                $primaryKeys = [];
                while ($row = $res_old->fetch_assoc()) {
                    $columns[] = $row['Field'];
                    if ($row['Key'] === "PRI") {
                        $primaryKeys[] = $row['Field'];
                    }
                }

                $column_names = "`" . implode("`, `", $columns) . "`";

                $insert_query = "INSERT INTO {$new_db['database']}.`$table` ($column_names) SELECT $column_names FROM `{$old_db['database']}`.`$table`;";
                $insert_queries[] = $insert_query;

                $escaped_query = htmlspecialchars($insert_query, ENT_QUOTES);
                $target_table = "{$new_db['database']}.`$table`";

                echo "<tr data-query=\"$escaped_query\" 
                          data-finalquery=\"$escaped_query\" 
                          data-target=\"$target_table\" 
                          data-columns='" . json_encode($columns) . "' 
                          data-primarykeys='" . json_encode($primaryKeys) . "' 
                          data-table=\"$table\">
                        <td><input type='checkbox' class='table-checkbox'></td>
                        <td>$table</td>
                        <td><code>$insert_query</code></td>
                        <td><button class='btn btn-sm btn-primary copy-btn' onclick='copyToClipboard(\"$escaped_query\")'>Copy</button></td>
                    </tr>";
            }
            ?>
        </tbody>
    </table>

    <textarea id="allQueries" class="form-control d-none" rows="10"><?php
        echo htmlspecialchars(implode("\n\n", $insert_queries));
    ?></textarea>

    <script>
        // ✅ Select/Deselect all checkboxes
        document.getElementById("selectAll").addEventListener("change", function() {
            document.querySelectorAll(".table-checkbox").forEach(cb => {
                cb.checked = this.checked;
            });
        });
    </script>
</body>
</html>
<?php
$old_db_connection->close();
$new_db_connection->close();
?>


<script>
    // ✅ Select/Deselect all checkboxes
    document.getElementById("selectAll").addEventListener("change", function() {
        document.querySelectorAll(".table-checkbox").forEach(cb => {
            cb.checked = this.checked;
        });
    });

    // ✅ Search filter for tables
    document.getElementById("tableSearch").addEventListener("input", function() {
        const searchValue = this.value.toLowerCase().trim();
        const rows = document.querySelectorAll("tbody tr");

        rows.forEach(row => {
            const tableName = row.querySelector("td:nth-child(2)").textContent.toLowerCase();
            if (tableName.includes(searchValue)) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        });
    });
</script>
