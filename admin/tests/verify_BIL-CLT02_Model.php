<?php
/**
 * BIL-CLT02 Model Logic Verification
 * Verifies that the Cash Abstract report requires item_type = 2 for Home Bill items.
 */

$modelPath = __DIR__ . '/../application/models/ret_reports_model.php';
if (!file_exists($modelPath)) {
    echo "❌ Model file not found at $modelPath\n";
    exit(1);
}

$content = file_get_contents($modelPath);

echo "Analyzing ret_reports_model.php for Cash Abstract query logic...\n";

// Check 1: Home Bill filter requires item_type = 2
// Looking for something like: $this->db->where('d.item_type', 2); or WHERE d.item_type = 2
$homeBillPattern = '/WHERE\s+d\.item_type\s*=\s*2/i';
if (preg_match($homeBillPattern, $content)) {
    echo "✅ Check 1: Home Bill filter requires item_type = 2 (Correct).\n";
} else {
    echo "❌ Check 1: Could not find item_type = 2 filter for Home Bills. Report logic might have changed.\n";
    exit(1);
}

// Check 2: Partial Sale filter requires item_type = 0
$partialSalePattern = '/WHERE\s+bil_det\.is_partial_sale\s*=\s*1.*item_type\s*=\s*0/is';
if (preg_match($partialSalePattern, $content)) {
    echo "✅ Check 2: Partial Sale filter requires item_type = 0 (Correct).\n";
} else {
    echo "⚠️ Check 2: Could not find item_type = 0 filter for Partial Sales. This is okay if only Home Bills were targeted.\n";
}

echo "\nBIL-CLT02 Model Logic Verification PASSED.\n";
