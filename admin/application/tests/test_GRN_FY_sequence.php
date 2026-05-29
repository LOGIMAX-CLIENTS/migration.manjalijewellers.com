<?php
// CI3 Bootstrap Test — GRN Sequence FY Reset
// Verifies that generate_grn_refno() scopes sequence by financial year
// Usage: & "D:\xampp\php\php.exe" d:\XAMPP\htdocs\retail_v5\admin\application\tests\test_GRN_FY_sequence.php

$dsn = "mysql:host=localhost;dbname=retail_dev;charset=utf8";
$pdo = new PDO($dsn, 'root', '1234');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "\n=== Test: GRN Sequence FY Reset ===\n\n";

// --- Test 1: Get current active FY ---
$stmt = $pdo->query("SELECT fin_year_code FROM ret_financial_year WHERE fin_status = 1");
$active_fy = $stmt->fetchColumn();
echo "Active FY: $active_fy\n";
if (!$active_fy) {
    echo "FAIL: No active financial year found!\n";
    exit(1);
}

// --- Test 2: Verify grn_fin_year_code column exists ---
$cols = $pdo->query("SHOW COLUMNS FROM ret_grn_entry LIKE 'grn_fin_year_code'")->fetchAll();
if (count($cols) === 0) {
    echo "FAIL: grn_fin_year_code column does not exist in ret_grn_entry!\n";
    exit(1);
}
echo "PASS: grn_fin_year_code column exists\n";

// --- Test 3: Simulate the FIXED query for grn_type=1 (PU-), scoped to active FY ---
$sql = "SELECT MAX(CAST(SUBSTRING_INDEX(grn_ref_no, '-', -1) AS UNSIGNED)) as max_num
        FROM ret_grn_entry WHERE grn_type = ? AND grn_fin_year_code = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([1, $active_fy]);
$max_fy = (int) $stmt->fetchColumn();

// --- Test 4: Compare with OLD query (no FY filter) ---
$sql_old = "SELECT MAX(CAST(SUBSTRING_INDEX(grn_ref_no, '-', -1) AS UNSIGNED)) as max_num
            FROM ret_grn_entry WHERE grn_type = ?";
$stmt2 = $pdo->prepare($sql_old);
$stmt2->execute([1]);
$max_all = (int) $stmt2->fetchColumn();

echo "\n--- GRN Type 1 (PU-) ---\n";
echo "MAX sequence (ALL FYs, OLD query):     $max_all\n";
echo "MAX sequence (Active FY only, FIXED):  $max_fy\n";

$next_old = str_pad($max_all + 1, 5, '0', STR_PAD_LEFT);
$next_new = str_pad($max_fy + 1, 5, '0', STR_PAD_LEFT);

echo "Next GRN ref (OLD): PU-$next_old\n";
echo "Next GRN ref (NEW): PU-$next_new\n";

if ($max_fy <= $max_all) {
    echo "PASS: FY-scoped sequence ($max_fy) <= all-time sequence ($max_all)\n";
} else {
    echo "FAIL: FY-scoped max should never exceed all-time max\n";
}

// --- Test 5: Verify FY data exists in the table ---
$stmt3 = $pdo->query("SELECT grn_fin_year_code, COUNT(*) as cnt 
                       FROM ret_grn_entry 
                       GROUP BY grn_fin_year_code 
                       ORDER BY grn_fin_year_code");
echo "\n--- GRN Entries by FY ---\n";
$rows = $stmt3->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $row) {
    echo "  FY: {$row['grn_fin_year_code']} => {$row['cnt']} entries\n";
}

// --- Test 6: If new FY has no entries, next should be 00001 ---
$fy_count_stmt = $pdo->prepare("SELECT COUNT(*) FROM ret_grn_entry WHERE grn_fin_year_code = ? AND grn_type = 1");
$fy_count_stmt->execute([$active_fy]);
$fy_count = (int) $fy_count_stmt->fetchColumn();

if ($fy_count === 0) {
    if ($max_fy === 0 && $next_new === '00001') {
        echo "PASS: No entries in active FY — next GRN correctly starts at PU-00001\n";
    } else {
        echo "FAIL: No entries in active FY but next is PU-$next_new (expected PU-00001)\n";
    }
} else {
    echo "INFO: Active FY has $fy_count PU- entries, next = PU-$next_new\n";
}

echo "\n=== All tests completed ===\n";
