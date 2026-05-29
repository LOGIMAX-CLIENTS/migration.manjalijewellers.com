<?php
// Migration: Add editor_mode column to print_templates
$pdo = new PDO('mysql:host=localhost;dbname=retaillogimaxind_etailv3', 'root', 'root@123');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try {
    $pdo->exec("ALTER TABLE print_templates ADD COLUMN editor_mode VARCHAR(10) DEFAULT 'simple'");
    echo "SUCCESS: editor_mode column added\n";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column') !== false) {
        echo "INFO: Column editor_mode already exists\n";
    } else {
        echo "ERROR: " . $e->getMessage() . "\n";
    }
}
