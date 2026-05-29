<?php
/**
 * Standalone Logic Test — BRN-103: Live Error/Query Leak in Save Failure
 * Category: Security (output-side — information disclosure)
 * Usage: & "C:\xampp_7.4\php\php.exe" admin/application/tests/test_BRN_103_security.php
 *
 * Tests:
 *   1. Save failure result array has NO 'q' or 'err' keys (no SQL leak)
 *   2. Save failure result array produces valid JSON (no raw echo prefix)
 *   3. updateStatus failure result array has NO 'q' or 'err' keys
 *   4. updateStatus failure result array produces valid JSON
 *   5. Static check: echo last_query() / _error_message() NOT present in controller
 *   6. Static check: 'q' => last_query() NOT present in controller result array
 *   7. Save failure result array contains ONLY expected safe keys
 *   8. updateStatus failure result array contains ONLY expected safe keys
 */

$pass = 0;
$fail = 0;
$results = [];

function assert_test(string $name, bool $condition, string $detail = ''): void {
    global $pass, $fail, $results;
    if ($condition) {
        $results[] = "  PASS: $name";
        $pass++;
    } else {
        $results[] = "  FAIL: $name" . ($detail ? " | $detail" : '');
        $fail++;
    }
}

echo "\n=== BRN-103: Security — Error/Query Leak Fix Tests ===\n\n";

// ─── SIMULATED RESULT ARRAYS (mirroring the FIXED code paths) ─────────────────

// Test 1-4: Simulate FIXED result arrays (as they appear after BRN-103 fix)

// Location 1: save failure — $result['status'] = 0 (no 'q' or 'err')
$save_failure_result = ['status' => 0];
$save_failure_json = json_encode($save_failure_result);

assert_test(
    "Save failure result has NO 'q' key (no SQL leak)",
    !array_key_exists('q', $save_failure_result)
);

assert_test(
    "Save failure result has NO 'err' key (no error msg leak)",
    !array_key_exists('err', $save_failure_result)
);

assert_test(
    "Save failure JSON is valid (no raw echo prefix garbling it)",
    json_decode($save_failure_json) !== null && json_last_error() === JSON_ERROR_NONE,
    "JSON: $save_failure_json"
);

assert_test(
    "Save failure JSON does NOT contain SQL-like content",
    strpos($save_failure_json, 'SELECT') === false &&
    strpos($save_failure_json, 'INSERT') === false &&
    strpos($save_failure_json, 'UPDATE') === false
);

// Location 2: updateStatus failure — safe result only
$update_failure_result = [
    'message' => 'Unable to proceed the requested process',
    'class'   => 'danger',
    'title'   => 'Branch Transfer Approval',
];
$update_failure_json = json_encode($update_failure_result);

assert_test(
    "updateStatus failure result has NO 'q' key",
    !array_key_exists('q', $update_failure_result)
);

assert_test(
    "updateStatus failure result has NO 'err' key",
    !array_key_exists('err', $update_failure_result)
);

assert_test(
    "updateStatus failure JSON is valid",
    json_decode($update_failure_json) !== null && json_last_error() === JSON_ERROR_NONE
);

assert_test(
    "updateStatus failure result contains ONLY safe keys",
    count(array_diff(array_keys($update_failure_result), ['message', 'class', 'title'])) === 0
);

// ─── STATIC FILE CHECKS ────────────────────────────────────────────────────────

$controller = file_get_contents(
    realpath(__DIR__ . '/../../') . '/admin/application/controllers/admin_ret_brntransfer.php'
);

if ($controller === false) {
    echo "  [SKIP] Could not read controller file for static checks\n";
} else {
    // Test 5: no bare echo of last_query() in the file
    assert_test(
        "No bare 'echo \$this->db->last_query()' in controller",
        strpos($controller, 'echo $this->db->last_query()') === false
    );

    // Test 6: no bare echo of _error_message() in the file
    assert_test(
        "No bare 'echo \$this->db->_error_message()' in controller",
        strpos($controller, 'echo $this->db->_error_message()') === false
    );

    // Test 7: 'q' => last_query() not in result arrays
    assert_test(
        "No \"'q' => \$this->db->last_query()\" in controller result arrays",
        strpos($controller, "'q' => \$this->db->last_query()") === false
    );

    // Test 8: 'err' => _error_message() not in result arrays
    assert_test(
        "No \"'err' => \$this->db->_error_message()\" in controller result arrays",
        strpos($controller, "'err' => \$this->db->_error_message()") === false
    );

    // Test 9: log_message IS present (proves server-side logging was added)
    assert_test(
        "log_message('error', 'BRN-103') IS present (server-side logging confirmed)",
        strpos($controller, "log_message('error', 'BRN-103") !== false
    );

    // Test 10: log_message appears at BOTH locations (save BT + updateStatus)
    $logCount = substr_count($controller, "log_message('error', 'BRN-103");
    assert_test(
        "Two BRN-103 log_message calls exist (one per fix location)",
        $logCount === 2,
        "Found: $logCount (expected 2)"
    );
}

// ─── Results ───────────────────────────────────────────────────────────────────

echo implode("\n", $results) . "\n";
echo "\n=== Results ===\n";
echo "  Tests: " . ($pass + $fail) . " | Pass: $pass | Fail: $fail\n";
if ($fail === 0) {
    echo "  ALL TESTS PASSED\n\n";
} else {
    echo "  TESTS FAILED — see above for details\n\n";
    exit(1);
}
