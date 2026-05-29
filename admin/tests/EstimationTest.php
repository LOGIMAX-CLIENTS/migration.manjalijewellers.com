<?php
/**
 * Estimation Module - PHPUnit Tests
 * Refactored to test the EstimationLibrary
 * 
 * Run with: ./vendor/bin/phpunit EstimationTest.php
 */

use PHPUnit\Framework\TestCase;

// Define BASEPATH to satisfy CodeIgniter security check
if (!defined('BASEPATH')) {
    define('BASEPATH', dirname(__DIR__) . '/system/');
}

// Manually require the library since we aren't using CI autoloading here
require_once dirname(__DIR__) . '/application/libraries/EstimationLibrary.php';

class EstimationTest extends TestCase
{
    private $library;

    protected function setUp(): void
    {
        $this->library = new EstimationLibrary();
    }

    // ============================================
    // METAL VALUE TESTS
    // ============================================
    
    public function testTC101_CaltypeGrossWeight()
    {
        $result = $this->library->calculateMetalValue(10, 2, 5500, 0);
        $this->assertEquals(55000, $result['metalValue']);
    }
    
    public function testTC102_CaltypeNetWeight()
    {
        $result = $this->library->calculateMetalValue(10, 2, 5500, 1);
        $this->assertEquals(8, $result['netWt']);
        $this->assertEquals(44000, $result['metalValue']);
    }
    
    public function testTC103_CaltypeNetMCOnGross()
    {
        $result = $this->library->calculateMetalValue(10, 2, 5500, 2);
        // Logic for metal value should be same as Net Weight
        $this->assertEquals(44000, $result['metalValue']); 
    }
    
    public function testTC104_CaltypeFixed()
    {
        $result = $this->library->calculateMetalValue(10, 2, 5500, 3);
        $this->assertEquals(0, $result['metalValue']);
    }
    
    public function testTC105_ZeroWeight()
    {
        $result = $this->library->calculateMetalValue(0, 0, 5500, 0);
        $this->assertEquals(0, $result['metalValue']);
    }
    
    public function testTC106_DecimalWeight()
    {
        $result = $this->library->calculateMetalValue(5.678, 0, 5500, 0);
        $this->assertEquals(31229, $result['metalValue']);
    }
    
    public function testTC107_LessEqualsGross()
    {
        $result = $this->library->calculateMetalValue(10, 10, 5500, 1);
        $this->assertEquals(0, $result['netWt']);
        $this->assertEquals(0, $result['metalValue']);
    }

    // ============================================
    // WASTAGE TESTS
    // ============================================
    
    public function testTC201_WastageOnGross()
    {
        $result = $this->library->calculateWastage(10, 12, 5500);
        $this->assertEquals(1.2, $result['wastageWt']);
        $this->assertEquals(6600, $result['wastageAmt']);
    }
    
    public function testTC202_WastageOnNet()
    {
        $netWt = 10 - 2; // 8
        $result = $this->library->calculateWastage($netWt, 12, 5500);
        $this->assertEquals(0.96, $result['wastageWt']);
        $this->assertEquals(5280, $result['wastageAmt']);
    }
    
    public function testTC203_ZeroWastage()
    {
        $result = $this->library->calculateWastage(10, 0, 5500);
        $this->assertEquals(0, $result['wastageAmt']);
    }
    
    public function testTC204_MaxWastage()
    {
        $result = $this->library->calculateWastage(10, 100, 5500);
        $this->assertEquals(55000, $result['wastageAmt']);
    }
    
    public function testTC205_FractionalWastage()
    {
        $result = $this->library->calculateWastage(10, 12.5, 5500);
        $this->assertEquals(6875, $result['wastageAmt']);
    }

    // ============================================
    // MAKING CHARGE TESTS
    // ============================================
    
    public function testTC301_MCPerGram()
    {
        $result = $this->library->calculateMC(400, 1, 10);
        $this->assertEquals(4000, $result);
    }
    
    public function testTC302_MCPerPiece()
    {
        $result = $this->library->calculateMC(1500, 2, 0, 2);
        $this->assertEquals(3000, $result);
    }
    
    public function testTC303_MCPercentage()
    {
        $result = $this->library->calculateMC(8, 3, 0, 1, 55000);
        $this->assertEquals(4400, $result);
    }
    
    public function testTC304_MCFixed()
    {
        $result = $this->library->calculateMC(5000, 4);
        $this->assertEquals(5000, $result);
    }
    
    public function testTC307_ZeroMC()
    {
        $result = $this->library->calculateMC(0, 1, 10);
        $this->assertEquals(0, $result);
    }
    
    public function testTC308_MCDecimal()
    {
        $result = $this->library->calculateMC(450.75, 1, 10.5);
        $this->assertEquals(4732.88, $result);
    }

    // ============================================
    // OLD METAL TESTS
    // ============================================
    
    public function testTC401_BasicOldGold()
    {
        $result = $this->library->calculateOldMetal(10, 0, 98, 5200);
        $this->assertEquals(10, $result['netWt']);
        $this->assertEquals(9.8, $result['pureWt']);
        $this->assertEquals(50960, $result['amount']);
    }
    
    public function testTC402_OldGoldWithTouch()
    {
        $result = $this->library->calculateOldMetal(10, 0, 91.6, 5500);
        $this->assertEquals(9.16, $result['pureWt']);
        $this->assertEquals(50380, $result['amount']);
    }
    
    public function testTC404_OldMetalWithStones()
    {
        $result = $this->library->calculateOldMetal(15, 3, 100, 5200);
        $this->assertEquals(12, $result['netWt']);
        $this->assertEquals(62400, $result['amount']);
    }

    // ============================================
    // VALIDATION TESTS
    // ============================================
    
    public function testTC405_RateBelowMinimum()
    {
        $result = $this->library->validateRate(3000, 4000, 7000);
        $this->assertFalse($result['valid']);
        $this->assertEquals('Rate below minimum', $result['error']);
    }
    
    public function testTC406_RateAboveMaximum()
    {
        $result = $this->library->validateRate(8000, 4000, 7000);
        $this->assertFalse($result['valid']);
        $this->assertEquals('Rate above maximum', $result['error']);
    }
    
    public function testValidRatePasses()
    {
        $result = $this->library->validateRate(5500, 4000, 7000);
        $this->assertTrue($result['valid']);
    }

    // ============================================
    // EDGE CASES
    // ============================================
    
    public function testNullInputTreatedAsZero()
    {
        $result = $this->library->calculateMetalValue(null, null, null, 0);
        $this->assertEquals(0, $result['metalValue']);
    }
    
    public function testStringInputParsed()
    {
        $result = $this->library->calculateMetalValue('10', '2', '5500', 1);
        $this->assertEquals(44000, $result['metalValue']);
    }

    // ============================================
    // TOTAL ITEM CALCULATION TESTS
    // ============================================
    
    public function testTC401_CompleteItemWithAllComponents()
    {
        $result = $this->library->calculateItemTotal([
            'grossWt' => 10,
            'lessWt' => 1,
            'rate' => 5500,
            'caltype' => 1,
            'wastagePercent' => 12,
            'mcValue' => 400,
            'mcType' => 1,
            'stonePrice' => 5000,
            'discount' => 0,
            'taxPercent' => 3
        ]);
        
        $this->assertEquals(49500, $result['metalValue']);
        $this->assertEquals(5940, $result['wastageAmt']);
        $this->assertEquals(3600, $result['mcAmt']);
        $this->assertEquals(64040, $result['subtotal']);
    }
    
    public function testTC402_ItemWithDiscount()
    {
        $result = $this->library->calculateItemTotal([
            'grossWt' => 10,
            'lessWt' => 0,
            'rate' => 5500,
            'caltype' => 0,
            'wastagePercent' => 10,
            'mcValue' => 300,
            'mcType' => 1,
            'stonePrice' => 0,
            'discount' => 5,
            'taxPercent' => 3
        ]);
        
        $this->assertEquals(63500, $result['subtotal']);
        $this->assertEquals(3175, $result['discountAmt']);
    }
    
    public function testTC403_FixedPriceItem()
    {
        $result = $this->library->calculateItemTotal([
            'grossWt' => 15,
            'lessWt' => 2,
            'rate' => 5500,
            'caltype' => 3,
            'wastagePercent' => 0,
            'mcValue' => 0,
            'mcType' => 1,
            'stonePrice' => 25000,
            'discount' => 0,
            'taxPercent' => 3
        ]);
        
        $this->assertEquals(0, $result['metalValue']);
        $this->assertEquals(25000, $result['subtotal']);
    }
    
    public function testTC404_MCOnGrossWeight()
    {
        $result = $this->library->calculateItemTotal([
            'grossWt' => 10,
            'lessWt' => 2,
            'rate' => 5500,
            'caltype' => 2,
            'wastagePercent' => 10,
            'mcValue' => 400,
            'mcType' => 1,
            'stonePrice' => 0,
            'discount' => 0,
            'taxPercent' => 0
        ]);
        
        $this->assertEquals(44000, $result['metalValue']);
        $this->assertEquals(4000, $result['mcAmt']);
    }
    
    public function testTC405_SilverItem()
    {
        $result = $this->library->calculateItemTotal([
            'grossWt' => 100,
            'lessWt' => 0,
            'rate' => 85,
            'caltype' => 0,
            'wastagePercent' => 5,
            'mcValue' => 50,
            'mcType' => 1,
            'stonePrice' => 0,
            'discount' => 0,
            'taxPercent' => 3
        ]);
        
        $this->assertEquals(8500, $result['metalValue']);
        $this->assertEquals(13925, $result['subtotal']);
    }
 
    // ============================================
    // ESTIMATION SUMMARY TESTS
    // ============================================
    
    public function testTC501_SimpleEstimationWithTwoItems()
    {
        $items = [
            ['total' => 55000],
            ['total' => 32000]
        ];
        
        $result = $this->library->calculateEstimationSummary($items, 0, 0, 0);
        
        $this->assertEquals(87000, $result['totalPurchase']);
        $this->assertEquals(87000, $result['netPayable']);
    }
    
    public function testTC502_EstimationWithOldMetalExchange()
    {
        $items = [
            ['total' => 75000],
            ['total' => 45000]
        ];
        
        $result = $this->library->calculateEstimationSummary($items, 35000, 0, 0);
        
        $this->assertEquals(120000, $result['totalPurchase']);
        $this->assertEquals(35000, $result['totalSale']);
        $this->assertEquals(85000, $result['netPayable']);
    }
    
    public function testTC503_EstimationWithChitScheme()
    {
        $items = [['total' => 100000]];
        
        $result = $this->library->calculateEstimationSummary($items, 0, 25000, 0);
        
        $this->assertEquals(75000, $result['netPayable']);
    }
    
    public function testTC504_EstimationWithAdvancePayment()
    {
        $items = [['total' => 80000]];
        
        $result = $this->library->calculateEstimationSummary($items, 20000, 0, 10000);
        
        $this->assertEquals(50000, $result['netPayable']);
    }
    
    public function testTC505_RefundScenario()
    {
        $items = [['total' => 30000]];
        
        $result = $this->library->calculateEstimationSummary($items, 50000, 0, 0);
        
        $this->assertEquals(0, $result['netPayable']);
        $this->assertEquals(20000, $result['balanceReturn']);
    }
    
    public function testTC506_ComplexEstimationAllComponents()
    {
        $items = [
            ['total' => 65000],
            ['total' => 43000],
            ['total' => 28000]
        ];
        
        $result = $this->library->calculateEstimationSummary($items, 45000, 30000, 15000);
        
        $this->assertEquals(136000, $result['totalPurchase']);
        $this->assertEquals(75000, $result['totalSale']);
        $this->assertEquals(46000, $result['netPayable']);
    }

    // ============================================
    // CHIT VALIDATION TESTS
    // ============================================
    
    public function testTC601_ValidMaturedScheme()
    {
        $result = $this->library->validateChitUtilization(25000, 50000, 12, 12);
        $this->assertTrue($result['valid']);
        $this->assertNull($result['warning']);
    }
    
    public function testTC602_AmountExceedsBalance()
    {
        $result = $this->library->validateChitUtilization(60000, 50000, 12, 12);
        $this->assertFalse($result['valid']);
        $this->assertEquals('Amount exceeds balance', $result['error']);
    }
    
    public function testTC603_ImmatureSchemeWarning()
    {
        $result = $this->library->validateChitUtilization(25000, 50000, 10, 12);
        $this->assertTrue($result['valid']);
        $this->assertStringContainsString('not fully matured', $result['warning']);
    }
    
    public function testTC604_ZeroAmountInvalid()
    {
        $result = $this->library->validateChitUtilization(0, 50000, 12, 12);
        $this->assertFalse($result['valid']);
    }
    
    public function testTC605_NegativeAmountInvalid()
    {
        $result = $this->library->validateChitUtilization(-5000, 50000, 12, 12);
        $this->assertFalse($result['valid']);
    }

    // ============================================
    // TAG VALIDATION TESTS
    // ============================================
    
    public function testTC701_ValidAvailableTag()
    {
        $tag = ['status' => 0, 'balanceWeight' => 10];
        $result = $this->library->validateTag($tag, 'CUST001');
        $this->assertTrue($result['valid']);
    }
    
    public function testTC702_TagAlreadySold()
    {
        $tag = ['status' => 1, 'balanceWeight' => 10];
        $result = $this->library->validateTag($tag, 'CUST001');
        $this->assertFalse($result['valid']);
        $this->assertEquals('Tag already sold', $result['error']);
    }
    
    public function testTC703_TagReservedForOtherCustomer()
    {
        $tag = ['status' => 0, 'reservedFor' => 'CUST002', 'balanceWeight' => 10];
        $result = $this->library->validateTag($tag, 'CUST001');
        $this->assertFalse($result['valid']);
        $this->assertEquals('Tag reserved for another customer', $result['error']);
    }
    
    public function testTC704_TagReservedForSameCustomer()
    {
        $tag = ['status' => 0, 'reservedFor' => 'CUST001', 'balanceWeight' => 10];
        $result = $this->library->validateTag($tag, 'CUST001');
        $this->assertTrue($result['valid']);
    }
    
    public function testTC705_TagNotFound()
    {
        $result = $this->library->validateTag(null, 'CUST001');
        $this->assertFalse($result['valid']);
        $this->assertEquals('Tag not found', $result['error']);
    }
    
    public function testTC706_ZeroBalanceWeight()
    {
        $tag = ['status' => 0, 'balanceWeight' => 0];
        $result = $this->library->validateTag($tag, 'CUST001');
        $this->assertFalse($result['valid']);
        $this->assertEquals('No balance weight available', $result['error']);
    }

    // ============================================
    // REAL-WORLD SCENARIO TESTS
    // ============================================
    
    public function testScenario1_WeddingNecklacePurchase()
    {
        $item = $this->library->calculateItemTotal([
            'grossWt' => 45,
            'lessWt' => 5,
            'rate' => 5800,
            'caltype' => 1,
            'wastagePercent' => 14,
            'mcValue' => 500,
            'mcType' => 1,
            'stonePrice' => 25000,
            'discount' => 2,
            'taxPercent' => 3
        ]);
        
        $this->assertEquals(232000, $item['metalValue']);
        $this->assertEquals(309480, $item['subtotal']);
    }
    
    public function testScenario2_GoldBangleWithOldGoldExchange()
    {
        $newItem = $this->library->calculateItemTotal([
            'grossWt' => 25,
            'lessWt' => 0,
            'rate' => 5700,
            'caltype' => 0,
            'wastagePercent' => 10,
            'mcValue' => 350,
            'mcType' => 1,
            'stonePrice' => 0,
            'discount' => 0,
            'taxPercent' => 3
        ]);
        
        $oldMetal = $this->library->calculateOldMetal(15, 0, 92, 5300);
        
        $summary = $this->library->calculateEstimationSummary(
            [$newItem],
            $oldMetal['amount'],
            0,
            0
        );
        
        $this->assertGreaterThan(0, $summary['totalPurchase']);
        $this->assertEquals($oldMetal['amount'], $summary['totalSale']);
    }
    
    public function testScenario3_MultipleItemsWithScheme()
    {
        $item1 = $this->library->calculateItemTotal([
            'grossWt' => 8, 'lessWt' => 0, 'rate' => 5600, 'caltype' => 0,
            'wastagePercent' => 12, 'mcValue' => 400, 'mcType' => 1,
            'stonePrice' => 0, 'discount' => 0, 'taxPercent' => 3
        ]);
        
        $item2 = $this->library->calculateItemTotal([
            'grossWt' => 5, 'lessWt' => 0.5, 'rate' => 5600, 'caltype' => 1,
            'wastagePercent' => 15, 'mcValue' => 600, 'mcType' => 1,
            'stonePrice' => 8000, 'discount' => 0, 'taxPercent' => 3
        ]);
        
        $summary = $this->library->calculateEstimationSummary(
            [$item1, $item2],
            0,
            50000,
            5000
        );
        
        $this->assertGreaterThan(0, $summary['netPayable']);
    }
}
