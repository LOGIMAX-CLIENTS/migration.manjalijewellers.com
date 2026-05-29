<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportController extends CI_Controller {

    public function readExcelRaw()
    {
        $filePath = FCPATH . 'uploads/employee_data.xlsx'; // example location

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
    public function readExcel()
    {
        $filePath = FCPATH . 'uploads/employee_data.xlsx'; // or your actual file

        // Load config JSON
        $configPath = APPPATH . 'config/migration/employee.json';
        if (!file_exists($configPath)) {
            show_error('Config JSON not found');
            return;
        }
        $config = json_decode(file_get_contents($configPath), true);

        // Load spreadsheet and get the required sheet
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getSheetByName($config['validation']['required_sheet_name']);
        if (!$sheet) {
            show_error("Required sheet '{$config['validation']['required_sheet_name']}' not found in Excel");
            return;
        }

        $rows = $sheet->toArray(null, true, true, true);
        $header = array_shift($rows); // Remove header row, assuming first row is header

        // Helper: Map Excel column letters to names in header
        $excelColumnsMap = [];
        foreach ($header as $col => $value) {
            $excelColumnsMap[strtolower(trim($value))] = $col;
        }

        $mainConfig = $config['migrations'][0]; // assuming 1 migration for employee

        foreach ($rows as $rowIndex => $row) {
            $dbData = [];
            $errors = [];

            // 1. Map and validate main table fields
            foreach ($mainConfig['mappings'] as $dbCol => $mapInfo) {
                $srcColName = strtolower($mapInfo['source_column']);
                if (!isset($excelColumnsMap[$srcColName])) {
                    $errors[] = "Source column '{$srcColName}' not found in Excel header.";
                    continue;
                }
                $excelColLetter = $excelColumnsMap[$srcColName];
                $cellValue = trim($row[$excelColLetter]);

                // Validate
                $valid = $this->validateField($cellValue, $mapInfo['validations']);
                if (!$valid['status']) {
                    $errors[] = "Row {$rowIndex}, Field '{$dbCol}': " . $valid['error'];
                }

                // Lookup if defined
                if (isset($mapInfo['lookup'])) {
                    $cellValue = $this->lookupValue(
                        $mapInfo['lookup']['table'],
                        $mapInfo['lookup']['match_column'],
                        $cellValue,
                        $mapInfo['lookup']['return_column'],
                        $mapInfo['lookup']['create_if_missing'] ?? false
                    );
                }

                $dbData[$dbCol] = $cellValue;

            }

            

            /*if (!empty($errors)) {
                // Log or handle errors as per your needs
                log_message('error', 'Import errors at row ' . ($rowIndex + 2) . ': ' . implode('; ', $errors));
                continue; // skip this row or handle differently
            }*/
            // 2. Insert main data
            $this->db->insert($mainConfig['destination_table'], $dbData);
            $mainId = $this->db->insert_id();
            // 3. Handle children
            if (isset($mainConfig['children'])) {
                foreach ($mainConfig['children'] as $childConfig) {
                    $childData = [];
                    foreach ($childConfig['mappings'] as $childDbCol => $childMapInfo) {
                        $srcColName = strtolower($childMapInfo['source_column']);
                        if (!isset($excelColumnsMap[$srcColName])) continue;
                        $excelColLetter = $excelColumnsMap[$srcColName];
                        $val = trim($row[$excelColLetter]);

                        // Validate child field
                        $valid = $this->validateField($val, $childMapInfo['validations']);
                        if (!$valid['status']) {
                            // Log error but proceed or skip
                            log_message('error', "Child import error row {$rowIndex} field {$childDbCol}: " . $valid['error']);
                        }

                        // Lookup for child if any
                        if (isset($childMapInfo['lookup'])) {
                            $val = $this->lookupValue(
                                $childMapInfo['lookup']['table'],
                                $childMapInfo['lookup']['match_column'],
                                $val,
                                $childMapInfo['lookup']['return_column'],
                                $childMapInfo['lookup']['create_if_missing'] ?? false
                            );
                        }

                        $childData[$childDbCol] = $val;
                    }

                    // Add foreign key
                    $childData[$childConfig['foreign_key']] = $mainId;

                    // Insert child row
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
        $this->db->select($returnColumn);
        $this->db->where($matchColumn, $value);
        $query = $this->db->get($table);
        $row = $query->row();

        if ($row) {
            return $row->$returnColumn;
        } elseif ($createIfMissing) {
            $this->db->insert($table, [$matchColumn => $value]);
            return $this->db->insert_id();
        }
        // Not found and no create allowed: return null or original value
        return null;
    }


}
