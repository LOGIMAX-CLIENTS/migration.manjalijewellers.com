<?php
/**
 * Customization Fingerprint Tool v2.0
 * 
 * Compares a client codebase against the source (retail_v5) to classify
 * each module's divergence into Level 1/2/3 for the Three-Lane bug fix strategy.
 * 
 * v2.0 adds: Bug pattern scanning, batch multi-client mode, version detection,
 *            dead code detection, security audit, fix propagation tracking.
 * 
 * Usage:
 *   php fingerprint.php --source="d:\XAMPP\htdocs\retail_v5" --client="d:\XAMPP\htdocs\srjewellery" --client-name="srjewellery"
 * 
 * Optional:
 *   --modules="Estimation,Billing"   Only scan specific modules
 *   --detail                         Include per-function diff details
 *   --json-only                      Skip markdown output
 *   --bug-scan                       Scan for known bug patterns
 *   --security                       Security-focused scan only
 *   --batch                          Scan all clients from clients.json
 *   --version-detect                 Detect which source version client is based on
 *   --fix-tracker                    Show which recipes apply to this client
 *   --line-diff                      Show unified diff for modified functions
 * 
 * Part of: AI_Bug_Fix_System / Bug Remediation System
 * Created: 2026-03-12 | Enhanced: 2026-03-27
 */

// ============================================================================
// CONFIGURATION
// ============================================================================

$scriptDir = __DIR__;
$moduleMapFile = $scriptDir . '/module_map.json';
$reportsDir = $scriptDir . '/reports';

// Parse CLI arguments
$args = parseArguments($argv);

if (!isset($args['source']) || !isset($args['client']) || !isset($args['client-name'])) {
    echo "\n";
    echo "  ╔══════════════════════════════════════════════════════════════╗\n";
    echo "  ║         CUSTOMIZATION FINGERPRINT TOOL v2.0                 ║\n";
    echo "  ╚══════════════════════════════════════════════════════════════╝\n\n";
    echo "  Usage:\n";
    echo "    php fingerprint.php --source=<path> --client=<path> --client-name=<name>\n\n";
    echo "  Required (single client mode):\n";
    echo "    --source        Path to source project (e.g., retail_v5)\n";
    echo "    --client        Path to client project\n";
    echo "    --client-name   Short client identifier\n\n";
    echo "  Optional:\n";
    echo "    --modules       Comma-separated module list (default: all)\n";
    echo "    --detail        Include per-function diff details\n";
    echo "    --json-only     Skip markdown output\n";
    echo "    --bug-scan      Scan for known bug patterns\n";
    echo "    --security      Security-focused scan only\n";
    echo "    --batch         Scan all clients from clients.json\n";
    echo "    --version-detect  Detect client source version\n";
    echo "    --fix-tracker   Show applicable fix recipes\n";
    echo "    --line-diff     Unified diff for modified functions\n\n";
    exit(1);
}

$sourcePath = rtrim(str_replace('/', '\\', $args['source']), '\\');
$clientPath = rtrim(str_replace('/', '\\', $args['client']), '\\');
$clientName = $args['client-name'];
$filterModules = isset($args['modules']) ? explode(',', $args['modules']) : null;
$showDetail = isset($args['detail']);
$jsonOnly = isset($args['json-only']);
$bugScan = isset($args['bug-scan']);
$securityScan = isset($args['security']);
$batchMode = isset($args['batch']);
$versionDetect = isset($args['version-detect']);
$fixTracker = isset($args['fix-tracker']);
$lineDiff = isset($args['line-diff']);

// Validate paths
if (!is_dir($sourcePath)) {
    fwrite(STDERR, "ERROR: Source path does not exist: $sourcePath\n");
    exit(1);
}
if (!is_dir($clientPath)) {
    fwrite(STDERR, "ERROR: Client path does not exist: $clientPath\n");
    exit(1);
}

// Load module map
if (!file_exists($moduleMapFile)) {
    fwrite(STDERR, "ERROR: module_map.json not found at: $moduleMapFile\n");
    exit(1);
}
$config = json_decode(file_get_contents($moduleMapFile), true);
if (!$config) {
    fwrite(STDERR, "ERROR: Failed to parse module_map.json\n");
    exit(1);
}

$modules = $config['modules'];
$paths = $config['paths'];
$weights = $config['weights'];
$thresholds = $config['thresholds'];

// Filter modules if --modules flag provided
if ($filterModules) {
    $filtered = [];
    foreach ($filterModules as $m) {
        $m = trim($m);
        foreach ($modules as $key => $val) {
            if (strcasecmp($key, $m) === 0 || strcasecmp(str_replace('_', ' ', $key), $m) === 0) {
                $filtered[$key] = $val;
            }
        }
    }
    if (empty($filtered)) {
        fwrite(STDERR, "ERROR: No matching modules found for: " . implode(', ', $filterModules) . "\n");
        exit(1);
    }
    $modules = $filtered;
}

// ============================================================================
// MAIN EXECUTION
// ============================================================================

echo "\n";
echo "  ╔══════════════════════════════════════════════════════════════╗\n";
echo "  ║         CUSTOMIZATION FINGERPRINT TOOL v2.0                 ║\n";
echo "  ╚══════════════════════════════════════════════════════════════╝\n\n";
echo "  Source:  $sourcePath\n";
echo "  Client:  $clientPath ($clientName)\n";
echo "  Modules: " . count($modules) . "\n";

// Show active modes
$activeModes = [];
if ($bugScan) $activeModes[] = 'Bug Scan';
if ($securityScan) $activeModes[] = 'Security Audit';
if ($versionDetect) $activeModes[] = 'Version Detect';
if ($fixTracker) $activeModes[] = 'Fix Tracker';
if ($lineDiff) $activeModes[] = 'Line Diff';
if (!empty($activeModes)) {
    echo "  Modes:   " . implode(', ', $activeModes) . "\n";
}
echo "  ────────────────────────────────────────────────────────────────\n\n";

// === BATCH MODE ===
if ($batchMode) {
    batchScan($sourcePath, $paths, $modules, $thresholds, $weights, $showDetail, $bugScan, $versionDetect);
    echo "\n  Done.\n\n";
    exit(0);
}

$results = [];
$totalModules = count($modules);
$current = 0;

foreach ($modules as $moduleName => $moduleFiles) {
    $current++;
    echo "  [$current/$totalModules] Scanning: $moduleName ... ";
    
    $result = scanModule($moduleName, $moduleFiles, $sourcePath, $clientPath, $paths, $thresholds, $showDetail);
    $results[$moduleName] = $result;
    
    $level = classifyLevel($result['overall_drift'], $thresholds);
    $emoji = $level === 1 ? '🟢' : ($level === 2 ? '🟡' : '🔴');
    echo sprintf("%.1f%% %s Level %d\n", $result['overall_drift'], $emoji, $level);
}

// Calculate summary
$summary = calculateSummary($results, $thresholds, $weights);

// Output
echo "\n  ════════════════════════════════════════════════════════════════\n";
echo "  RESULTS\n";
echo "  ════════════════════════════════════════════════════════════════\n\n";
printResultsTable($results, $thresholds);
echo "\n";
printLaneSummary($results, $thresholds);

// Save reports
$date = date('Y-m-d');
$jsonFile = "$reportsDir/fingerprint_{$clientName}_{$date}.json";
$mdFile = "$reportsDir/fingerprint_{$clientName}_{$date}.md";

if (!is_dir($reportsDir)) {
    mkdir($reportsDir, 0755, true);
}

// === v2.0 ENHANCEMENTS ===

// Bug Pattern Scan
$bugResults = [];
if ($bugScan || $securityScan) {
    echo "\n  ════════════════════════════════════════════════════════════════\n";
    echo "  BUG PATTERN SCAN" . ($securityScan ? ' (Security Focus)' : '') . "\n";
    echo "  ════════════════════════════════════════════════════════════════\n\n";
    $bugResults = scanBugPatterns($clientPath, $paths, $securityScan);
    printBugScanResults($bugResults);
}

// Version Detection
$versionInfo = [];
if ($versionDetect && isset($config['version_markers'])) {
    echo "\n  ════════════════════════════════════════════════════════════════\n";
    echo "  CLIENT VERSION DETECTION\n";
    echo "  ════════════════════════════════════════════════════════════════\n\n";
    $versionInfo = detectClientVersion($clientPath, $paths, $config['version_markers']);
    printVersionResults($versionInfo);
}

// Dead Code Detection (always runs with bug-scan)
$deadCodeResults = [];
if ($bugScan) {
    echo "\n  ════════════════════════════════════════════════════════════════\n";
    echo "  DEAD CODE DETECTION\n";
    echo "  ════════════════════════════════════════════════════════════════\n\n";
    $deadCodeResults = detectDeadCode($clientPath, $paths);
    printDeadCodeResults($deadCodeResults);
}

// Fix Tracker / Recipe Cross-Reference
$recipeResults = [];
if ($fixTracker) {
    echo "\n  ════════════════════════════════════════════════════════════════\n";
    echo "  FIX PROPAGATION TRACKER\n";
    echo "  ════════════════════════════════════════════════════════════════\n\n";
    $recipeResults = crossReferenceRecipes($clientPath, $sourcePath);
    printRecipeResults($recipeResults);
}

// === INCREMENTAL SCAN (auto — compares with previous scan) ===
$incrementalDiff = [];
$previousScan = loadPreviousScan($clientName, $reportsDir, $date);
if ($previousScan) {
    echo "\n  ════════════════════════════════════════════════════════════════\n";
    echo "  INCREMENTAL DIFF (vs " . $previousScan['metadata']['scan_date'] . ")\n";
    echo "  ════════════════════════════════════════════════════════════════\n\n";
    $incrementalDiff = computeIncrementalDiff($previousScan, $results, $summary, $bugResults, $deadCodeResults);
    printIncrementalDiff($incrementalDiff);
} else {
    echo "\n  ℹ️  No previous scan found for $clientName — full results shown (next scan will show incremental diff)\n";
}

// Save JSON
$jsonReport = [
    'metadata' => [
        'tool' => 'Customization Fingerprint v2.0',
        'scan_date' => $date,
        'scan_time' => date('H:i:s'),
        'source_path' => $sourcePath,
        'client_path' => $clientPath,
        'client_name' => $clientName,
        'modules_scanned' => count($results),
        'modes' => array_filter([
            'bug_scan' => $bugScan,
            'security_scan' => $securityScan,
            'version_detect' => $versionDetect,
            'fix_tracker' => $fixTracker,
            'line_diff' => $lineDiff,
        ]),
    ],
    'thresholds' => $thresholds,
    'weights' => $weights,
    'summary' => $summary,
    'modules' => $results,
    'bug_patterns' => $bugResults,
    'version_info' => $versionInfo,
    'dead_code' => $deadCodeResults,
    'recipe_tracker' => $recipeResults,
    'incremental' => $incrementalDiff,
];
file_put_contents($jsonFile, json_encode($jsonReport, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo "\n  JSON report: $jsonFile\n";

// Save Markdown
if (!$jsonOnly) {
    $md = generateMarkdownReport($results, $summary, $thresholds, $sourcePath, $clientPath, $clientName, $date, $incrementalDiff);
    file_put_contents($mdFile, $md);
    echo "  MD report:   $mdFile\n";
}

echo "\n  Done.\n\n";

// ============================================================================
// CORE FUNCTIONS
// ============================================================================

/**
 * Scan a single module across all 5 layers
 */
function scanModule($moduleName, $moduleFiles, $sourcePath, $clientPath, $paths, $thresholds, $showDetail) {
    $result = [
        'module' => $moduleName,
        'layers' => [],
        'overall_drift' => 0,
        'missing_files' => [],
    ];
    
    // Layer 1: Controller
    $srcFile = $sourcePath . '\\' . $paths['controller_dir'] . '\\' . $moduleFiles['controller'];
    $cltFile = $clientPath . '\\' . $paths['controller_dir'] . '\\' . $moduleFiles['controller'];
    $result['layers']['controller'] = comparePhpFile($srcFile, $cltFile, $thresholds, $showDetail);
    
    // Layer 2: Model
    $srcFile = $sourcePath . '\\' . $paths['model_dir'] . '\\' . $moduleFiles['model'];
    $cltFile = $clientPath . '\\' . $paths['model_dir'] . '\\' . $moduleFiles['model'];
    $result['layers']['model'] = comparePhpFile($srcFile, $cltFile, $thresholds, $showDetail);
    
    // Layer 3: JS
    $srcFile = $sourcePath . '\\' . $paths['js_dir'] . '\\' . $moduleFiles['js'];
    $cltFile = $clientPath . '\\' . $paths['js_dir'] . '\\' . $moduleFiles['js'];
    $result['layers']['js'] = compareJsFile($srcFile, $cltFile, $thresholds, $showDetail);
    
    // Layer 4: Views
    $srcDir = $sourcePath . '\\' . $paths['view_dir'] . '\\' . $moduleFiles['view_dir'];
    $cltDir = $clientPath . '\\' . $paths['view_dir'] . '\\' . $moduleFiles['view_dir'];
    $result['layers']['views'] = compareViewDir($srcDir, $cltDir);
    
    // Layer 5: CSS
    if ($moduleFiles['css']) {
        $srcFile = $sourcePath . '\\' . $paths['css_dir'] . '\\' . $moduleFiles['css'];
        $cltFile = $clientPath . '\\' . $paths['css_dir'] . '\\' . $moduleFiles['css'];
        $result['layers']['css'] = compareCssFile($srcFile, $cltFile);
    } else {
        $result['layers']['css'] = ['drift' => 0, 'status' => 'no_css', 'detail' => 'No CSS file for this module'];
    }
    
    // Track missing files
    foreach (['controller', 'model', 'js'] as $layer) {
        if (isset($result['layers'][$layer]['status']) && $result['layers'][$layer]['status'] === 'client_missing') {
            $result['missing_files'][] = $layer;
        }
    }
    
    // Calculate weighted overall drift
    $result['overall_drift'] = calculateWeightedDrift($result['layers']);
    
    // v2.0: Classify drift into Customization vs Update Lag
    $result['drift_classification'] = classifyDriftSources($result['layers']);
    
    return $result;
}

/**
 * Compare a PHP file using token_get_all for function extraction
 */
function comparePhpFile($srcFile, $cltFile, $thresholds, $showDetail = false) {
    $result = [
        'drift' => 0,
        'status' => 'ok',
        'source_file' => $srcFile,
        'client_file' => $cltFile,
    ];
    
    if (!file_exists($srcFile)) {
        $result['status'] = 'source_missing';
        $result['drift'] = 0;
        $result['detail'] = "Source file not found: $srcFile";
        return $result;
    }
    
    if (!file_exists($cltFile)) {
        $result['status'] = 'client_missing';
        $result['drift'] = 100;
        $result['detail'] = "Client file not found — module may not exist in client";
        return $result;
    }
    
    // Extract functions from both files
    $srcFunctions = extractPhpFunctions($srcFile);
    $cltFunctions = extractPhpFunctions($cltFile);
    
    $srcNames = array_keys($srcFunctions);
    $cltNames = array_keys($cltFunctions);
    
    $shared = array_intersect($srcNames, $cltNames);
    $sourceOnly = array_diff($srcNames, $cltNames);
    $clientOnly = array_diff($cltNames, $srcNames);
    
    $totalUnique = count(array_unique(array_merge($srcNames, $cltNames)));
    
    // For shared functions, compare function bodies
    $modified = 0;
    $modifiedFunctions = [];
    $bodyChangeThreshold = $thresholds['function_body_change_threshold'];
    
    foreach ($shared as $funcName) {
        $srcBody = $srcFunctions[$funcName]['body'];
        $cltBody = $cltFunctions[$funcName]['body'];
        
        if ($srcBody !== $cltBody) {
            $bodyChange = fastBodyCompare($srcBody, $cltBody, $srcFunctions[$funcName]['line_count'], $cltFunctions[$funcName]['line_count']);
            
            if ($bodyChange > $bodyChangeThreshold) {
                $modified++;
                if ($showDetail) {
                    $modifiedFunctions[] = [
                        'name' => $funcName,
                        'body_change' => round($bodyChange, 1),
                        'src_lines' => $srcFunctions[$funcName]['line_count'],
                        'clt_lines' => $cltFunctions[$funcName]['line_count'],
                    ];
                }
            }
        }
    }
    
    // Calculate drift
    $changedCount = count($sourceOnly) + count($clientOnly) + $modified;
    $drift = $totalUnique > 0 ? ($changedCount / $totalUnique) * 100 : 0;
    
    $result['drift'] = round($drift, 1);
    $result['source_functions'] = count($srcNames);
    $result['client_functions'] = count($cltNames);
    $result['shared'] = count($shared);
    $result['source_only'] = array_values($sourceOnly);
    $result['client_only'] = array_values($clientOnly);
    $result['modified_count'] = $modified;
    $result['source_lines'] = countFileLines($srcFile);
    $result['client_lines'] = countFileLines($cltFile);
    
    if ($showDetail && !empty($modifiedFunctions)) {
        $result['modified_detail'] = $modifiedFunctions;
    }
    
    return $result;
}

/**
 * Compare a JS file using regex-based function extraction
 */
function compareJsFile($srcFile, $cltFile, $thresholds, $showDetail = false) {
    $result = [
        'drift' => 0,
        'status' => 'ok',
        'source_file' => $srcFile,
        'client_file' => $cltFile,
    ];
    
    if (!file_exists($srcFile)) {
        $result['status'] = 'source_missing';
        $result['drift'] = 0;
        $result['detail'] = "Source JS not found: $srcFile";
        return $result;
    }
    
    if (!file_exists($cltFile)) {
        $result['status'] = 'client_missing';
        $result['drift'] = 100;
        $result['detail'] = "Client JS not found";
        return $result;
    }
    
    // Extract JS functions
    $srcFunctions = extractJsFunctions($srcFile);
    $cltFunctions = extractJsFunctions($cltFile);
    
    $srcNames = array_keys($srcFunctions);
    $cltNames = array_keys($cltFunctions);
    
    $shared = array_intersect($srcNames, $cltNames);
    $sourceOnly = array_diff($srcNames, $cltNames);
    $clientOnly = array_diff($cltNames, $srcNames);
    
    $totalUnique = count(array_unique(array_merge($srcNames, $cltNames)));
    
    // Calculate drift (no body comparison for JS — files are too large, function body extraction is unreliable)
    // Instead, use line count difference as a proxy for modification
    $srcLineCount = countFileLines($srcFile);
    $cltLineCount = countFileLines($cltFile);
    $lineDelta = abs($srcLineCount - $cltLineCount);
    $lineChangePct = $srcLineCount > 0 ? ($lineDelta / $srcLineCount) * 100 : 0;
    
    // Combine function diff + line delta
    $funcChangePct = $totalUnique > 0 ? ((count($sourceOnly) + count($clientOnly)) / $totalUnique) * 100 : 0;
    $drift = ($funcChangePct * 0.7) + (min($lineChangePct, 100) * 0.3); // 70% function names, 30% line count
    
    $result['drift'] = round($drift, 1);
    $result['source_functions'] = count($srcNames);
    $result['client_functions'] = count($cltNames);
    $result['shared'] = count($shared);
    $result['source_only'] = array_values($sourceOnly);
    $result['client_only'] = array_values($clientOnly);
    $result['source_lines'] = $srcLineCount;
    $result['client_lines'] = $cltLineCount;
    $result['line_delta'] = $lineDelta;
    
    return $result;
}

/**
 * Compare view directories — file list + line count delta
 */
function compareViewDir($srcDir, $cltDir) {
    $result = [
        'drift' => 0,
        'status' => 'ok',
    ];
    
    if (!is_dir($srcDir)) {
        $result['status'] = 'source_missing';
        $result['drift'] = 0;
        $result['detail'] = "Source view dir not found: $srcDir";
        return $result;
    }
    
    if (!is_dir($cltDir)) {
        $result['status'] = 'client_missing';
        $result['drift'] = 100;
        $result['detail'] = "Client view dir not found";
        return $result;
    }
    
    $srcFiles = getViewFiles($srcDir);
    $cltFiles = getViewFiles($cltDir);
    
    $shared = array_intersect($srcFiles, $cltFiles);
    $sourceOnly = array_diff($srcFiles, $cltFiles);
    $clientOnly = array_diff($cltFiles, $srcFiles);
    
    $totalUnique = count(array_unique(array_merge($srcFiles, $cltFiles)));
    
    // For shared files, check line count differences
    $significantlyChanged = 0;
    foreach ($shared as $fileName) {
        $srcLines = countFileLines($srcDir . '\\' . $fileName);
        $cltLines = countFileLines($cltDir . '\\' . $fileName);
        $delta = abs($srcLines - $cltLines);
        $changePct = $srcLines > 0 ? ($delta / $srcLines) * 100 : 0;
        if ($changePct > 20) {
            $significantlyChanged++;
        }
    }
    
    $changedCount = count($sourceOnly) + count($clientOnly) + $significantlyChanged;
    $drift = $totalUnique > 0 ? ($changedCount / $totalUnique) * 100 : 0;
    
    $result['drift'] = round($drift, 1);
    $result['source_files'] = count($srcFiles);
    $result['client_files'] = count($cltFiles);
    $result['shared'] = count($shared);
    $result['source_only'] = array_values($sourceOnly);
    $result['client_only'] = array_values($clientOnly);
    $result['significantly_changed'] = $significantlyChanged;
    
    return $result;
}

/**
 * Compare CSS files using similar_text()
 */
function compareCssFile($srcFile, $cltFile) {
    $result = [
        'drift' => 0,
        'status' => 'ok',
    ];
    
    if (!file_exists($srcFile)) {
        $result['status'] = 'source_missing';
        $result['drift'] = 0;
        return $result;
    }
    
    if (!file_exists($cltFile)) {
        $result['status'] = 'client_missing';
        $result['drift'] = 100;
        return $result;
    }
    
    $srcContent = file_get_contents($srcFile);
    $cltContent = file_get_contents($cltFile);
    
    // Normalize whitespace for comparison
    $srcNorm = preg_replace('/\s+/', ' ', trim($srcContent));
    $cltNorm = preg_replace('/\s+/', ' ', trim($cltContent));
    
    if ($srcNorm === $cltNorm) {
        $result['drift'] = 0;
    } else {
        $similarity = 0;
        similar_text($srcNorm, $cltNorm, $similarity);
        $result['drift'] = round(100 - $similarity, 1);
    }
    
    $result['source_lines'] = countFileLines($srcFile);
    $result['client_lines'] = countFileLines($cltFile);
    
    return $result;
}

// ============================================================================
// FAST COMPARISON HELPERS
// ============================================================================

/**
 * Fast body comparison that avoids O(n²) similar_text() for large function bodies.
 * For small bodies (<5000 chars), uses similar_text() for accuracy.
 * For large bodies, uses a fast heuristic based on length ratio + line count ratio.
 * Returns the estimated body change percentage (0-100).
 */
function fastBodyCompare($srcBody, $cltBody, $srcLineCount, $cltLineCount) {
    $srcLen = strlen($srcBody);
    $cltLen = strlen($cltBody);
    
    // For small functions, similar_text() is fast enough
    $MAX_SIZE_FOR_SIMILAR_TEXT = 5000;
    if ($srcLen < $MAX_SIZE_FOR_SIMILAR_TEXT && $cltLen < $MAX_SIZE_FOR_SIMILAR_TEXT) {
        $similarity = 0;
        similar_text($srcBody, $cltBody, $similarity);
        return 100 - $similarity;
    }
    
    // For large functions, use fast heuristic:
    // 1. Length ratio (how different are the character counts?)
    $maxLen = max($srcLen, $cltLen);
    $minLen = min($srcLen, $cltLen);
    $lengthRatio = $maxLen > 0 ? ($maxLen - $minLen) / $maxLen * 100 : 0;
    
    // 2. Line count ratio (how different are the line counts?)
    $maxLines = max($srcLineCount, $cltLineCount, 1);
    $minLines = min($srcLineCount, $cltLineCount, 1);
    $lineRatio = ($maxLines - $minLines) / $maxLines * 100;
    
    // 3. Token sampling — compare first/last 1000 chars as a quick fingerprint
    $sampleSize = 1000;
    $srcHead = substr($srcBody, 0, $sampleSize);
    $cltHead = substr($cltBody, 0, $sampleSize);
    $srcTail = substr($srcBody, -$sampleSize);
    $cltTail = substr($cltBody, -$sampleSize);
    
    $headSim = 0;
    $tailSim = 0;
    similar_text($srcHead, $cltHead, $headSim);
    similar_text($srcTail, $cltTail, $tailSim);
    $sampleChange = 100 - (($headSim + $tailSim) / 2);
    
    // Weighted combination: 30% length, 20% line count, 50% sample comparison
    $bodyChange = ($lengthRatio * 0.30) + ($lineRatio * 0.20) + ($sampleChange * 0.50);
    
    return min($bodyChange, 100);
}

// ============================================================================
// EXTRACTION HELPERS
// ============================================================================

/**
 * Extract PHP function names and bodies using token_get_all
 */
function extractPhpFunctions($filePath) {
    $code = file_get_contents($filePath);
    $tokens = token_get_all($code);
    $functions = [];
    $tokenCount = count($tokens);
    
    for ($i = 0; $i < $tokenCount; $i++) {
        if (!is_array($tokens[$i])) continue;
        
        // Look for 'function' keyword
        if ($tokens[$i][0] === T_FUNCTION) {
            // Skip whitespace to find function name
            $funcName = null;
            for ($j = $i + 1; $j < $tokenCount; $j++) {
                if (is_array($tokens[$j]) && $tokens[$j][0] === T_WHITESPACE) continue;
                if (is_array($tokens[$j]) && $tokens[$j][0] === T_STRING) {
                    $funcName = $tokens[$j][1];
                    break;
                }
                // Anonymous function (closure) — skip it
                if ($tokens[$j] === '(') break;
                break;
            }
            
            if ($funcName === null) continue;
            
            // Find the opening brace and extract body
            $braceDepth = 0;
            $bodyStart = -1;
            $bodyEnd = -1;
            $startLine = $tokens[$i][2];
            
            for ($k = $j; $k < $tokenCount; $k++) {
                $tok = $tokens[$k];
                if ($tok === '{') {
                    if ($bodyStart === -1) $bodyStart = $k;
                    $braceDepth++;
                } elseif ($tok === '}') {
                    $braceDepth--;
                    if ($braceDepth === 0) {
                        $bodyEnd = $k;
                        break;
                    }
                }
            }
            
            // Extract body as string (for comparison)
            $bodyTokens = [];
            if ($bodyStart > 0 && $bodyEnd > $bodyStart) {
                for ($b = $bodyStart; $b <= $bodyEnd; $b++) {
                    if (is_array($tokens[$b])) {
                        // Skip whitespace-only tokens for cleaner comparison
                        if ($tokens[$b][0] !== T_WHITESPACE && $tokens[$b][0] !== T_COMMENT && $tokens[$b][0] !== T_DOC_COMMENT) {
                            $bodyTokens[] = $tokens[$b][1];
                        }
                    } else {
                        $bodyTokens[] = $tokens[$b];
                    }
                }
            }
            
            $bodyStr = implode('', $bodyTokens);
            $bodyLineCount = $bodyEnd > $bodyStart ? countLinesInRange($tokens, $bodyStart, $bodyEnd) : 0;
            
            $functions[$funcName] = [
                'line' => $startLine,
                'body' => $bodyStr,
                'line_count' => $bodyLineCount,
            ];
        }
    }
    
    return $functions;
}

/**
 * Extract JS function names using regex
 */
function extractJsFunctions($filePath) {
    $content = file_get_contents($filePath);
    $functions = [];
    
    // Match: function functionName(
    // Match: var functionName = function(
    // Match: functionName: function(
    $patterns = [
        '/function\s+([a-zA-Z_$][a-zA-Z0-9_$]*)\s*\(/m',
        '/(?:var|let|const)\s+([a-zA-Z_$][a-zA-Z0-9_$]*)\s*=\s*function\s*\(/m',
        '/([a-zA-Z_$][a-zA-Z0-9_$]*)\s*:\s*function\s*\(/m',
    ];
    
    foreach ($patterns as $pattern) {
        if (preg_match_all($pattern, $content, $matches, PREG_OFFSET_CAPTURE)) {
            foreach ($matches[1] as $match) {
                $funcName = $match[0];
                $offset = $match[1];
                $line = substr_count(substr($content, 0, $offset), "\n") + 1;
                
                // Avoid duplicates (same function declared differently)
                if (!isset($functions[$funcName])) {
                    $functions[$funcName] = [
                        'line' => $line,
                    ];
                }
            }
        }
    }
    
    return $functions;
}

/**
 * Get PHP/HTML view files from a directory (non-recursive, ignoring backups)
 */
function getViewFiles($dir) {
    $files = [];
    $handle = opendir($dir);
    if (!$handle) return $files;
    
    while (false !== ($entry = readdir($handle))) {
        if ($entry === '.' || $entry === '..') continue;
        if (is_file($dir . '\\' . $entry)) {
            // Only include .php files (ignore .html index files)
            if (pathinfo($entry, PATHINFO_EXTENSION) === 'php') {
                $files[] = $entry;
            }
        }
    }
    closedir($handle);
    sort($files);
    return $files;
}

// ============================================================================
// CALCULATION HELPERS
// ============================================================================

/**
 * Calculate weighted overall drift from layer results
 */
function calculateWeightedDrift($layers) {
    // Weights from config (hardcoded here for reliability)
    $weights = [
        'controller' => 0.25,
        'model' => 0.30,
        'js' => 0.25,
        'views' => 0.15,
        'css' => 0.05,
    ];
    
    $totalWeight = 0;
    $weightedSum = 0;
    
    foreach ($weights as $layer => $weight) {
        if (isset($layers[$layer])) {
            $drift = $layers[$layer]['drift'];
            // If layer is missing on source side, don't count it
            if (isset($layers[$layer]['status']) && $layers[$layer]['status'] === 'source_missing') {
                continue;
            }
            if (isset($layers[$layer]['status']) && $layers[$layer]['status'] === 'no_css') {
                continue; // No CSS for this module — skip
            }
            $weightedSum += $drift * $weight;
            $totalWeight += $weight;
        }
    }
    
    return $totalWeight > 0 ? round($weightedSum / $totalWeight, 1) : 0;
}

/**
 * Classify drift into Level 1/2/3
 */
function classifyLevel($drift, $thresholds) {
    if ($drift <= $thresholds['level_1_max']) return 1;
    if ($drift <= $thresholds['level_2_max']) return 2;
    return 3;
}

/**
 * Classify drift into Customization (client changed/added) vs Update Lag (source evolved, client didn't get it)
 * source_only = update lag (source has functions the client never received)
 * client_only + modified = customization (client intentionally added or changed code)
 */
function classifyDriftSources($layers) {
    $totalSourceOnly = 0;
    $totalClientOnly = 0;
    $totalModified = 0;
    $totalFunctions = 0;
    
    foreach (['controller', 'model', 'js'] as $layer) {
        if (!isset($layers[$layer]) || !isset($layers[$layer]['source_functions'])) continue;
        if (isset($layers[$layer]['status']) && in_array($layers[$layer]['status'], ['source_missing', 'client_missing'])) continue;
        
        $srcOnly = isset($layers[$layer]['source_only']) ? count($layers[$layer]['source_only']) : 0;
        $cltOnly = isset($layers[$layer]['client_only']) ? count($layers[$layer]['client_only']) : 0;
        $modified = isset($layers[$layer]['modified_count']) ? $layers[$layer]['modified_count'] : 0;
        $total = max($layers[$layer]['source_functions'], $layers[$layer]['client_functions'], 1);
        
        $totalSourceOnly += $srcOnly;
        $totalClientOnly += $cltOnly;
        $totalModified += $modified;
        $totalFunctions += $total;
    }
    
    $totalChanged = $totalSourceOnly + $totalClientOnly + $totalModified;
    
    return [
        'update_lag' => $totalSourceOnly,
        'customization' => $totalClientOnly + $totalModified,
        'update_lag_pct' => $totalChanged > 0 ? round(($totalSourceOnly / $totalChanged) * 100, 0) : 0,
        'customization_pct' => $totalChanged > 0 ? round((($totalClientOnly + $totalModified) / $totalChanged) * 100, 0) : 0,
        'total_divergent_functions' => $totalChanged,
        'source_only' => $totalSourceOnly,
        'client_only' => $totalClientOnly,
        'modified' => $totalModified,
    ];
}

function getLaneName($level) {
    switch ($level) {
        case 1: return 'Express';
        case 2: return 'Adapted';
        case 3: return 'Independent';
        default: return 'Unknown';
    }
}

function getLevelEmoji($level) {
    switch ($level) {
        case 1: return '🟢';
        case 2: return '🟡';
        case 3: return '🔴';
        default: return '⚪';
    }
}

function calculateSummary($results, $thresholds, $weights) {
    $lanes = ['Express' => 0, 'Adapted' => 0, 'Independent' => 0];
    $totalDrift = 0;
    
    foreach ($results as $moduleName => $result) {
        $level = classifyLevel($result['overall_drift'], $thresholds);
        $laneName = getLaneName($level);
        $lanes[$laneName]++;
        $totalDrift += $result['overall_drift'];
    }
    
    $avgDrift = count($results) > 0 ? round($totalDrift / count($results), 1) : 0;
    
    // Estimate effort
    $estimatedHours = ($lanes['Express'] * 3) + ($lanes['Adapted'] * 12) + ($lanes['Independent'] * 35);
    
    return [
        'total_modules' => count($results),
        'average_drift' => $avgDrift,
        'lane_distribution' => $lanes,
        'estimated_hours' => $estimatedHours,
        'estimated_days' => round($estimatedHours / 8, 1),
    ];
}

// ============================================================================
// OUTPUT HELPERS
// ============================================================================

function printResultsTable($results, $thresholds) {
    // Header
    $fmt = "  %-22s %8s %8s %8s %8s %8s │ %8s %7s %s\n";
    printf($fmt, 'Module', 'Ctrl', 'Model', 'JS', 'Views', 'CSS', 'Overall', 'Level', 'Lane');
    echo "  " . str_repeat('─', 100) . "\n";
    
    foreach ($results as $moduleName => $result) {
        $level = classifyLevel($result['overall_drift'], $thresholds);
        $emoji = getLevelEmoji($level);
        $lane = getLaneName($level);
        
        $ctrlDrift = formatDrift($result['layers']['controller']);
        $modelDrift = formatDrift($result['layers']['model']);
        $jsDrift = formatDrift($result['layers']['js']);
        $viewsDrift = formatDrift($result['layers']['views']);
        $cssDrift = formatDrift($result['layers']['css']);
        $overall = sprintf('%.1f%%', $result['overall_drift']);
        
        $displayName = str_replace('_', ' ', $moduleName);
        if (strlen($displayName) > 22) $displayName = substr($displayName, 0, 20) . '..';
        
        printf($fmt, $displayName, $ctrlDrift, $modelDrift, $jsDrift, $viewsDrift, $cssDrift, $overall, "$emoji L$level", $lane);
    }
}

function formatDrift($layerResult) {
    if (isset($layerResult['status'])) {
        switch ($layerResult['status']) {
            case 'client_missing': return 'MISS';
            case 'source_missing': return 'N/A';
            case 'no_css': return '—';
        }
    }
    return sprintf('%.0f%%', $layerResult['drift']);
}

function printLaneSummary($results, $thresholds) {
    $lanes = ['Express' => [], 'Adapted' => [], 'Independent' => []];
    
    foreach ($results as $moduleName => $result) {
        $level = classifyLevel($result['overall_drift'], $thresholds);
        $lanes[getLaneName($level)][] = str_replace('_', ' ', $moduleName);
    }
    
    echo "  LANE DISTRIBUTION\n";
    echo "  " . str_repeat('─', 60) . "\n";
    
    $estimates = ['Express' => '2-4 hrs/module', 'Adapted' => '8-15 hrs/module', 'Independent' => '25-40 hrs/module'];
    
    foreach ($lanes as $lane => $modules) {
        if (empty($modules)) continue;
        $emoji = $lane === 'Express' ? '🟢' : ($lane === 'Adapted' ? '🟡' : '🔴');
        echo "  $emoji $lane (" . count($modules) . " modules — {$estimates[$lane]}):\n";
        foreach ($modules as $m) {
            echo "     • $m\n";
        }
    }
    
    $totalHours = (count($lanes['Express']) * 3) + (count($lanes['Adapted']) * 12) + (count($lanes['Independent']) * 35);
    echo "\n  Estimated total effort: ~{$totalHours} hours (~" . round($totalHours / 8, 1) . " working days)\n";
}

/**
 * Generate full Markdown report
 */
function generateMarkdownReport($results, $summary, $thresholds, $sourcePath, $clientPath, $clientName, $date, $incrementalDiff = []) {
    $md = "# Customization Fingerprint — $clientName\n\n";
    $md .= "> **Scan Date**: $date  \n";
    $md .= "> **Source**: `$sourcePath`  \n";
    $md .= "> **Client**: `$clientPath`  \n";
    $md .= "> **Modules Scanned**: {$summary['total_modules']}  \n";
    $md .= "> **Average Drift**: {$summary['average_drift']}%  \n\n";
    $md .= "---\n\n";
    
    // Summary table
    $md .= "## Summary\n\n";
    $md .= "| Module | Ctrl | Model | JS | Views | CSS | Overall | Level | Lane |\n";
    $md .= "|--------|------|-------|----|-------|-----|---------|-------|------|\n";
    
    foreach ($results as $moduleName => $result) {
        $level = classifyLevel($result['overall_drift'], $thresholds);
        $emoji = getLevelEmoji($level);
        $lane = getLaneName($level);
        $displayName = str_replace('_', ' ', $moduleName);
        
        $ctrlDrift = formatDrift($result['layers']['controller']);
        $modelDrift = formatDrift($result['layers']['model']);
        $jsDrift = formatDrift($result['layers']['js']);
        $viewsDrift = formatDrift($result['layers']['views']);
        $cssDrift = formatDrift($result['layers']['css']);
        $overall = sprintf('%.1f%%', $result['overall_drift']);
        
        $md .= "| $displayName | $ctrlDrift | $modelDrift | $jsDrift | $viewsDrift | $cssDrift | $overall | $emoji L$level | $lane |\n";
    }
    
    $md .= "\n";
    
    // Lane distribution
    $md .= "## Lane Distribution\n\n";
    $lanes = $summary['lane_distribution'];
    $md .= "- 🟢 **Express** (Level 1): {$lanes['Express']} modules — estimated 2-4 hours each\n";
    $md .= "- 🟡 **Adapted** (Level 2): {$lanes['Adapted']} modules — estimated 8-15 hours each\n";
    $md .= "- 🔴 **Independent** (Level 3): {$lanes['Independent']} modules — estimated 25-40 hours each\n\n";
    $md .= "> **Total estimated effort**: ~{$summary['estimated_hours']} hours (~{$summary['estimated_days']} working days)\n\n";
    $md .= "---\n\n";
    
    // Incremental diff section
    if (!empty($incrementalDiff)) {
        $md .= "## Changes Since Last Scan ({$incrementalDiff['previous_date']})\n\n";
        
        if (!empty($incrementalDiff['drift_changes'])) {
            $md .= "### Drift Changes\n\n";
            $md .= "| Module | Previous | Current | Change |\n";
            $md .= "|--------|----------|---------|--------|\n";
            foreach ($incrementalDiff['drift_changes'] as $dc) {
                $arrow = $dc['delta'] > 0 ? '↑' : ($dc['delta'] < 0 ? '↓' : '→');
                $emoji = $dc['delta'] > 2 ? '🔴' : ($dc['delta'] < -2 ? '🟢' : '⬜');
                $md .= "| {$dc['module']} | {$dc['previous']}% | {$dc['current']}% | $emoji {$arrow} {$dc['delta']}% |\n";
            }
            $md .= "\n";
        }
        
        if (!empty($incrementalDiff['new_bugs'])) {
            $md .= "### ⚠️ New Bug Patterns Found\n\n";
            foreach ($incrementalDiff['new_bugs'] as $b) {
                $md .= "- **{$b['pattern_id']}**: {$b['name']} ({$b['instances']} instances)\n";
            }
            $md .= "\n";
        }
        
        if (!empty($incrementalDiff['fixed_bugs'])) {
            $md .= "### ✅ Bug Patterns Fixed\n\n";
            foreach ($incrementalDiff['fixed_bugs'] as $b) {
                $md .= "- ~~{$b['pattern_id']}: {$b['name']}~~ — no longer detected\n";
            }
            $md .= "\n";
        }
        
        if (!empty($incrementalDiff['new_dead_code'])) {
            $md .= "### New Dead Code\n\n";
            foreach ($incrementalDiff['new_dead_code'] as $dc) {
                $md .= "- `{$dc['function']}` in `{$dc['file']}`\n";
            }
            $md .= "\n";
        }
        
        $md .= "---\n\n";
    }
    
    // Per-module detail
    $md .= "## Per-Module Detail\n\n";
    
    foreach ($results as $moduleName => $result) {
        $level = classifyLevel($result['overall_drift'], $thresholds);
        $emoji = getLevelEmoji($level);
        $lane = getLaneName($level);
        $displayName = str_replace('_', ' ', $moduleName);
        $driftPct = sprintf('%.1f%%', $result['overall_drift']);
        
        $md .= "### $displayName ($emoji Level $level — $lane Lane)\n\n";
        
        // v2.0: Drift classification
        if (isset($result['drift_classification'])) {
            $dc = $result['drift_classification'];
            if ($dc['total_divergent_functions'] > 0) {
                $md .= "> **Drift Breakdown**: {$dc['update_lag_pct']}% Update Lag ({$dc['source_only']} source-only) | {$dc['customization_pct']}% Customization ({$dc['client_only']} client-only + {$dc['modified']} modified)\n\n";
            }
        }
        
        foreach (['controller', 'model', 'js'] as $layer) {
            $l = $result['layers'][$layer];
            $layerName = ucfirst($layer);
            
            if (isset($l['status']) && ($l['status'] === 'client_missing' || $l['status'] === 'source_missing')) {
                $md .= "- **$layerName**: ⚠️ {$l['status']} — {$l['detail']}\n";
                continue;
            }
            
            $shared = $l['shared'] ?? 0;
            $srcFunc = $l['source_functions'] ?? 0;
            $cltFunc = $l['client_functions'] ?? 0;
            $srcOnly = isset($l['source_only']) ? count($l['source_only']) : 0;
            $cltOnly = isset($l['client_only']) ? count($l['client_only']) : 0;
            $modified = $l['modified_count'] ?? 0;
            $dft = sprintf('%.1f%%', $l['drift']);
            $srcLines = $l['source_lines'] ?? '?';
            $cltLines = $l['client_lines'] ?? '?';
            
            $md .= "- **$layerName** ($dft drift): $shared/$srcFunc functions shared, $srcOnly source-only, $cltOnly client-only";
            if ($modified > 0) $md .= ", $modified significantly modified";
            $md .= " | Lines: $srcLines → $cltLines";
            $md .= "\n";
            
            // List source-only and client-only functions if any
            if ($srcOnly > 0 && $srcOnly <= 10) {
                $md .= "  - Source-only: `" . implode('`, `', $l['source_only']) . "`\n";
            } elseif ($srcOnly > 10) {
                $md .= "  - Source-only: " . implode(', ', array_map(function($f) { return "`$f`"; }, array_slice($l['source_only'], 0, 5))) . " ... and " . ($srcOnly - 5) . " more\n";
            }
            if ($cltOnly > 0 && $cltOnly <= 10) {
                $md .= "  - Client-only: `" . implode('`, `', $l['client_only']) . "`\n";
            } elseif ($cltOnly > 10) {
                $md .= "  - Client-only: " . implode(', ', array_map(function($f) { return "`$f`"; }, array_slice($l['client_only'], 0, 5))) . " ... and " . ($cltOnly - 5) . " more\n";
            }
        }
        
        // Views
        $v = $result['layers']['views'];
        if (isset($v['status']) && $v['status'] === 'client_missing') {
            $md .= "- **Views**: ⚠️ Client view directory missing\n";
        } elseif (isset($v['source_files'])) {
            $vDrift = sprintf('%.1f%%', $v['drift']);
            $md .= "- **Views** ($vDrift drift): {$v['shared']}/{$v['source_files']} files shared";
            if (!empty($v['source_only'])) $md .= ", " . count($v['source_only']) . " source-only";
            if (!empty($v['client_only'])) $md .= ", " . count($v['client_only']) . " client-only";
            if (($v['significantly_changed'] ?? 0) > 0) $md .= ", {$v['significantly_changed']} significantly changed";
            $md .= "\n";
        }
        
        // CSS
        $c = $result['layers']['css'];
        if (isset($c['status']) && $c['status'] === 'no_css') {
            $md .= "- **CSS**: No CSS file for this module\n";
        } elseif (isset($c['status']) && $c['status'] === 'client_missing') {
            $md .= "- **CSS**: ⚠️ Client CSS missing\n";
        } else {
            $cDrift = sprintf('%.1f%%', $c['drift']);
            $md .= "- **CSS** ($cDrift drift)";
            if (isset($c['source_lines'])) $md .= " | Lines: {$c['source_lines']} → {$c['client_lines']}";
            $md .= "\n";
        }
        
        $md .= "\n";
    }
    
    return $md;
}

// ============================================================================
// INCREMENTAL SCAN FUNCTIONS
// ============================================================================

/**
 * Load the most recent previous scan for this client
 * Skips today's scan to avoid comparing against itself
 */
function loadPreviousScan($clientName, $reportsDir, $currentDate) {
    // Find all JSON reports for this client
    $pattern = $reportsDir . '/fingerprint_' . $clientName . '_*.json';
    $files = glob($pattern);
    
    if (empty($files)) return null;
    
    // Sort by date (filename contains date), newest first
    rsort($files);
    
    // Find the first file that's NOT today's scan
    foreach ($files as $file) {
        // Extract date from filename: fingerprint_clientname_YYYY-MM-DD.json
        if (preg_match('/(\d{4}-\d{2}-\d{2})\.json$/', $file, $matches)) {
            $fileDate = $matches[1];
            if ($fileDate !== $currentDate) {
                $content = file_get_contents($file);
                $data = json_decode($content, true);
                if ($data) return $data;
            }
        }
    }
    
    return null;
}

/**
 * Compute incremental diff between previous and current scan
 */
function computeIncrementalDiff($previousScan, $currentResults, $currentSummary, $currentBugs, $currentDeadCode) {
    $diff = [
        'previous_date' => $previousScan['metadata']['scan_date'] ?? 'unknown',
        'drift_changes' => [],
        'new_bugs' => [],
        'fixed_bugs' => [],
        'new_dead_code' => [],
        'summary_delta' => [],
    ];
    
    // 1. Module drift changes
    $prevModules = $previousScan['modules'] ?? [];
    foreach ($currentResults as $moduleName => $currentModule) {
        $currentDrift = $currentModule['overall_drift'];
        $prevDrift = isset($prevModules[$moduleName]) ? $prevModules[$moduleName]['overall_drift'] : null;
        
        if ($prevDrift !== null) {
            $delta = round($currentDrift - $prevDrift, 1);
            if (abs($delta) >= 0.5) { // Only report changes >= 0.5%
                $diff['drift_changes'][] = [
                    'module' => $moduleName,
                    'previous' => $prevDrift,
                    'current' => $currentDrift,
                    'delta' => $delta,
                ];
            }
        } else {
            // New module scanned
            $diff['drift_changes'][] = [
                'module' => $moduleName,
                'previous' => 'NEW',
                'current' => $currentDrift,
                'delta' => 0,
            ];
        }
    }
    
    // 2. Bug pattern changes
    $prevBugIds = [];
    if (isset($previousScan['bug_patterns'])) {
        foreach ($previousScan['bug_patterns'] as $bug) {
            $prevBugIds[$bug['pattern_id']] = $bug;
        }
    }
    
    $currentBugIds = [];
    foreach ($currentBugs as $bug) {
        $currentBugIds[$bug['pattern_id']] = $bug;
    }
    
    // New bugs (in current but not in previous)
    foreach ($currentBugIds as $id => $bug) {
        if (!isset($prevBugIds[$id])) {
            $diff['new_bugs'][] = $bug;
        }
    }
    
    // Fixed bugs (in previous but not in current)
    foreach ($prevBugIds as $id => $bug) {
        if (!isset($currentBugIds[$id])) {
            $diff['fixed_bugs'][] = $bug;
        }
    }
    
    // 3. Dead code changes
    $prevDeadFuncs = [];
    if (isset($previousScan['dead_code']['date_suffixed'])) {
        foreach ($previousScan['dead_code']['date_suffixed'] as $dc) {
            $prevDeadFuncs[$dc['function']] = $dc;
        }
    }
    
    if (isset($currentDeadCode['date_suffixed'])) {
        foreach ($currentDeadCode['date_suffixed'] as $dc) {
            if (!isset($prevDeadFuncs[$dc['function']])) {
                $diff['new_dead_code'][] = $dc;
            }
        }
    }
    
    // 4. Summary delta
    if (isset($previousScan['summary'])) {
        $prevAvg = $previousScan['summary']['average_drift'] ?? 0;
        $curAvg = $currentSummary['average_drift'] ?? 0;
        $diff['summary_delta'] = [
            'avg_drift_change' => round($curAvg - $prevAvg, 1),
            'previous_avg' => $prevAvg,
            'current_avg' => $curAvg,
        ];
    }
    
    return $diff;
}

/**
 * Print incremental diff to console
 */
function printIncrementalDiff($diff) {
    // Summary change
    if (!empty($diff['summary_delta'])) {
        $sd = $diff['summary_delta'];
        $arrow = $sd['avg_drift_change'] > 0 ? '↑' : ($sd['avg_drift_change'] < 0 ? '↓' : '→');
        $word = $sd['avg_drift_change'] > 0 ? 'INCREASED' : ($sd['avg_drift_change'] < 0 ? 'DECREASED' : 'UNCHANGED');
        echo "  Average drift: {$sd['previous_avg']}% → {$sd['current_avg']}% ($arrow $word by {$sd['avg_drift_change']}%)\n\n";
    }
    
    // Module drift changes
    if (!empty($diff['drift_changes'])) {
        echo "  Module changes:\n";
        foreach ($diff['drift_changes'] as $dc) {
            if ($dc['previous'] === 'NEW') {
                echo "    ➕ {$dc['module']}: NEW ({$dc['current']}%)\n";
            } else {
                $emoji = $dc['delta'] > 2 ? '🔴' : ($dc['delta'] < -2 ? '🟢' : '⬜');
                $sign = $dc['delta'] > 0 ? '+' : '';
                echo "    $emoji {$dc['module']}: {$dc['previous']}% → {$dc['current']}% ({$sign}{$dc['delta']}%)\n";
            }
        }
        echo "\n";
    } else {
        echo "  🟢 No drift changes detected.\n\n";
    }
    
    // New bugs
    if (!empty($diff['new_bugs'])) {
        echo "  ⚠️  NEW bugs found:\n";
        foreach ($diff['new_bugs'] as $b) {
            echo "    • {$b['pattern_id']}: {$b['name']} ({$b['instances']} instances)\n";
        }
        echo "\n";
    }
    
    // Fixed bugs
    if (!empty($diff['fixed_bugs'])) {
        echo "  ✅ FIXED bugs (no longer detected):\n";
        foreach ($diff['fixed_bugs'] as $b) {
            echo "    • {$b['pattern_id']}: {$b['name']}\n";
        }
        echo "\n";
    }
    
    // New dead code
    if (!empty($diff['new_dead_code'])) {
        echo "  🟡 NEW dead code:\n";
        foreach ($diff['new_dead_code'] as $dc) {
            echo "    • {$dc['function']}() in {$dc['file']}\n";
        }
        echo "\n";
    }
    
    // Summary
    $totalChanges = count($diff['drift_changes']) + count($diff['new_bugs']) + count($diff['fixed_bugs']) + count($diff['new_dead_code']);
    if ($totalChanges === 0) {
        echo "  ✅ No significant changes since last scan.\n";
    }
}

// ============================================================================
// v2.0 FUNCTIONS — Bug Scan, Version Detect, Dead Code, Fix Tracker, Batch
// ============================================================================

/**
 * Scan client codebase for known bug patterns from bug_patterns.json
 */
function scanBugPatterns($clientPath, $paths, $securityOnly = false) {
    global $scriptDir;
    $patternsFile = $scriptDir . '/bug_patterns.json';
    
    if (!file_exists($patternsFile)) {
        echo "  WARNING: bug_patterns.json not found at $patternsFile\n";
        return [];
    }
    
    $patternConfig = json_decode(file_get_contents($patternsFile), true);
    if (!$patternConfig || !isset($patternConfig['patterns'])) {
        echo "  WARNING: Invalid bug_patterns.json\n";
        return [];
    }
    
    $patterns = $patternConfig['patterns'];
    $results = [];
    $severityFilter = $securityOnly ? ['CRITICAL'] : null;
    
    $scanDirs = [
        'controllers' => $clientPath . '\\' . $paths['controller_dir'],
        'models' => $clientPath . '\\' . $paths['model_dir'],
    ];
    
    foreach ($patterns as $pattern) {
        // Security mode: only scan CRITICAL patterns
        if ($severityFilter && !in_array($pattern['severity'], $severityFilter)) {
            continue;
        }
        
        $matches = [];
        $regex = '/' . $pattern['regex'] . '/i';
        
        // Scan relevant directories
        foreach ($scanDirs as $dirType => $dir) {
            if (!is_dir($dir)) continue;
            
            // Check if this pattern applies to this dir type
            $applies = false;
            foreach ($pattern['files'] as $filePattern) {
                if (strpos($filePattern, $dirType) !== false || strpos($filePattern, '*') !== false) {
                    $applies = true;
                    break;
                }
            }
            if (!$applies) continue;
            
            $phpFiles = glob($dir . '\\*.php');
            foreach ($phpFiles as $file) {
                $content = file_get_contents($file);
                $lines = explode("\n", $content);
                
                foreach ($lines as $lineNum => $line) {
                    if (@preg_match($regex, $line)) {
                        $matches[] = [
                            'file' => basename($file),
                            'line' => $lineNum + 1,
                            'content' => trim(substr($line, 0, 120)),
                        ];
                    }
                }
            }
        }
        
        if (!empty($matches)) {
            $results[] = [
                'pattern_id' => $pattern['id'],
                'name' => $pattern['name'],
                'severity' => $pattern['severity'],
                'description' => $pattern['description'],
                'instances' => count($matches),
                'matches' => $matches,
            ];
        }
    }
    
    return $results;
}

/**
 * Print bug scan results to console
 */
function printBugScanResults($results) {
    if (empty($results)) {
        echo "  ✅ No known bug patterns detected.\n";
        return;
    }
    
    $totalInstances = 0;
    $criticalCount = 0;
    
    foreach ($results as $r) {
        $emoji = $r['severity'] === 'CRITICAL' ? '🔴' : ($r['severity'] === 'HIGH' ? '🟡' : '🟢');
        echo "  $emoji {$r['pattern_id']}: {$r['name']} ({$r['severity']})\n";
        echo "     {$r['instances']} instance(s) found\n";
        
        // Show up to 3 examples
        $shown = 0;
        foreach ($r['matches'] as $m) {
            if ($shown >= 3) {
                echo "     ... and " . ($r['instances'] - 3) . " more\n";
                break;
            }
            echo "     • {$m['file']}:{$m['line']} → {$m['content']}\n";
            $shown++;
        }
        echo "\n";
        
        $totalInstances += $r['instances'];
        if ($r['severity'] === 'CRITICAL') $criticalCount += $r['instances'];
    }
    
    echo "  TOTAL: " . count($results) . " patterns, $totalInstances instances";
    if ($criticalCount > 0) echo " ($criticalCount CRITICAL)";
    echo "\n";
}

/**
 * Detect which version of source the client was forked from
 * Uses marker functions that were added at known dates
 */
function detectClientVersion($clientPath, $paths, $versionMarkers) {
    $controllerDir = $clientPath . '\\' . $paths['controller_dir'];
    $modelDir = $clientPath . '\\' . $paths['model_dir'];
    
    $results = [];
    
    foreach ($versionMarkers as $version => $marker) {
        $found = 0;
        $missing = 0;
        $missingFunctions = [];
        
        foreach ($marker['functions'] as $funcName) {
            $exists = false;
            
            // Search in all PHP files in controllers and models
            foreach ([$controllerDir, $modelDir] as $dir) {
                if (!is_dir($dir)) continue;
                $files = glob($dir . '\\*.php');
                foreach ($files as $file) {
                    $content = file_get_contents($file);
                    if (preg_match('/function\s+' . preg_quote($funcName, '/') . '\s*\(/', $content)) {
                        $exists = true;
                        break 2;
                    }
                }
            }
            
            if ($exists) {
                $found++;
            } else {
                $missing++;
                $missingFunctions[] = $funcName;
            }
        }
        
        $total = count($marker['functions']);
        $coverage = $total > 0 ? round(($found / $total) * 100, 0) : 0;
        
        $results[$version] = [
            'description' => $marker['description'],
            'total_markers' => $total,
            'found' => $found,
            'missing' => $missing,
            'coverage' => $coverage,
            'missing_functions' => $missingFunctions,
        ];
    }
    
    // Determine estimated version
    $estimatedVersion = 'pre-2025_Q4'; // Default
    foreach ($results as $ver => $data) {
        if ($data['coverage'] >= 80) {
            $estimatedVersion = $ver;
        }
    }
    
    return [
        'estimated_version' => $estimatedVersion,
        'markers' => $results,
    ];
}

/**
 * Print version detection results
 */
function printVersionResults($versionInfo) {
    echo "  Estimated client base: " . strtoupper($versionInfo['estimated_version']) . "\n\n";
    
    foreach ($versionInfo['markers'] as $ver => $data) {
        $bar = str_repeat('█', intval($data['coverage'] / 5)) . str_repeat('░', 20 - intval($data['coverage'] / 5));
        $emoji = $data['coverage'] >= 80 ? '✅' : ($data['coverage'] >= 50 ? '🟡' : '❌');
        echo "  $emoji $ver ({$data['coverage']}%) $bar\n";
        echo "     {$data['description']}\n";
        if (!empty($data['missing_functions'])) {
            echo "     Missing: " . implode(', ', array_slice($data['missing_functions'], 0, 5));
            if (count($data['missing_functions']) > 5) echo " +" . (count($data['missing_functions']) - 5) . " more";
            echo "\n";
        }
    }
}

/**
 * Detect dead code: date-suffixed methods, commented functions
 */
function detectDeadCode($clientPath, $paths) {
    $results = ['date_suffixed' => [], 'summary' => ['total_dead' => 0]];
    
    $dirs = [
        $clientPath . '\\' . $paths['controller_dir'],
        $clientPath . '\\' . $paths['model_dir'],
    ];
    
    // Scan for date-suffixed function copies
    $datePattern = '/function\s+(\w+_\d{2}_\d{2}_\d{4})\s*\(/';
    
    foreach ($dirs as $dir) {
        if (!is_dir($dir)) continue;
        $files = glob($dir . '\\*.php');
        
        foreach ($files as $file) {
            $content = file_get_contents($file);
            $lines = explode("\n", $content);
            
            foreach ($lines as $lineNum => $line) {
                if (preg_match($datePattern, $line, $matches)) {
                    $funcName = $matches[1];
                    
                    // Extract the base function name (without date suffix)
                    $baseName = preg_replace('/_\d{2}_\d{2}_\d{4}$/', '', $funcName);
                    
                    // Check if base function also exists
                    $baseExists = preg_match('/function\s+' . preg_quote($baseName, '/') . '\s*\(/', $content);
                    
                    $results['date_suffixed'][] = [
                        'file' => basename($file),
                        'line' => $lineNum + 1,
                        'function' => $funcName,
                        'base_function' => $baseName,
                        'base_exists' => (bool)$baseExists,
                        'risk' => $baseExists ? 'HIGH — both old and new exist, old may be called' : 'MEDIUM — old copy, base missing',
                    ];
                    $results['summary']['total_dead']++;
                }
            }
        }
    }
    
    return $results;
}

/**
 * Print dead code results
 */
function printDeadCodeResults($results) {
    if (empty($results['date_suffixed'])) {
        echo "  ✅ No date-suffixed dead code detected.\n";
        return;
    }
    
    echo "  Found {$results['summary']['total_dead']} date-suffixed method copies:\n\n";
    
    foreach ($results['date_suffixed'] as $dc) {
        $emoji = $dc['base_exists'] ? '🔴' : '🟡';
        echo "  $emoji {$dc['file']}:{$dc['line']}\n";
        echo "     {$dc['function']}() — base: {$dc['base_function']}()\n";
        echo "     {$dc['risk']}\n\n";
    }
}

/**
 * Cross-reference fix recipes against client code
 * Shows which recipes are applicable (client still has the bug)
 */
function crossReferenceRecipes($clientPath, $sourcePath) {
    $recipesDir = $sourcePath . '\\.agent\\skills\\bug-fix-engine\\recipes';
    
    if (!is_dir($recipesDir)) {
        echo "  No recipes directory found at: $recipesDir\n";
        return ['applicable' => [], 'not_applicable' => []];
    }
    
    $recipeFiles = glob($recipesDir . '\\*.md');
    $results = ['applicable' => [], 'not_applicable' => [], 'total_recipes' => count($recipeFiles)];
    
    foreach ($recipeFiles as $file) {
        $content = file_get_contents($file);
        $recipeName = basename($file, '.md');
        
        // Extract detection pattern from recipe (## Detection section)
        $detectionPattern = null;
        if (preg_match('/## Detection\s*\n```(?:command|bash|powershell)?\s*\n(.*?)\n```/s', $content, $matches)) {
            // Try to extract grep pattern
            $grepLine = trim($matches[1]);
            if (preg_match('/grep.*"([^"]+)"/', $grepLine, $grepMatch)) {
                $detectionPattern = $grepMatch[1];
            } elseif (preg_match("/grep.*'([^']+)'/", $grepLine, $grepMatch)) {
                $detectionPattern = $grepMatch[1];
            }
        }
        
        // Extract symptom
        $symptom = '';
        if (preg_match('/## Symptom\s*\n(.*?)(?=\n##)/s', $content, $matches)) {
            $symptom = trim(substr($matches[1], 0, 100));
        }
        
        $applicable = false;
        $instanceCount = 0;
        
        if ($detectionPattern) {
            // Search client code for the pattern
            $searchDirs = [
                $clientPath . '\\admin\\application\\controllers',
                $clientPath . '\\admin\\application\\models',
            ];
            
            foreach ($searchDirs as $dir) {
                if (!is_dir($dir)) continue;
                $files = glob($dir . '\\*.php');
                foreach ($files as $phpFile) {
                    $fileContent = file_get_contents($phpFile);
                    $count = substr_count($fileContent, $detectionPattern);
                    if ($count > 0) {
                        $applicable = true;
                        $instanceCount += $count;
                    }
                }
            }
        }
        
        $entry = [
            'recipe' => $recipeName,
            'symptom' => $symptom,
            'detection_pattern' => $detectionPattern,
            'instances' => $instanceCount,
        ];
        
        if ($applicable) {
            $results['applicable'][] = $entry;
        } else {
            $results['not_applicable'][] = $entry;
        }
    }
    
    return $results;
}

/**
 * Print recipe cross-reference results
 */
function printRecipeResults($results) {
    if ($results['total_recipes'] === 0) {
        echo "  No recipes found. Create recipes using the bug-fix-engine skill.\n";
        return;
    }
    
    $applicable = count($results['applicable']);
    echo "  Recipes: {$results['total_recipes']} total, $applicable applicable to this client\n\n";
    
    if (!empty($results['applicable'])) {
        echo "  ⚠️  APPLICABLE (client still has these bugs):\n";
        foreach ($results['applicable'] as $r) {
            echo "     • {$r['recipe']} — {$r['instances']} instance(s)\n";
            if ($r['symptom']) echo "       {$r['symptom']}\n";
        }
    }
    
    if (!empty($results['not_applicable'])) {
        echo "\n  ✅ NOT APPLICABLE (already fixed or not present):\n";
        foreach ($results['not_applicable'] as $r) {
            echo "     • {$r['recipe']}\n";
        }
    }
}

/**
 * Batch scan all clients from clients.json
 */
function batchScan($sourcePath, $paths, $modules, $thresholds, $weights, $showDetail, $bugScan, $versionDetect) {
    global $scriptDir, $reportsDir;
    $clientsFile = $scriptDir . '/clients.json';
    
    if (!file_exists($clientsFile)) {
        echo "  ERROR: clients.json not found at $clientsFile\n";
        return;
    }
    
    $clientsConfig = json_decode(file_get_contents($clientsFile), true);
    $clients = array_filter($clientsConfig['clients'], function($c) { return $c['active']; });
    
    echo "  Batch scanning " . count($clients) . " clients...\n\n";
    
    $allResults = [];
    
    foreach ($clients as $client) {
        $clientPath = $client['path'];
        $clientName = $client['name'];
        
        if (!is_dir($clientPath)) {
            echo "  ⚠️  SKIP: $clientName — path not found: $clientPath\n";
            continue;
        }
        
        echo "  ─── $clientName ───\n";
        
        $moduleResults = [];
        foreach ($modules as $moduleName => $moduleFiles) {
            $result = scanModule($moduleName, $moduleFiles, $sourcePath, $clientPath, $paths, $thresholds, $showDetail);
            $moduleResults[$moduleName] = $result;
        }
        
        $summary = calculateSummary($moduleResults, $thresholds, $weights);
        
        $clientResult = [
            'client' => $clientName,
            'path' => $clientPath,
            'summary' => $summary,
            'modules' => $moduleResults,
        ];
        
        // Bug scan if enabled
        if ($bugScan) {
            $clientResult['bugs'] = scanBugPatterns($clientPath, $paths, false);
        }
        
        $allResults[$clientName] = $clientResult;
        
        $l1 = $summary['lane_distribution']['Express'];
        $l2 = $summary['lane_distribution']['Adapted'];
        $l3 = $summary['lane_distribution']['Independent'];
        $avgDrift = $summary['average_drift'];
        $bugCount = isset($clientResult['bugs']) ? array_sum(array_column($clientResult['bugs'], 'instances')) : 0;
        
        echo "    Avg drift: {$avgDrift}% | L1: $l1, L2: $l2, L3: $l3";
        if ($bugCount > 0) echo " | Bugs: $bugCount";
        echo "\n\n";
    }
    
    // Save consolidated batch report
    $date = date('Y-m-d');
    $batchReport = [
        'metadata' => [
            'tool' => 'Customization Fingerprint v2.0 — Batch',
            'scan_date' => $date,
            'clients_scanned' => count($allResults),
        ],
        'clients' => $allResults,
    ];
    
    $batchFile = "$reportsDir/fingerprint_BATCH_{$date}.json";
    file_put_contents($batchFile, json_encode($batchReport, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    echo "  Batch report: $batchFile\n";
    
    // Print cross-client comparison matrix
    echo "\n  CROSS-CLIENT COMPARISON:\n";
    $fmt = "  %-22s %8s %8s %8s %8s %10s\n";
    printf($fmt, 'Client', 'Avg%', 'L1', 'L2', 'L3', 'Bugs');
    echo "  " . str_repeat('─', 64) . "\n";
    
    foreach ($allResults as $name => $data) {
        $s = $data['summary'];
        $bugTotal = isset($data['bugs']) ? array_sum(array_column($data['bugs'], 'instances')) : '-';
        printf($fmt, $name, $s['average_drift'].'%', $s['lane_distribution']['Express'], $s['lane_distribution']['Adapted'], $s['lane_distribution']['Independent'], $bugTotal);
    }
    
    return $allResults;
}

// ============================================================================
// UTILITY FUNCTIONS
// ============================================================================

function countFileLines($filePath) {
    if (!file_exists($filePath)) return 0;
    $count = 0;
    $handle = fopen($filePath, 'rb');
    while (!feof($handle)) {
        $count += substr_count(fread($handle, 8192), "\n");
    }
    fclose($handle);
    return $count;
}

function countLinesInRange($tokens, $start, $end) {
    $startLine = null;
    $endLine = null;
    for ($i = $start; $i <= $end; $i++) {
        if (is_array($tokens[$i]) && isset($tokens[$i][2])) {
            if ($startLine === null) $startLine = $tokens[$i][2];
            $endLine = $tokens[$i][2];
        }
    }
    return ($startLine !== null && $endLine !== null) ? ($endLine - $startLine + 1) : 0;
}

function parseArguments($argv) {
    $args = [];
    foreach ($argv as $arg) {
        if (strpos($arg, '--') === 0) {
            $arg = substr($arg, 2);
            if (strpos($arg, '=') !== false) {
                list($key, $value) = explode('=', $arg, 2);
                $args[$key] = trim($value, '"\'');
            } else {
                $args[$arg] = true;
            }
        }
    }
    return $args;
}
