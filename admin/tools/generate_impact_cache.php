<?php
// CLI Tool to Generate/Refresh the Impact Cache

echo "-------------------------------------\n";
echo "   Impact Cache Generator (CLI)      \n";
echo "-------------------------------------\n";

$jsFile = __DIR__ . '/../assets/js/ret_estimation.js';
$cacheFile = __DIR__ . '/../../knowledge_base/impact_index.json';

// Ensure Knowledge Base Dir exists
if (!is_dir(dirname($cacheFile))) {
    echo "Creating directory: " . dirname($cacheFile) . "\n";
    mkdir(dirname($cacheFile), 0777, true);
}

if (!file_exists($jsFile)) {
    die("Error: ret_estimation.js not found at $jsFile\n");
}

echo "Input: $jsFile\n";
echo "Output: $cacheFile\n";

$start = microtime(true);

echo "Parsing Code... ";

// --- LOGIC COPY FROM CONTROLLER ---
$content = file_get_contents($jsFile);
$lines = explode("\n", $content);

$fullIndex = [
    'functions' => [],
    'calls' => []
];

$map = []; 
$curr = 'GLOBAL';

// 1. Map Lines to Functions
foreach ($lines as $ln => $line) {
    if (preg_match('/function\s+([a-zA-Z0-9_]+)\s*\(/', $line, $matches)) {
        $curr = $matches[1];
    }
    $map[$ln + 1] = $curr;
}
$fullIndex['line_map'] = $map;

// 2. Build Call Graph
$definedFuncs = array_values(array_unique(array_values($map)));
$definedFuncs = array_diff($definedFuncs, ['GLOBAL']);
$fullIndex['functions'] = $definedFuncs;

$calls = [];
foreach($lines as $line) {
    foreach($definedFuncs as $f) {
        if (strpos($line, $f . '(') !== false) {
            $calls[] = ['target' => $f, 'line_content' => trim($line)];
        }
    }
}
$fullIndex['call_graph'] = $calls;

// --- END LOGIC ---

file_put_contents($cacheFile, json_encode($fullIndex)); // Save pretty print removed for speed

$end = microtime(true);
$duration = round(($end - $start) * 1000, 2);

echo "Done! ($duration ms)\n";
echo "Functions Found: " . count($definedFuncs) . "\n";
echo "Calls Mapped: " . count($calls) . "\n";
echo "Cache Size: " . round(filesize($cacheFile) / 1024, 2) . " KB\n";

// Verification Check
echo "\n--- VERIFICATION ---\n";
if ($duration < 2000) {
    echo "Performance: GOOD (Under 2s for generation)\n";
} else {
    echo "Performance: SLOW (Optimization needed?)\n";
}

if (file_exists($cacheFile)) {
    echo "Status: SUCCESS - Cache File Created.\n";
} else {
    echo "Status: FAIL - Cache File Not Created.\n";
}
