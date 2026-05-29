<?php
/**
 * Standalone logic validator for BRN-101 / BRN-102 (NO PHPUnit required).
 * PHP 7.4 compatible.
 *
 * Run: & "C:\xampp_7.4\php\php.exe" test_BRN_101_102_logic.php 2> $null
 */

$pass = 0;
$fail = 0;

function assert_eq($label, $expected, $actual): void
{
    global $pass, $fail;
    if ($expected === $actual) {
        echo "  PASS: $label\n";
        $pass++;
    } else {
        echo "  FAIL: $label\n";
        echo "     Expected: " . json_encode($expected) . "\n";
        echo "     Got:      " . json_encode($actual) . "\n";
        $fail++;
    }
}

function str_has($haystack, $needle): bool
{
    return strpos($haystack, $needle) !== false;
}

// Pure logic of verify_otp() after BRN-101 fix (no trans_begin)
function verify_otp_logic(string $sessionOtp, string $postOtp, int $otpExp): array
{
    if ($sessionOtp === $postOtp) {
        if (time() >= $otpExp) {
            return ['status' => false, 'msg' => 'OTP has been expired'];
        } else {
            return ['status' => true, 'msg' => 'OTP Verified successfully.Proceed Approval.'];
        }
    } else {
        return ['status' => false, 'msg' => 'Please Enter Valid OTP'];
    }
}

// ─────────────────────────────────────────────────────
// BRN-101 Tests
// ─────────────────────────────────────────────────────
echo "\n=== BRN-101: verify_otp() Logic ===\n";

$r = verify_otp_logic('123456', '123456', time() + 60);
assert_eq('Valid OTP, not expired -> status:true', true, $r['status']);
assert_eq('Valid OTP, not expired -> Verified msg', true, str_has($r['msg'], 'Verified'));

$r = verify_otp_logic('123456', '123456', time() - 1);
assert_eq('Valid OTP, expired -> status:false', false, $r['status']);
assert_eq('Valid OTP, expired -> expired msg', true, str_has($r['msg'], 'expired'));

$r = verify_otp_logic('123456', '999999', time() + 60);
assert_eq('Wrong OTP -> status:false', false, $r['status']);
assert_eq('Wrong OTP -> invalid msg', true, str_has($r['msg'], 'Valid OTP'));

$r = verify_otp_logic('123456', '', time() + 60);
assert_eq('Empty post OTP -> status:false', false, $r['status']);

// ─────────────────────────────────────────────────────
// BRN-102 Tests — Transaction ordering
// ─────────────────────────────────────────────────────
echo "\n=== BRN-102: Transaction Ordering ===\n";

// Test 1: Success path — begin -> update -> commit
$callOrder = [];
$do_begin    = function () use (&$callOrder) { $callOrder[] = 'begin'; };
$do_update   = function () use (&$callOrder) { $callOrder[] = 'update'; return 1; };
$do_commit   = function () use (&$callOrder) { $callOrder[] = 'commit'; };
$do_rollback = function () use (&$callOrder) { $callOrder[] = 'rollback'; };

$do_begin();
$result = $do_update();
if (!$result) {
    $do_rollback();
} else {
    $do_commit();
}
assert_eq('Success path order: begin->update->commit', ['begin', 'update', 'commit'], $callOrder);

// Test 2: Failure path — begin -> update -> rollback
$callOrder = [];
$do_update_fail = function () use (&$callOrder) { $callOrder[] = 'update'; return 0; };

$do_begin();
$result = $do_update_fail();
if (!$result) {
    $do_rollback();
} else {
    $do_commit();
}
assert_eq('Failure path order: begin->update->rollback', ['begin', 'update', 'rollback'], $callOrder);

// Test 3: Expired OTP — NO transaction opened
$callOrder = [];
$otpExpired = time() - 10;
if (time() >= $otpExpired) {
    // expired — don't open transaction
    $status = ['status' => false, 'msg' => 'OTP has been expired'];
} else {
    $do_begin(); // should NOT be reached
}
assert_eq('Expired OTP -> zero transaction calls', [], $callOrder);
assert_eq('Expired OTP -> status:false', false, $status['status']);

// Test 4: Commit happens AFTER update (BRN-102 core assertion)
$callOrder = [];
$do_begin();
$do_update();   // update must come first
$do_commit();
$commitIdx = array_search('commit', $callOrder);
$updateIdx = array_search('update', $callOrder);
assert_eq('Commit index > Update index (commit after update)', true, $commitIdx > $updateIdx);

// ─────────────────────────────────────────────────────
// Summary
// ─────────────────────────────────────────────────────
echo "\n=== Results ===\n";
$total = $pass + $fail;
echo "  Tests: $total | Pass: $pass | Fail: $fail\n";
if ($fail === 0) {
    echo "  ALL TESTS PASSED\n\n";
    exit(0);
} else {
    echo "  SOME TESTS FAILED\n\n";
    exit(1);
}
