<?php
// AI Analysis Engine (Prototype)
// This script simulates the "Thinking" process of the AI Architect.
// It reads PENDING tickets, traces impacts using the Graph, and generates a Plan.

echo "-------------------------------------\n";
echo "   AI Analysis Engine v1.0           \n";
echo "-------------------------------------\n";

$ticketsDir = __DIR__ . '/../../knowledge_base/tickets/';
$impactIndexFile = __DIR__ . '/../../knowledge_base/impact_index.json';
$jsFile = __DIR__ . '/../assets/js/ret_estimation.js';

// 1. Load Impact Graph
if (!file_exists($impactIndexFile)) {
    die("Error: Impact Index not found. Please run generate_impact_cache.php first.\n");
}
$graph = json_decode(file_get_contents($impactIndexFile), true);
if (!$graph) die("Error: Invalid Impact Index format.\n");

echo "Loaded Knowledge Base: " . count($graph['functions']) . " functions mapped.\n";

// 2. Scan for Pending Tickets
$tickets = glob($ticketsDir . '*.json');
$pendingCount = 0;

foreach ($tickets as $ticketFile) {
    $ticket = json_decode(file_get_contents($ticketFile), true);
    
    // Skip if already processed
    if ($ticket['status'] !== 'PENDING_ANALYSIS') continue;

    echo "\nProcessing Ticket: " . $ticket['id'] . " (" . $ticket['title'] . ")\n";
    $pendingCount++;

    // --- AI LOGIC: TRACE IMPACTS ---
    $targetFile = $ticket['target']; // e.g., 'ret_estimation.js'
    
    // In this prototype, we assume the user might describe a FUNCTION or a CONCEPT in the details.
    // We will blindly 'search' the graph for any keywords in the description to find entry points.
    $keywords = explode(' ', $ticket['description']);
    $impactedFuncs = [];

    // Simple Keyword Search against Graph Functions
    foreach ($keywords as $word) {
        $word = trim($word, " .,;");
        if(strlen($word) < 4) continue;
        
        foreach($graph['functions'] as $funcName) {
            if (stripos($funcName, $word) !== false) {
                $impactedFuncs[] = $funcName;
            }
        }
    }
    $impactedFuncs = array_unique($impactedFuncs);
    
    // Find Callers (Reverse Dependency)
    $callers = [];
    foreach($impactedFuncs as $target) {
        foreach($graph['call_graph'] as $call) {
            if ($call['target'] === $target) {
                // Naive: we don't know the caller function name easily from just the line content in this simple graph
                // But we can guess or just list the dependency.
                // For this MVP, we just list "Callers found".
                $callers[] = "Code calls " . $target;
            }
        }
    }

    // --- AI LOGIC: GENERATE PLAN ---
    $plan = "# 🧠 AI Implementation Plan\n\n";
    $plan .= "**Ticket:** " . $ticket['title'] . "\n";
    $plan .= "**Target:** " . $targetFile . "\n\n";
    
    $plan .= "## 1. Impact Analysis\n";
    if (count($impactedFuncs) > 0) {
        $plan .= "Based on the description, the following functions may be involved:\n";
        foreach($impactedFuncs as $f) $plan .= "- `$f`\n";
    } else {
        $plan .= "No direct function matches found for keywords. Manual investigation recommended.\n";
    }
    
    $plan .= "\n## 2. Proposed Strategy (Isolation)\n";
    $plan .= "To avoid regressions, we will apply the **Strangler Fig Pattern**:\n\n";
    $plan .= "1.  **Extract:** Create a new module in `assets/js/modules/` for this logic.\n";
    $plan .= "2.  **Route:** Update `" . $targetFile . "` to delegate calls to the new module.\n";
    $plan .= "3.  **Verify:** Add unit tests for the new module.\n";
    
    $plan .= "\n## 3. Verification Plan\n";
    $plan .= "- [ ] Check browser console for errors.\n";
    $plan .= "- [ ] Run `npm test` on new module.\n";

    echo "Plan Generated. Saving...\n";

    // Update Ticket
    $ticket['status'] = 'PLAN_READY';
    $ticket['ai_plan'] = $plan;
    $ticket['analyzed_at'] = date('Y-m-d H:i:s');

    file_put_contents($ticketFile, json_encode($ticket, JSON_PRETTY_PRINT));
    echo "Ticket Updated: " . basename($ticketFile) . "\n";
}

if ($pendingCount === 0) {
    echo "No pending tickets found.\n";
}

echo "\nDone.\n";
