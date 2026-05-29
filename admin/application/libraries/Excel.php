<?php
 if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
/* 
 *  ======================================= 
 *  License    : Protected 
 *   
 *  ======================================= 
 */  
require_once APPPATH."/third_party/PHPExcel.php"; 
 
class Excel extends PHPExcel { 
    public function __construct() { 
        parent::__construct(); 
    } 
}
?>