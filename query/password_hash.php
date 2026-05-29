<?php
require 'config.php'; // Should define $db

function connect($config)
{
    $conn = new mysqli($config['host'], $config['username'], $config['password'], $config['database']);
    if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);
    return $conn;
}

$conn = connect($new_db);

$data = [];
if (isset($_GET['generate'])) {
    $res = $conn->query("SELECT id_employee, passwd FROM employee");

    while ($row = $res->fetch_assoc()) {
        $decoded = base64_decode($row['passwd'], true); // true = strict mode
        if ($decoded === false) continue; // skip invalid base64

        $hashed = password_hash($decoded, PASSWORD_DEFAULT);
        $sql = "UPDATE employee SET pwd_hash = '" . $conn->real_escape_string($hashed) . "' WHERE id_employee = " . intval($row['id_employee']) . ";";

        $data[] = [
            'id_employee' => $row['id_employee'],
            'passwd_encoded' => $row['passwd'],
            'passwd_decoded' => $decoded,
            'passwd_hashed' => $hashed,
            'sql_query' => $sql
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Password Migration Tool</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                alert("Query copied to clipboard!");
            });
        }
    </script>
</head>

<body class="p-4">
    <h3 class="mb-4">Employee Password Hash Generator</h3>
    <a href="?generate=true" class="btn btn-primary mb-3">Generate</a>

    <?php if (!empty($data)): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Employee ID</th>
                        <th>Encoded Password</th>
                        <th>Decoded Password</th>
                        <th>Hashed Password</th>
                        <th>SQL Update Query</th>
                        <th>Copy</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data as $i => $row): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= htmlspecialchars($row['id_employee']) ?></td>
                            <td><?= htmlspecialchars($row['passwd_encoded']) ?></td>
                            <td><?= htmlspecialchars($row['passwd_decoded']) ?></td>
                            <td><code><?= htmlspecialchars($row['passwd_hashed']) ?></code></td>
                            <td><code><?= htmlspecialchars($row['sql_query']) ?></code></td>
                            <td>
                                <button class="btn btn-sm btn-success" onclick="copyToClipboard(`<?= addslashes($row['sql_query']) ?>`)">Copy</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
        // Prepare combined SQL preview
        $allQueries = "";
        foreach ($data as $row) {
            $allQueries .= $row['sql_query'] . "\n";
        }
        ?>
        <div class="mt-4">
            <h5>All SQL Update Queries</h5>
            <textarea id="allQueries" class="form-control mb-2" rows="10" readonly><?= htmlspecialchars($allQueries) ?></textarea>
            <button class="btn btn-success" onclick="copyAllQueries()">Copy All Queries</button>
        </div>
    <?php endif; ?>

</body>
<script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            alert("Query copied to clipboard!");
        });
    }

    function copyAllQueries() {
        const textarea = document.getElementById('allQueries');
        textarea.select();
        textarea.setSelectionRange(0, 99999); // For mobile
        document.execCommand('copy');
        alert("All queries copied to clipboard!");
    }
</script>

</html>
<?php $conn->close(); ?>