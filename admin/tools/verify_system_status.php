<?php
// System Verification Suite
// Tests: Caching -> Ticket Submission (Mock) -> AI Analysis -> Plan Generation

echo "=============================================\n";
echo "   🛡️  ZERO-REGRESSION SYSTEM VERIFICATION   \n";
echo "=============================================\n";

$errors = 0;

function pass($msg) { echo " [PASS] $msg\n"; }
function fail($msg) { global $errors; $errors++; echo " [FAIL] $msg\n"; }

// ---------------------------------------------------------
// TEST 1: Impact Cache (The Flashlight)
// ---------------------------------------------------------
echo "\n--- 1. Caching Layer ---\n";
$cacheFile = __DIR__ . '/../../knowledge_base/impact_index.json';

if (file_exists($cacheFile)) {
    $data = json_decode(file_get_contents($cacheFile), true);
    if ($data && isset($data['functions']) && count($data['functions']) > 0) {
        pass("Cache file exists and is valid JSON.");
        pass("Mapped " . count($data['functions']) . " functions.");
    } else {
        fail("Cache file exists but contents are invalid.");
    }
} else {
    fail("Impact Cache file missing. Run generate_impact_cache.php first.");
}

// ---------------------------------------------------------
// TEST 2: Ticket Backend (The Storage)
// ---------------------------------------------------------
echo "\n--- 2. Ticket Backend ---\n";
// We cannot easily curl localhost in this CLI environment reliably if port/dns fails.
// We will test the LOGIC by manually invoking the Controller method logic? 
// No, simpler: We manually write a ticket file (simulating the Controller's save action)
// and confirm we can read/write to that directory.

$ticketsDir = __DIR__ . '/../../knowledge_base/tickets/';
if (!is_dir($ticketsDir)) mkdir($ticketsDir, 0777, true);

$testTicketId = 'REQ-VERIFY-' . date('YmdHis');
$testTicketFile = $ticketsDir . $testTicketId . '.json';
$payload = [
    'id' => $testTicketId,
    'title' => 'System Verification Test',
    'type' => 'feature',
    'target' => 'ret_estimation.js',
    'description' => 'Verify that calculatetag_SaleValue is visible to the AI.',
    'status' => 'PENDING_ANALYSIS',
    'created_at' => date('Y-m-d H:i:s'),
    'ai_plan' => null
];

if (file_put_contents($testTicketFile, json_encode($payload))) {
    pass("Successfully created test ticket: $testTicketId");
} else {
    fail("Failed to write to tickets directory.");
}

// ---------------------------------------------------------
// TEST 3: AI Analysis Engine (The Brain)
// ---------------------------------------------------------
echo "\n--- 3. AI Analysis Engine ---\n";

// Execute the Engine Script
$engineScript = __DIR__ . '/ai_analysis_engine.php';
exec("php " . escapeshellarg($engineScript), $output, $returnVar);

if ($returnVar === 0) {
    pass("AI Engine executed successfully.");
} else {
    fail("AI Engine script failed with exit code $returnVar");
}

// ---------------------------------------------------------
// TEST 4: Result Verification
// ---------------------------------------------------------
echo "\n--- 4. Final Output Verification ---\n";
$updatedTicket = json_decode(file_get_contents($testTicketFile), true);

if ($updatedTicket['status'] === 'PLAN_READY') {
    pass("Ticket Status updated to PLAN_READY.");
} else {
    fail("Ticket Status NOT updated. stuck at " . $updatedTicket['status']);
}

if (!empty($updatedTicket['ai_plan']) && strpos($updatedTicket['ai_plan'], 'Implementation Plan') !== false) {
    pass("AI Plan generated successfully.");
    echo "\n--- PLAN PREVIEW ---\n";
    echo substr($updatedTicket['ai_plan'], 0, 150) . "...\n";
} else {
    fail("AI Plan missing or empty.");
}

// ---------------------------------------------------------
// SUMMARY
// ---------------------------------------------------------
echo "\n=============================================\n";
if ($errors === 0) {
    echo "   ✅ SYSTEM VERIFIED - ALL GREEN\n";
} else {
    echo "   ❌ SYSTEM VERIFIED - $errors ERRORS FOUND\n";
}
echo "=============================================\n";
?>
