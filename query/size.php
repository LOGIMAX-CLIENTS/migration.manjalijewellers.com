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

function extract_length($type)
{
    if (preg_match('/\((\d+)\)/', $type, $matches)) {
        return (int)$matches[1];
    }
    return null;
}

function getDatabaseSize($conn)
{
    $total_size = 0;
    $res = $conn->query("SHOW TABLE STATUS");
    while ($row = $res->fetch_assoc()) {
        $total_size += $row['Data_length'] + $row['Index_length']; // Data and index sizes
    }
    return $total_size; // Total size in bytes
}



// Get the sizes of both databases
$old_db_size = getDatabaseSize($old_db_connection);
$new_db_size = getDatabaseSize($new_db_connection);

// Compare sizes and determine which is smaller or larger
$size_comparison = '';
if ($old_db_size > $new_db_size) {
    $size_comparison = "The old database is larger than the new database.";
} elseif ($old_db_size < $new_db_size) {
    $size_comparison = "The new database is larger than the old database.";
} else {
    $size_comparison = "Both databases have the same size.";
}

// Convert sizes to a human-readable format (MB)
$old_db_size_mb = round($old_db_size / 1024 / 1024, 2); // Convert bytes to MB
$new_db_size_mb = round($new_db_size / 1024 / 1024, 2); // Convert bytes to MB

$tables = [];
$res = $old_db_connection->query("SHOW TABLES");
while ($row = $res->fetch_array()) {
    $tables[] = $row[0];
}

$all_queries = []; // Store all ALTER queries
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Column Size Mismatch Checker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                alert("Query copied to clipboard!");
            });
        }

        function copyAllQueries() {
            const allQueries = document.getElementById("allQueries").value;
            navigator.clipboard.writeText(allQueries).then(() => {
                alert("All queries copied to clipboard!");
            });
        }
    </script>
</head>

<body class="p-4">
    <div class="btn btn-primary" style="width:100%" onclick="window.location.href='config.php'">
        Home
    </div>
    <h3 class="mb-3">Column Size Mismatches (New DB has smaller length)</h3>

    <!-- <div class="alert alert-info mb-4">
        <strong>Database Size Comparison:</strong><br>
        Old Database Size: <?php echo $old_db_size_mb; ?> MB<br>
        New Database Size: <?php echo $new_db_size_mb; ?> MB<br>
        <?php echo $size_comparison; ?>
    </div> -->

    <div class="mb-3">
        <button class="btn btn-success" onclick="copyAllQueries()">Copy All Queries</button>
    </div>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Table</th>
                <th>Column</th>
                <th>Old Type</th>
                <th>New Type</th>
                <th>ALTER Query</th>
                <th>Copy</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($tables as $table) {
                $old_cols = [];
                $new_cols = [];

                $res_old = $old_db_connection->query("SHOW COLUMNS FROM `$table`");
                while ($row = $res_old->fetch_assoc()) {
                    $old_cols[$row['Field']] = $row['Type'];
                }

                $res_new = $new_db_connection->query("SHOW COLUMNS FROM `$table`");
                if (!$res_new) continue;
                while ($row = $res_new->fetch_assoc()) {
                    $new_cols[$row['Field']] = $row['Type'];
                }

                foreach ($old_cols as $col => $old_type) {
                    if (isset($new_cols[$col])) {
                        $old_len = extract_length($old_type);
                        $new_len = extract_length($new_cols[$col]);

                        if ($old_len !== null && $new_len !== null && $new_len < $old_len) {
                            $base_type = preg_replace('/\(\d+\)/', '', $old_type);
                            $alter_query = "ALTER TABLE '$table' MODIFY '$col' $base_type($old_len)";
                            $all_queries[] = $alter_query;

                            echo "<tr>
                        <td>$table</td>
                        <td>$col</td>
                        <td>$old_type</td>
                        <td>{$new_cols[$col]}</td>
                        <td><code>$alter_query;</code></td>
                        <td><button class='btn btn-sm btn-primary' onclick='copyToClipboard(\"" . htmlspecialchars($alter_query, ENT_QUOTES) . "\")'>Copy</button></td>
                    </tr>";
                        }
                    }
                }
            }
            ?>
        </tbody>
    </table>

    <textarea id="allQueries" class="form-control d-none" rows="10"><?php
                                                                    echo htmlspecialchars(implode(";\n", $all_queries) . ";");
                                                                    ?></textarea>

</body>

</html>
<?php
$old_db_connection->close();
$new_db_connection->close();
?>