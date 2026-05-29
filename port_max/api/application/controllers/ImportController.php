<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Settings;
use PhpOffice\PhpSpreadsheet\CachedObjectStorageFactory;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;

class ImportController extends MY_Controller
{
    private $bulkCache = [];

    public function __construct()
    {
        parent::__construct();
        // $this->loadClientDB();
        header('Content-Type: application/json');
        $this->load->helper('ai_helper');
        $this->load->helper('log_helper');
    }
    public function readExcelold()
    {

        $input = json_decode(file_get_contents("php://input"), true);
        $configFile = $input['configFile'] ?? null;
        $excelFile = $input['excelFile'] ?? null; // uploaded excel file path
        $clientName = $input['client'] ?? null;
        $fileId = $input['fileId'] ?? null;

        if (!$configFile) {
            echo json_encode(["success" => false, "message" => "Config file missing"]);
            return;
        }
        // Load config JSON
        $configPath = APPPATH . 'config/migration/' . $configFile;
        if (!file_exists($configPath)) {
            echo json_encode(["success" => false, "message" => "Config JSON not found"]);
            return;
        }
        $config = json_decode(file_get_contents($configPath), true);

        // $filePath = FCPATH . $config['file_path']; // or your actual file
        $filePath = FCPATH . 'uploads/' . $clientName . '/' . $excelFile;
        if (!file_exists($filePath)) {
            echo json_encode(["success" => false, "message" => "Excel not found"]);
            return;
        }

        // Load spreadsheet and get the required sheet
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getSheetByName($config['validation']['required_sheet_name']);
        if (!$sheet) {
            echo json_encode(["success" => false, "message" => "Required sheet '{$config['validation']['required_sheet_name']}' not found in Excel or Selected template is wrong!"]);
            return;
        }

        $highestColumn = $sheet->getHighestColumn();
        $errorColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(
            \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn) + 1
        );
        $sheet->setCellValue($errorColumn . "1", "Errors"); // Header

        $rows = $sheet->toArray(null, true, true, true);
        $header = array_shift($rows); // Remove header row, assuming first row is header
        // echo '<pre>';print_r(sizeof($rows));exit;
        // Helper: Map Excel column letters to names in header
        $excelColumnsMap = [];
        $allow_import = true;
        foreach ($header as $col => $value) {
            $excelColumnsMap[strtolower(trim($value))] = $col;
        }

        $mainConfig = $config['migrations'][0]; // assuming 1 migration for employee
        $allRowsData = [];
        $resData = [];

        foreach ($rows as $rowIndex => $row) {
            $dbData = [];
            $errors = [];
            $skippedRows = [];
            $skipRow = false;

            // 1. Map and validate main table fields
            foreach ($mainConfig['mappings'] as $dbCol => $mapInfo) {

                $srcColName = strtolower(isset($mapInfo['lookup']) ? $mapInfo['lookup']['source_column'] : $mapInfo['source_column']);
                $excelColLetter = $excelColumnsMap[$srcColName];
                $cellValue = trim($row[$excelColLetter]);
                $table = $mainConfig['destination_table'];

                // Lookup if defined
                if (isset($mapInfo['lookup'])) {
                    if (isset($mapInfo['lookup']['check_if_have']) && $mapInfo['lookup']['check_if_have']) {

                        $is_exists = $this->checkDataExists($mapInfo['lookup']['table'], $mapInfo['lookup']['match_column'], $cellValue);
                        if (!$is_exists) {
                            $errors[] = "<pre>{$dbCol} = {$cellValue} not available";

                            // $skipRow = true;
                            // break; // stop processing further fields for this row
                        }
                    }

                    $cellValue = $this->lookupValue(
                        $mapInfo['lookup']['table'],
                        $mapInfo['lookup']['match_column'],
                        $cellValue,
                        $mapInfo['lookup']['return_column'],
                        $mapInfo['lookup']['create_if_missing'] ?? false
                    );
                    $table = $mapInfo['lookup']['table'];
                }

                // Excel date handling
                if (isset($mapInfo['validations']['type']) && strtolower($mapInfo['validations']['type']) === 'date') {
                    if (is_numeric($cellValue)) {
                        $cellValue = ExcelDate::excelToDateTimeObject($cellValue)->format('Y-m-d');
                    } else {
                        $phpDate = date_create($cellValue);
                        if ($phpDate) {
                            $cellValue = $phpDate->format('Y-m-d');
                        }
                    }
                }

                // ✅ If exists check: skip entire row if duplicate found
                if (isset($mapInfo['check_if_exists']) && $mapInfo['check_if_exists']) {
                    $is_exists = $this->checkDataExists($table, $dbCol, $cellValue);
                    if ($is_exists) {
                        $msg = "Field '{$dbCol}': {$cellValue} already exists";
                        $skippedRows[] = "<pre>{$msg}</pre>";
                        // $skippedRows[] = "<pre>{$dbCol} = {$cellValue} already exists</pre>";
                        $skipRow = true;
                        break; // stop processing further fields for this row
                    } else {
                        foreach ($allRowsData as $prevRow) {
                            if (isset($prevRow[$dbCol]) && $prevRow[$dbCol] == $cellValue) {
                                $skippedRows[] = "<pre>{$dbCol} = {$cellValue} already exists in previous rows.</pre>";
                                $skipRow = true;
                                break;
                            }
                        }
                    }
                }

                if (!isset($excelColumnsMap[$srcColName])) {
                    $errors[] = "<pre>Source column '{$srcColName}' not found in Excel header";
                    continue;
                }

                // Validate
                $valid = $this->validateField($cellValue, $mapInfo['validations']);
                if (!$valid['status']) {
                    $errors[] = "<pre>Field '{$dbCol}': " . $valid['error'];
                }

                // Client based validation
                /* "is_client_based": true,
                "format": {
                    "branch": "id_branch",
                    "scheme": "id_scheme",
                    "year": "start_year"
                } */
                /* if(isset($mapInfo['is_client_based']) && $mapInfo['is_client_based']){
                    // Pass the table name, column name and value
                    $isClientBased = $this->checkFormatValidation($mainConfig['destination_table'],$mapInfo['source_column'],$cellValue, $mapInfo['format']);
                    if(!$isClientBased){
                        $errors[] = "<pre>Row " . ($rowIndex + 2) . ": {$dbCol} = {$cellValue} duplicate entry.";
                    }
                } */

                $dbData[$dbCol] = $cellValue;
            }

            // ✅ Skip the whole row if duplicate found
            if ($skipRow) {
                $resData['SkipedRows'][] = [
                    'row' => $rowIndex + 2,
                    'message' => $skippedRows
                ];
                $errorMessage = implode("\n", $skippedRows);  // convert array → string
                $sheet->setCellValue($errorColumn . ($rowIndex + 2), $errorMessage);

                unset($dbData);
                continue; // move to the next row
            }
            // print_r($errors);exit;
            // Optional: log validation errors
            if (sizeof($errors) > 0 && !empty($errors)) {
                $resData['ErrorRows'][] = [
                    'row' => $rowIndex + 2,
                    'message' => $errors
                ];
                $errorMessage = implode("\n", $errors);  // convert array → string
                $sheet->setCellValue($errorColumn . ($rowIndex + 2), $errorMessage);

                $allow_import = false;
            } else {
                if (isset($mainConfig['children'])) {
                    foreach ($mainConfig['children'] as $childConfig) {
                        $childData = [];
                        $childerrors = [];
                        $childData['table'] = $childConfig['destination_table'];
                        foreach ($childConfig['mappings'] as $childDbCol => $childMapInfo) {

                            $srcColName = strtolower(isset($childMapInfo['lookup']) ? $childMapInfo['lookup']['source_column'] : $childMapInfo['source_column']);
                            $excelColLetter = $excelColumnsMap[$srcColName];
                            $val = trim($row[$excelColLetter]);

                            if (isset($childMapInfo['lookup'])) {
                                if (isset($childMapInfo['lookup']['check_if_have']) && $childMapInfo['lookup']['check_if_have']) {
                                    // print_r($childMapInfo);exit;
                                    $is_exists = $this->checkDataExists($childMapInfo['lookup']['table'], $childMapInfo['lookup']['match_column'], $val);
                                    // var_dump($is_exists);exit;
                                    if (!$is_exists) {
                                        $childerrors[] = "<pre> {$childDbCol} = {$val} not available";
                                        // $skipRow = true;
                                        // break; // stop processing further fields for this row
                                    }
                                }

                                $val = $this->lookupValue(
                                    $childMapInfo['lookup']['table'],
                                    $childMapInfo['lookup']['match_column'],
                                    $val,
                                    $childMapInfo['lookup']['return_column'],
                                    $childMapInfo['lookup']['create_if_missing'] ?? false
                                );
                            }

                            $valid = $this->validateField($val, $childMapInfo['validations']);

                            if (!$valid['status']) {
                                $childerrors[] = "<pre>Field {$childDbCol}: " . $valid['error'];
                            }

                            $childData[$childDbCol] = $val;
                        }
                        $dbData['child'][] = $childData;

                        if (!empty($childerrors)) {
                            $allow_import = false;
                            $resData['ChildErrorRows'][] = [
                                'row' => $rowIndex + 2,
                                'message' => $childerrors
                            ];
                            // continue; // uncomment to skip inserting rows with validation errors
                        } else {
                            $allRowsData[] = $dbData;
                        }
                    }
                } else {
                    $allRowsData[] = $dbData;
                }
            }
        }
        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        switch ($ext) {
            case 'xlsx':
                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
                break;

            case 'xls':
                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xls($spreadsheet);
                break;

            case 'ods':
                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Ods($spreadsheet);
                break;

            default:
                throw new \Exception("Unsupported file format: $ext");
        }
        $writer->save($filePath);

        $LogData = array(
            "page" => 'validate',
            "fileId" => $fileId,
            "Entity" => $configFile,
            "ExcelName" => $excelFile,
            "SheetRowCount" => sizeof($rows),
            "ErrorRowCount" => (!empty($resData['ErrorRows']) ? sizeof($resData['ErrorRows']) : 0),
            "SkippedRowCount" => (!empty($resData['SkipedRows']) ? sizeof($resData['SkipedRows']) : 0)
        );
        if ($allow_import && sizeof($allRowsData) > 0) {
            $resData['message'] = "Ready to import..: " . sizeof($allRowsData) . " row(s)";
            $LogData['ImportMessage'] = $resData['message'];
            $LogData['status'] = true;
            $LogData['type'] = "rawData";
            LogHelper::activityLog($LogData);
            echo json_encode([
                'success' => true,
                'data' => $resData,
                'allrows' => $allRowsData
            ]);
        } else {
            $resData['message'] = "Please correct your data. Then import!";
            $resData['ErrorSummary'] = "";
            if ($config['ai']['enabled']) {
                $aiMsg = AiHelper::explain($resData['ErrorRows'], $resData['ChildErrorRows'], $resData['SkipedRows']);

                if ($aiMsg) {
                    $resData['ErrorSummary'] = htmlspecialchars($aiMsg);
                    $resData['ErrorMessage'] = $resData['ErrorSummary'];
                }
            }
            // print_r($resData['ErrorSummary']);exit;
            $LogData['ErrorMessage'] = $resData['message'];
            $LogData['ErrorSummary'] = $resData['ErrorSummary'];
            $LogData['status'] = false;
            LogHelper::activityLog($LogData);
            echo json_encode([
                'success' => false,
                'data' => $resData
            ]);
        }
    }

    public function readExcel()
    {

        $taskId = uniqid();
        $tmpFile = FCPATH . "tmp/{$taskId}.json";

        $input = json_decode(file_get_contents("php://input"), true);
        $configFile = $input['configFile'] ?? null;
        $excelFile = $input['excelFile'] ?? null; // uploaded excel file path
        $clientName = $input['client'] ?? null;
        $fileId = $input['fileId'] ?? null;

        if (!$configFile) {
            echo json_encode(["success" => false, 'status' => 'failed', "message" => "Config file missing"]);
            return;
        }
        // Load config JSON
        $configPath = APPPATH . 'config/migration/' . $configFile;
        if (!file_exists($configPath)) {
            echo json_encode(["success" => false, 'status' => 'failed', "message" => "Config JSON not found"]);
            return;
        }
        $config = json_decode(file_get_contents($configPath), true);

        // $filePath = FCPATH . $config['file_path']; // or your actual file
        $filePath = FCPATH . 'uploads/' . $clientName . '/' . $excelFile;
        if (!file_exists($filePath)) {
            echo json_encode(["success" => false, 'status' => 'failed', "message" => "Excel not found"]);
            return;
        }

        $validateArray = array(
            'config' => $config,
            'configFile' => $configFile,
            'filePath' => $filePath,
            'excelFile' => $excelFile,
            'clientName' => $clientName,
            'fileId' => $fileId,
            'taskId' =>  $taskId
        );

        file_put_contents($tmpFile, json_encode([
            'status' => 'processing',
            'progress' => 0
        ]));

        // Prepare response
        $response = json_encode(['status' => 'processing', 'taskId' => $taskId]);

        // Send response immediately
        header('Content-Type: application/json');
        header('Content-Length: ' . strlen($response));
        header('Connection: close');

        echo $response;

        // Try to flush output to client
        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        } else {
            // Alternative method for non-FastCGI environments
            ignore_user_abort(true);
            set_time_limit(0);

            // Flush all output buffers
            while (ob_get_level() > 0) {
                ob_end_flush();
            }
            flush();

            // Close the connection
            if (function_exists('apache_setenv')) {
                apache_setenv('no-gzip', '1');
            }
        }

        // 🔒 Release session lock so polling requests from the same user aren't blocked!
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }

        // ⏱️ Continue processing in background (thread will be busy but user gets immediate response)
        set_time_limit(0); // No time limit for long-running validation
        ini_set('memory_limit', '1024M'); // Increase memory for large files

        $this->validateProcess($validateArray);
    }

    public function validateProcess($data)
    {
        $startTime = microtime(true);
        error_log("DEBUG: validateProcess started for taskId: " . $data['taskId']);
        // ✅ MUST be before reader/load

        ignore_user_abort(true);

        $taskId = $data['taskId'];
        $config = $data['config'];
        $configFile = $data['configFile'];
        $filePath = $data['filePath'];
        $excelFile = $data['excelFile'];
        $clientName = $data['clientName'];
        $fileId = $data['fileId'];
        $tmpFile = FCPATH . "tmp/{$taskId}.json";

        // Load spreadsheet using Box\Spout for fast streaming (PHP 7.1 compatible)
        error_log("DEBUG: Loading file with Box\Spout... " . (microtime(true) - $startTime) . "s");
        $reader = ReaderEntityFactory::createReaderFromFile($filePath);
        $reader->open($filePath);

        $foundSheet = null;
        foreach ($reader->getSheetIterator() as $sheet) {
            if ($sheet->getName() === $config['validation']['required_sheet_name']) {
                $foundSheet = $sheet;
                break;
            }
        }

        if (!$foundSheet) {
            $reader->close();
            file_put_contents($tmpFile, json_encode([
                'status' => 'failed',
                'progress' => 0,
                "message" => "Required sheet '{$config['validation']['required_sheet_name']}' not found in Excel or Selected template is wrong!"
            ]));
            return;
        }

        error_log("DEBUG: Sheet found. Mapping headers... " . (microtime(true) - $startTime) . "s");

        $excelColumnsMap = [];
        $rowIterator = $foundSheet->getRowIterator();
        $rowIterator->rewind();
        
        if ($rowIterator->valid()) {
            $headerRow = $rowIterator->current();
            $cells = $headerRow->toArray();
            foreach ($cells as $idx => $cellValue) {
                if ($cellValue !== null && $cellValue !== '') {
                    $excelColumnsMap[strtolower(trim($cellValue))] = $idx;
                }
            }
            $rowIterator->next(); // Move to data rows
        }

        $mainConfig = $config['migrations'][0];
        $allRowsData = [];
        $resData = [];
        $allow_import = true;
        $sheetRowCount = 0;
        
        // OpenSpout doesn't give total count easily without scanning, 
        // but we can estimate or scan if needed. For now, we'll use a dynamic progress.
        $totalRows = 0; 
        
         file_put_contents($tmpFile, json_encode([
            'status' => 'processing',
            'progress' => 0,
            'total' => 'Calculating...'
        ]));

        // 🚀 PERFORMANCE BOOST: Bulk Cache Database Lookups
        $this->bulkCache = ['exists' => [], 'lookup' => []];
        error_log("DEBUG: Building Bulk Cache... " . (microtime(true) - $startTime) . "s");
        $cacheConfigs = [$mainConfig];
        if (isset($mainConfig['children'])) {
            foreach ($mainConfig['children'] as $child) $cacheConfigs[] = $child;
        }

        foreach ($cacheConfigs as $c) {
            $table = $c['destination_table'];
            foreach ($c['mappings'] as $dbCol => $mapInfo) {
                // Cache for check_if_exists
                if (isset($mapInfo['check_if_exists']) && $mapInfo['check_if_exists']) {
                    if (!isset($this->bulkCache['exists'][$table][$dbCol])) {
                        $this->bulkCache['exists'][$table][$dbCol] = [];
                        $query = $this->db->select($dbCol)->from($table)->get();
                        foreach ($query->result_array() as $r) {
                            $this->bulkCache['exists'][$table][$dbCol][$r[$dbCol]] = true;
                        }
                    }
                }
                // Cache for lookups
                if (isset($mapInfo['lookup'])) {
                    $l = $mapInfo['lookup'];
                    $lTable = $l['table'];
                    $match = $l['match_column'];
                    $ret = $l['return_column'];
                    if (!isset($this->bulkCache['lookup'][$lTable][$match][$ret])) {
                        $this->bulkCache['lookup'][$lTable][$match][$ret] = [];
                        $query = $this->db->select("$match, $ret")->from($lTable)->get();
                        foreach ($query->result_array() as $r) {
                            $this->bulkCache['lookup'][$lTable][$match][$ret][$r[$match]] = $r[$ret];
                        }
                    }
                }

                // Preload format combination caches
                if (!empty($mapInfo['format']) && is_array($mapInfo['format'])) {
                    $uniqueDbKey = $dbCol . '_format';
                    if (!isset($this->bulkCache['exists'][$table][$uniqueDbKey])) {
                        $this->bulkCache['exists'][$table][$uniqueDbKey] = [];

                        // select all format columns from DB
                        $query = $this->db->select(array_keys($mapInfo['format']))
                            ->from($table)
                            ->get();

                        foreach ($query->result_array() as $r) {
                            // build key like "id_scheme_id_branch"
                            $key = implode('_', array_map(function ($col) use ($r) {
                                return $r[$col];
                            }, array_keys($mapInfo['format'])));
                            $this->bulkCache['exists'][$table][$uniqueDbKey][$key] = true;
                        }
                    }
                }
            }
        }
        error_log("DEBUG: Bulk Cache built. " . (microtime(true) - $startTime) . "s");

        // 🚀 EXTREME SPEED BOOST: Streaming validation with Box\Spout
        error_log("DEBUG: Starting stream validation... " . (microtime(true) - $startTime) . "s");
        
        $errorMessagesByRow = []; // Track errors for final reporting

        while ($rowIterator->valid()) {
            $rowObj = $rowIterator->current();
            $row = $rowObj->toArray();
            
            $sheetRowCount++;
            $rowIndex = $sheetRowCount - 1; 

            if ($sheetRowCount % 1000 === 0) {
                gc_collect_cycles();
            }

            // Clean up row data (trim everything, convert DateTime objects)
            foreach ($row as $colKey => $val) {
                if ($val instanceof \DateTime || $val instanceof \DateTimeInterface) {
                    $row[$colKey] = $val->format('Y-m-d H:i:s');
                } else {
                    $row[$colKey] = trim((string)$val);
                }
            }

            $dbData = [];
            $errors = [];
            $skippedRows = [];
            $skipRow = false;

            // 1. Map and validate main table fields
            foreach ($mainConfig['mappings'] as $dbCol => $mapInfo) {

                $srcColName = strtolower(isset($mapInfo['lookup']) ? $mapInfo['lookup']['source_column'] : $mapInfo['source_column']);

                if (!isset($excelColumnsMap[$srcColName])) {
                    $errors[] = "Source column '{$srcColName}' not found in Excel header";
                    continue;
                }
                $excelColIdx = $excelColumnsMap[$srcColName];
                $cellValue = trim((string)($row[$excelColIdx] ?? ''));
                $table = $mainConfig['destination_table'];

                // Lookup if defined
                if (isset($mapInfo['lookup'])) {

                    $lookup = $mapInfo['lookup'];
                    $lTable = $lookup['table'];
                    $match  = $lookup['match_column'];
                    $ret    = $lookup['return_column'];

                    /**
                     * ✅ check_if_have (EXISTS CHECK) — from bulkCache
                     */
                    if (!empty($lookup['check_if_have'])) {

                        $exists = isset($this->bulkCache['lookup'][$lTable][$match][$ret][$cellValue]);

                        if (!$exists) {
                            $errors[] = "{$dbCol} = {$cellValue} not available";
                        }
                    }

                    /**
                     * ✅ LOOKUP VALUE — from bulkCache
                     */
                    if (isset($this->bulkCache['lookup'][$lTable][$match][$ret][$cellValue])) {
                        $cellValue = $this->bulkCache['lookup'][$lTable][$match][$ret][$cellValue];
                    } else {
                        // Optional: create_if_missing fallback
                        if (!empty($lookup['create_if_missing'])) {
                            $cellValue = $this->lookupValue(
                                $lTable,
                                $match,
                                $cellValue,
                                $ret,
                                true
                            );
                        } else if (empty($lookup['check_if_have'])) {
                            // Only null out if this isn't a pure existence check
                            // check_if_have already adds its own error, keeping value avoids double errors
                            $cellValue = null;
                        }
                    }
                }


                // Excel date handling
                if (isset($mapInfo['validations']['type']) && strtolower($mapInfo['validations']['type']) === 'date') {
                    if (is_numeric($cellValue)) {
                        $cellValue = ExcelDate::excelToDateTimeObject($cellValue)->format('Y-m-d');
                    } else {
                        $phpDate = date_create($cellValue);
                        if ($phpDate) {
                            $cellValue = $phpDate->format('Y-m-d');
                        }
                    }
                }

                // ✅ If exists check: skip entire row if duplicate found
                if (!empty($mapInfo['check_if_exists'])) {

                    if (isset($this->bulkCache['exists'][$table][$dbCol][$cellValue])) {
                        $skippedRows[] = "{$dbCol} = {$cellValue} already exists";
                        $skipRow = true;
                        break;
                    }

                    // duplicate in same file
                    $uniqueKey = $dbCol . '_' . $cellValue;
                    if (isset($uniqueTracker[$uniqueKey])) {
                        $skippedRows[] = "{$dbCol} = {$cellValue} duplicate in file";
                        $skipRow = true;
                        break;
                    }
                    $uniqueTracker[$uniqueKey] = true;
                }


                // Validate
                $valid = $this->validateField($cellValue, $mapInfo['validations']);
                if (!$valid['status']) {
                    $errors[] = "Field '{$dbCol}': " . $valid['error'];
                }

                if (!empty($mapInfo['format'])) {
                    $formatValues = [];
                    foreach ($mapInfo['format'] as $col => $sourceCol) {
                        // Use already-resolved DB value (post-lookup) if available,
                        // otherwise fall back to raw Excel value
                        if (isset($dbData[$col])) {
                            $val = (string)$dbData[$col];
                        } else {
                            $val = trim((string)($row[$excelColumnsMap[strtolower($sourceCol)]] ?? ''));
                        }
                        $formatValues[] = $val;
                    }
                    $formatKey = implode('_', $formatValues);
                    $uniqueDbKey = $dbCol . '_format';

                    if (isset($this->bulkCache['exists'][$table][$uniqueDbKey][$formatKey])) {
                        $skippedRows[] = "{$dbCol} with format " . implode('|', $formatValues) . " already exists in DB";
                        $skipRow = true;
                    }
                    // also track duplicates within file
                    $uniqueFieldKey = $dbCol . '_' . $formatKey;
                    if (isset($uniqueTracker[$uniqueFieldKey])) {
                        $skippedRows[] = "{$dbCol} with format " . implode('|', $formatValues) . " duplicate in file";
                        $skipRow = true;
                    }
                    $uniqueTracker[$uniqueFieldKey] = true;
                }
                
                $dbData[$dbCol] = $cellValue;
            }

            // ✅ Skip the whole row if duplicate found
            if ($skipRow) {
                $resData['SkipedRows'][] = [
                    'row' => $sheetRowCount + 1,
                    'message' => $skippedRows
                ];
                $errorMessagesByRow[$sheetRowCount + 1] = "Skipped: " . implode("\n", $skippedRows);

                unset($dbData);
                $rowIterator->next();
                continue; 
            }

            if (sizeof($errors) > 0 && !empty($errors)) {
                $resData['ErrorRows'][] = [
                    'row' => $sheetRowCount + 1,
                    'message' => $errors
                ];
                $errorMessagesByRow[$sheetRowCount + 1] = "Error: " . implode("\n", $errors);

                $allow_import = false;
            } else {
                if (isset($mainConfig['children'])) {
                    foreach ($mainConfig['children'] as $childConfig) {
                        $childData = [];
                        $childerrors = [];
                        $childData['table'] = $childConfig['destination_table'];
                        foreach ($childConfig['mappings'] as $childDbCol => $childMapInfo) {

                            $srcColName = strtolower(isset($childMapInfo['lookup']) ? $childMapInfo['lookup']['source_column'] : $childMapInfo['source_column']);
                            $excelColIdx = $excelColumnsMap[$srcColName] ?? null;
                            $val = trim((string)($row[$excelColIdx] ?? ''));

                            if (isset($childMapInfo['lookup'])) {
                                $lookup = $childMapInfo['lookup'];
                                $lTable = $lookup['table'];
                                $match  = $lookup['match_column'];
                                $ret    = $lookup['return_column'];

                                if (!empty($lookup['check_if_have'])) {
                                    if (!isset($this->bulkCache['lookup'][$lTable][$match][$ret][$val])) {
                                        $childerrors[] = "{$childDbCol} = {$val} not available";
                                    }
                                }

                                $val = $this->bulkCache['lookup'][$lTable][$match][$ret][$val] ?? null;
                            }

                            $valid = $this->validateField($val, $childMapInfo['validations']);

                            if (!$valid['status']) {
                                $childerrors[] = "Field {$childDbCol}: " . $valid['error'];
                            }

                            $childData[$childDbCol] = $val;
                        }
                        $dbData['child'][] = $childData;

                        if (!empty($childerrors)) {
                            $allow_import = false;
                            $resData['ChildErrorRows'][] = [
                                'row' => $sheetRowCount + 1,
                                'message' => $childerrors
                            ];
                            // Also add to the Excel report
                            $existingErr = isset($errorMessagesByRow[$sheetRowCount + 1]) ? $errorMessagesByRow[$sheetRowCount + 1] . "\n" : "";
                            $errorMessagesByRow[$sheetRowCount + 1] = $existingErr . "Child Error: " . implode("; ", $childerrors);
                        } else {
                            $allRowsData[] = $dbData;
                        }
                    }
                } else {
                    $allRowsData[] = $dbData;
                }
            }

            if ($sheetRowCount % 500 === 0) {
                 error_log("DEBUG: Processed $sheetRowCount rows... " . (microtime(true) - $startTime) . "s");
                file_put_contents($tmpFile, json_encode([
                    'status' => 'processing',
                    'progress' => 'Streaming...',
                    'validated' => $sheetRowCount,
                    'total' => 'Processing...'
                ]));
            }
            $rowIterator->next();
        }
        $totalRows = $sheetRowCount;
        $validatedSheetName = $foundSheet->getName(); // Save name BEFORE closing reader
        $reader->close();

        // 🚀 PERFORMANCE: Record total counts and TRUNCATE immediately to free memory
        $totalErrors = count($resData['ErrorRows'] ?? []);
        $totalChildErrors = count($resData['ChildErrorRows'] ?? []);
        $totalSkiped = count($resData['SkipedRows'] ?? []);
        
        $errorCount = $totalErrors; // For legacy log variable
        
        error_log("DEBUG: Validation loop complete. Total Rows: $totalRows, Errors: $totalErrors, Child Errors: $totalChildErrors, Skipped: $totalSkiped");

        // We only keep a sample for the UI. The spreadsheet writer will use $errorMessagesByRow which is already built.
        if ($totalErrors > 100) $resData['ErrorRows'] = array_slice($resData['ErrorRows'], 0, 100);
        if ($totalChildErrors > 100) $resData['ChildErrorRows'] = array_slice($resData['ChildErrorRows'], 0, 100);
        if ($totalSkiped > 100) $resData['SkipedRows'] = array_slice($resData['SkipedRows'], 0, 100);

        if (!empty($errorMessagesByRow)) {
            $errorCount = count($errorMessagesByRow);
            error_log("DEBUG: Writing $errorCount errors back to file using Streaming Writer... " . (microtime(true) - $startTime) . "s");

            $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            $tempReportPath = $filePath . '.tmp.' . $ext;

            // Initialize Streaming Reader and Writer
            $reportReader = ReaderEntityFactory::createReaderFromFile($filePath);
            $reportReader->open($filePath);

            // Spout 3.3 writers (XLSX, ODS)
            if ($ext === 'ods') {
                $reportWriter = WriterEntityFactory::createODSWriter();
            } else {
                // Default to XLSX if not ODS (Spout 3.3 doesn't write legacy .xls)
                $reportWriter = WriterEntityFactory::createXLSXWriter();
                if ($ext === 'xls') $tempReportPath = str_replace('.xls', '.xlsx', $tempReportPath);
            }

            $reportWriter->openToFile($tempReportPath);

            foreach ($reportReader->getSheetIterator() as $reportSheet) {
                // Ensure we are writing to the correct sheet
                if ($reportSheet->getName() !== $validatedSheetName) continue; 

                $existingErrorColIdx = null; // Track if "Errors" column already exists
                $reporterRowIndex = 0;
                foreach ($reportSheet->getRowIterator() as $row) {
                    $reporterRowIndex++;
                    $cells = $row->getCells();
                    $cellValues = [];
                    foreach ($cells as $cell) {
                        $val = $cell->getValue();
                        // Convert DateTime objects to strings (Box\Spout writer crashes on objects)
                        if ($val instanceof \DateTime || $val instanceof \DateTimeInterface) {
                            $val = $val->format('Y-m-d H:i:s');
                        } elseif (is_object($val)) {
                            $val = (string)$val;
                        }
                        $cellValues[] = $val;
                    }

                    // On header row: detect if "Errors" column already exists
                    if ($reporterRowIndex === 1) {
                        foreach ($cellValues as $idx => $hdr) {
                            if (strtolower(trim((string)$hdr)) === 'errors') {
                                $existingErrorColIdx = $idx;
                                break;
                            }
                        }
                    }

                    // Write error value: REPLACE existing column or APPEND new one
                    if ($existingErrorColIdx !== null) {
                        // Replace old Errors column value
                        if ($reporterRowIndex === 1) {
                            $cellValues[$existingErrorColIdx] = "Errors";
                        } else {
                            $cellValues[$existingErrorColIdx] = isset($errorMessagesByRow[$reporterRowIndex])
                                ? $errorMessagesByRow[$reporterRowIndex] : "";
                        }
                    } else {
                        // Append new Errors column
                        if ($reporterRowIndex === 1) {
                            $cellValues[] = "Errors";
                        } else {
                            $cellValues[] = isset($errorMessagesByRow[$reporterRowIndex])
                                ? $errorMessagesByRow[$reporterRowIndex] : "";
                        }
                    }

                    $newRow = WriterEntityFactory::createRowFromArray($cellValues);
                    $reportWriter->addRow($newRow);
                }
            }

            $reportReader->close();
            $reportWriter->close();

            // Replace original file with the one containing errors
            if (file_exists($tempReportPath)) {
                if ($ext === 'xls') {
                    // If original was .xls, we had to convert to .xlsx
                    unlink($filePath);
                    $filePath = str_replace('.xls', '.xlsx', $filePath);
                    rename($tempReportPath, $filePath);
                } else {
                    rename($tempReportPath, $filePath);
                }
            }
            $elapsed = microtime(true) - $startTime;
            error_log("DEBUG: Writing " . count($errorMessagesByRow) . " errors back to file using Streaming Writer... {$elapsed}s");
        }

        error_log("DEBUG: Error writing phase complete. Preparing final response...");



        $LogData = array(
            "page" => 'validate',
            "fileId" => $fileId,
            "Entity" => $configFile,
            "ExcelName" => $excelFile,
            "SheetRowCount" => $sheetRowCount,
            "ErrorRowCount" => $totalErrors,
            "SkippedRowCount" => $totalSkiped
        );
        if ($allow_import && sizeof($allRowsData) > 0) {
            $resData['message'] = "Ready to import..: " . sizeof($allRowsData) . " row(s)";
            $LogData['ImportMessage'] = $resData['message'];
            $LogData['status'] = true;
            $LogData['type'] = "import";
            $LogData['client'] = $clientName;
            LogHelper::activityLog($LogData);

            $progress = round(($sheetRowCount / $totalRows) * 100);
            
            file_put_contents($tmpFile, json_encode([
                'status' => 'validated',
                'progress' => $progress,
                'validated' => $sheetRowCount,
                'total' => $totalRows,
                'data' => $resData,
                'allrows' => $allRowsData
            ]));
            /* echo json_encode([
                'success' => true,
                'data' => $resData,
                'allrows' => $allRowsData
            ]); */
        } else {
            $resData['ErrorSummary'] = "";
            if ($config['ai']['enabled']) {
                // Truncate data for AI to prevent massive prompts
                $aiMainErr = array_slice($resData['ErrorRows'] ?? [], 0, 20);
                $aiChildErr = array_slice($resData['ChildErrorRows'] ?? [], 0, 20);
                $aiSkiped = array_slice($resData['SkipedRows'] ?? [], 0, 20);
                
                $aiMsg = AiHelper::explain($aiMainErr, $aiChildErr, $aiSkiped);
                error_log("DEBUG: AI explanation received.");

                if ($aiMsg) {
                    $resData['ErrorSummary'] = htmlspecialchars($aiMsg);
                    $resData['ErrorMessage'] = $resData['ErrorSummary'];
                }
            }

            error_log("DEBUG: Preparing Activity Log...");
            $progress = round(($sheetRowCount / $totalRows) * 100);
            
            // Add a status message about truncation if needed
            if ($totalErrors > 100 || $totalSkiped > 100) {
                 $resData['message'] = "(Showing first 100 errors. Please download the file for the full report.)";
            }

            // If there are valid rows OR skipped rows, let user choose to skip errors / fix & re-import
            if (sizeof($allRowsData) > 0 || $totalSkiped > 0) {
                $resData['message'] = "Validation found " . ($totalErrors + $totalSkiped) . " error/skipped row(s) and " . sizeof($allRowsData) . " valid row(s). You can skip error rows and import valid ones, or fix errors first.";
                $LogData['ErrorMessage'] = $resData['message'];
                $LogData['ErrorSummary'] = $resData['ErrorSummary'] ?? '';
                $LogData['status'] = false;
                $LogData['type'] = "import";
                $LogData['client'] = $clientName;
                LogHelper::activityLog($LogData);
                file_put_contents($tmpFile, json_encode([
                    'status' => 'has_errors',
                    'progress' => $progress,
                    'validated' => $sheetRowCount,
                    'total' => $totalRows,
                    'validRowCount' => sizeof($allRowsData),
                    'errorRowCount' => $totalErrors + $totalSkiped,
                    'data' => $resData,
                    'allrows' => $allRowsData
                ]));
            } else {
                $resData['message'] = "Please correct your data. Then import!";
                $LogData['ErrorMessage'] = $resData['message'];
                $LogData['ErrorSummary'] = $resData['ErrorSummary'] ?? '';
                $LogData['status'] = false;
                LogHelper::activityLog($LogData);

                file_put_contents($tmpFile, json_encode([
                    'status' => 'failed',
                    'progress' => $progress,
                    'validated' => $sheetRowCount,
                    'total' => $totalRows,
                    'data' => $resData
                ]));
            }
            error_log("DEBUG: validateProcess finished successfully.");
            /* echo json_encode([
                'success' => false,
                'data' => $resData
            ]); */
        }
        /* echo json_encode([
                'success' => true,
                'status' => 'processing',
                'progress' => $progress,
                'validated' => $sheetRowCount,
                'total' => $totalRows
            ]); */
    }

    public function insertAllRows()
    {
        $taskId = uniqid();
        $tmpFile = FCPATH . "tmp/{$taskId}.json";
        $input = json_decode(file_get_contents("php://input"), true);

        $data = $input['allrows'] ?? null;
        $total = count($data);

        // Initialize progress file
        file_put_contents($tmpFile, json_encode([
            'status' => 'processing',
            'progress' => 0,
            'total' => $total
        ]));

        // Prepare response
        $response = json_encode(['status' => 'processing', 'taskId' => $taskId]);

        // Send response immediately
        header('Content-Type: application/json');
        header('Content-Length: ' . strlen($response));
        header('Connection: close');
        echo $response;

        // Try to flush output to client
        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        } else {
            ignore_user_abort(true);
            set_time_limit(0);
            while (ob_get_level() > 0) ob_end_flush();
            flush();
            if (function_exists('apache_setenv')) apache_setenv('no-gzip', '1');
        }

        // 🔒 Release session lock
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }

        // ⏱️ Continue processing in background
        set_time_limit(0);
        ini_set('memory_limit', '1024M');

        $this->insertProcess($input, $taskId);
    }

    private function insertProcess($input, $taskId)
    {
        $tmpFile = FCPATH . "tmp/{$taskId}.json";
        $configFile = $input['configFile'] ?? null;
        $data = $input['allrows'] ?? null;
        $fileId = $input['fileId'] ?? null;
        $client = $input['client'] ?? null;
        $total = count($data);
        $count = 0;

        $configPath = APPPATH . 'config/migration/' . $configFile;
        $config = json_decode(file_get_contents($configPath), true);
        $mainConfig = $config['migrations'][0];
        $table = $mainConfig['destination_table'];

        error_log("DEBUG INSERT: Starting insertProcess. Total rows: $total, Table: $table");

        /* Before Import Data */
        $before_import = $this->getReportData('before_import', $table, $config['report']);
        error_log("DEBUG INSERT: Before import data fetched.");

        $batchData = [];
        $batchSize = 500;

        $this->db->trans_start(); // 🚀 Enable Transactions for massive speedup

        foreach ($data as $dbData) {
            $count++;
            if (count($dbData) > 0) {
                $children = $dbData['child'] ?? [];
                unset($dbData['child']);

                if (empty($children)) {
                    // Collect for batch insert if no children
                    $batchData[] = $dbData;
                    if (count($batchData) >= $batchSize) {
                        $this->db->insert_batch($table, $batchData);
                        $batchData = [];
                    }
                } else {
                    // If has children, must insert individually to get last_id
                    // Flush any pending batch first to keep order if relevant
                    if (!empty($batchData)) {
                        $this->db->insert_batch($table, $batchData);
                        $batchData = [];
                    }

                    $this->db->insert($table, $dbData);
                    $mainId = $this->db->insert_id();

                    if (isset($mainConfig['children']) && count($children) && $mainId > 0) {
                        $childBatch = [];
                        foreach ($mainConfig['children'] as $childConfig) {
                            foreach ($children as $child) {
                                if ($childConfig['destination_table'] == $child['table']) {
                                    $child[$childConfig['foreign_key']] = $mainId;
                                    unset($child['table']);
                                    $childBatch[] = $child;
                                }
                            }
                            if (!empty($childBatch)) {
                                $this->db->insert_batch($childConfig['destination_table'], $childBatch);
                                $childBatch = [];
                            }
                        }
                    }
                }

                // Update progress file (throttled)
                if ($count % 50 === 0 || $count === $total) {
                    $progress = round(($count / $total) * 100);
                    file_put_contents($tmpFile, json_encode([
                        'status' => $progress == 100 ? 'completed' : 'processing',
                        'progress' => $progress,
                        'inserted' => $count,
                        'total' => $total
                    ]));
                }
            }
        }
        // Final batch flush
        if (!empty($batchData)) {
            $this->db->insert_batch($table, $batchData);
        }

        $this->db->trans_complete(); // Commit all at once

        // Check if transaction failed — report error to user
        if ($this->db->trans_status() === FALSE) {
            $dbError = $this->db->error();
            $errorMsg = "Database error: " . ($dbError['message'] ?? 'Unknown error') . " (Code: " . ($dbError['code'] ?? '?') . ")";
            error_log("DEBUG INSERT: Transaction FAILED! " . $errorMsg);
            file_put_contents($tmpFile, json_encode([
                'status' => 'error',
                'progress' => 100,
                'message' => $errorMsg
            ]));
            return;
        }

        error_log("DEBUG INSERT: Transaction complete. Rows inserted: $count");

        /* Current and After Import Data */
        // Get max last_id from first report config's before_import data
        $maxLastId = 0;
        if (!empty($before_import)) {
            foreach ($before_import as $reportRows) {
                $ids = array_column($reportRows, 'last_id');
                if (!empty($ids)) {
                    $maxLastId = max($maxLastId, max($ids));
                }
            }
        }
        $current_import = $this->getReportData('current_import', $table, $config['report'], $maxLastId);
        $after_import = $this->getReportData('after_import', $table, $config['report']);
        error_log("DEBUG INSERT: Report data fetched successfully.");

        // Remove last_id from all report data (not needed in UI)
        $cleanReport = function($reportData) {
            $cleaned = [];
            foreach ($reportData as $idx => $rows) {
                $cleaned[$idx] = array_map(function ($row) {
                    unset($row['last_id']);
                    return $row;
                }, $rows);
            }
            return $cleaned;
        };

        $before_import = $cleanReport($before_import);
        $current_import = $cleanReport($current_import);
        $after_import = $cleanReport($after_import);

        $summaryData = [
            "before" => $before_import,
            "current" => $current_import,
            "after" => $after_import,
            "fileId" => $fileId,
            "type" => "import",
            "page" => "import",
            "client" => $client,
            "table" => $table, 
            "status" => true,
            "report_config" => $config['report']
        ];
        LogHelper::activityLog($summaryData);
        
        // Mark as completed in the status file
        file_put_contents($tmpFile, json_encode([
            'status' => 'completed',
            'progress' => 100,
            'inserted' => $count,
            'total' => $total
        ]));
        error_log("DEBUG INSERT: insertProcess finished successfully. $count rows imported.");
    }
    private function validateField($value, $rules)
    {
        // print_r($rules);exit;

        if (($rules['required'] ?? false) && (empty($value) || $value === '' || $value === null)) {
            return ['status' => false, 'error' => 'Value is required but missing'];
        }
        if (empty($value))
            return ['status' => true]; // skip validation if not required and empty

        switch ($rules['type'] ?? 'string') {
            case 'string':
                if (isset($rules['max_length']) && mb_strlen($value) > $rules['max_length']) {
                    return ['status' => false, 'error' => "Max length {$rules['max_length']} exceeded"];
                }
                break;

            case 'numeric':
                if (!is_numeric($value)) {
                    return ['status' => false, 'error' => "Value is not numeric"];
                }
                if (isset($rules['length']) && strlen((string) $value) != $rules['length']) {
                    return ['status' => false, 'error' => "Length must be {$rules['length']}"];
                }
                if (isset($rules['min']) && $value < $rules['min']) {
                    return ['status' => false, 'error' => "Value less than minimum {$rules['min']}"];
                }
                if (isset($rules['max']) && $value > $rules['max']) {
                    return ['status' => false, 'error' => "Value greater than maximum {$rules['max']}"];
                }
                if (isset($rules['max_length']) && strlen((string) $value) > $rules['max_length']) {
                    return ['status' => false, 'error' => "Max length {$rules['max_length']} exceeded"];
                }
                break;

            case 'email':
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    return ['status' => false, 'error' => "Invalid email format"];
                }
                if (isset($rules['max_length']) && mb_strlen($value) > $rules['max_length']) {
                    return ['status' => false, 'error' => "Max length {$rules['max_length']} exceeded"];
                }
                break;

            case 'date':
                $format = $rules['format'] ?? 'Y-m-d';
                // Normalize: strip time portion or parse any valid date string to Y-m-d
                $parsed = date_create($value);
                if ($parsed) {
                    $value = $parsed->format('Y-m-d');
                }
                $d = DateTime::createFromFormat($format, $value);
                if (!($d && $d->format($format) === $value)) {
                    return ['status' => false, 'error' => "Date format should be {$format}"];
                } else {
                    // Compare only dates (not time)
                    $today = new DateTime();
                    if ($d > $today) {
                        return ['status' => false, 'error' => "Future date not allowed"];
                    }
                }
                break;

            case 'time':
                $format = $rules['format'] ?? 'H:i';
                $d = DateTime::createFromFormat($format, $value);
                if (!($d && $d->format($format) === $value)) {
                    return ['status' => false, 'error' => "Time format should be {$format}"];
                }
                break;
        }
        return ['status' => true];
    }

    private function lookupValue($table, $matchColumn, $value, $returnColumn, $createIfMissing = false)
    {
        // ✅ BULK CACHE ONLY
        if (isset($this->bulkCache['lookup'][$table][$matchColumn][$returnColumn][$value])) {

            $result = $this->bulkCache['lookup'][$table][$matchColumn][$returnColumn][$value];

            // Handle multiple IDs (from FIND_IN_SET)
            return is_array($result)
                ? implode(',', array_unique($result))
                : $result;
        }

        // ❌ No DB lookup here
        if ($createIfMissing) {
            $id = $this->createifmissing($table, $matchColumn, $value);

            // update cache dynamically
            $this->bulkCache['lookup'][$table][$matchColumn][$returnColumn][$value] = $id;

            return $id;
        }

        return null;
    }


    public function createifmissing($table, $matchColumn, $value)
    {
        /* echo '<pre>';
        print_r($table);
        print_r($matchColumn); */
        $ins = [];
        if (!empty($value)) {
            $value = explode(',', $value);
            foreach ($value as $v) {
                if (!empty($v)) {
                    $this->db->insert($table, [$matchColumn => $v]);
                    $ins[] = $this->db->insert_id();
                }
            }
        }

        return implode(',', $ins);
    }

    public function upd_emp_passhash()
    {
        $noPass = $this->db->query("SELECT * FROM employee WHERE (pwd_hash IS NULL OR pwd_hash = '')")->result_array();
        foreach ($noPass as $row) {
            $upddata = array("pwd_hash" => password_hash(trim($row['emp_code']), PASSWORD_DEFAULT));
            $this->db->where('id_employee', $row['id_employee']);
            $transtatus = $this->db->update('employee', $upddata);
        }
    }

    private function checkDataExists($table, $dbCol, $cellValue)
    {
        // Check Bulk Cache First
        if (isset($this->bulkCache['exists'][$table][$dbCol][$cellValue])) {
            return true;
        }

        // error_log("CACHE MISS [exists]: $table.$dbCol=$cellValue");

        $is_exists = $this->db->query("SELECT * FROM " . $table . " WHERE " . $dbCol . " = '" . $cellValue . "'");

        if ($is_exists->num_rows() > 0) {
            $exists = true;
        } else {
            $exists = false;
        }

        return $exists;
    }

    public function uploadFile()
    {

        if (!isset($_POST['client'])) {
            echo json_encode(['error' => 'No client selected. Please login again...']);
            return;
        }

        // Upload directory
        $upload_path = FCPATH . 'uploads/' . $_POST['client'] . '/';

        // Ensure folder exists
        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0777, true);
        }

        // Check if file exists in request
        if (!isset($_FILES['file'])) {
            echo json_encode(['error' => 'No file uploaded']);
            return;
        }

        $file = $_FILES['file'];
        $filename = preg_replace('/\s+/', '_', $file['name']); // sanitize filename
        $target_path = $upload_path . $filename;

        // Move file to upload folder
        if (!move_uploaded_file($file['tmp_name'], $target_path)) {
            echo json_encode(['error' => 'Failed to save file']);
            return;
        }

        // Return file info
        echo json_encode([
            'success' => true,
            'fileId' => uniqid(),
            'filename' => $filename,
        ]);
    }

    public function listConfigs()
    {
        $path = APPPATH . "config/migration/";
        $files = glob($path . "*.json");

        $configList = [];

        foreach ($files as $file) {
            $raw = pathinfo($file, PATHINFO_FILENAME); // e.g. ret_metal_cat_purity
            // Strip common prefixes
            $name = preg_replace('/^ret_/', '', $raw);
            // Replace underscores with spaces and title-case
            $name = ucwords(str_replace('_', ' ', $name));
            $configList[] = [
                "file" => basename($file),
                "name" => $name
            ];
        }

        echo json_encode([
            "success" => true,
            "configs" => $configList
        ]);
    }

    /**
     * Generate and download an Excel template for a given config JSON.
     * Row 1: Column headers (source_column names)
     * Row 2: Validation rules (type, required, max_length, format, lookup)
     * GET: ?config=employee.json
     */
    public function downloadTemplate()
    {
        $configFile = $this->input->get('config', TRUE);
        if (!$configFile) {
            echo json_encode(['success' => false, 'message' => 'Config file not specified']);
            return;
        }

        $configPath = APPPATH . 'config/migration/' . basename($configFile);
        if (!file_exists($configPath)) {
            echo json_encode(['success' => false, 'message' => 'Config file not found']);
            return;
        }

        $config = json_decode(file_get_contents($configPath), true);
        if (!$config || empty($config['migrations'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid config format']);
            return;
        }

        $headers = [];
        $rules = [];
        $mainMigration = $config['migrations'][0];

        // Helper to build rule string from a mapping
        $buildRule = function ($mapping) {
            $v = $mapping['validations'] ?? [];
            $parts = [];
            // Type
            if (!empty($v['type'])) $parts[] = strtoupper($v['type']);
            // Required
            $parts[] = (!empty($v['required'])) ? 'Required' : 'Optional';
            // Length constraints
            if (isset($v['max_length'])) $parts[] = 'Max: ' . $v['max_length'];
            if (isset($v['length'])) $parts[] = 'Length: ' . $v['length'];
            if (isset($v['min'])) $parts[] = 'Min: ' . $v['min'];
            if (isset($v['max'])) $parts[] = 'Max val: ' . $v['max'];
            // Format (date/time)
            if (!empty($v['format'])) $parts[] = 'Format: ' . $v['format'];
            // Lookup info
            if (isset($mapping['lookup'])) {
                $lk = $mapping['lookup'];
                $lkInfo = 'Lookup: ' . ($lk['table'] ?? '') . '.' . ($lk['match_column'] ?? '');
                if (!empty($lk['create_if_missing'])) $lkInfo .= ' (auto-create)';
                $parts[] = $lkInfo;
            }
            return implode(' | ', $parts);
        };

        // Main table mappings
        foreach ($mainMigration['mappings'] as $dbCol => $mapping) {
            if (isset($mapping['source_column'])) {
                $headers[] = $mapping['source_column'];
            } elseif (isset($mapping['lookup']['source_column'])) {
                $headers[] = $mapping['lookup']['source_column'];
            } else {
                continue;
            }
            $rules[] = $buildRule($mapping);
        }

        // Children mappings
        if (!empty($mainMigration['children'])) {
            foreach ($mainMigration['children'] as $child) {
                foreach ($child['mappings'] as $dbCol => $mapping) {
                    if (isset($mapping['source_column'])) {
                        $headers[] = $mapping['source_column'];
                    } elseif (isset($mapping['lookup']['source_column'])) {
                        $headers[] = $mapping['lookup']['source_column'];
                    } else {
                        continue;
                    }
                    $rules[] = $buildRule($mapping);
                }
            }
        }

        // Generate Excel
        $entityName = ucfirst(str_replace('.json', '', $configFile));
        $tmpDir = FCPATH . 'tmp/';
        if (!is_dir($tmpDir)) mkdir($tmpDir, 0777, true);
        $tmpFile = $tmpDir . $entityName . '_template_' . date('YmdHis') . '.xlsx';

        $writer = WriterEntityFactory::createXLSXWriter();
        $writer->openToFile($tmpFile);

        // Header style — green bold
        $headerStyle = (new \Box\Spout\Writer\Common\Creator\Style\StyleBuilder())
            ->setFontBold()
            ->setFontSize(11)
            ->setFontColor('FFFFFF')
            ->setBackgroundColor('2E7D32')
            ->build();

        // Rules row style — grey italic
        $rulesStyle = (new \Box\Spout\Writer\Common\Creator\Style\StyleBuilder())
            ->setFontItalic()
            ->setFontSize(9)
            ->setFontColor('666666')
            ->setBackgroundColor('F5F5F5')
            ->build();

        // Row 1: Headers
        $writer->addRow(WriterEntityFactory::createRowFromArray($headers, $headerStyle));
        // Row 2: Validation rules
        $writer->addRow(WriterEntityFactory::createRowFromArray($rules, $rulesStyle));
        // Row 3: Empty data row
        $writer->addRow(WriterEntityFactory::createRowFromArray(array_fill(0, count($headers), '')));

        $writer->close();

        // Force download
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $entityName . '_template.xlsx"');
        header('Content-Length: ' . filesize($tmpFile));
        header('Cache-Control: no-cache, no-store, must-revalidate');
        readfile($tmpFile);

        @unlink($tmpFile);
        exit;
    }

    /**
     * Backup entity tables to Excel + SQL before import.
     * Reads config to find target tables, exports existing data.
     * POST: { client, entity }
     */
    public function backupEntityTables()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $client = $input['client'] ?? '';
        $entity = basename($input['entity'] ?? '');

        if (!$entity) {
            echo json_encode(['success' => false, 'message' => 'Entity not specified']);
            return;
        }

        $configPath = APPPATH . 'config/migration/' . $entity;
        if (strpos($entity, '.json') === false) $configPath .= '.json';
        if (!file_exists($configPath)) {
            echo json_encode(['success' => false, 'message' => 'Config not found']);
            return;
        }

        $config = json_decode(file_get_contents($configPath), true);
        if (!$config || empty($config['migrations'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid config']);
            return;
        }

        // Collect all target tables from config
        $tables = [];
        foreach ($config['migrations'] as $migration) {
            if (!empty($migration['table'])) {
                $tables[] = $migration['table'];
            }
            if (!empty($migration['destination_table'])) {
                $tables[] = $migration['destination_table'];
            }
            if (!empty($migration['children'])) {
                foreach ($migration['children'] as $child) {
                    if (!empty($child['table'])) {
                        $tables[] = $child['table'];
                    }
                    if (!empty($child['destination_table'])) {
                        $tables[] = $child['destination_table'];
                    }
                }
            }
        }
        $tables = array_unique($tables);

        if (empty($tables)) {
            echo json_encode(['success' => false, 'message' => 'No tables found in config']);
            return;
        }

        // Create backup directory
        $backupDir = FCPATH . 'tmp/backups/';
        if (!is_dir($backupDir)) mkdir($backupDir, 0777, true);

        $timestamp = date('Ymd_His');
        $entityClean = str_replace('.json', '', $entity);
        $excelFile = $backupDir . $entityClean . '_backup_' . $timestamp . '.xlsx';
        $sqlFile   = $backupDir . $entityClean . '_backup_' . $timestamp . '.sql';

        try {
            // ──────────────────────────────────────
            // 1. EXCEL BACKUP
            // ──────────────────────────────────────
            $writer = WriterEntityFactory::createXLSXWriter();
            $writer->openToFile($excelFile);

            $headerStyle = (new \Box\Spout\Writer\Common\Creator\Style\StyleBuilder())
                ->setFontBold()
                ->setFontSize(11)
                ->setFontColor('FFFFFF')
                ->setBackgroundColor('1565C0')
                ->build();

            $backedUpTables = [];
            $isFirstSheet = true;

            // ──────────────────────────────────────
            // 2. SQL BACKUP header
            // ──────────────────────────────────────
            $sql = "-- PortMax Backup\n";
            $sql .= "-- Entity: " . $entityClean . "\n";
            $sql .= "-- Client: " . $client . "\n";
            $sql .= "-- Date: " . date('Y-m-d H:i:s') . "\n";
            $sql .= "-- Tables: " . implode(', ', $tables) . "\n\n";
            $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

            foreach ($tables as $table) {
                // Validate table name
                if (!preg_match('/^[a-zA-Z0-9_]+$/', $table)) continue;
                if (!$this->db->table_exists($table)) continue;

                // Get all data
                $query = $this->db->get($table);
                $rows = $query->result_array();
                $rowCount = count($rows);

                // ── Excel: write sheet ──
                if ($isFirstSheet) {
                    $sheet = $writer->getCurrentSheet();
                    $isFirstSheet = false;
                } else {
                    $sheet = $writer->addNewSheetAndMakeItCurrent();
                }
                $sheet->setName(substr($table, 0, 31));

                if (empty($rows)) {
                    $fields = $this->db->list_fields($table);
                    $writer->addRow(WriterEntityFactory::createRowFromArray($fields, $headerStyle));
                    $writer->addRow(WriterEntityFactory::createRowFromArray(['(no data)']));
                } else {
                    $headers = array_keys($rows[0]);
                    $writer->addRow(WriterEntityFactory::createRowFromArray($headers, $headerStyle));
                    foreach ($rows as $row) {
                        $writer->addRow(WriterEntityFactory::createRowFromArray(array_values($row)));
                    }
                }

                // ── SQL: CREATE TABLE + INSERT ──
                $createResult = $this->db->query("SHOW CREATE TABLE `{$table}`");
                if ($createResult && $createResult->num_rows() > 0) {
                    $createRow = $createResult->row_array();
                    $createSql = $createRow['Create Table'] ?? '';
                    $sql .= "-- -----------------------------------------------\n";
                    $sql .= "-- Table: {$table}\n";
                    $sql .= "-- -----------------------------------------------\n";
                    $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";
                    $sql .= $createSql . ";\n\n";
                }

                if ($rowCount > 0) {
                    $columns = array_keys($rows[0]);
                    $colList = '`' . implode('`, `', $columns) . '`';
                    $batchSize = 500;
                    for ($i = 0; $i < $rowCount; $i += $batchSize) {
                        $batch = array_slice($rows, $i, $batchSize);
                        $sql .= "INSERT INTO `{$table}` ({$colList}) VALUES\n";
                        $values = [];
                        foreach ($batch as $row) {
                            $vals = [];
                            foreach ($row as $val) {
                                $vals[] = ($val === null) ? 'NULL' : "'" . addslashes($val) . "'";
                            }
                            $values[] = '(' . implode(', ', $vals) . ')';
                        }
                        $sql .= implode(",\n", $values) . ";\n\n";
                    }
                }

                $backedUpTables[] = ['table' => $table, 'rows' => $rowCount];
            }

            $writer->close();

            $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";
            file_put_contents($sqlFile, $sql);

            // Build download URLs via API endpoint
            $excelUrl = 'ImportController/downloadBackup?file=' . urlencode(basename($excelFile));
            $sqlUrl   = 'ImportController/downloadBackup?file=' . urlencode(basename($sqlFile));

            echo json_encode([
                'success' => true,
                'tables' => $backedUpTables,
                'excelFile' => basename($excelFile),
                'sqlFile' => basename($sqlFile),
                'downloadUrl' => $excelUrl,
                'sqlDownloadUrl' => $sqlUrl,
                'message' => 'Backup completed — ' . count($backedUpTables) . ' table(s) exported'
            ]);

        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Backup error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Serve a backup file for download.
     * GET: ?file=filename.xlsx or ?file=filename.sql
     */
    public function downloadBackup()
    {
        $file = basename($this->input->get('file', true));
        if (!$file) {
            show_404();
            return;
        }

        $path = FCPATH . 'tmp/backups/' . $file;
        if (!file_exists($path)) {
            show_404();
            return;
        }

        // Determine content type
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        $contentType = ($ext === 'sql') ? 'application/sql' : 'application/octet-stream';

        header('Content-Type: ' . $contentType);
        header('Content-Disposition: attachment; filename="' . $file . '"');
        header('Content-Length: ' . filesize($path));
        header('Cache-Control: no-cache, no-store, must-revalidate');
        readfile($path);
        exit;
    }

    public function import_status()
    {
        $taskId = $this->input->get('taskId');
        $tmpPath = FCPATH . "tmp/";
        // Ensure folder exists
        if (!is_dir($tmpPath)) {
            mkdir($tmpPath, 0777, true);
        }
        $tmpFile = FCPATH . "tmp/{$taskId}.json";

        if (file_exists($tmpFile)) {
            echo file_get_contents($tmpFile);
        } else {
            echo json_encode(['status' => 'unknown', 'progress' => 0]);
        }
    }

    public function download()
    {
        $file = $this->input->get('file', TRUE);
        $client = $this->input->get('client', TRUE);
        $type = $this->input->get('type', TRUE);

        if (!$file) {
            show_error("File name is required.", 400);
            return;
        }

        if ($type == 'report') {
            $filePath = FCPATH . "uploads/" . $client . "/summary/" . $file;
        } else if ($type == 'temp') {
            $filePath = FCPATH . "tmp/" . $file;
        } else {
            $filePath = FCPATH . "uploads/" . $client . "/" . $file;
            // Fallback: after import, file is moved to 'imported/' folder
            if (!file_exists($filePath)) {
                $filePath = FCPATH . "uploads/" . $client . "/imported/" . $file;
            }
        }

        if (!file_exists($filePath)) {
            show_error("File not found: " . $file, 404);
            return;
        }

        // Force download
        $this->load->helper('download');
        force_download($filePath, NULL);

        // Auto-delete temp files after download
        if ($type == 'temp' && file_exists($filePath)) {
            @unlink($filePath);
        }
    }

    public function cleanupResources()
    {
        $input = json_decode(file_get_contents("php://input"), true);
        $taskId = $input['taskId'] ?? null;
        $client = $input['client'] ?? null;
        $file = $input['file'] ?? null;
        $action = $input['action'] ?? null; // 'archive' or 'delete'

        // 1. Always try to delete the temp progress file if taskId is provided
        if ($taskId) {
            $taskId = basename($taskId);
            $tmpFile = FCPATH . "tmp/{$taskId}.json";
            if (file_exists($tmpFile)) {
                unlink($tmpFile);
            }
        }

        // 2. Handle the physical Excel file if client and file are provided
        if ($client && $file && $action) {
            $sourcePath = FCPATH . "uploads/" . $client . "/" . $file;

            if (file_exists($sourcePath)) {
                if ($action === 'archive') {
                    // Move to 'imported' folder
                    $destDir = FCPATH . "uploads/" . $client . "/imported/";
                    if (!is_dir($destDir)) {
                        mkdir($destDir, 0777, true);
                    }
                    $destPath = $destDir . $file;
                    rename($sourcePath, $destPath);
                } elseif ($action === 'delete') {
                    // Delete the file
                    unlink($sourcePath);
                }
            }
        }

        echo json_encode(['success' => true]);
    }

    public function generateReport()
    {
        $input = json_decode(file_get_contents("php://input"), true);
        $fileId = $input['fileId'] ?? null;
        $client = $input['client'] ?? null;
        $today = date('Y-m-d');

        if (!$fileId) {
            echo json_encode(['success' => false, 'message' => 'fileId missing']);
            return;
        }

        $importDir = FCPATH . "uploads/" . $client . "/imported/";
        $logFile = $importDir.$today.".txt";
        if (!file_exists($logFile)) {
            echo json_encode(['success' => false, 'message' => 'Log file not found']);
            return;
        }
        $handle = fopen($logFile, 'r');
        $records = [];

        while (($line = fgets($handle)) !== false) {
            $line = trim($line);
            if ($line === '') continue;

            list($time, $logFileId, $json) = explode('|', $line, 3);

            $temp = json_decode($json, true);
            if ($logFileId == $fileId && $temp['page'] == 'validate') {
                $records['raw_data'] = $temp;
            } else if (isset($records['raw_data']) && $logFileId == $records['raw_data']['fileId'] && $temp['page'] == 'import') {
                $records['before'] = $temp['before'];
                $records['current'] = $temp['current'];
                $records['after'] = $temp['after'];
                $records['report_config'] = $temp['report_config'] ?? [];
            }
        }
        fclose($handle);

        // Pivot data: merge before/current/after into consolidated tables
        $tables = [];
        $reportConfigs = $records['report_config'] ?? [];
        
        foreach ($reportConfigs as $idx => $cfg) {
            $groupByFields = $cfg['group_by'] ?? [];
            $beforeRows = $records['before'][$idx] ?? [];
            $currentRows = $records['current'][$idx] ?? [];
            $afterRows = $records['after'][$idx] ?? [];

            // Find the label column from actual data (first non-count, non-last_id column)
            $allRows = array_merge($beforeRows, $currentRows, $afterRows);
            $labelColumn = null;
            if (!empty($allRows)) {
                $sampleRow = $allRows[0];
                foreach ($sampleRow as $col => $v) {
                    if ($col !== 'count' && $col !== 'last_id') {
                        $labelColumn = $col;
                        break;
                    }
                }
            }

            // Build lookup maps by label column value
            $buildMap = function($rows) use ($labelColumn) {
                $map = [];
                foreach ($rows as $row) {
                    $label = $labelColumn ? ($row[$labelColumn] ?? '') : '';
                    $key = (string)$label;
                    if ($key === '') $key = '(empty)';
                    // Sum counts for duplicate labels
                    $count = $row['count'] ?? 0;
                    if (isset($map[$key])) {
                        $map[$key]['count'] += $count;
                    } else {
                        $map[$key] = [
                            'label' => $key,
                            'count' => $count
                        ];
                    }
                }
                return $map;
            };

            $beforeMap = $buildMap($beforeRows);
            $currentMap = $buildMap($currentRows);
            $afterMap = $buildMap($afterRows);

            // Collect all unique keys
            $allKeys = array_unique(array_merge(
                array_keys($beforeMap),
                array_keys($currentMap),
                array_keys($afterMap)
            ));

            // Column header from detected label column
            $columnHeader = $labelColumn ? ucwords(str_replace('_', ' ', $labelColumn)) : 'Group';

            $pivotedRows = [];
            foreach ($allKeys as $key) {
                $label = $beforeMap[$key]['label'] ?? ($currentMap[$key]['label'] ?? ($afterMap[$key]['label'] ?? $key));
                $pivotedRows[] = [
                    'label' => $label,
                    'before' => $beforeMap[$key]['count'] ?? 0,
                    'current' => $currentMap[$key]['count'] ?? 0,
                    'after' => $afterMap[$key]['count'] ?? 0
                ];
            }

            $tables[] = [
                'title' => $columnHeader,
                'column_header' => $columnHeader,
                'rows' => $pivotedRows
            ];
        }

        $records['tables'] = $tables;
        // Remove raw before/current/after (no longer needed by frontend)
        unset($records['before'], $records['current'], $records['after'], $records['report_config']);

        echo json_encode(['success' => true, 'message' => 'Report generated.', 'data' => $records]);
    }

    public function downloadReportExcel()
    {
        $input = json_decode(file_get_contents("php://input"), true);
        $fileId = $input['fileId'] ?? null;
        $client = $input['client'] ?? null;

        if (!$fileId || !$client) {
            echo json_encode(['success' => false, 'message' => 'Missing parameters']);
            return;
        }

        // Re-generate the report data
        $_SERVER['REQUEST_METHOD'] = 'POST';
        ob_start();
        // We'll build the report inline
        $today = date('Y-m-d');
        $importDir = FCPATH . "uploads/" . $client . "/imported/";
        $logFile = $importDir . $today . ".txt";

        if (!file_exists($logFile)) {
            echo json_encode(['success' => false, 'message' => 'Log file not found']);
            return;
        }

        $handle = fopen($logFile, 'r');
        $records = [];
        while (($line = fgets($handle)) !== false) {
            $line = trim($line);
            if ($line === '') continue;
            list($time, $logFileId, $json) = explode('|', $line, 3);
            $temp = json_decode($json, true);
            if ($logFileId == $fileId && $temp['page'] == 'validate') {
                $records['raw_data'] = $temp;
            } else if (isset($records['raw_data']) && $logFileId == $records['raw_data']['fileId'] && $temp['page'] == 'import') {
                $records['before'] = $temp['before'];
                $records['current'] = $temp['current'];
                $records['after'] = $temp['after'];
                $records['report_config'] = $temp['report_config'] ?? [];
            }
        }
        fclose($handle);

        // Build pivoted tables (same logic as generateReport)
        $reportConfigs = $records['report_config'] ?? [];
        $tables = [];
        foreach ($reportConfigs as $idx => $cfg) {
            $beforeRows = $records['before'][$idx] ?? [];
            $currentRows = $records['current'][$idx] ?? [];
            $afterRows = $records['after'][$idx] ?? [];

            // Auto-detect label column from actual data
            $allDataRows = array_merge($beforeRows, $currentRows, $afterRows);
            $labelColumn = null;
            if (!empty($allDataRows)) {
                foreach ($allDataRows[0] as $col => $v) {
                    if ($col !== 'count' && $col !== 'last_id') {
                        $labelColumn = $col;
                        break;
                    }
                }
            }

            $buildMap = function($rows) use ($labelColumn) {
                $map = [];
                foreach ($rows as $row) {
                    $label = $labelColumn ? ($row[$labelColumn] ?? '') : '';
                    $key = (string)$label;
                    if ($key === '') $key = '(empty)';
                    // Sum counts for duplicate labels
                    if (isset($map[$key])) {
                        $map[$key] += $row['count'] ?? 0;
                    } else {
                        $map[$key] = $row['count'] ?? 0;
                    }
                }
                return $map;
            };

            $beforeMap = $buildMap($beforeRows);
            $currentMap = $buildMap($currentRows);
            $afterMap = $buildMap($afterRows);
            $allKeys = array_unique(array_merge(array_keys($beforeMap), array_keys($currentMap), array_keys($afterMap)));
            
            $columnHeader = $labelColumn ? ucwords(str_replace('_', ' ', $labelColumn)) : 'Group';

            $pivotedRows = [];
            foreach ($allKeys as $key) {
                $pivotedRows[] = [$key, $beforeMap[$key] ?? 0, $currentMap[$key] ?? 0, $afterMap[$key] ?? 0];
            }
            $tables[] = ['header' => $columnHeader, 'rows' => $pivotedRows];
        }

        // Generate Excel using Box\Spout
        $reportPath = FCPATH . "uploads/" . $client . "/summary/";
        if (!is_dir($reportPath)) mkdir($reportPath, 0777, true);
        $entityName = ucfirst(str_replace('.json', '', $records['raw_data']['Entity'] ?? 'migration'));
        $reportFile = $reportPath . $entityName . "_report_" . date('Y-m-d_His') . ".xlsx";

        $writer = WriterEntityFactory::createXLSXWriter();
        $writer->openToFile($reportFile);

        // Styles
        $titleStyle = (new \Box\Spout\Writer\Common\Creator\Style\StyleBuilder())
            ->setFontBold()
            ->setFontSize(14)
            ->setFontColor('1F4E79')
            ->build();

        $summaryLabelStyle = (new \Box\Spout\Writer\Common\Creator\Style\StyleBuilder())
            ->setFontBold()
            ->setFontSize(11)
            ->setFormat('#,##0')
            ->build();

        $headerStyle = (new \Box\Spout\Writer\Common\Creator\Style\StyleBuilder())
            ->setFontBold()
            ->setFontSize(10)
            ->setFontColor('FFFFFF')
            ->setBackgroundColor('2E7D32')
            ->build();

        $totalStyle = (new \Box\Spout\Writer\Common\Creator\Style\StyleBuilder())
            ->setFontBold()
            ->setFontSize(10)
            ->setBackgroundColor('E0E0E0')
            ->build();

        $tableTitleStyle = (new \Box\Spout\Writer\Common\Creator\Style\StyleBuilder())
            ->setFontBold()
            ->setFontSize(12)
            ->setFontColor('1565C0')
            ->build();

        // Number format style for comma-separated right-aligned numbers
        $numStyle = (new \Box\Spout\Writer\Common\Creator\Style\StyleBuilder())
            ->setFormat('#,##0')
            ->build();

        $totalNumStyle = (new \Box\Spout\Writer\Common\Creator\Style\StyleBuilder())
            ->setFontBold()
            ->setFontSize(10)
            ->setBackgroundColor('E0E0E0')
            ->setFormat('#,##0')
            ->build();

        // Summary rows
        $raw = $records['raw_data'] ?? [];
        $writer->addRow(WriterEntityFactory::createRowFromArray(['Import Report - ' . $entityName . ' - ' . $client], $titleStyle));
        $writer->addRow(WriterEntityFactory::createRowFromArray(['Total Rows', (int)($raw['SheetRowCount'] ?? 0)], $summaryLabelStyle));
        $writer->addRow(WriterEntityFactory::createRowFromArray(['Skipped', (int)($raw['SkippedRowCount'] ?? 0)], $summaryLabelStyle));
        $writer->addRow(WriterEntityFactory::createRowFromArray([])); // Spacer

        foreach ($tables as $table) {
            // Table title
            $writer->addRow(WriterEntityFactory::createRowFromArray([$table['header']], $tableTitleStyle));
            // Header row
            $writer->addRow(WriterEntityFactory::createRowFromArray([$table['header'], 'Overall Count (Before Import)', 'Current Import', 'Overall Count (After Import)'], $headerStyle));
            $totalBefore = 0; $totalCurrent = 0; $totalAfter = 0;
            foreach ($table['rows'] as $row) {
                $writer->addRow(WriterEntityFactory::createRowFromArray([
                    $row[0],
                    (int)($row[1] ?? 0),
                    (int)($row[2] ?? 0),
                    (int)($row[3] ?? 0)
                ], $numStyle));
                $totalBefore += $row[1] ?? 0;
                $totalCurrent += $row[2] ?? 0;
                $totalAfter += $row[3] ?? 0;
            }
            // Total row
            $writer->addRow(WriterEntityFactory::createRowFromArray([
                'Total',
                (int)$totalBefore,
                (int)$totalCurrent,
                (int)$totalAfter
            ], $totalNumStyle));
            $writer->addRow(WriterEntityFactory::createRowFromArray([])); // Spacer
        }

        $writer->close();

        // Send file path for download
        $fileName = basename($reportFile);
        echo json_encode([
            'success' => true,
            'file' => $fileName,
            'client' => $client
        ]);
    }

    private function getReportData($type, $table, $reports, $lastId = "")
    {
        $results = [];
        foreach ($reports as $idx => $config) {
            try {
                $this->db->select($config['select']);
                $this->db->from($table);

                // Support left_join from config
                $hasJoin = false;
                if (!empty($config['left_join'])) {
                    $join = $config['left_join'];
                    $this->db->join($join['table'], $join['on'], 'left');
                    $hasJoin = true;
                }

                if (!empty($config['where']) && !empty($config['where'][$type])) {
                    $this->db->where($config['where'][$type]);
                }
                if (!empty($lastId)) {
                    $this->db->where($config['primary_key'] . ' >', $lastId);
                }
                if (!empty($config['group_by'])) {
                    $groupBy = is_array($config['group_by'])
                        ? $config['group_by']
                        : array_map('trim', explode(',', $config['group_by']));

                    // Qualify columns with main table name to avoid ambiguity when JOINs are used
                    if ($hasJoin) {
                        $groupBy = array_map(function($col) use ($table) {
                            // Only qualify if not already qualified (no dot in the name)
                            return (strpos($col, '.') === false) ? $table . '.' . $col : $col;
                        }, $groupBy);
                    }

                    $this->db->group_by($groupBy);
                }
                $result = $this->db->get();
                $results[$idx] = $result->result_array();
            } catch (\Exception $e) {
                error_log("DEBUG INSERT ERROR in getReportData[$idx]: " . $e->getMessage());
                $results[$idx] = [];
            }
        }
        return $results;
    }

    // Get list of tables from entity config
    public function getEntityTables()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $client = $input['client'] ?? '';
        $entity = $input['entity'] ?? '';

        if (empty($entity)) {
            echo json_encode(['success' => false, 'message' => 'Entity not specified']);
            return;
        }

        $configPath = APPPATH . "config/migration/" . $entity;
        if (strpos($entity, '.json') === false) $configPath .= '.json';

        if (!file_exists($configPath)) {
            echo json_encode(['success' => false, 'message' => 'Config file not found']);
            return;
        }

        $config = json_decode(file_get_contents($configPath), true);
        $tables = [];
        $tableColumns = [];
        $joinConfig = []; // For combined export

        if (!empty($config['migrations'])) {
            foreach ($config['migrations'] as $migration) {
                if (!empty($migration['destination_table'])) {
                    $tbl = $migration['destination_table'];
                    $tables[] = $tbl;
                    $cols = !empty($migration['mappings']) ? array_keys($migration['mappings']) : [];
                    // Add primary key if exists
                    if (!empty($migration['primary_key']) && !in_array($migration['primary_key'], $cols)) {
                        array_unshift($cols, $migration['primary_key']);
                    }
                    $tableColumns[$tbl] = $cols;

                    // Build join config for children
                    $joinConfig['main_table'] = $tbl;
                    $joinConfig['primary_key'] = $migration['primary_key'] ?? '';
                    $joinConfig['children'] = [];
                }
                // Also include child tables
                if (!empty($migration['children'])) {
                    foreach ($migration['children'] as $child) {
                        if (!empty($child['destination_table'])) {
                            $tbl = $child['destination_table'];
                            $tables[] = $tbl;
                            $tableColumns[$tbl] = !empty($child['mappings']) ? array_keys($child['mappings']) : [];
                            $joinConfig['children'][] = [
                                'table' => $tbl,
                                'foreign_key' => $child['foreign_key'] ?? ''
                            ];
                        }
                    }
                }
            }
        }

        echo json_encode([
            'success' => true,
            'tables' => array_values(array_unique($tables)),
            'columns' => $tableColumns,
            'joinConfig' => $joinConfig
        ]);
    }

    // Download full table data as Excel
    public function downloadTableData()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $client = $input['client'] ?? '';
        $tableName = $input['table'] ?? '';
        $columns = $input['columns'] ?? [];

        if (empty($tableName) || empty($client)) {
            echo json_encode(['success' => false, 'message' => 'Table or client not specified']);
            return;
        }

        // Security: only allow alphanumeric and underscore in table name
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $tableName)) {
            echo json_encode(['success' => false, 'message' => 'Invalid table name']);
            return;
        }

        try {
            // Select specific columns or all
            if (!empty($columns) && is_array($columns)) {
                // Sanitize column names
                $safeCols = array_filter($columns, function($c) { return preg_match('/^[a-zA-Z0-9_]+$/', $c); });
                $this->db->select(implode(',', $safeCols));
            } else {
                $this->db->select('*');
            }
            $this->db->from($tableName);
            $result = $this->db->get();
            $rows = $result->result_array();

            if (empty($rows)) {
                echo json_encode(['success' => false, 'message' => 'No data found in table: ' . $tableName]);
                return;
            }

            $reportPath = FCPATH . "tmp/";
            if (!is_dir($reportPath)) mkdir($reportPath, 0777, true);
            $reportFile = $reportPath . $tableName . "_export_" . date('Y-m-d_His') . ".xlsx";

            $writer = WriterEntityFactory::createXLSXWriter();
            $writer->openToFile($reportFile);

            // Title style
            $titleStyle = (new \Box\Spout\Writer\Common\Creator\Style\StyleBuilder())
                ->setFontBold()
                ->setFontSize(14)
                ->setFontColor('1F4E79')
                ->build();

            // Header style
            $headerStyle = (new \Box\Spout\Writer\Common\Creator\Style\StyleBuilder())
                ->setFontBold()
                ->setFontSize(10)
                ->setFontColor('FFFFFF')
                ->setBackgroundColor('2E7D32')
                ->build();

            // Title row
            $writer->addRow(WriterEntityFactory::createRowFromArray(
                [ucfirst($tableName) . ' Data Export - ' . $client . ' (' . count($rows) . ' rows)'],
                $titleStyle
            ));
            $writer->addRow(WriterEntityFactory::createRowFromArray([]));

            // Header row from column names
            $columns = array_keys($rows[0]);
            $writer->addRow(WriterEntityFactory::createRowFromArray($columns, $headerStyle));

            // Data rows
            foreach ($rows as $row) {
                $writer->addRow(WriterEntityFactory::createRowFromArray(array_values($row)));
            }

            $writer->close();

            $fileName = basename($reportFile);
            echo json_encode([
                'success' => true,
                'file' => $fileName,
                'client' => $client,
                'total_rows' => count($rows),
                'type' => 'temp'
            ]);

        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error exporting: ' . $e->getMessage()]);
        }
    }

    // Download combined main + child table data as Excel
    public function downloadCombinedData()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $client = $input['client'] ?? '';
        $entity = $input['entity'] ?? '';
        $selectedColumns = $input['columns'] ?? []; // e.g. {"customer": ["id_customer","firstname"], "address": ["address1"]}

        if (empty($client) || empty($entity)) {
            echo json_encode(['success' => false, 'message' => 'Client or entity not specified']);
            return;
        }

        $configPath = APPPATH . "config/migration/" . $entity;
        if (strpos($entity, '.json') === false) $configPath .= '.json';
        if (!file_exists($configPath)) {
            echo json_encode(['success' => false, 'message' => 'Config file not found']);
            return;
        }

        $config = json_decode(file_get_contents($configPath), true);
        if (empty($config['migrations'])) {
            echo json_encode(['success' => false, 'message' => 'No migrations configured']);
            return;
        }

        $migration = $config['migrations'][0];
        $mainTable = $migration['destination_table'] ?? '';
        $primaryKey = $migration['primary_key'] ?? '';

        if (empty($mainTable)) {
            echo json_encode(['success' => false, 'message' => 'Main table not found']);
            return;
        }

        try {
            // Build SELECT columns with table prefix
            $selectCols = [];
            $headerLabels = [];

            // Main table columns
            if (!empty($selectedColumns[$mainTable])) {
                foreach ($selectedColumns[$mainTable] as $col) {
                    if (preg_match('/^[a-zA-Z0-9_]+$/', $col)) {
                        $selectCols[] = $mainTable . '.' . $col . ' as `' . $mainTable . '.' . $col . '`';
                        $headerLabels[] = $mainTable . '.' . $col;
                    }
                }
            }

            // Child table columns
            $children = $migration['children'] ?? [];
            foreach ($children as $child) {
                $childTable = $child['destination_table'] ?? '';
                if (!empty($selectedColumns[$childTable])) {
                    foreach ($selectedColumns[$childTable] as $col) {
                        if (preg_match('/^[a-zA-Z0-9_]+$/', $col)) {
                            $selectCols[] = $childTable . '.' . $col . ' as `' . $childTable . '.' . $col . '`';
                            $headerLabels[] = $childTable . '.' . $col;
                        }
                    }
                }
            }

            if (empty($selectCols)) {
                echo json_encode(['success' => false, 'message' => 'No columns selected']);
                return;
            }

            $this->db->select(implode(',', $selectCols), false);
            $this->db->from($mainTable);

            // LEFT JOIN child tables
            foreach ($children as $child) {
                $childTable = $child['destination_table'] ?? '';
                $foreignKey = $child['foreign_key'] ?? '';
                if (!empty($childTable) && !empty($foreignKey) && !empty($selectedColumns[$childTable])) {
                    $this->db->join($childTable, $childTable . '.' . $foreignKey . ' = ' . $mainTable . '.' . $primaryKey, 'left');
                }
            }

            $result = $this->db->get();
            $rows = $result->result_array();

            if (empty($rows)) {
                echo json_encode(['success' => false, 'message' => 'No data found']);
                return;
            }

            // Generate Excel
            $reportPath = FCPATH . "tmp/";
            if (!is_dir($reportPath)) mkdir($reportPath, 0777, true);
            $reportFile = $reportPath . $mainTable . "_combined_" . date('Y-m-d_His') . ".xlsx";

            $writer = WriterEntityFactory::createXLSXWriter();
            $writer->openToFile($reportFile);

            $titleStyle = (new \Box\Spout\Writer\Common\Creator\Style\StyleBuilder())
                ->setFontBold()->setFontSize(14)->setFontColor('1F4E79')->build();
            $headerStyle = (new \Box\Spout\Writer\Common\Creator\Style\StyleBuilder())
                ->setFontBold()->setFontSize(10)->setFontColor('FFFFFF')->setBackgroundColor('2E7D32')->build();

            $writer->addRow(WriterEntityFactory::createRowFromArray(
                [ucfirst($mainTable) . ' Combined Export - ' . $client . ' (' . count($rows) . ' rows)'],
                $titleStyle
            ));
            $writer->addRow(WriterEntityFactory::createRowFromArray([]));
            $writer->addRow(WriterEntityFactory::createRowFromArray($headerLabels, $headerStyle));

            foreach ($rows as $row) {
                $writer->addRow(WriterEntityFactory::createRowFromArray(array_values($row)));
            }

            $writer->close();

            echo json_encode([
                'success' => true,
                'file' => basename($reportFile),
                'client' => $client,
                'total_rows' => count($rows),
                'type' => 'temp'
            ]);

        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
}