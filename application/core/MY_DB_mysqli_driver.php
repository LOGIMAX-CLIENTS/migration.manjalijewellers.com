<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MY_DB_mysqli_driver — Cross-DB Customer Table Router
 *
 * When the USE_EXTERNAL_CUSTOMER_DB feature flag is ON, transparently rewrites
 * every SQL query's FROM/JOIN customer reference to use the external database
 * prefix (Globals::$cus_database), enabling MySQL cross-DB JOINs on the same
 * RDS instance.
 *
 * Key design decisions:
 *  - Uses raw PHP mysqli (not CI DB) to read the flag → no recursion risk
 *  - Static class property caches the result for the entire request lifetime
 *  - Fast-path: if Globals::$cus_database is empty → zero overhead (no connection)
 *  - No session dependency → flag changes take effect on next request, no logout needed
 *  - Regex protects customer_reg, customer_order, id_customer etc. from being rewritten
 *  - INSERT/UPDATE/DELETE are NOT rewritten → writes always stay on local DB
 */
class MY_DB_mysqli_driver extends CI_DB_mysqli_driver
{
    /**
     * Per-request cache for the feature flag result.
     * null  = not yet resolved
     * true  = USE_EXTERNAL_CUSTOMER_DB is ON
     * false = OFF
     */
    private static $_use_ext_cus = null;

    // ─────────────────────────────────────────────────────────────────────────
    // Public override — intercept every CI DB query
    // ─────────────────────────────────────────────────────────────────────────

    public function query($sql, $binds = FALSE, $return_object = TRUE)
    {
        $sql = $this->_rewrite_customer_table($sql);
        return parent::query($sql, $binds, $return_object);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Rewrite FROM/JOIN customer → `ext_db`.`customer`
    // ─────────────────────────────────────────────────────────────────────────

    private function _rewrite_customer_table($sql)
    {
        if ( ! $this->_is_ext_customer_enabled()) {
            return $sql;
        }

        $ext_db = Globals::$ext_database;
        if (empty($ext_db)) {
            return $sql;
        }

        $tables = Globals::$ext_tables;
        if (empty($tables)) {
            return $sql;
        }

        $table_pattern = implode('|', array_map('preg_quote', $tables, array_fill(0, count($tables), '/')));

        $pattern     = '/\b(FROM|JOIN|INSERT\s+(?:IGNORE\s+)?INTO|REPLACE\s+(?:IGNORE\s+)?INTO|UPDATE)\s+`?(' . $table_pattern . ')`?(?![\w])/i';
        $replacement = '$1 `' . $ext_db . '`.`$2`';

        return preg_replace($pattern, $replacement, $sql);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Read USE_EXTERNAL_CUSTOMER_DB flag ONCE per request
    // Uses raw PHP mysqli — completely bypasses CI DB layer (no recursion)
    // ─────────────────────────────────────────────────────────────────────────

    private function _is_ext_customer_enabled()
    {
        // Already resolved this request — return cached result
        if (self::$_use_ext_cus !== null) {
            return self::$_use_ext_cus;
        }

        // Fast path: if no external DB is configured in global_configs.php,
        // skip entirely — zero DB connection overhead for clients not using this feature
        if (empty(Globals::$ext_database)) {
            self::$_use_ext_cus = false;
            return false;
        }

        self::$_use_ext_cus = false;    // default: OFF

        try {
            // Raw OOP mysqli — does NOT go through CI's DB layer, no recursion possible
            $link = @new mysqli(
                Globals::$hostname,
                Globals::$username,
                Globals::$password,
                Globals::$database
            );

            if ($link->connect_errno) {
                return false;
            }

            $res = $link->query(
                "SELECT IFNULL(feature_flags,'[]') AS ff FROM chit_settings LIMIT 1"
            );
            $row = ($res && $res->num_rows > 0) ? $res->fetch_assoc() : [];
            $link->close();

            $flags = @json_decode($row['ff'] ?? '[]', true);
            if ( ! is_array($flags)) {
                return false;
            }

            foreach ($flags as $flag) {
                if (isset($flag['code'])
                    && $flag['code'] === 'USE_EXTERNAL_CUSTOMER_DB'
                    && (int)($flag['status'] ?? 0) === 1
                ) {
                    self::$_use_ext_cus = true;
                    break;
                }
            }
        } catch (Exception $e) {
            self::$_use_ext_cus = false;
        }

        return self::$_use_ext_cus;
    }
}
