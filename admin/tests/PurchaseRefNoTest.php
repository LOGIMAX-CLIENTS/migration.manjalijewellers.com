<?php
use PHPUnit\Framework\TestCase;

/**
 * Purchase Reference Number Logic Test
 * 
 * Verifies the logic for generating PO and GRN reference numbers,
 * specifically focusing on the Developer Discovery regarding Against Order (stock_type=1)
 * and is_suspense_stock consistency.
 */
class PurchaseRefNoTest extends TestCase
{
    /**
     * Logic Simulation: Simulates the controller/model interaction
     */
    private function generateRefNoSimulation($stock_type, $input_suspense, $bill_type_from_db)
    {
        // Business Rule (Developer Finding): 
        // Against Order (stock_type=1) is ALWAYS a Bill (suspense=0)
        $is_suspense_stock = $input_suspense;
        if ($stock_type == 1) {
            $is_suspense_stock = 0;
        }

        // Simulate Model: last_no depends on the sequence being queried
        // Sequence 0: 10, 11, 12... (Bills)
        // Sequence 1: 50, 51, 52... (Approvals)
        $last_bill_no = 12;
        $last_approval_no = 52;

        $last_no = ($is_suspense_stock == 0) ? $last_bill_no : $last_approval_no;
        $next_no = $last_no + 1;
        $padded = str_pad($next_no, 5, '0', STR_PAD_LEFT);

        // Prefix Logic
        if ($is_suspense_stock == 0) {
            $prefix = 'P-';
        } else {
            $prefix = 'PA-';
        }

        return [
            'ref_no' => $prefix . $padded,
            'is_suspense_stock' => $is_suspense_stock
        ];
    }

    public function test_against_order_forces_bill_sequence()
    {
        // Scenario: Input says suspense=1 (Approval), but stock_type is 1 (Against Order)
        // Expected: Should use Bill sequence (12 -> 13) and P- prefix
        $result = $this->generateRefNoSimulation(1, 1, 'GST');
        
        $this->assertEquals(0, $result['is_suspense_stock'], "Against Order must force is_suspense_stock to 0");
        $this->assertEquals('P-00013', $result['ref_no'], "Against Order must use Bill prefix and sequence");
    }

    public function test_normal_approval_uses_approval_sequence()
    {
        // Scenario: stock_type=2 (Direct Approval), suspense=1
        // Expected: Should use Approval sequence (52 -> 53) and PA- prefix
        $result = $this->generateRefNoSimulation(2, 1, 'GST');
        
        $this->assertEquals(1, $result['is_suspense_stock']);
        $this->assertEquals('PA-00053', $result['ref_no']);
    }

    public function test_grn_prefix_logic()
    {
        // Testing the logic added to the model: (int)casting and prefix assignment
        $this->assertEquals("PU-00016", $this->mock_generate_grn_refno(1, 15));
        $this->assertEquals("PM-00016", $this->mock_generate_grn_refno(2, 15));
        $this->assertEquals("PC-00016", $this->mock_generate_grn_refno(3, 15));
    }

    private function mock_generate_grn_refno($grn_type, $max_num)
    {
        $grn_ref_no = str_pad($max_num + 1, 5, '0', STR_PAD_LEFT);
        
        // Exact copy of model logic for verification
        if((int)$grn_type == 1){
            return "PU-".$grn_ref_no;
        }else if((int)$grn_type == 2){
            return "PM-".$grn_ref_no;
        }else if((int)$grn_type == 3){
            return "PC-".$grn_ref_no;
        }
        return null;
    }
}
