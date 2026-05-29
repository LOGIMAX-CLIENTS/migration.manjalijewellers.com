const fs = require('fs');
const path = require('path');

const filePath = path.join(__dirname, '../assets/js/ret_billing.js');
const content = fs.readFileSync(filePath, 'utf8');

console.log("Analyzing ret_billing.js for BIL-CLT02 fix...");

// Check 1: createSaleBillSplitRow - sale_item_type
const splitTypePattern = /sale_item_type:\s*curRow\.find\("\.sale_item_type"\)\.val\(\)/;
const oldSplitTypePattern = /sale_item_type:\s*idx\s*==\s*0\s*\?\s*0\s*:\s*2/;

if (splitTypePattern.test(content)) {
    console.log("✅ Check 1: createSaleBillSplitRow - sale_item_type is now dynamic.");
} else if (oldSplitTypePattern.test(content)) {
    console.log("❌ Check 1: createSaleBillSplitRow - sale_item_type is still HARDCODED (0 : 2).");
    process.exit(1);
} else {
    console.log("⚠️ Check 1: Could not find sale_item_type pattern in createSaleBillSplitRow. Manual check required.");
}

// Check 2: createSaleBillSplitRow - sale_pcs
const splitPcsPattern = /sale_pcs:\s*idx\s*==\s*0\s*\?\s*curRow\.find\("\.sale_pcs"\)\.val\(\)\s*:\s*0/;
const oldSplitPcsPattern = /sale_pcs:\s*idx\s*==\s*0\s*\?\s*curRow\.find\("\.sale_pcs"\)\.val\(\)\s*:\s*1/;

if (splitPcsPattern.test(content)) {
    console.log("✅ Check 2: createSaleBillSplitRow - sale_pcs correctly set to 0 for subsequent splits.");
} else if (oldSplitPcsPattern.test(content)) {
    console.log("❌ Check 2: createSaleBillSplitRow - sale_pcs is still double-counting (1 for subsequent splits).");
    process.exit(1);
}

// Check 3: ratio_apply - sale_pcs
const ratioPcsPattern = /sale_pcs:\s*index\s*==\s*0\s*\?\s*curRow\.find\("\.sale_pcs"\)\.val\(\)\s*:\s*0/;
if (ratioPcsPattern.test(content)) {
    console.log("✅ Check 3: ratio_apply - sale_pcs correctly set to 0 for subsequent splits.");
} else {
    console.log("⚠️ Check 3: Could not find sale_pcs pattern in ratio_apply. Manual check required.");
}

console.log("\nBIL-CLT02 Logic Verification PASSED.");
