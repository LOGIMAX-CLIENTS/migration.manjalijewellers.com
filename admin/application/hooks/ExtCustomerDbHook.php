<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * ExtCustomerDbHook
 *
 * Uses ReflectionClass to copy ALL properties (public + protected) from
 * $CI->db to our MY_DB_mysqli_driver wrapper, then swaps $CI->db.
 * This ensures the live MySQL conn_id and all internal CI DB state
 * are correctly transferred regardless of visibility.
 */
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

        // Use Reflection to copy ALL properties including protected ones.
        // get_object_vars() from external scope only returns public properties,
        // missing critical protected state like _trans_status, conn_id (if protected), etc.
        try {
            $ref = new ReflectionObject($original);
            foreach ($ref->getProperties() as $prop) {
                $prop->setAccessible(true);
                $key = $prop->getName();
                $val = $prop->getValue($original);
                $wrapper->$key = $val;
            }
        } catch (Exception $e) {
            // Reflection failed — fall back to public-only copy (original approach)
            foreach (get_object_vars($original) as $key => $val) {
                $wrapper->$key = $val;
            }
        }

        $CI->db = $wrapper;
    }
}
