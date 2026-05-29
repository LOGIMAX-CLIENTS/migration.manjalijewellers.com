<?php
$pdo = new PDO('mysql:host=localhost;dbname=klson_staging;charset=utf8mb4', 'root', '1234');

// Check if ret_bill_pay_device table exists
$r = $pdo->query("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA='klson_staging' AND TABLE_NAME='ret_bill_pay_device'");
echo "ret_bill_pay_device: " . ($r->rowCount() > 0 ? 'EXISTS' : 'MISSING') . "\n";

// Check pay_by_pos setting
$r2 = $pdo->query("SELECT value FROM ret_settings WHERE name='pay_by_pos'");
$row = $r2->fetch();
echo "pay_by_pos = " . ($row ? $row['value'] : 'NOT FOUND') . "\n";

// Check which columns ret_pos_device_list has
echo "\nret_pos_device_list columns:\n";
$cols = $pdo->query("SHOW COLUMNS FROM ret_pos_device_list");
foreach($cols as $c) echo "  {$c['Field']} ({$c['Type']})\n";

// Check id_pay_device - does it exist?
$r3 = $pdo->query("SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='klson_staging' AND TABLE_NAME='ret_pos_device_list' AND COLUMN_NAME='id_pay_device'");
echo "\nid_pay_device column: " . ($r3->fetchColumn() > 0 ? 'EXISTS' : 'MISSING') . "\n";

// Check id_bank column
$r4 = $pdo->query("SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='klson_staging' AND TABLE_NAME='ret_pos_device_list' AND COLUMN_NAME='id_bank'");
echo "id_bank column: " . ($r4->fetchColumn() > 0 ? 'EXISTS' : 'MISSING') . "\n";
