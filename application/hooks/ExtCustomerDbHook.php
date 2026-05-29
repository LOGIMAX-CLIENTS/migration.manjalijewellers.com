<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ExtCustomerDbHook
{
    private static $_applied = false;

    public function init()
    {
        if (self::$_applied) return;
        self::$_applied = true;

        $CI =& get_instance();

        if ( ! isset($CI->db) || ! is_object($CI->db)) return;

        require_once APPPATH . 'core/MY_DB_mysqli_driver.php';

        $original = $CI->db;
        $wrapper  = new MY_DB_mysqli_driver([]);

        try {
            $ref = new ReflectionObject($original);
            foreach ($ref->getProperties() as $prop) {
                $prop->setAccessible(true);
                $key = $prop->getName();
                $val = $prop->getValue($original);
                $wrapper->$key = $val;
            }
        } catch (Exception $e) {
            foreach (get_object_vars($original) as $key => $val) {
                $wrapper->$key = $val;
            }
        }

        $CI->db = $wrapper;
    }
}
