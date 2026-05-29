<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Excel/pdf file creation & export Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Logimax Team
 */

// ------------------------------------------------------------------------

if ( ! function_exists('download_excel_')) {
	/**
	 * Create,download excel for the given data
	 *
	 * @param 	string $filename - The file name you want any resulting file to be called.
	 * @param 	string $title - Headers for top of the spreadsheet
	 * @param 	array $header - Array of columns in order
	 * @param 	array $rows - Excel data
	 * @return	excel file download status
	 */
	function download_excel_($filename, $title, $header, $rows)
	{
	    $CI = get_instance();
        $CI->load->helper('lmx/classes/excel');
        #create an instance of the class
        $xls = new ExportXLS($filename);
        $xls->addHeader($title);
        
        #add 2nd header as an array of columns
        $xls->addHeader($header);
        
        if(sizeof($rows) > 0){
            #add a multi dimension array
            $xls->addRow($rows);
            
            # You can send the sheet directly to the browser as a file 
            $xls->sendFile();
            return array("status" => true,  "title"=>"Success!", "msg" => "Excel downloaded successfully..");
        }else{
            return array("status" => false,  "title"=>"Warning!", "msg" => "No data to export!!");
        }
	}
}

if ( ! function_exists('download_excel')) {
	/**
	 * Create,download excel for the given data
	 *
	 * @param 	string $filename - The file name you want any resulting file to be called.
	 * @param 	string $title - Headers for top of the spreadsheet
	 * @param 	array $header - Array of columns in order
	 * @param 	array $rows - Excel data
	 * @return	excel file download status
	 */
	function download_excel($filename, $title, $header, $rows)
	{
        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();
        
        if(sizeof($rows) > 0){
            
            // Set title
            $objPHPExcel->getActiveSheet()->setTitle($title);
            //set cell A1 content with some text
            $objPHPExcel->getActiveSheet()->setCellValue('A1', $title);
            $objPHPExcel->getActiveSheet()->setCellValue('A2', null);
            //change the font size
            $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(12);
            //make the font become bold
            $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
            // set the header
            $objPHPExcel->getActiveSheet()->fromArray($header, null, 'A3');
            $objPHPExcel->getActiveSheet()->getStyle('A3:Z3')->getFont()->setBold(true);
            
            // Fill worksheet from values in array
            $objPHPExcel->getActiveSheet()->fromArray($rows, null, 'A3');
            
            /* # Sample code
            //merge cell A1 until D1
            $objPHPExcel->getActiveSheet()->mergeCells('A1:D1');
            //set aligment to center for that merged cell (A1 to D1)
            $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            */
            
            header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            //save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
            //if you want to save it as .XLSX Excel 2007 format
            //force user to download the Excel file without writing it to server's HD
            $objWriter->save('php://output');
            
            //$excelOutput = ob_get_clean();

            return array("status" => true,  "title"=>"Success!", "msg" => "Excel downloaded successfully..");
        }else{
            return array("status" => false,  "title"=>"Warning!", "msg" => "No data to export!!");
        }
	}
}

