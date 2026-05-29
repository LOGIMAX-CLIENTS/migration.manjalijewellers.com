<?php
ini_set('memory_limit', '512M');
/**
 * Retail Data Export → Excel
 * 
 * Runs all verified export queries against retail_dev 
 * and writes results into a multi-sheet Excel file.
 * 
 * Usage: php export_to_excel.php
 * Output: port_max/sample_retail/retail_export_data.xlsx
 */

require_once __DIR__ . '/../api/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

// --- DB Config ---
// $host = '127.0.0.1';
// $port = 3310;
// $db   = 'konika_prod';
// $user = 'admin_konika';
// $pass = 'LXiZ50QSLk7Ct3qLVE0k';

$host = '127.0.0.1';
$port = 3306;
$db   = 'konika_old';
$user = 'root';
$pass = 'root@123';

$TAG_LIMIT = 10; // 0 = all tags; set >0 for testing

// --- Connect ---
$conn = new mysqli($host, $user, $pass, $db, $port);
if ($conn->connect_error) {
    die("DB Connection failed: " . $conn->connect_error . "\n");
}
$conn->set_charset('utf8mb4');
echo "Connected to {$db}\n\n";

// --- Define sheets & queries ---
$sheets = [

    'port_karigar' => "
        SELECT 
            k.id_karigar       AS old_id,
            k.karigar_type,
            k.firstname        AS first_name,
            k.lastname         AS last_name,
            k.company          AS company_name,
            k.code_karigar     AS short_code,
            k.contactno1       AS mobile,
            k.address1,
            k.address2,
            k.address3,
            cn.name            AS country_name,
            st.name            AS state_name,
            ct.name            AS city_name,
            k.gst_number,
            k.is_tcs,
            k.tcs_tax,
            k.is_tds,
            k.tds_tax,
            k.fin_year_code,
            k.opening_balance_amount AS opening_amount
        FROM ret_karigar k
        LEFT JOIN country cn ON k.id_country = cn.id_country
        LEFT JOIN state st ON k.id_state = st.id_state
        LEFT JOIN city ct ON k.id_city = ct.id_city
        WHERE k.status_karigar = 1",

    'port_category' => "
        SELECT 
            c.id_ret_category  AS old_id,
            c.name             AS category_name,
            c.hsn_code,
            c.cat_code         AS short_code,
            c.cat_type         AS category_type,
            c.is_multi_metal_cateory AS is_multi_metal,
            m.metal            AS metal_name,
            tg.tgrp_name       AS tax_group,
            c.description
        FROM ret_category c
        LEFT JOIN metal m ON c.id_metal = m.id_metal
        LEFT JOIN ret_taxgroupmaster tg ON c.tgrp_id = tg.tgrp_id
        WHERE c.status = 1",

    'port_purity' => "
        SELECT 
            id_purity          AS old_id,
            purity             AS purity_name,
            description
        FROM ret_purity
        WHERE status = 1",

    'port_category_purity_map' => "
        SELECT 
            c.name             AS category_name,
            p.purity           AS purity_name
        FROM ret_metal_cat_purity cpm
        LEFT JOIN ret_category c ON cpm.id_category = c.id_ret_category
        LEFT JOIN ret_purity p ON cpm.id_purity = p.id_purity",

    'port_product_master' => "
        SELECT 
            p.pro_id           AS old_id,
            p.product_name,
            p.product_short_code AS short_code,
            p.hsn_code,
            p.product_status     AS status,
            m.metal              AS metal_name,
            c.name               AS category_name,
            tg.tgrp_name         AS tax_group,
            p.tax_type,
            p.no_of_pieces,
            p.stock_type,
            u.uom_name           AS uom,
            p.purchase_mode      AS purchase_based_on,
            p.sales_mode         AS sales_based_on,
            p.reorder_based_on,
            p.calculation_based_on
        FROM ret_product_master p
        JOIN ret_category c ON p.cat_id = c.id_ret_category
        LEFT JOIN metal m ON p.metal_type = m.id_metal
        LEFT JOIN ret_taxgroupmaster tg ON p.tgrp_id = tg.tgrp_id
        LEFT JOIN ret_uom u ON p.uom_id = u.uom_id
        WHERE p.product_status = 1",

    'port_design_master' => "
        SELECT 
            design_no          AS old_id,
            design_name,
            design_code AS short_code
        FROM ret_design_master
        WHERE design_status = 1",

    'port_sub_design_master' => "
        SELECT 
            id_sub_design      AS old_id,
            sub_design_name,
            sub_design_code AS short_code
        FROM ret_sub_design_master
        WHERE status = 1",

    'port_product_mapping' => "
        SELECT 
            p.product_name,
            d.design_name
        FROM ret_product_mapping pm
        JOIN ret_product_master p ON pm.pro_id = p.pro_id
        JOIN ret_design_master d ON pm.id_design = d.design_no",

    'port_sub_design_mapping' => "
        SELECT 
            p.product_name,
            d.design_name,
            sd.sub_design_name
        FROM ret_sub_design_mapping sdm
        JOIN ret_product_master p ON sdm.id_product = p.pro_id
        JOIN ret_design_master d ON sdm.id_design = d.design_no
        JOIN ret_sub_design_master sd ON sdm.id_sub_design = sd.id_sub_design",

    'port_section' => "
        SELECT 
            id_section         AS old_id,
            section_name,
            section_short_code AS short_code,
            is_home_bill_counter
        FROM ret_section
        WHERE status = 1",

    'port_product_section' => "
        SELECT 
            p.product_name,
            s.section_name
        FROM ret_product_section ps
        LEFT JOIN ret_product_master p ON ps.pro_id = p.pro_id
        LEFT JOIN ret_section s ON ps.id_section = s.id_section",

    'port_size' => "
        SELECT 
            sz.id_size         AS old_id,
            p.product_name,
            sz.value       AS size_value,
            sz.name        AS size_uom,
            sz.active      AS status
        FROM ret_size sz
        LEFT JOIN ret_product_master p ON sz.id_product = p.pro_id",

    'port_stone_master' => "
        SELECT 
            s.stone_id         AS old_id,
            s.stone_name,
            s.stone_code       AS short_code,
            st.stone_type,
            u.uom_name         AS uom,
            s.is_certificate_req AS certificate_req,
            s.is_4c_req        AS four_c_req
        FROM ret_stone s
        LEFT JOIN ret_stone_type st ON s.stone_type = st.id_stone_type
        LEFT JOIN ret_uom u ON s.uom_id = u.uom_id
        WHERE s.stone_status = 1",

    'port_charges_master' => "
        SELECT 
            id_charge          AS old_id,
            name_charge        AS charges_name,
            code_charge        AS short_code,
            value_charge       AS amount,
            charge_tax         AS charge_tax_percent,
            tag_display
        FROM ret_charges",

    // --- TAG/STONE/CHARGE: columns match temp table schemas ---

    'port_tag_master' => "
        SELECT 
            t.cat_type             AS old_metal_id,
            p.cat_id               AS old_category_id,
            t.product_id           AS old_product_id,
            t.design_id            AS old_design_id,
            t.id_sub_design        AS old_sub_design_id,
            t.purity               AS old_purity_id,
            t.current_branch       AS old_branch_id,
            l.gold_smith           AS old_karigar_id,
            t.id_section           AS old_counter_id,
            t.size                 AS old_size_id,
            m.metal                AS metal_name,
            c.name                 AS category_name,
            p.product_name,
            d.design_name,
            sd.sub_design_name,
            pu.purity              AS purity_name,
            b.name                 AS branch_name,
            kar.firstname          AS karigar_name,
            sec.section_name       AS counter_name,
            sz.value               AS size_value,
            sz.name                AS size_name,
            t.piece                AS pieces,
            t.gross_wt,
            t.net_wt,
            t.less_wt,
            p.sales_mode           AS sales_type,
            p.calculation_based_on AS prod_calc_based_on,
            t.calculation_based_on AS tag_calc_based_on,
            t.retail_max_wastage_percent AS wastage_per,
            t.tag_mc_type          AS mc_type,
            t.tag_mc_value         AS mc_value,
            t.tag_code             AS tag_number,
            t.hu_id                AS huid1,
            t.hu_id2               AS huid2
        FROM ret_taging t
        LEFT JOIN ret_product_master p ON t.product_id = p.pro_id
        LEFT JOIN ret_category c ON p.cat_id = c.id_ret_category
        LEFT JOIN metal m ON t.cat_type = m.id_metal
        LEFT JOIN ret_design_master d ON t.design_id = d.design_no
        LEFT JOIN ret_sub_design_master sd ON t.id_sub_design = sd.id_sub_design
        LEFT JOIN ret_section sec ON t.id_section = sec.id_section
        LEFT JOIN ret_purity pu ON t.purity = pu.id_purity
        LEFT JOIN branch b ON t.current_branch = b.id_branch
        LEFT JOIN ret_lot_inwards l ON l.lot_no = t.tag_lot_id
        LEFT JOIN ret_karigar kar ON kar.id_karigar = l.gold_smith
        LEFT JOIN ret_size sz ON sz.id_size = t.size
        WHERE t.tag_status IN (0, 1)" . ($TAG_LIMIT > 0 ? " LIMIT {$TAG_LIMIT}" : ""),

    'port_tag_stone_details' => "
        SELECT 
            ts.stone_id            AS old_stone_id,
            sm.stone_type          AS old_stone_type_id,
            ts.uom_id              AS old_uom_id,
            t.tag_code             AS tag_number,
            rst.stone_type         AS stone_type_name,
            sm.stone_name,
            u.uom_name,
            ts.is_apply_in_lwt     AS is_less_id,
            ts.pieces,
            ts.wt                  AS stone_wt,
            ts.stone_cal_type      AS stone_calc_type,
            ts.rate_per_gram,
            ts.amount
        FROM ret_taging_stone ts
        LEFT JOIN ret_taging t ON ts.tag_id = t.tag_id
        LEFT JOIN ret_stone sm ON ts.stone_id = sm.stone_id
        LEFT JOIN ret_stone_type rst ON rst.id_stone_type = sm.stone_type
        LEFT JOIN ret_uom u ON ts.uom_id = u.uom_id
        WHERE t.tag_status IN (0, 1)" . ($TAG_LIMIT > 0 ? " AND t.tag_id IN (SELECT tag_id FROM (SELECT tag_id FROM ret_taging WHERE tag_status IN (0,1) LIMIT {$TAG_LIMIT}) tmp)" : "") . "",

    'port_tag_charges' => "
        SELECT 
            t.tag_code             AS tag_number,
            ch.name_charge         AS charge_name,
            tc.charge_value
        FROM ret_taging_charges tc
        LEFT JOIN ret_taging t ON tc.tag_id = t.tag_id
        LEFT JOIN ret_charges ch ON tc.charge_id = ch.id_charge
        WHERE t.tag_status IN (0, 1)" . ($TAG_LIMIT > 0 ? " AND t.tag_id IN (SELECT tag_id FROM (SELECT tag_id FROM ret_taging WHERE tag_status IN (0,1) LIMIT {$TAG_LIMIT}) tmp)" : "") . "",
];

// --- Column format rules (by column name pattern) ---
// Weight columns → 3 decimal places
$weightCols = ['gross_wt', 'less_wt', 'net_wt', 'stone_wt', 'size_value', 'wt'];

// Amount/rate columns → 2 decimal places
$amountCols = ['amount', 'rate_per_gram',
    'value_charge', 'charge_tax_percent', 'charge_value',
    'opening_amount', 'mc_value',
    'tcs_tax', 'tds_tax'];

// Percentage columns → 2 decimal places (no % symbol)
$pctCols = ['wastage_per', 'charge_tax_percent'];

// Integer columns → 0 decimal places
$intCols = ['karigar_type', 'is_multi_metal', 'is_tcs', 'is_tds',
    'category_type', 'pieces', 'piece', 'no_of_pieces',
    'tag_display', 'certificate_req', 'four_c_req', 'is_less_id',
    'mc_type', 'stone_calc_type', 'is_home_bill_counter',
    'tax_type', 'stock_type', 'purchase_based_on', 'sales_based_on', 'sales_type',
    'reorder_based_on', 'calculation_based_on', 'prod_calc_based_on', 'tag_calc_based_on',
    'status',
    'old_id', 'old_metal_id', 'old_category_id', 'old_product_id', 'old_design_id',
    'old_sub_design_id', 'old_purity_id', 'old_branch_id', 'old_karigar_id',
    'old_counter_id', 'old_size_id', 'old_stone_id', 'old_stone_type_id', 'old_uom_id'];

/**
 * Determine the format code for an Excel column
 */
function getColumnFormat($colName) {
    global $weightCols, $amountCols, $pctCols, $intCols;
    
    $colName = strtolower($colName);
    
    if (in_array($colName, $weightCols))  return ['fmt' => '0.000',  'align' => 'right'];
    if (in_array($colName, $pctCols))     return ['fmt' => '0.00',   'align' => 'right'];
    if (in_array($colName, $amountCols))  return ['fmt' => '0.00',   'align' => 'right'];
    if (in_array($colName, $intCols))     return ['fmt' => '0',      'align' => 'right'];
    
    // Default: text, left-aligned
    return ['fmt' => '@', 'align' => 'left'];
}

// --- Header styling ---
$headerStyle = [
    'font' => [
        'bold' => true,
        'color' => ['rgb' => 'FFFFFF'],
        'size' => 11,
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => '2D5F8A'],
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical'   => Alignment::VERTICAL_CENTER,
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['rgb' => '999999'],
        ],
    ],
];

// --- Create workbook ---
$spreadsheet = new Spreadsheet();
$spreadsheet->removeSheetByIndex(0); // Remove default sheet

$sheetIndex = 0;
$totalRows  = 0;

foreach ($sheets as $sheetName => $query) {
    echo "Exporting: {$sheetName}... ";
    
    $result = $conn->query($query);
    if (!$result) {
        echo "ERROR: " . $conn->error . "\n";
        continue;
    }
    
    $rowCount = $result->num_rows;
    echo "{$rowCount} rows... ";
    
    // Create sheet
    $sheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, $sheetName);
    $spreadsheet->addSheet($sheet, $sheetIndex++);
    
    // Write headers + data
    if ($rowCount > 0) {
        $fields = $result->fetch_fields();
        $colFormats = []; // column index => format info
        
        // Write headers & determine formats
        $col = 1;
        foreach ($fields as $field) {
            $sheet->setCellValue([$col, 1], $field->name);
            $colFormats[$col] = getColumnFormat($field->name);
            $col++;
        }
        
        // Apply header style
        $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col - 1);
        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(22);
        
        // Write data rows with proper types
        $row = 2;
        while ($data = $result->fetch_assoc()) {
            $col = 1;
            foreach ($data as $value) {
                if ($value !== null && $value !== '') {
                    $fmt = $colFormats[$col];
                    if ($fmt['align'] === 'right' && is_numeric($value)) {
                        // Store as actual number so Excel treats it numerically
                        $sheet->setCellValue([$col, $row], (float)$value);
                    } else {
                        // Store as explicit string to prevent Excel auto-conversion
                        $sheet->setCellValueExplicit(
                            [$col, $row], 
                            $value, 
                            \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING
                        );
                    }
                }
                $col++;
            }
            $row++;
        }
        
        $lastRow = $row - 1;
        
        // Apply column formatting & alignment
        foreach ($colFormats as $colIdx => $fmt) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx);
            $range = "{$colLetter}2:{$colLetter}{$lastRow}";
            
            // Number format
            $sheet->getStyle($range)->getNumberFormat()
                ->setFormatCode($fmt['fmt']);
            
            // Alignment
            $alignConst = $fmt['align'] === 'right' 
                ? Alignment::HORIZONTAL_RIGHT 
                : Alignment::HORIZONTAL_LEFT;
            $sheet->getStyle($range)->getAlignment()
                ->setHorizontal($alignConst);
        }
        
        // Auto-width columns
        foreach (range(1, count($fields)) as $colIdx) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }
        
        // Freeze header row
        $sheet->freezePane('A2');
        
        // Auto-filter
        $sheet->setAutoFilter("A1:{$lastCol}1");
    } else {
        $sheet->setCellValue('A1', 'No data found');
    }
    
    $totalRows += $rowCount;
    $result->free();
    echo "OK\n";
}

// --- Write file ---
$outputFile = __DIR__ . '/retail_export_data_' . date('Ymd_His') . '.xlsx';
$writer = new Xlsx($spreadsheet);
$writer->save($outputFile);

echo "\n" . str_repeat('=', 50) . "\n";
echo "Export complete!\n";
echo "Sheets: {$sheetIndex}\n";
echo "Total rows: {$totalRows}\n";
echo "Output: {$outputFile}\n";
echo str_repeat('=', 50) . "\n";

$conn->close();
