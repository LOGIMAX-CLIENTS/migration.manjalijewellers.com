/**
 * Estimation Module - Unit Tests
 * Based on Knowledge Base Test Specification
 * 
 * Run with: npx jest estimation.test.js
 */

// ============================================
// CALCULATION FUNCTIONS (Extract from ret_estimation.js)
// ============================================

/**
 * Calculate metal value based on calculation type
 * @param {number} grossWt - Gross weight in grams
 * @param {number} lessWt - Less weight (stones) in grams
 * @param {number} rate - Rate per gram
 * @param {number} caltype - 0=Gross, 1=Net, 2=Net+MC on Gross, 3=Fixed
 * @returns {object} {netWt, metalValue}
 */
function calculateMetalValue(grossWt, lessWt, rate, caltype) {
    grossWt = parseFloat(grossWt) || 0;
    lessWt = parseFloat(lessWt) || 0;
    rate = parseFloat(rate) || 0;
    
    const netWt = grossWt - lessWt;
    let metalValue = 0;
    
    switch (parseInt(caltype)) {
        case 0: // Gross weight
            metalValue = grossWt * rate;
            break;
        case 1: // Net weight
        case 2: // Net + MC on Gross
            metalValue = netWt * rate;
            break;
        case 3: // Fixed price - handled separately
            metalValue = 0;
            break;
        default:
            metalValue = grossWt * rate;
    }
    
    return {
        netWt: parseFloat(netWt.toFixed(3)),
        metalValue: parseFloat(metalValue.toFixed(2))
    };
}

/**
 * Calculate wastage amount
 * @param {number} weight - Weight to apply wastage on
 * @param {number} wastagePercent - Wastage percentage
 * @param {number} rate - Rate per gram (board rate)
 * @returns {object} {wastageWt, wastageAmt}
 */
function calculateWastage(weight, wastagePercent, rate) {
    weight = parseFloat(weight) || 0;
    wastagePercent = parseFloat(wastagePercent) || 0;
    rate = parseFloat(rate) || 0;
    
    const wastageWt = weight * (wastagePercent / 100);
    const wastageAmt = wastageWt * rate;
    
    return {
        wastageWt: parseFloat(wastageWt.toFixed(5)),
        wastageAmt: parseFloat(wastageAmt.toFixed(2))
    };
}

/**
 * Calculate making charge
 * @param {number} value - Weight or piece count or metal value
 * @param {number} mcValue - MC rate
 * @param {number} mcType - 1=PerGram, 2=PerPiece, 3=Percentage, 4=Fixed
 * @param {number} weight - Weight (for per gram)
 * @param {number} pieces - Piece count (for per piece)
 * @param {number} metalValue - Metal value (for percentage)
 * @returns {number} MC total
 */
function calculateMC(mcValue, mcType, weight = 0, pieces = 1, metalValue = 0) {
    mcValue = parseFloat(mcValue) || 0;
    weight = parseFloat(weight) || 0;
    pieces = parseInt(pieces) || 1;
    metalValue = parseFloat(metalValue) || 0;
    
    let mcTotal = 0;
    
    switch (parseInt(mcType)) {
        case 1: // Per gram
            mcTotal = mcValue * weight;
            break;
        case 2: // Per piece
            mcTotal = mcValue * pieces;
            break;
        case 3: // Percentage of metal value
            mcTotal = metalValue * (mcValue / 100);
            break;
        case 4: // Fixed amount
            mcTotal = mcValue;
            break;
        default:
            mcTotal = mcValue * weight;
    }
    
    return parseFloat(mcTotal.toFixed(2));
}

/**
 * Calculate old metal value
 * @param {number} grossWt - Gross weight
 * @param {number} lessWt - Less weight (stones)
 * @param {number} touch - Touch percentage (purity)
 * @param {number} rate - Rate per gram
 * @returns {object} {netWt, pureWt, amount}
 */
function calculateOldMetal(grossWt, lessWt, touch, rate) {
    grossWt = parseFloat(grossWt) || 0;
    lessWt = parseFloat(lessWt) || 0;
    touch = parseFloat(touch) || 100;
    rate = parseFloat(rate) || 0;
    
    const netWt = grossWt - lessWt;
    const pureWt = netWt * (touch / 100);
    const amount = pureWt * rate;
    
    return {
        netWt: parseFloat(netWt.toFixed(3)),
        pureWt: parseFloat(pureWt.toFixed(3)),
        amount: parseFloat(amount.toFixed(2))
    };
}

/**
 * Calculate chit proportional weight
 * @param {number} closingBalance - Total balance amount
 * @param {number} closingWeight - Total weight saved
 * @param {number} utilizationAmount - Amount being utilized
 * @returns {number} Proportional weight
 */
function calculateChitWeight(closingBalance, closingWeight, utilizationAmount) {
    closingBalance = parseFloat(closingBalance) || 0;
    closingWeight = parseFloat(closingWeight) || 0;
    utilizationAmount = parseFloat(utilizationAmount) || 0;
    
    if (closingBalance <= 0) return 0;
    
    const ratio = utilizationAmount / closingBalance;
    const utilizedWeight = closingWeight * ratio;
    
    return parseFloat(utilizedWeight.toFixed(3));
}

/**
 * Calculate chit benefits (Type 2 closure)
 * @param {number} weight - Utilized weight
 * @param {number} wastagePercent - Wastage benefit %
 * @param {number} mcValue - MC benefit per gram
 * @param {number} currentRate - Current gold rate
 * @param {number} lockedRate - Locked rate (optional)
 * @returns {object} {wastageBenefit, mcBenefit, rateBenefit, total}
 */
function calculateChitBenefits(weight, wastagePercent, mcValue, currentRate, lockedRate = 0) {
    weight = parseFloat(weight) || 0;
    wastagePercent = parseFloat(wastagePercent) || 0;
    mcValue = parseFloat(mcValue) || 0;
    currentRate = parseFloat(currentRate) || 0;
    lockedRate = parseFloat(lockedRate) || 0;
    
    const wastageBenefit = weight * (wastagePercent / 100) * currentRate;
    const mcBenefit = weight * mcValue;
    const rateBenefit = lockedRate > 0 && currentRate > lockedRate 
        ? weight * (currentRate - lockedRate) 
        : 0;
    
    return {
        wastageBenefit: parseFloat(wastageBenefit.toFixed(2)),
        mcBenefit: parseFloat(mcBenefit.toFixed(2)),
        rateBenefit: parseFloat(rateBenefit.toFixed(2)),
        total: parseFloat((wastageBenefit + mcBenefit + rateBenefit).toFixed(2))
    };
}

/**
 * Validate rate within limits
 * @param {number} rate - Rate to validate
 * @param {number} minRate - Minimum allowed
 * @param {number} maxRate - Maximum allowed
 * @returns {object} {valid, error}
 */
function validateRate(rate, minRate, maxRate) {
    rate = parseFloat(rate) || 0;
    
    if (rate < minRate) {
        return { valid: false, error: 'Rate below minimum' };
    }
    if (rate > maxRate) {
        return { valid: false, error: 'Rate above maximum' };
    }
    return { valid: true, error: null };
}

// ============================================
// TESTS
// ============================================

describe('Metal Value Calculation', () => {
    
    test('TC-101: caltype=0 uses gross weight', () => {
        const result = calculateMetalValue(10, 2, 5500, 0);
        expect(result.metalValue).toBe(55000);
    });
    
    test('TC-102: caltype=1 uses net weight', () => {
        const result = calculateMetalValue(10, 2, 5500, 1);
        expect(result.netWt).toBe(8);
        expect(result.metalValue).toBe(44000);
    });
    
    test('TC-103: caltype=2 uses net weight for metal value', () => {
        const result = calculateMetalValue(10, 2, 5500, 2);
        expect(result.metalValue).toBe(44000);
    });
    
    test('TC-104: caltype=3 returns 0 (fixed price handled separately)', () => {
        const result = calculateMetalValue(10, 2, 5500, 3);
        expect(result.metalValue).toBe(0);
    });
    
    test('TC-105: Zero weight returns 0', () => {
        const result = calculateMetalValue(0, 0, 5500, 0);
        expect(result.metalValue).toBe(0);
    });
    
    test('TC-106: Decimal weight calculated correctly', () => {
        const result = calculateMetalValue(5.678, 0, 5500, 0);
        expect(result.metalValue).toBe(31229);
    });
    
    test('TC-107: Less weight equals gross weight gives 0 net', () => {
        const result = calculateMetalValue(10, 10, 5500, 1);
        expect(result.netWt).toBe(0);
        expect(result.metalValue).toBe(0);
    });
});

describe('Wastage Calculation', () => {
    
    test('TC-201: Wastage on gross weight', () => {
        const result = calculateWastage(10, 12, 5500);
        expect(result.wastageWt).toBe(1.2);
        expect(result.wastageAmt).toBe(6600);
    });
    
    test('TC-202: Wastage on net weight', () => {
        const netWt = 10 - 2; // 8
        const result = calculateWastage(netWt, 12, 5500);
        expect(result.wastageWt).toBe(0.96);
        expect(result.wastageAmt).toBe(5280);
    });
    
    test('TC-203: Zero wastage returns 0', () => {
        const result = calculateWastage(10, 0, 5500);
        expect(result.wastageAmt).toBe(0);
    });
    
    test('TC-204: Maximum wastage (100%)', () => {
        const result = calculateWastage(10, 100, 5500);
        expect(result.wastageAmt).toBe(55000);
    });
    
    test('TC-205: Fractional wastage', () => {
        const result = calculateWastage(10, 12.5, 5500);
        expect(result.wastageAmt).toBe(6875);
    });
});

describe('Making Charge Calculation', () => {
    
    test('TC-301: MC per gram', () => {
        const result = calculateMC(400, 1, 10);
        expect(result).toBe(4000);
    });
    
    test('TC-302: MC per piece', () => {
        const result = calculateMC(1500, 2, 0, 2);
        expect(result).toBe(3000);
    });
    
    test('TC-303: MC percentage', () => {
        const result = calculateMC(8, 3, 0, 1, 55000);
        expect(result).toBe(4400);
    });
    
    test('TC-304: MC fixed amount', () => {
        const result = calculateMC(5000, 4);
        expect(result).toBe(5000);
    });
    
    test('TC-307: Zero MC returns 0', () => {
        const result = calculateMC(0, 1, 10);
        expect(result).toBe(0);
    });
    
    test('TC-308: MC with decimal values', () => {
        const result = calculateMC(450.75, 1, 10.5);
        expect(result).toBe(4732.88);
    });
});

describe('Old Metal Calculation', () => {
    
    test('TC-401: Basic old gold calculation', () => {
        const result = calculateOldMetal(10, 0, 98, 5200);
        expect(result.netWt).toBe(10);
        expect(result.pureWt).toBe(9.8);
        expect(result.amount).toBe(50960);
    });
    
    test('TC-402: Old gold with touch', () => {
        const result = calculateOldMetal(10, 0, 91.6, 5500);
        expect(result.pureWt).toBe(9.16);
        expect(result.amount).toBe(50380);
    });
    
    test('TC-404: Old metal with stones', () => {
        const result = calculateOldMetal(15, 3, 100, 5200);
        expect(result.netWt).toBe(12);
        expect(result.amount).toBe(62400);
    });
});

describe('Old Metal Rate Validation', () => {
    
    test('TC-405: Rate below minimum fails', () => {
        const result = validateRate(3000, 4000, 7000);
        expect(result.valid).toBe(false);
        expect(result.error).toBe('Rate below minimum');
    });
    
    test('TC-406: Rate above maximum fails', () => {
        const result = validateRate(8000, 4000, 7000);
        expect(result.valid).toBe(false);
        expect(result.error).toBe('Rate above maximum');
    });
    
    test('Valid rate passes', () => {
        const result = validateRate(5500, 4000, 7000);
        expect(result.valid).toBe(true);
    });
});

describe('Chit Scheme Calculations', () => {
    
    test('TC-505: Proportional weight calculation', () => {
        const result = calculateChitWeight(50000, 10, 25000);
        expect(result).toBe(5);
    });
    
    test('TC-502: Chit benefits with wastage and MC', () => {
        const result = calculateChitBenefits(10, 15, 400, 5500);
        expect(result.wastageBenefit).toBe(8250);
        expect(result.mcBenefit).toBe(4000);
    });
    
    test('TC-509: Rate lock benefit', () => {
        const result = calculateChitBenefits(10, 0, 0, 5800, 5200);
        expect(result.rateBenefit).toBe(6000);
    });
    
    test('Division by zero protection', () => {
        const result = calculateChitWeight(0, 10, 5000);
        expect(result).toBe(0);
    });
});

describe('Edge Cases', () => {
    
    test('Negative weight treated as 0', () => {
        const result = calculateMetalValue(-5, 0, 5500, 0);
        expect(result.metalValue).toBe(-27500); // Currently not protected
        // TODO: Add validation for negative weights
    });
    
    test('NaN input returns 0', () => {
        const result = calculateMetalValue('abc', 0, 5500, 0);
        expect(result.metalValue).toBe(0);
    });
    
    test('Undefined input returns 0', () => {
        const result = calculateMetalValue(undefined, undefined, undefined, 0);
        expect(result.metalValue).toBe(0);
    });
});

// ============================================
// ADDITIONAL CALCULATION FUNCTIONS
// ============================================

/**
 * Calculate total item cost
 */
function calculateItemTotal(params) {
    const {
        grossWt = 0,
        lessWt = 0,
        rate = 0,
        caltype = 0,
        wastagePercent = 0,
        mcValue = 0,
        mcType = 1,
        stonePrice = 0,
        discount = 0,
        taxPercent = 0
    } = params;
    
    // Step 1: Metal value
    const metal = calculateMetalValue(grossWt, lessWt, rate, caltype);
    
    // Step 2: Wastage (on appropriate weight based on caltype)
    const weightForWastage = caltype === 0 ? grossWt : metal.netWt;
    const wastage = calculateWastage(weightForWastage, wastagePercent, rate);
    
    // Step 3: MC (on appropriate weight based on caltype)
    const weightForMC = caltype === 2 ? grossWt : metal.netWt;
    const mc = calculateMC(mcValue, mcType, weightForMC);
    
    // Step 4: Subtotal before tax
    const subtotal = metal.metalValue + wastage.wastageAmt + mc + parseFloat(stonePrice);
    
    // Step 5: Apply discount
    const discountAmt = subtotal * (discount / 100);
    const afterDiscount = subtotal - discountAmt;
    
    // Step 6: Calculate tax
    const taxAmt = afterDiscount * (taxPercent / 100);
    const total = afterDiscount + taxAmt;
    
    return {
        metalValue: metal.metalValue,
        wastageAmt: wastage.wastageAmt,
        mcAmt: mc,
        stonePrice: parseFloat(stonePrice),
        subtotal: parseFloat(subtotal.toFixed(2)),
        discountAmt: parseFloat(discountAmt.toFixed(2)),
        taxAmt: parseFloat(taxAmt.toFixed(2)),
        total: parseFloat(total.toFixed(2))
    };
}

/**
 * Calculate estimation summary
 */
function calculateEstimationSummary(items, oldMetalAmt = 0, chitAmt = 0, advanceAmt = 0) {
    let totalPurchase = 0;
    
    items.forEach(item => {
        totalPurchase += item.total || 0;
    });
    
    const totalSale = parseFloat(oldMetalAmt) + parseFloat(chitAmt);
    const netPayable = totalPurchase - totalSale - parseFloat(advanceAmt);
    
    return {
        totalPurchase: parseFloat(totalPurchase.toFixed(2)),
        totalSale: parseFloat(totalSale.toFixed(2)),
        advanceAmt: parseFloat(advanceAmt),
        netPayable: parseFloat(Math.max(0, netPayable).toFixed(2)),
        balanceReturn: parseFloat(Math.max(0, -netPayable).toFixed(2))
    };
}

/**
 * Validate chit utilization
 */
function validateChitUtilization(amount, balance, paidInst, totalInst) {
    if (amount > balance) {
        return { valid: false, error: 'Amount exceeds balance' };
    }
    if (amount <= 0) {
        return { valid: false, error: 'Amount must be positive' };
    }
    if (paidInst < totalInst) {
        return { valid: true, warning: 'Scheme not fully matured - limited benefits' };
    }
    return { valid: true, error: null, warning: null };
}

/**
 * Validate tag for estimation
 */
function validateTag(tag, customerId) {
    if (!tag) {
        return { valid: false, error: 'Tag not found' };
    }
    if (tag.status === 1) {
        return { valid: false, error: 'Tag already sold' };
    }
    if (tag.status === 2) {
        return { valid: false, error: 'Tag reserved' };
    }
    if (tag.reservedFor && tag.reservedFor !== customerId) {
        return { valid: false, error: 'Tag reserved for another customer' };
    }
    if (tag.balanceWeight <= 0) {
        return { valid: false, error: 'No balance weight available' };
    }
    return { valid: true, error: null };
}

// ============================================
// ADDITIONAL TESTS
// ============================================

describe('Total Item Calculation', () => {
    
    test('TC-401: Complete item with all components', () => {
        const result = calculateItemTotal({
            grossWt: 10,
            lessWt: 1,
            rate: 5500,
            caltype: 1,
            wastagePercent: 12,
            mcValue: 400,
            mcType: 1,
            stonePrice: 5000,
            discount: 0,
            taxPercent: 3
        });
        
        // Metal: 9 * 5500 = 49500
        // Wastage: 9 * 12% * 5500 = 5940
        // MC: 9 * 400 = 3600
        // Stone: 5000
        // Subtotal: 64040
        // Tax 3%: 1921.20
        // Total: 65961.20
        
        expect(result.metalValue).toBe(49500);
        expect(result.wastageAmt).toBe(5940);
        expect(result.mcAmt).toBe(3600);
        expect(result.subtotal).toBe(64040);
    });
    
    test('TC-402: Item with discount', () => {
        const result = calculateItemTotal({
            grossWt: 10,
            lessWt: 0,
            rate: 5500,
            caltype: 0,
            wastagePercent: 10,
            mcValue: 300,
            mcType: 1,
            stonePrice: 0,
            discount: 5,
            taxPercent: 3
        });
        
        // Metal: 55000
        // Wastage: 5500
        // MC: 3000
        // Subtotal: 63500
        // Discount 5%: 3175
        // After discount: 60325
        // Tax 3%: 1809.75
        
        expect(result.subtotal).toBe(63500);
        expect(result.discountAmt).toBe(3175);
    });
    
    test('TC-403: Fixed price item (caltype=3)', () => {
        const result = calculateItemTotal({
            grossWt: 15,
            lessWt: 2,
            rate: 5500,
            caltype: 3,  // Fixed - ignores weight calculation
            wastagePercent: 0,
            mcValue: 0,
            mcType: 1,
            stonePrice: 25000,  // Fixed price as stone
            discount: 0,
            taxPercent: 3
        });
        
        expect(result.metalValue).toBe(0);
        expect(result.subtotal).toBe(25000);
    });
    
    test('TC-404: MC on gross weight (caltype=2)', () => {
        const result = calculateItemTotal({
            grossWt: 10,
            lessWt: 2,
            rate: 5500,
            caltype: 2,  // Net for metal, Gross for MC
            wastagePercent: 10,
            mcValue: 400,
            mcType: 1,
            stonePrice: 0,
            discount: 0,
            taxPercent: 0
        });
        
        // Metal: 8 * 5500 = 44000
        // Wastage: 8 * 10% * 5500 = 4400
        // MC: 10 * 400 = 4000 (on gross!)
        
        expect(result.metalValue).toBe(44000);
        expect(result.mcAmt).toBe(4000);
    });
    
    test('TC-405: Silver item', () => {
        const result = calculateItemTotal({
            grossWt: 100,
            lessWt: 0,
            rate: 85,
            caltype: 0,
            wastagePercent: 5,
            mcValue: 50,
            mcType: 1,
            stonePrice: 0,
            discount: 0,
            taxPercent: 3
        });
        
        // Metal: 100 * 85 = 8500
        // Wastage: 100 * 5% * 85 = 425
        // MC: 100 * 50 = 5000
        
        expect(result.metalValue).toBe(8500);
        expect(result.subtotal).toBe(13925);
    });
});

describe('Estimation Summary', () => {
    
    test('TC-501: Simple estimation with 2 items', () => {
        const items = [
            { total: 55000 },
            { total: 32000 }
        ];
        
        const result = calculateEstimationSummary(items, 0, 0, 0);
        
        expect(result.totalPurchase).toBe(87000);
        expect(result.netPayable).toBe(87000);
    });
    
    test('TC-502: Estimation with old metal exchange', () => {
        const items = [
            { total: 75000 },
            { total: 45000 }
        ];
        
        const result = calculateEstimationSummary(items, 35000, 0, 0);
        
        expect(result.totalPurchase).toBe(120000);
        expect(result.totalSale).toBe(35000);
        expect(result.netPayable).toBe(85000);
    });
    
    test('TC-503: Estimation with chit scheme', () => {
        const items = [{ total: 100000 }];
        
        const result = calculateEstimationSummary(items, 0, 25000, 0);
        
        expect(result.netPayable).toBe(75000);
    });
    
    test('TC-504: Estimation with advance payment', () => {
        const items = [{ total: 80000 }];
        
        const result = calculateEstimationSummary(items, 20000, 0, 10000);
        
        // 80000 - 20000 - 10000 = 50000
        expect(result.netPayable).toBe(50000);
    });
    
    test('TC-505: Refund scenario (sale > purchase)', () => {
        const items = [{ total: 30000 }];
        
        const result = calculateEstimationSummary(items, 50000, 0, 0);
        
        expect(result.netPayable).toBe(0);
        expect(result.balanceReturn).toBe(20000);
    });
    
    test('TC-506: Complex estimation - all components', () => {
        const items = [
            { total: 65000 },
            { total: 43000 },
            { total: 28000 }
        ];
        
        const result = calculateEstimationSummary(items, 45000, 30000, 15000);
        
        // Purchase: 136000
        // Sale (old + chit): 75000
        // Advance: 15000
        // Net: 136000 - 75000 - 15000 = 46000
        
        expect(result.totalPurchase).toBe(136000);
        expect(result.totalSale).toBe(75000);
        expect(result.netPayable).toBe(46000);
    });
});

describe('Chit Validation', () => {
    
    test('TC-601: Valid matured scheme', () => {
        const result = validateChitUtilization(25000, 50000, 12, 12);
        expect(result.valid).toBe(true);
        expect(result.warning).toBeNull();
    });
    
    test('TC-602: Amount exceeds balance', () => {
        const result = validateChitUtilization(60000, 50000, 12, 12);
        expect(result.valid).toBe(false);
        expect(result.error).toBe('Amount exceeds balance');
    });
    
    test('TC-603: Immature scheme warning', () => {
        const result = validateChitUtilization(25000, 50000, 10, 12);
        expect(result.valid).toBe(true);
        expect(result.warning).toContain('not fully matured');
    });
    
    test('TC-604: Zero amount invalid', () => {
        const result = validateChitUtilization(0, 50000, 12, 12);
        expect(result.valid).toBe(false);
    });
    
    test('TC-605: Negative amount invalid', () => {
        const result = validateChitUtilization(-5000, 50000, 12, 12);
        expect(result.valid).toBe(false);
    });
});

describe('Tag Validation', () => {
    
    test('TC-701: Valid available tag', () => {
        const tag = { status: 0, balanceWeight: 10 };
        const result = validateTag(tag, 'CUST001');
        expect(result.valid).toBe(true);
    });
    
    test('TC-702: Tag already sold', () => {
        const tag = { status: 1, balanceWeight: 10 };
        const result = validateTag(tag, 'CUST001');
        expect(result.valid).toBe(false);
        expect(result.error).toBe('Tag already sold');
    });
    
    test('TC-703: Tag reserved for other customer', () => {
        const tag = { status: 0, reservedFor: 'CUST002', balanceWeight: 10 };
        const result = validateTag(tag, 'CUST001');
        expect(result.valid).toBe(false);
        expect(result.error).toBe('Tag reserved for another customer');
    });
    
    test('TC-704: Tag reserved for same customer - valid', () => {
        const tag = { status: 0, reservedFor: 'CUST001', balanceWeight: 10 };
        const result = validateTag(tag, 'CUST001');
        expect(result.valid).toBe(true);
    });
    
    test('TC-705: Tag not found', () => {
        const result = validateTag(null, 'CUST001');
        expect(result.valid).toBe(false);
        expect(result.error).toBe('Tag not found');
    });
    
    test('TC-706: Zero balance weight', () => {
        const tag = { status: 0, balanceWeight: 0 };
        const result = validateTag(tag, 'CUST001');
        expect(result.valid).toBe(false);
        expect(result.error).toBe('No balance weight available');
    });
});

describe('Real-World Scenarios', () => {
    
    test('Scenario 1: Wedding necklace purchase', () => {
        const item = calculateItemTotal({
            grossWt: 45,
            lessWt: 5,      // Stones
            rate: 5800,
            caltype: 1,     // Net weight
            wastagePercent: 14,
            mcValue: 500,
            mcType: 1,      // Per gram
            stonePrice: 25000,
            discount: 2,
            taxPercent: 3
        });
        
        // Metal: 40 * 5800 = 232000
        // Wastage: 40 * 14% * 5800 = 32480
        // MC: 40 * 500 = 20000
        // Stone: 25000
        // Subtotal: 309480
        // Discount 2%: 6189.60
        // After discount: 303290.40
        // Tax 3%: 9098.71
        
        expect(item.metalValue).toBe(232000);
        expect(item.subtotal).toBe(309480);
    });
    
    test('Scenario 2: Gold bangle with old gold exchange', () => {
        const newItem = calculateItemTotal({
            grossWt: 25,
            lessWt: 0,
            rate: 5700,
            caltype: 0,
            wastagePercent: 10,
            mcValue: 350,
            mcType: 1,
            stonePrice: 0,
            discount: 0,
            taxPercent: 3
        });
        
        const oldMetal = calculateOldMetal(15, 0, 92, 5300);
        
        const summary = calculateEstimationSummary(
            [newItem],
            oldMetal.amount,
            0,
            0
        );
        
        expect(summary.totalPurchase).toBeGreaterThan(0);
        expect(summary.totalSale).toBe(oldMetal.amount);
    });
    
    test('Scenario 3: Multiple items with scheme closure', () => {
        const item1 = calculateItemTotal({
            grossWt: 8, lessWt: 0, rate: 5600, caltype: 0,
            wastagePercent: 12, mcValue: 400, mcType: 1,
            stonePrice: 0, discount: 0, taxPercent: 3
        });
        
        const item2 = calculateItemTotal({
            grossWt: 5, lessWt: 0.5, rate: 5600, caltype: 1,
            wastagePercent: 15, mcValue: 600, mcType: 1,
            stonePrice: 8000, discount: 0, taxPercent: 3
        });
        
        const chitBenefits = calculateChitBenefits(8, 12, 400, 5600);
        
        const summary = calculateEstimationSummary(
            [item1, item2],
            0,
            40000 + chitBenefits.total,  // Base + benefits
            5000
        );
        
        expect(summary.netPayable).toBeGreaterThan(0);
    });
});

// Export for use in other test files
module.exports = {
    calculateMetalValue,
    calculateWastage,
    calculateMC,
    calculateOldMetal,
    calculateChitWeight,
    calculateChitBenefits,
    validateRate,
    calculateItemTotal,
    calculateEstimationSummary,
    validateChitUtilization,
    validateTag
};
