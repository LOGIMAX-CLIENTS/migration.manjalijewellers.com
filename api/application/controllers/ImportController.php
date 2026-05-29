<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class ImportController extends CI_Controller
{

    public function readExcelRaw()
    {
        $filePath = FCPATH . 'uploads/vinsmera/customer.ods'; // example location

        if (!file_exists($filePath)) {
            show_error("Excel file not found at: " . $filePath);
            return;
        }

        try {
            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, true);

            echo "<pre>";
            print_r($rows); // we will replace this with validation next
            echo "</pre>";
        } catch (Exception $e) {
            log_message('error', 'Excel Read Error: ' . $e->getMessage());
            show_error('Error reading Excel file.');
        }
    }
    public function readExcel($entity)
    {
        // Load config JSON
        $configPath = APPPATH . 'config/migration/' . $entity . '.json';
        if (!file_exists($configPath)) {
            show_error('Config JSON not found');
            return;
        }
        $config = json_decode(file_get_contents($configPath), true);

        //echo '<pre>';print_r($config);exit;

        $filePath = FCPATH . $config['file_path']; // or your actual file
        if (!file_exists($filePath)) {
            show_error('Excel not found');
            return;
        }

        // Load spreadsheet and get the required sheet
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getSheetByName($config['validation']['required_sheet_name']);
        if (!$sheet) {
            show_error("Required sheet '{$config['validation']['required_sheet_name']}' not found in Excel");
            return;
        }

        $rows = $sheet->toArray(null, true, true, true);
        $header = array_shift($rows); // Remove header row, assuming first row is header
        //echo '<pre>';print_r($rows);exit;
        // Helper: Map Excel column letters to names in header
        $excelColumnsMap = [];
        $allow_import = true;
        foreach ($header as $col => $value) {
            $excelColumnsMap[strtolower(trim($value))] = $col;
        }

        $mainConfig = $config['migrations'][0]; // assuming 1 migration for employee
        $allRowsData = [];

        foreach ($rows as $rowIndex => $row) {
            $dbData = [];
            $errors = [];
            $skippedRows = [];
            $skipRow = false; // 👈 added flag to skip entire row

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
                        $skippedRows[] = "<pre>{$dbCol} = {$cellValue} already exists</pre>";
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
                unset($dbData); // completely remove any accumulated data
                echo "<pre>Skipping row " . ($rowIndex + 2) . " completely (duplicate found): " . implode('; ', $skippedRows);
                continue; // move to the next row
            }
            // print_r($errors);exit;
            // Optional: log validation errors
            if (sizeof($errors) > 0 && !empty($errors)) {
                $allow_import = false;
                echo '<pre>Import errors at row ' . ($rowIndex + 2) . ': ' . implode('; ', $errors);
                // continue; // uncomment to skip inserting rows with validation errors
                // $allRowsData[] = $dbData;
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
                                $childerrors[] =  "<pre>Field {$childDbCol}: " . $valid['error'];
                            }

                            $childData[$childDbCol] = $val;
                        }
                        $dbData['child'][] = $childData;

                        if (!empty($childerrors)) {
                            $allow_import = false;
                            echo '<pre>Child Import errors at row ' . ($rowIndex + 2) . ': ' . implode('; ', $childerrors);
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
        if ($allow_import && sizeof($allRowsData) > 0) {
            echo "<pre>Ready to import..:)";
            $this->insertAllRows($mainConfig, $allRowsData);
            // echo '<pre>';print_r($allRowsData);exit;
        } else {
            echo "<pre>Please correct your data. Then import!";
        }
    }

    private function insertAllRows($mainConfig, $data)
    {
        $count = 0;
        foreach ($data as $dbData) {
            $count++;
            if (sizeof($dbData) > 0) {

                // Extract child data
                $children = $dbData['child'] ?? [];
                unset($dbData['child']); // ✅ remove child rows from parent insert

                $this->db->insert($mainConfig['destination_table'], $dbData);
                $mainId = $this->db->insert_id();

                if (isset($mainConfig['children'])) {
                    // 3. Handle children
                    if (sizeof($children) && $mainId > 0) {
                        foreach ($mainConfig['children'] as $childConfig) {
                            foreach ($children as $child) {
                                if ($childConfig['destination_table'] == $child['table']) {
                                    $child[$childConfig['foreign_key']] = $mainId;
                                    unset($child['table']); // ✅ remove child rows from parent insert
                                    // print_r($child);
                                    $this->db->insert($childConfig['destination_table'], $child);
                                }
                            }
                        }
                    }
                }
                // echo "<pre>Please wait... Processing... " . $count . " rows..!";
            }
        }

        echo "<pre>" . $count . " Rows Import completed..:)";
    }
    private function validateField($value, $rules)
    {
        // print_r($rules);exit;

        if (($rules['required'] ?? false) && (empty($value) || $value === '' || $value === null)) {
            return ['status' => false, 'error' => 'Value is required but missing'];
        }
        if (empty($value)) return ['status' => true]; // skip validation if not required and empty

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
                if (isset($rules['length']) && strlen((string)$value) != $rules['length']) {
                    return ['status' => false, 'error' => "Length must be {$rules['length']}"];
                }
                if (isset($rules['min']) && $value < $rules['min']) {
                    return ['status' => false, 'error' => "Value less than minimum {$rules['min']}"];
                }
                if (isset($rules['max']) && $value > $rules['max']) {
                    return ['status' => false, 'error' => "Value greater than maximum {$rules['max']}"];
                }
                if (isset($rules['max_length']) && strlen((string)$value) > $rules['max_length']) {
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
                // print_r($value);exit;
                $d = DateTime::createFromFormat($format, $value);
                // var_dump(!($d && $d->format($format) === $value));exit;
                if (!($d && $d->format($format) === $value)) {
                    return ['status' => false, 'error' => "Date format should be {$format}"];
                } else {
                    // Compare only dates (not time)
                    // print_r($d);exit;
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
        $ids = null;
        $this->db->select('GROUP_CONCAT(' . $returnColumn . ') AS ids');
        $this->db->from($table);
        $this->db->where("FIND_IN_SET(" . $matchColumn . ", '" . $value . "') >", 0, FALSE); // FALSE to avoid escaping
        $query = $this->db->get();
        $row = $query->row();
        // print_r($this->db->last_query());
        if (!empty($row->ids)) {
            $ids = $row->ids;
        } elseif ($createIfMissing) {
            $ids = $this->createifmissing($table, $matchColumn, $value);
        }

        // Not found and no create allowed: return null or original value
        return $ids;
    }

    public function createifmissing($table, $matchColumn, $value)
    {
        $value = explode(',', $value);

        foreach ($value as $v) {
            if (!empty($v)) {
                $this->db->insert($table, [$matchColumn => $v]);
                $ins[] = $this->db->insert_id();
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
        $is_exists = $this->db->query("SELECT * FROM " . $table . " WHERE " . $dbCol . " = '" . $cellValue . "'");

        if ($is_exists->num_rows() > 0) {
            $exists = true;
        } else {
            $exists = false;
        }

        return $exists;
    }

    private function checkFormatValidation($table, $column, $value, $format){
        print_r($format);exit;

    }   
}
