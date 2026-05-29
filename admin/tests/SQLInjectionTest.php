<?php
/**
 * SQL Injection Fix (EST-R301) — Unit Tests
 * 
 * Tests the whitelist + query binding fixes applied to ret_estimation_model.php.
 * Verifies that:
 *   1. Whitelisted searchField values (tag_id, tag_code, barcode) are accepted
 *   2. Non-whitelisted searchField values return empty array (no DB query executed)
 *   3. SQL injection payloads in searchField are blocked
 *   4. Query bindings (?) are used instead of raw concatenation
 * 
 * Run with: ./vendor/bin/phpunit SQLInjectionTest.php
 */

use PHPUnit\Framework\TestCase;

// Define CI constants
if (!defined('BASEPATH')) define('BASEPATH', dirname(__DIR__) . '/system/');
if (!defined('APPPATH'))  define('APPPATH', dirname(__DIR__) . '/application/');

// ─── Minimal CI stubs ──────────────────────────────────────────────────────────
if (!class_exists('CI_Model')) {
    class CI_Model {
        public $db;
        public $session;
        public function __construct() {}
    }
}

if (!class_exists('CI_Session')) {
    class CI_Session {
        public function userdata($key) { return 1; }
    }
}

// ─── Mock DB Result ─────────────────────────────────────────────────────────────
class Mock_DB_Result {
    private $rows;
    public function __construct(array $rows = []) { $this->rows = $rows; }
    public function result_array() { return $this->rows; }
    public function row_array()    { return !empty($this->rows) ? $this->rows[0] : []; }
    public function num_rows()     { return count($this->rows); }
}

// ─── Spy DB that captures queries and bindings ──────────────────────────────────
class Spy_DB {
    public $last_query_string = '';
    public $last_bindings = [];
    public $query_count = 0;
    public $return_rows = [];

    public function query($sql, $bindings = []) {
        $this->last_query_string = $sql;
        $this->last_bindings = $bindings;
        $this->query_count++;
        return new Mock_DB_Result($this->return_rows);
    }

    public function reset() {
        $this->last_query_string = '';
        $this->last_bindings = [];
        $this->query_count = 0;
    }
}

// Load the actual model
require_once dirname(__DIR__) . '/application/models/ret_estimation_model.php';

use PHPUnit\Framework\Attributes\DataProvider;

// ═════════════════════════════════════════════════════════════════════════════════
// TESTS
// ═════════════════════════════════════════════════════════════════════════════════

class SQLInjectionTest extends TestCase
{
    private $model;
    private $spyDb;

    protected function setUp(): void
    {
        $this->spyDb = new Spy_DB();
        $this->model = new Ret_estimation_model();
        $this->model->db = $this->spyDb;
        $this->model->session = new CI_Session();
    }

    // ─── getTaggingBySearch: Whitelist Tests ─────────────────────────────────

    #[DataProvider('validSearchFieldProvider')]
    public function testGetTaggingBySearch_AcceptsWhitelistedField(string $field): void
    {
        $this->model->getTaggingBySearch('ABC123', $field, 1);

        $this->assertGreaterThan(0, $this->spyDb->query_count,
            "Query should execute for whitelisted field '$field'");
        $this->assertStringContainsString("tag.$field", $this->spyDb->last_query_string,
            "Query should reference the whitelisted column 'tag.$field'");
    }

    public static function validSearchFieldProvider(): array
    {
        return [
            'tag_id'  => ['tag_id'],
            'tag_code'=> ['tag_code'],
            'barcode' => ['barcode'],
        ];
    }

    #[DataProvider('maliciousSearchFieldProvider')]
    public function testGetTaggingBySearch_BlocksMaliciousField(string $maliciousField): void
    {
        $result = $this->model->getTaggingBySearch('test', $maliciousField, 1);

        $this->assertSame([], $result,
            "Malicious searchField '$maliciousField' must return empty array");
        $this->assertSame(0, $this->spyDb->query_count,
            "No DB query should execute for malicious searchField '$maliciousField'");
    }

    public static function maliciousSearchFieldProvider(): array
    {
        return [
            'basic injection'       => ["1=1 --"],
            'union select'          => ["tag_id UNION SELECT * FROM users --"],
            'drop table'            => ["tag_id; DROP TABLE ret_taging; --"],
            'subquery'              => ["(SELECT password FROM admin_users LIMIT 1)"],
            'boolean blind'         => ["tag_id AND 1=1"],
            'time-based blind'      => ["tag_id AND SLEEP(5)"],
            'empty string'          => [''],
            'random column'         => ['nonexistent_column'],
            'admin column'          => ['password'],
            'single quote'          => ["tag_id'"],
            'double dash comment'   => ["tag_id--"],
            'null byte'             => ["tag_id\0"],
        ];
    }

    public function testGetTaggingBySearch_UsesQueryBindings(): void
    {
        $this->model->getTaggingBySearch('GOLD-001', 'tag_code', 5);

        // Query should contain ? placeholders, NOT raw values
        $this->assertStringContainsString('current_branch=?', $this->spyDb->last_query_string,
            "Branch should be bound via ? placeholder");
        $this->assertStringContainsString('= ?', $this->spyDb->last_query_string,
            "SearchTxt should be bound via ? placeholder");

        // Verify bindings array contains the actual values
        $this->assertContains(5, $this->spyDb->last_bindings,
            "Bindings should contain branch value");
        $this->assertContains('GOLD-001', $this->spyDb->last_bindings,
            "Bindings should contain search text value");

        // Query must NOT contain the raw values inline
        $this->assertStringNotContainsString("'GOLD-001'", $this->spyDb->last_query_string,
            "Raw SearchTxt value must NOT appear in query string");
    }

    // ─── getTaggingScanBySearch: Whitelist Tests ─────────────────────────────

    #[DataProvider('validSearchFieldProvider')]
    public function testGetTaggingScanBySearch_AcceptsWhitelistedField(string $field): void
    {
        $this->model->getTaggingScanBySearch('ABC123', $field, 1, '');

        $this->assertGreaterThan(0, $this->spyDb->query_count,
            "Query should execute for whitelisted field '$field'");
    }

    #[DataProvider('maliciousSearchFieldProvider')]
    public function testGetTaggingScanBySearch_BlocksMaliciousField(string $maliciousField): void
    {
        $result = $this->model->getTaggingScanBySearch('test', $maliciousField, 1, '');

        $this->assertSame([], $result,
            "Malicious searchField '$maliciousField' must return empty array");
        $this->assertSame(0, $this->spyDb->query_count,
            "No DB query should execute for malicious searchField");
    }

    public function testGetTaggingScanBySearch_UsesQueryBindings(): void
    {
        $this->model->getTaggingScanBySearch('RING-55', 'barcode', 3, 'ORD-100');

        // Branch should be bound
        $this->assertStringContainsString('current_branch=?', $this->spyDb->last_query_string);

        // SearchTxt and order_no should be bound
        $this->assertStringNotContainsString("'RING-55'", $this->spyDb->last_query_string,
            "Raw SearchTxt must NOT appear in query");
        $this->assertStringNotContainsString("'ORD-100'", $this->spyDb->last_query_string,
            "Raw order_no must NOT appear in query");

        // Bindings should contain all values
        $this->assertContains(3, $this->spyDb->last_bindings, "Branch should be in bindings");
        $this->assertContains('RING-55', $this->spyDb->last_bindings, "SearchTxt should be in bindings");
        $this->assertContains('ORD-100', $this->spyDb->last_bindings, "order_no should be in bindings");
    }

    public function testGetTaggingScanBySearch_EmptySearchTxt_SkipsSearchCondition(): void
    {
        $this->model->getTaggingScanBySearch('', '', 1, '');

        // Query should still execute (branch condition is always applied)
        $this->assertGreaterThan(0, $this->spyDb->query_count);

        // Verify only 1 binding (branch) is used
        $this->assertCount(1, $this->spyDb->last_bindings,
            "Only branch should be bound when search text is empty");
        
        // Ensure WHERE clause doesn't have the search condition
        // We check for "AND tag. = ?" which would happen if it didn't skip
        $this->assertStringNotContainsString('AND tag. = ?', $this->spyDb->last_query_string);
    }

    // ─── getAvailableCustomers: Query Binding Tests ─────────────────────────

    public function testGetAvailableCustomers_UsesBindingsForCustomerSearch(): void
    {
        $this->model->getAvailableCustomers("O'Brien", 1, 1);

        $this->assertStringContainsString('mobile like ?', $this->spyDb->last_query_string,
            "Mobile LIKE should use ? placeholder");
        $this->assertStringContainsString('firstname like ?', $this->spyDb->last_query_string,
            "Firstname LIKE should use ? placeholder");

        // Raw value with special chars must NOT be in query
        $this->assertStringNotContainsString("O'Brien", $this->spyDb->last_query_string,
            "Raw SearchTxt with single quote must NOT be in query");

        // Bindings should contain wrapped values
        $this->assertContains("%O'Brien%", $this->spyDb->last_bindings,
            "Bindings should contain LIKE-wrapped search text");
    }

    public function testGetAvailableCustomers_KarigarSearch_UsesBindings(): void
    {
        $this->model->getAvailableCustomers('Ravi', 1, 3);

        $this->assertStringContainsString('k.firstname like ?', $this->spyDb->last_query_string);
        $this->assertStringContainsString('k.contactno1 like ?', $this->spyDb->last_query_string);
        $this->assertContains('%Ravi%', $this->spyDb->last_bindings);
    }

    public function testGetAvailableCustomers_SQLInjectionInSearchTxt(): void
    {
        $this->model->getAvailableCustomers("'; DROP TABLE customer; --", 1, 1);

        // The injection payload must be in bindings, NOT in the query itself
        $this->assertStringNotContainsString("DROP TABLE", $this->spyDb->last_query_string,
            "SQL injection payload must NOT appear in query string");
        $this->assertContains("%'; DROP TABLE customer; --%", $this->spyDb->last_bindings,
            "Injection payload should be safely wrapped in bindings");
    }

    // ─── getNonTagLots: Query Binding Tests ─────────────────────────────────

    public function testGetNonTagLots_UsesBindingsForSearchTxt(): void
    {
        $this->model->getNonTagLots("LOT-001' OR 1=1 --", 5);

        // SearchTxt LIKE should use binding
        $this->assertStringContainsString('LIKE ?', $this->spyDb->last_query_string,
            "lot_no LIKE should use ? placeholder");
        $this->assertStringNotContainsString("OR 1=1", $this->spyDb->last_query_string,
            "SQL injection in SearchTxt must NOT appear in query");
    }

    // ─── getProductSubDesignBySearch: Query Binding Tests ────────────────────

    public function testGetProductSubDesignBySearch_UsesBindings(): void
    {
        $this->model->getProductSubDesignBySearch("Ring'; DELETE FROM ret_sub_design_master;--", 'PRO1', 1);

        $this->assertStringContainsString('LIKE ?', $this->spyDb->last_query_string);
        $this->assertStringNotContainsString('DELETE', $this->spyDb->last_query_string,
            "SQL injection payload must NOT appear in query");
    }

    // ─── get_mc_va_limit: Query Binding Tests ───────────────────────────────

    public function testGetMcVaLimit_UsesBindingsForNumericParams(): void
    {
        $result = $this->model->get_mc_va_limit(1, 2, 3, 4);

        $this->assertStringContainsString('id_branch = ?', $this->spyDb->last_query_string);
        $this->assertStringContainsString('id_product = ?', $this->spyDb->last_query_string);
        $this->assertStringContainsString('id_design = ?', $this->spyDb->last_query_string);
        $this->assertStringContainsString('id_sub_design = ?', $this->spyDb->last_query_string);

        $this->assertEquals([1, 2, 3, 4], $this->spyDb->last_bindings,
            "All four numeric params should be in bindings array");
    }

    public function testGetMcVaLimit_SQLInjectionInNumericParam(): void
    {
        $this->model->get_mc_va_limit("1 OR 1=1", 2, 3, 4);

        // Injection should be in bindings, not raw query
        $this->assertStringNotContainsString('OR 1=1', $this->spyDb->last_query_string);
        $this->assertContains("1 OR 1=1", $this->spyDb->last_bindings);
    }

    // ─── get_non_tag_stock_details: Query Binding Tests ─────────────────────

    public function testGetNonTagStockDetails_UsesBindings(): void
    {
        $data = [
            'id_section'  => 1,
            'id_product'  => 10,
            'id_branch'   => 5,
            'id_design'   => 20,
            'id_sub_design' => 30,
        ];

        $this->model->get_non_tag_stock_details($data);

        $this->assertStringContainsString('rn.id_section = ?', $this->spyDb->last_query_string);
        $this->assertStringContainsString('rn.product = ?', $this->spyDb->last_query_string);
        $this->assertStringContainsString('rn.branch = ?', $this->spyDb->last_query_string);
        $this->assertStringContainsString('rn.design = ?', $this->spyDb->last_query_string);
        $this->assertStringContainsString('rn.id_sub_design = ?', $this->spyDb->last_query_string);

        $this->assertEquals([1, 10, 5, 20, 30], $this->spyDb->last_bindings);
    }

    public function testGetNonTagStockDetails_InjectionInIdSection(): void
    {
        $data = [
            'id_section'    => "1; DROP TABLE ret_nontag_item;",
            'id_product'    => 10,
            'id_branch'     => 5,
            'id_design'     => 20,
            'id_sub_design' => 30,
        ];

        $this->model->get_non_tag_stock_details($data);

        $this->assertStringNotContainsString('DROP TABLE', $this->spyDb->last_query_string,
            "SQL injection must NOT appear in query string");
        $this->assertContains("1; DROP TABLE ret_nontag_item;", $this->spyDb->last_bindings,
            "Injection payload should be safely in bindings only");
    }
}
