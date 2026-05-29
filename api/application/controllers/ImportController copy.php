<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class ImportController extends CI_Controller {

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
        $configPath = APPPATH . 'config/migration/'.$entity.'.json';
        if (!file_exists($configPath)) {
            show_error('Config JSON not found');
            return;
        }
        $config = json_decode(file_get_contents($configPath), true);

        //echo '<pre>';print_r($config);exit;

        $filePath = FCPATH . $config['file_path']; // or your actual file

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
        foreach ($header as $col => $value) {
            $excelColumnsMap[strtolower(trim($value))] = $col;
        }

        $mainConfig = $config['migrations'][0]; // assuming 1 migration for employee

        foreach ($rows as $rowIndex => $row) {
            $dbData = [];
            $errors = [];
            $skipRow = false; // 👈 added flag to skip entire row
        
            // 1. Map and validate main table fields
            foreach ($mainConfig['mappings'] as $dbCol => $mapInfo) {
        
                $srcColName = strtolower(isset($mapInfo['lookup']) ? $mapInfo['lookup']['source_column'] : $mapInfo['source_column']);
                $excelColLetter = $excelColumnsMap[$srcColName];
                $cellValue = trim($row[$excelColLetter]);
                $table = $mainConfig['destination_table'];
        
                // Lookup if defined
                if (isset($mapInfo['lookup'])) {
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
                if (!empty($mapInfo['check_if_exists'])) {
                    $is_exists = $this->checkDataExists($table, $dbCol, $cellValue);
                    if ($is_exists) {
                        echo "<pre>Row {$rowIndex}: {$dbCol} = {$cellValue} already exists. Skipping entire row.</pre>";
                        $skipRow = true;
                        break; // stop processing further fields for this row
                    }
                }
        
                if (!isset($excelColumnsMap[$srcColName])) {
                    $errors[] = "Source column '{$srcColName}' not found in Excel header.";
                    continue;
                }
                
                // Validate
                $valid = $this->validateField($cellValue, $mapInfo['validations']);
                if (!$valid['status']) {
                    $errors[] = "Row {$rowIndex}, Field '{$dbCol}': " . $valid['error'];
                }
        
                $dbData[$dbCol] = $cellValue;
            }
        
            
            // ✅ Skip the whole row if duplicate found
            if ($skipRow) {
                unset($dbData); // completely remove any accumulated data
                //echo "<pre>Skipping row {$rowIndex} completely (duplicate found)</pre>";
                continue; // move to the next row
            }
            
            // Optional: log validation errors
            if (!empty($errors)) {
                echo '<pre>Import errors at row ' . ($rowIndex + 2) . ': ' . implode('; ', $errors);
                // continue; // uncomment to skip inserting rows with validation errors
            }
        
            // 2. Insert main data
            if(sizeof($dbData) > 0){
                $this->db->insert($mainConfig['destination_table'], $dbData);
                $mainId = $this->db->insert_id();
            }
            // 3. Handle children
            if (isset($mainConfig['children']) && $mainId > 0) {
                foreach ($mainConfig['children'] as $childConfig) {
                    $childData = [];
                    foreach ($childConfig['mappings'] as $childDbCol => $childMapInfo) {
        
                        $srcColName = strtolower(isset($childMapInfo['lookup']) ? $childMapInfo['lookup']['source_column'] : $childMapInfo['source_column']);
                        $excelColLetter = $excelColumnsMap[$srcColName];
                        $val = trim($row[$excelColLetter]);
        
                        if (isset($childMapInfo['lookup'])) {
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
                            log_message('error', "Child import error row {$rowIndex} field {$childDbCol}: " . $valid['error']);
                        }
        
                        $childData[$childDbCol] = $val;
                    }
        
                    $childData[$childConfig['foreign_key']] = $mainId;
                    $this->db->insert($childConfig['destination_table'], $childData);
                }
            }
        }
        
        
        echo "Import completed!";
    }
    private function validateField($value, $rules)
    {
        if (($rules['required'] ?? false) && ($value === '' || $value === null)) {
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
                $d = DateTime::createFromFormat($format, $value);
                if (!($d && $d->format($format) === $value)) {
                    return ['status' => false, 'error' => "Date format should be {$format}"];
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
        $this->db->select('GROUP_CONCAT('.$returnColumn.') AS ids');
        $this->db->from($table);
        $this->db->where("FIND_IN_SET(".$matchColumn.", '".$value."') >", 0, FALSE); // FALSE to avoid escaping
        $query = $this->db->get();
        $row = $query->row();
//print_r($this->db->last_query());
        if (!empty($row->ids)) {
            $ids = $row->ids;
        } elseif ($createIfMissing) {
            $ids = $this->createifmissing($table, $matchColumn,$value);
        }
        // Not found and no create allowed: return null or original value
        return $ids;
    }

    public function createifmissing($table,$matchColumn,$value){
        $value = explode(',',$value);

        foreach ($value as $v) {
            if(!empty($v)){
                $this->db->insert($table, [$matchColumn => $v]);
                $ins[] = $this->db->insert_id();
            }
        }
        return implode(',',$ins);
    }

    public function upd_emp_passhash(){
        $noPass = $this->db->query("SELECT * FROM employee WHERE (pwd_hash IS NULL OR pwd_hash = '')")->result_array();
        foreach($noPass as $row){
            $upddata = array("pwd_hash" => password_hash(trim($row['emp_code']), PASSWORD_DEFAULT));
            $this->db->where('id_employee', $row['id_employee']);
            $transtatus = $this->db->update('employee', $upddata);
        }
    }

    private function checkDataExists($table,$dbCol,$cellValue){
        $is_exists = $this->db->query("SELECT * FROM ".$table." WHERE ".$dbCol." = '".$cellValue."'" );
        if($is_exists->num_rows() > 0){
            $exists = true;
        }else{
            $exists = false;
        }
        return $exists;
    }


}
