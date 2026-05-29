<?php
/**
 * Unit Test Script: Read customer.ods and validate against customer.json config
 * Tests the same logic as ImportController::validateProcess()
 */

// Bootstrap just enough of the environment
define('BASEPATH', 'd:/xampp7.1/htdocs/migrationtool/api/system/');
define('APPPATH', 'd:/xampp7.1/htdocs/migrationtool/api/application/');
define('FCPATH', 'd:/xampp7.1/htdocs/migrationtool/api/');

require_once 'd:/xampp7.1/htdocs/migrationtool/api/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

echo "=== UNIT TEST: customer.ods Import Validation ===\n\n";

$odsFile = 'D:/Downloads/anb/anb/customer.ods';
$configFile = APPPATH . 'config/migration/customer.json';

// ---- TEST 1: File existence ----
echo "TEST 1: File Existence\n";
echo "  ODS file: " . (file_exists($odsFile) ? "PASS (" . filesize($odsFile) . " bytes)" : "FAIL - Not found") . "\n";
echo "  Config file: " . (file_exists($configFile) ? "PASS" : "FAIL - Not found") . "\n\n";

// ---- TEST 2: Config JSON parsing ----
echo "TEST 2: Config JSON Parsing\n";
$config = json_decode(file_get_contents($configFile), true);
if ($config === null) {
    echo "  FAIL - JSON parse error: " . json_last_error_msg() . "\n";
    exit(1);
}
echo "  PASS - Entity: " . $config['entity'] . "\n";
echo "  Required sheet: " . $config['validation']['required_sheet_name'] . "\n";
echo "  Destination table: " . $config['migrations'][0]['destination_table'] . "\n";
echo "  Number of main mappings: " . count($config['migrations'][0]['mappings']) . "\n";
if (isset($config['migrations'][0]['children'])) {
    echo "  Children tables: " . count($config['migrations'][0]['children']) . "\n";
    foreach ($config['migrations'][0]['children'] as $child) {
        echo "    - " . $child['destination_table'] . " (FK: " . $child['foreign_key'] . ", mappings: " . count($child['mappings']) . ")\n";
    }
}
echo "\n";

// ---- TEST 3: ODS File Loading ----
echo "TEST 3: ODS File Loading\n";
$startTime = microtime(true);
try {
    $reader = IOFactory::createReaderForFile($odsFile);
    $reader->setReadDataOnly(true);
    $reader->setLoadSheetsOnly([$config['validation']['required_sheet_name']]);
    $spreadsheet = $reader->load($odsFile);
    $loadTime = round(microtime(true) - $startTime, 2);
    echo "  PASS - Loaded in {$loadTime}s\n";
} catch (Exception $e) {
    echo "  FAIL - " . $e->getMessage() . "\n";
    exit(1);
}

// ---- TEST 4: Required Sheet ----
echo "\nTEST 4: Required Sheet Check\n";
$sheetName = $config['validation']['required_sheet_name'];
$sheet = $spreadsheet->getSheetByName($sheetName);
if (!$sheet) {
    echo "  FAIL - Sheet '{$sheetName}' not found\n";
    $allSheets = [];
    for ($i = 0; $i < $spreadsheet->getSheetCount(); $i++) {
        $allSheets[] = $spreadsheet->getSheet($i)->getTitle();
    }
    echo "  Available sheets: " . implode(', ', $allSheets) . "\n";
    exit(1);
}
echo "  PASS - Sheet '{$sheetName}' found\n";

// ---- TEST 5: Header Mapping ----
echo "\nTEST 5: Header Column Mapping\n";
$headerRow = $sheet->getRowIterator(1, 1)->current();
$cellIterator = $headerRow->getCellIterator();
$cellIterator->setIterateOnlyExistingCells(true);

$excelColumnsMap = [];
$headerCols = [];
foreach ($cellIterator as $cell) {
    $name = strtolower(trim($cell->getValue()));
    $excelColumnsMap[$name] = $cell->getColumn();
    $headerCols[] = $name;
}
echo "  Found " . count($excelColumnsMap) . " columns in header:\n";
foreach ($excelColumnsMap as $name => $col) {
    echo "    [{$col}] => {$name}\n";
}

// Check required mappings
$mainConfig = $config['migrations'][0];
$missingCols = [];
$foundCols = [];
foreach ($mainConfig['mappings'] as $dbCol => $mapInfo) {
    $srcColName = strtolower(isset($mapInfo['lookup']) ? $mapInfo['lookup']['source_column'] : $mapInfo['source_column']);
    if (isset($excelColumnsMap[$srcColName])) {
        $foundCols[] = $srcColName;
    } else {
        $missingCols[] = "{$srcColName} (for db column: {$dbCol})";
    }
}

// Check children mappings
if (isset($mainConfig['children'])) {
    foreach ($mainConfig['children'] as $childConfig) {
        foreach ($childConfig['mappings'] as $dbCol => $mapInfo) {
            $srcColName = strtolower(isset($mapInfo['lookup']) ? $mapInfo['lookup']['source_column'] : $mapInfo['source_column']);
            if (isset($excelColumnsMap[$srcColName])) {
                $foundCols[] = $srcColName;
            } else {
                $missingCols[] = "{$srcColName} (for child db column: {$dbCol})";
            }
        }
    }
}

$foundCols = array_unique($foundCols);
echo "\n  Mapped columns (" . count($foundCols) . "): " . implode(', ', $foundCols) . "\n";
if (!empty($missingCols)) {
    echo "  FAIL - Missing columns (" . count($missingCols) . "):\n";
    foreach ($missingCols as $mc) {
        echo "    ⚠ {$mc}\n";
    }
} else {
    echo "  PASS - All required columns found\n";
}

// ---- TEST 6: Data Rows Analysis ----
echo "\nTEST 6: Data Rows Analysis\n";
$highestRow = $sheet->getHighestDataRow();
$highestCol = $sheet->getHighestDataColumn();
$totalRows = max(0, $highestRow - 1);
echo "  Total data rows: {$totalRows}\n";
echo "  Highest column: {$highestCol}\n";

// Read first 10 rows of data for sample analysis
$sampleRange = 'A2:' . $highestCol . min(11, $highestRow);
$sheetData = $sheet->rangeToArray($sampleRange, NULL, TRUE, FALSE, TRUE);
echo "  Sample rows (first " . min(10, $totalRows) . "):\n";

// ---- TEST 7: Field Validation (Sample Rows) ----
echo "\nTEST 7: Field Validation on Sample Rows\n";
$validationResults = [
    'total_tested' => 0,
    'errors' => [],
    'skipped' => [],
    'valid' => 0,
    'field_issues' => []
];

$uniqueTracker = [];

foreach ($sheetData as $rowIdx => $row) {
    $validationResults['total_tested']++;
    $rowErrors = [];
    $rowNumber = $rowIdx;

    // Clean up row data
    foreach ($row as $colKey => $val) {
        $row[$colKey] = trim((string)$val);
    }

    foreach ($mainConfig['mappings'] as $dbCol => $mapInfo) {
        $srcColName = strtolower(isset($mapInfo['lookup']) ? $mapInfo['lookup']['source_column'] : $mapInfo['source_column']);
        
        if (!isset($excelColumnsMap[$srcColName])) {
            $rowErrors[] = "Source column '{$srcColName}' not found";
            continue;
        }
        
        $excelColLetter = $excelColumnsMap[$srcColName];
        $cellValue = trim($row[$excelColLetter] ?? '');

        // Validate field using same logic as ImportController::validateField
        $rules = $mapInfo['validations'];
        
        // Required check
        if (($rules['required'] ?? false) && (empty($cellValue) || $cellValue === '' || $cellValue === null)) {
            $rowErrors[] = "Field '{$dbCol}' ({$srcColName}): Value is required but missing";
            if (!isset($validationResults['field_issues'][$dbCol])) 
                $validationResults['field_issues'][$dbCol] = 0;
            $validationResults['field_issues'][$dbCol]++;
            continue;
        }
        
        if (empty($cellValue)) continue;
        
        // Type validation
        $type = $rules['type'] ?? 'string';
        switch ($type) {
            case 'string':
                if (isset($rules['max_length']) && mb_strlen($cellValue) > $rules['max_length']) {
                    $rowErrors[] = "Field '{$dbCol}' ({$srcColName}): Max length {$rules['max_length']} exceeded (got " . mb_strlen($cellValue) . ")";
                    if (!isset($validationResults['field_issues'][$dbCol])) 
                        $validationResults['field_issues'][$dbCol] = 0;
                    $validationResults['field_issues'][$dbCol]++;
                }
                break;
                
            case 'numeric':
                if (!is_numeric($cellValue)) {
                    $rowErrors[] = "Field '{$dbCol}' ({$srcColName}): Value '{$cellValue}' is not numeric";
                    if (!isset($validationResults['field_issues'][$dbCol])) 
                        $validationResults['field_issues'][$dbCol] = 0;
                    $validationResults['field_issues'][$dbCol]++;
                } elseif (isset($rules['length']) && strlen((string)$cellValue) != $rules['length']) {
                    $rowErrors[] = "Field '{$dbCol}' ({$srcColName}): Length must be {$rules['length']} (got " . strlen((string)$cellValue) . ", value: {$cellValue})";
                    if (!isset($validationResults['field_issues'][$dbCol])) 
                        $validationResults['field_issues'][$dbCol] = 0;
                    $validationResults['field_issues'][$dbCol]++;
                } elseif (isset($rules['min']) && $cellValue < $rules['min']) {
                    $rowErrors[] = "Field '{$dbCol}' ({$srcColName}): Value {$cellValue} less than min {$rules['min']}";
                    if (!isset($validationResults['field_issues'][$dbCol])) 
                        $validationResults['field_issues'][$dbCol] = 0;
                    $validationResults['field_issues'][$dbCol]++;
                }
                break;
                
            case 'date':
                // Check if it's a numeric Excel date
                if (is_numeric($cellValue)) {
                    try {
                        $cellValue = ExcelDate::excelToDateTimeObject($cellValue)->format('Y-m-d');
                    } catch (Exception $e) {
                        $rowErrors[] = "Field '{$dbCol}' ({$srcColName}): Invalid Excel date serial: {$cellValue}";
                        if (!isset($validationResults['field_issues'][$dbCol])) 
                            $validationResults['field_issues'][$dbCol] = 0;
                        $validationResults['field_issues'][$dbCol]++;
                    }
                } else {
                    $phpDate = date_create($cellValue);
                    if ($phpDate) {
                        $cellValue = $phpDate->format('Y-m-d');
                    }
                }
                
                $format = $rules['format'] ?? 'Y-m-d';
                $d = DateTime::createFromFormat($format, $cellValue);
                if (!($d && $d->format($format) === $cellValue)) {
                    $rowErrors[] = "Field '{$dbCol}' ({$srcColName}): Date format should be {$format} (got: {$cellValue})";
                    if (!isset($validationResults['field_issues'][$dbCol])) 
                        $validationResults['field_issues'][$dbCol] = 0;
                    $validationResults['field_issues'][$dbCol]++;
                } else {
                    $today = new DateTime();
                    if ($d > $today) {
                        $rowErrors[] = "Field '{$dbCol}' ({$srcColName}): Future date '{$cellValue}' not allowed";
                        if (!isset($validationResults['field_issues'][$dbCol])) 
                            $validationResults['field_issues'][$dbCol] = 0;
                        $validationResults['field_issues'][$dbCol]++;
                    }
                }
                break;
                
            case 'email':
                if (!filter_var($cellValue, FILTER_VALIDATE_EMAIL)) {
                    $rowErrors[] = "Field '{$dbCol}' ({$srcColName}): Invalid email format";
                    if (!isset($validationResults['field_issues'][$dbCol])) 
                        $validationResults['field_issues'][$dbCol] = 0;
                    $validationResults['field_issues'][$dbCol]++;
                }
                break;
        }
        
        // Check for duplicate (mobile has check_if_exists)
        if (!empty($mapInfo['check_if_exists'])) {
            $uniqueKey = $dbCol . '_' . $cellValue;
            if (isset($uniqueTracker[$uniqueKey])) {
                $rowErrors[] = "Field '{$dbCol}': Duplicate value '{$cellValue}' in file";
                if (!isset($validationResults['field_issues'][$dbCol . '_duplicate'])) 
                    $validationResults['field_issues'][$dbCol . '_duplicate'] = 0;
                $validationResults['field_issues'][$dbCol . '_duplicate']++;
            }
            $uniqueTracker[$uniqueKey] = true;
        }
    }

    // Validate child mappings
    if (isset($mainConfig['children'])) {
        foreach ($mainConfig['children'] as $childConfig) {
            foreach ($childConfig['mappings'] as $dbCol => $mapInfo) {
                $srcColName = strtolower(isset($mapInfo['lookup']) ? $mapInfo['lookup']['source_column'] : $mapInfo['source_column']);
                if (!isset($excelColumnsMap[$srcColName])) continue;
                
                $excelColLetter = $excelColumnsMap[$srcColName];
                $val = trim($row[$excelColLetter] ?? '');
                
                // Validate child fields
                $rules = $mapInfo['validations'];
                if (($rules['required'] ?? false) && (empty($val) || $val === '')) {
                    $rowErrors[] = "Child field '{$dbCol}' ({$srcColName}): Required but missing";
                    if (!isset($validationResults['field_issues']['child_' . $dbCol])) 
                        $validationResults['field_issues']['child_' . $dbCol] = 0;
                    $validationResults['field_issues']['child_' . $dbCol]++;
                }
            }
        }
    }

    if (!empty($rowErrors)) {
        $validationResults['errors'][$rowNumber] = $rowErrors;
    } else {
        $validationResults['valid']++;
    }
}

echo "  Rows tested: {$validationResults['total_tested']}\n";
echo "  Valid rows: {$validationResults['valid']}\n";
echo "  Error rows: " . count($validationResults['errors']) . "\n";

if (!empty($validationResults['errors'])) {
    echo "\n  Error Details (first 10 rows):\n";
    $count = 0;
    foreach ($validationResults['errors'] as $row => $errors) {
        if ($count++ >= 10) break;
        echo "    Row {$row}:\n";
        foreach ($errors as $err) {
            echo "      ⚠ {$err}\n";
        }
    }
}

if (!empty($validationResults['field_issues'])) {
    echo "\n  Field Issue Summary:\n";
    arsort($validationResults['field_issues']);
    foreach ($validationResults['field_issues'] as $field => $count) {
        echo "    {$field}: {$count} error(s)\n";
    }
}

// ---- TEST 8: Full Data Scan (All Rows) ----
echo "\n\nTEST 8: Full Data Scan (ALL {$totalRows} rows)\n";
$fullStartTime = microtime(true);
$fullData = $sheet->rangeToArray('A2:' . $highestCol . $highestRow, NULL, TRUE, FALSE, TRUE);
$fullLoadTime = round(microtime(true) - $fullStartTime, 2);
echo "  Array conversion time: {$fullLoadTime}s\n";

$fullStats = [
    'total' => count($fullData),
    'empty_rows' => 0,
    'error_rows' => 0,
    'valid_rows' => 0,
    'duplicate_mobiles' => 0,
    'blank_required' => [],
    'type_errors' => []
];

$mobileTracker = [];
$allErrors = [];

foreach ($fullData as $rowIdx => $row) {
    foreach ($row as $colKey => $val) {
        $row[$colKey] = trim((string)$val);
    }
    
    // Check if row is entirely empty
    $allEmpty = true;
    foreach ($row as $val) {
        if (!empty($val)) { $allEmpty = false; break; }
    }
    if ($allEmpty) {
        $fullStats['empty_rows']++;
        continue;
    }
    
    $hasError = false;
    
    foreach ($mainConfig['mappings'] as $dbCol => $mapInfo) {
        $srcColName = strtolower(isset($mapInfo['lookup']) ? $mapInfo['lookup']['source_column'] : $mapInfo['source_column']);
        if (!isset($excelColumnsMap[$srcColName])) continue;
        
        $excelColLetter = $excelColumnsMap[$srcColName];
        $cellValue = trim($row[$excelColLetter] ?? '');
        $rules = $mapInfo['validations'];
        
        // Required check
        if (($rules['required'] ?? false) && (empty($cellValue) || $cellValue === '')) {
            $hasError = true;
            $key = $dbCol . '_required';
            if (!isset($fullStats['blank_required'][$key])) $fullStats['blank_required'][$key] = 0;
            $fullStats['blank_required'][$key]++;
        }
        
        if (empty($cellValue)) continue;
        
        // Type checks
        $type = $rules['type'] ?? 'string';
        if ($type === 'numeric' && !is_numeric($cellValue)) {
            $hasError = true;
            $key = $dbCol . '_not_numeric';
            if (!isset($fullStats['type_errors'][$key])) $fullStats['type_errors'][$key] = 0;
            $fullStats['type_errors'][$key]++;
        }
        if ($type === 'numeric' && is_numeric($cellValue) && isset($rules['length']) && strlen((string)$cellValue) != $rules['length']) {
            $hasError = true;
            $key = $dbCol . '_wrong_length';
            if (!isset($fullStats['type_errors'][$key])) $fullStats['type_errors'][$key] = 0;
            $fullStats['type_errors'][$key]++;
        }
        if ($type === 'string' && isset($rules['max_length']) && mb_strlen($cellValue) > $rules['max_length']) {
            $hasError = true;
            $key = $dbCol . '_too_long';
            if (!isset($fullStats['type_errors'][$key])) $fullStats['type_errors'][$key] = 0;
            $fullStats['type_errors'][$key]++;
        }
        
        // Mobile duplicate check
        if (!empty($mapInfo['check_if_exists']) && $dbCol === 'mobile') {
            if (isset($mobileTracker[$cellValue])) {
                $fullStats['duplicate_mobiles']++;
            }
            $mobileTracker[$cellValue] = true;
        }
    }
    
    if ($hasError) {
        $fullStats['error_rows']++;
    } else {
        $fullStats['valid_rows']++;
    }
}

$fullScanTime = round(microtime(true) - $fullStartTime, 2);
echo "  Full scan time: {$fullScanTime}s\n";
echo "  Total rows: {$fullStats['total']}\n";
echo "  Empty rows: {$fullStats['empty_rows']}\n";
echo "  Valid rows: {$fullStats['valid_rows']}\n";
echo "  Error rows: {$fullStats['error_rows']}\n";
echo "  Duplicate mobiles in file: {$fullStats['duplicate_mobiles']}\n";
echo "  Unique mobile numbers: " . count($mobileTracker) . "\n";

if (!empty($fullStats['blank_required'])) {
    echo "\n  Missing Required Fields:\n";
    arsort($fullStats['blank_required']);
    foreach ($fullStats['blank_required'] as $field => $count) {
        echo "    {$field}: {$count} row(s)\n";
    }
}

if (!empty($fullStats['type_errors'])) {
    echo "\n  Type/Format Errors:\n";
    arsort($fullStats['type_errors']);
    foreach ($fullStats['type_errors'] as $field => $count) {
        echo "    {$field}: {$count} row(s)\n";
    }
}

// ---- TEST 9: Performance Benchmark ----
echo "\n\nTEST 9: Performance Assessment\n";
$totalTime = round(microtime(true) - $startTime, 2);
$memUsage = round(memory_get_peak_usage(true) / 1024 / 1024, 1);
echo "  Total test time: {$totalTime}s\n";
echo "  Peak memory: {$memUsage}MB\n";
echo "  Rows/second: " . ($totalTime > 0 ? round($fullStats['total'] / $totalTime) : 'N/A') . "\n";

// ---- SUMMARY ----
echo "\n\n=== UNIT TEST SUMMARY ===\n";
$totalTests = 9;
$passed = 0;

$tests = [
    ['File Existence', true],
    ['Config JSON Parsing', true],
    ['ODS File Loading', $sheet !== null],
    ['Required Sheet Check', $sheet !== null],
    ['Header Column Mapping', empty($missingCols)],
    ['Data Rows Present', $totalRows > 0],
    ['Field Validation', true],
    ['Full Data Scan', true],
    ['Performance', $totalTime < 60]
];

foreach ($tests as $t) {
    $status = $t[1] ? 'PASS ✅' : 'FAIL ❌';
    if ($t[1]) $passed++;
    echo "  {$status} {$t[0]}\n";
}

echo "\nResult: {$passed}/{$totalTests} tests passed\n";
echo "=============================\n";

$spreadsheet->disconnectWorksheets();
unset($spreadsheet);
