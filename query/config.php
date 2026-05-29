<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!function_exists('str_contains')) {
    function str_contains($haystack, $needle)
    {
        return $needle !== '' && strpos($haystack, $needle) !== false;
    }
}


session_start();

$errors = [];
$old_db = ['host' => '', 'username' => '', 'password' => '', 'database' => ''];
$new_db = ['host' => '', 'username' => '', 'password' => '', 'database' => ''];

// Reset session if reset_session POST or expired (1 hour = 3600 seconds)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_session'])) {
    session_unset();
    session_destroy();
    session_start();
} elseif (isset($_SESSION['config_saved_time']) && (time() - $_SESSION['config_saved_time'] > 3600)) {
    session_unset();
    session_destroy();
    session_start();
}

// Load saved configs if any
$old_db = $_SESSION['old_db'] ?? $old_db;
$new_db = $_SESSION['new_db'] ?? $new_db;

function parseConnectionError($errorMessage)
{
    if (str_contains($errorMessage, 'Access denied')) {
        return 'Invalid username or password.';
    }
    if (str_contains($errorMessage, 'Unknown database')) {
        return 'Database name does not exist.';
    }
    if (str_contains($errorMessage, 'getaddrinfo') || str_contains($errorMessage, 'Name or service not known') || str_contains($errorMessage, 'php_network_getaddresses')) {
        return 'Hostname not found.';
    }
    return $errorMessage; // default fallback
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['reset_session'])) {
    // Collect POST data
    $old_db = [
        'host' => trim($_POST['old_host'] ?? ''),
        'username' => trim($_POST['old_user'] ?? ''),
        'password' => $_POST['old_pass'] ?? '',
        'database' => trim($_POST['old_db'] ?? ''),
    ];

    $new_db = [
        'host' => trim($_POST['new_host'] ?? ''),
        'username' => trim($_POST['new_user'] ?? ''),
        'password' => $_POST['new_pass'] ?? '',
        'database' => trim($_POST['new_db'] ?? ''),
    ];

    // Validate fields
    if ($old_db['host'] === '') $errors['old_host'] = "Old DB host is required.";
    if ($old_db['username'] === '') $errors['old_user'] = "Old DB username is required.";
    if ($old_db['database'] === '') $errors['old_db'] = "Old DB database name is required.";
    if ($new_db['host'] === '') $errors['new_host'] = "New DB host is required.";
    if ($new_db['username'] === '') $errors['new_user'] = "New DB username is required.";
    if ($new_db['database'] === '') $errors['new_db'] = "New DB database name is required.";

    // Only check connection if basic fields are filled
    if (!$errors) {
        // Attempt DB connections
        $old_conn = @mysqli_connect($old_db['host'], $old_db['username'], $old_db['password'], $old_db['database']);
        $new_conn = @mysqli_connect($new_db['host'], $new_db['username'], $new_db['password'], $new_db['database']);

        if (!$old_conn) {
            $errors['old_conn'] = "Old DB: " . parseConnectionError(mysqli_connect_error());
        }

        if (!$new_conn) {
            $errors['new_conn'] = "New DB: " . parseConnectionError(mysqli_connect_error());
        }

        if (!$errors) {
            // Save to session
            $_SESSION['old_db'] = $old_db;
            $_SESSION['new_db'] = $new_db;
            $_SESSION['config_saved_time'] = time();

            mysqli_close($old_conn);
            mysqli_close($new_conn);

            header('Location: config.php?success=1');
            exit;
        }
    }
}

// Calculate remaining time for timer
$time_left = 0;
if (isset($_SESSION['config_saved_time'])) {
    $elapsed = time() - $_SESSION['config_saved_time'];
    $time_left = max(3600 - $elapsed, 0);
}

// AJAX reset session
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_session']) && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    echo json_encode(['status' => 'reset']);
    exit;
}
?>

<!-- HTML (same as before, with error display additions) -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Database Configuration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background-color: #f4f7f6;
        }

        .container {
            max-width: 900px;
        }

        .card {
            border-radius: 12px;
        }

        .form-label {
            font-weight: bold;
        }

        .error-msg {
            color: #d6336c;
            font-size: 0.875em;
        }
    </style>
</head>

<body>

    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white text-center">
                <h4>Database Configuration</h4>
            </div>
            <div class="card-body">

                <?php if ($time_left > 0): ?>
                    <div class="alert alert-info text-center" id="timer-box">
                        Session expires in <span id="timer"><?= gmdate("H:i:s", $time_left) ?></span>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success">Configuration saved successfully!</div>
                <?php endif; ?>

                <?php if (isset($errors['old_conn'])): ?>
                    <div class="alert alert-danger"><?= $errors['old_conn'] ?></div>
                <?php endif; ?>
                <?php if (isset($errors['new_conn'])): ?>
                    <div class="alert alert-danger"><?= $errors['new_conn'] ?></div>
                <?php endif; ?>


                <form method="post" novalidate>
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="mb-3">Old Database</h5>

                            <div class="mb-3">
                                <label class="form-label" for="old_host">Host</label>
                                <input id="old_host" type="text" name="old_host" class="form-control <?= isset($errors['old_host']) ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars($old_db['host']) ?>" required />
                                <?php if (isset($errors['old_host'])): ?>
                                    <div class="error-msg"><?= $errors['old_host'] ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="old_user">Username</label>
                                <input id="old_user" type="text" name="old_user" class="form-control <?= isset($errors['old_user']) ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars($old_db['username']) ?>" required />
                                <?php if (isset($errors['old_user'])): ?>
                                    <div class="error-msg"><?= $errors['old_user'] ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="old_pass">Password</label>
                                <input id="old_pass" type="password" name="old_pass" class="form-control" value="<?= htmlspecialchars($old_db['password']) ?>" />
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="old_db">Database Name</label>
                                <input id="old_db" type="text" name="old_db" class="form-control <?= isset($errors['old_db']) ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars($old_db['database']) ?>" required />
                                <?php if (isset($errors['old_db'])): ?>
                                    <div class="error-msg"><?= $errors['old_db'] ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h5 class="mb-3">New Database</h5>

                            <div class="mb-3">
                                <label class="form-label" for="new_host">Host</label>
                                <input id="new_host" type="text" name="new_host" class="form-control <?= isset($errors['new_host']) ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars($new_db['host']) ?>" required />
                                <?php if (isset($errors['new_host'])): ?>
                                    <div class="error-msg"><?= $errors['new_host'] ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="new_user">Username</label>
                                <input id="new_user" type="text" name="new_user" class="form-control <?= isset($errors['new_user']) ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars($new_db['username']) ?>" required />
                                <?php if (isset($errors['new_user'])): ?>
                                    <div class="error-msg"><?= $errors['new_user'] ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="new_pass">Password</label>
                                <input id="new_pass" type="password" name="new_pass" class="form-control" value="<?= htmlspecialchars($new_db['password']) ?>" />
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="new_db">Database Name</label>
                                <input id="new_db" type="text" name="new_db" class="form-control <?= isset($errors['new_db']) ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars($new_db['database']) ?>" required />
                                <?php if (isset($errors['new_db'])): ?>
                                    <div class="error-msg"><?= $errors['new_db'] ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary mt-3" <?= $time_left > 0 ? 'disabled' : '' ?>>Save Configuration</button>
                </form>

                <form id="reset-session-form" method="post" class="mt-3">
                    <input type="hidden" name="reset_session" value="1" />
                    <button type="submit" class="btn btn-danger">Reset Session</button>
                </form>

            </div>
        </div>

        <div class="container my-5">
            <div class="row g-4">
                <?php
                $features = [
                    [
                        "label" => "Tables Comparison",
                        "href" => "tables.php",
                        "desc" => "Compare tables between old and new databases."
                    ],
                    [
                        "label" => "Columns Comparison",
                        "href" => "columns.php",
                        "desc" => "View differences in columns for matching tables."
                    ],
                    [
                        "label" => "Size Comparison",
                        "href" => "size.php",
                        "desc" => "Compare data sizes for each table."
                    ],
                    [
                        "label" => "Insert Data",
                        "href" => "insert_datas.php",
                        "desc" => "Insert missing data into the new database."
                    ],
                    [
                        "label" => "Non-Default Columns",
                        "href" => "non_default_value.php",
                        "desc" => "Set default values for NOT NULL columns missing defaults."
                    ],

                    // 🔑 Schema & Keys
                    [
                        "label" => "Primary/Foreign Keys",
                        "href" => "keys_check.php",
                        "desc" => "Compare primary keys, foreign keys, and constraints."
                    ],
                    [
                        "label" => "Indexes Comparison",
                        "href" => "check_indexes.php",
                        "desc" => "Compare indexes (unique, composite, fulltext) between DBs."
                    ],
                    [
                        "label" => "Triggers & Procedures",
                        "href" => "triggers.php",
                        "desc" => "Check triggers, stored procedures, and functions."
                    ],

                    // 📦 Data Integrity
                    [
                        "label" => "Row Count",
                        "href" => "row_count.php",
                        "desc" => "Compare row counts for each table."
                    ],
                    [
                        "label" => "Checksum Validation",
                        "href" => "checksum_validation.php",
                        "desc" => "Verify data integrity using checksum/hash."
                    ],
                    [
                        "label" => "Duplicate Data",
                        "href" => "duplicates.php",
                        "desc" => "Detect duplicate records before migration."
                    ],

                    // 🔄 Migration Utilities
                    [
                        "label" => "Data Transformation",
                        "href" => "transform.php",
                        "desc" => "Apply rules for column type or format changes."
                    ],
                    [
                        "label" => "Batch Migration",
                        "href" => "batch_migration.php",
                        "desc" => "Migrate large tables in smaller chunks."
                    ],
                    [
                        "label" => "Rollback / Backup",
                        "href" => "rollback.php",
                        "desc" => "Backup and rollback if migration fails."
                    ],

                    // 📊 Reporting
                    [
                        "label" => "Migration Summary",
                        "href" => "summary.php",
                        "desc" => "View migration results, mismatches, and skipped rows."
                    ],
                    [
                        "label" => "Error Logs",
                        "href" => "logs.php",
                        "desc" => "View migration error logs in one place."
                    ],
                ];

                ?>

                <?php foreach ($features as $feature): ?>
                    <div class="col-md-4">
                        <div class="card h-100 shadow-sm border-0">
                            <div class="card-body">
                                <h5 class="card-title"><?= $feature['label'] ?></h5>
                                <p class="card-text"><?= $feature['desc'] ?></p>
                            </div>
                            <div class="card-footer bg-transparent border-0">
                                <button class="btn btn-primary w-100"
                                    <?= $time_left > 0 ? "onclick=\"window.location.href='{$feature['href']}'\"" : "disabled" ?>>
                                    <?= $feature['label'] ?>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>

    <script>
        (function() {
            let secondsLeft = <?= $time_left ?>;
            const timerBox = document.getElementById('timer-box');
            const timerSpan = document.getElementById('timer');

            if (secondsLeft > 0) {
                const interval = setInterval(() => {
                    secondsLeft--;
                    if (secondsLeft <= 0) {
                        clearInterval(interval);
                        timerSpan.textContent = "00:00:00";
                        timerBox.textContent = "Session expired. Resetting session...";
                        resetSession();
                    } else {
                        let h = Math.floor(secondsLeft / 3600);
                        let m = Math.floor((secondsLeft % 3600) / 60);
                        let s = secondsLeft % 60;
                        timerSpan.textContent = String(h).padStart(2, '0') + ':' + String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
                    }
                }, 1000);
            }

            function resetSession() {
                fetch(window.location.href, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: 'reset_session=1'
                }).then(res => res.json()).then(data => {
                    if (data.status === 'reset') location.reload();
                }).catch(() => location.reload());
            }

            document.getElementById('reset-session-form').addEventListener('submit', function(e) {
                e.preventDefault();
                resetSession();
            });
        })();
    </script>

</body>

</html>