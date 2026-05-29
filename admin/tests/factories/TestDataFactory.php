<?php
/**
 * Test Data Factories for eTail v3
 * 
 * Usage in tests:
 *   class MyTest extends TestCase {
 *       use EstimationFactory;
 *       
 *       public function test_example() {
 *           $estimation = $this->makeValidEstimation(['discount' => 500]);
 *       }
 *   }
 */

trait EstimationFactory {

    /**
     * Create a valid estimation data array
     */
    protected function makeValidEstimation(array $overrides = []): array {
        return array_merge([
            'id_branch' => 1,
            'esti_for' => 1,
            'cus_id' => 101,
            'discount' => 0,
            'blk_discount' => 0,
            'gift_voucher_amt' => 0,
            'total_cost' => 50000,
            'created_by' => 1,
            'is_eda' => 0,
            'goldrate_22ct' => 5500,
            'silverrate_1gm' => 75,
            'cgst' => 1.5,
            'sgst' => 1.5,
            'igst' => 0,
            'status' => 'draft',
        ], $overrides);
    }

    /**
     * Create estimation items array
     */
    protected function makeEstimationItems(int $count = 2, array $baseOverrides = []): array {
        $items = [];
        for ($i = 1; $i <= $count; $i++) {
            $items[] = array_merge([
                'tag_no' => "TAG00{$i}",
                'gross_wt' => 10.5 + $i,
                'net_wt' => 9.8 + $i,
                'id_product' => $i,
                'id_design' => $i,
                'making_charge' => 500,
                'stone_charge' => 0,
                'wastage' => 10,
                'rate_per_gram' => 5500,
            ], $baseOverrides);
        }
        return $items;
    }
}

trait CustomerFactory {

    /**
     * Create a valid customer data array
     */
    protected function makeCustomer(array $overrides = []): array {
        static $counter = 0;
        $counter++;
        
        return array_merge([
            'cusName' => "Test Customer {$counter}",
            'cusMobile' => '98765' . str_pad($counter, 5, '0', STR_PAD_LEFT),
            'mail' => "customer{$counter}@example.com",
            'id_branch' => 1,
            'id_state' => 1,
            'id_city' => 1,
            'address' => '123 Test Street',
            'pincode' => '600001',
            'gst_no' => '',
            'pan_no' => '',
        ], $overrides);
    }

    /**
     * Create a corporate customer
     */
    protected function makeCorporateCustomer(array $overrides = []): array {
        return $this->makeCustomer(array_merge([
            'cusName' => 'Corporate Client Pvt Ltd',
            'gst_no' => '33AABCU9603R1ZM',
            'pan_no' => 'AABCU9603R',
            'is_corporate' => 1,
        ], $overrides));
    }
}

trait ProductFactory {

    /**
     * Create a valid product data array
     */
    protected function makeProduct(array $overrides = []): array {
        static $counter = 0;
        $counter++;
        
        return array_merge([
            'prod_name' => "Test Product {$counter}",
            'id_category' => 1,
            'id_subcategory' => 1,
            'hsn_code' => '7113',
            'purity' => '22K',
            'metal_type' => 'gold',
            'making_type' => 'percentage',
            'making_value' => 12,
            'wastage_type' => 'percentage',
            'wastage_value' => 8,
            'status' => 1,
        ], $overrides);
    }

    /**
     * Create a tagged item (inventory)
     */
    protected function makeTaggingItem(array $overrides = []): array {
        static $tagCounter = 0;
        $tagCounter++;
        
        return array_merge([
            'tag_no' => 'TAG' . str_pad($tagCounter, 6, '0', STR_PAD_LEFT),
            'gross_wt' => 10.5,
            'net_wt' => 9.8,
            'stone_wt' => 0.7,
            'id_product' => 1,
            'id_design' => 1,
            'id_branch' => 1,
            'status' => 'available',
            'location' => 'showcase',
        ], $overrides);
    }
}

trait BillingFactory {

    /**
     * Create a valid bill data array
     */
    protected function makeBill(array $overrides = []): array {
        return array_merge([
            'id_branch' => 1,
            'cus_id' => 101,
            'bill_type' => 'sales',
            'payment_mode' => 'cash',
            'subtotal' => 50000,
            'discount' => 0,
            'tax_amount' => 1500,
            'grand_total' => 51500,
            'paid_amount' => 51500,
            'balance' => 0,
            'created_by' => 1,
            'bill_date' => date('Y-m-d'),
        ], $overrides);
    }

    /**
     * Create bill items
     */
    protected function makeBillItems(int $count = 2, array $baseOverrides = []): array {
        $items = [];
        for ($i = 1; $i <= $count; $i++) {
            $items[] = array_merge([
                'tag_no' => "TAG00{$i}",
                'gross_wt' => 10.5,
                'net_wt' => 9.8,
                'rate' => 5000,
                'amount' => 5000 * 9.8,
                'making_charge' => 500,
                'tax' => 750,
            ], $baseOverrides);
        }
        return $items;
    }
}

trait UserFactory {

    /**
     * Create a valid user data array
     */
    protected function makeUser(array $overrides = []): array {
        static $counter = 0;
        $counter++;
        
        return array_merge([
            'username' => "testuser{$counter}",
            'password' => password_hash('Test@123', PASSWORD_DEFAULT),
            'email' => "user{$counter}@example.com",
            'name' => "Test User {$counter}",
            'id_profile' => 2, // Staff
            'id_branch' => 1,
            'status' => 1,
            'mobile' => '98765' . str_pad($counter, 5, '0', STR_PAD_LEFT),
        ], $overrides);
    }

    /**
     * Create an admin user
     */
    protected function makeAdminUser(array $overrides = []): array {
        return $this->makeUser(array_merge([
            'id_profile' => 1, // Admin
            'name' => 'Admin User',
        ], $overrides));
    }
}

/**
 * Combined factory trait - use this in most tests
 */
trait TestDataFactory {
    use EstimationFactory;
    use CustomerFactory;
    use ProductFactory;
    use BillingFactory;
    use UserFactory;
}
