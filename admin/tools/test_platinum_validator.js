// Test Runner for PlatinumRateValidator

// Mock Window
global.window = {};

// Load Module
const fs = require('fs');
const modulePath = 'c:/xampp/htdocs/etail_v3/admin/assets/js/modules/PlatinumRateValidator.js';
const moduleCode = fs.readFileSync(modulePath, 'utf8');
eval(moduleCode); // Load it into global.window

const validator = window.PlatinumRateValidator;

console.log('--- Testing PlatinumRateValidator ---');

// Test Case 1: Valid Rate
const settings = { min_platinum_tol: 50, max_platinum_tol: 50 };
const marketRate = 1000;
const validRate = 1020;
const res1 = validator.validate(validRate, 'platinum', marketRate, settings);
if(res1.isValid) console.log('PASS: Valid Rate accepted');
else console.log('FAIL: Valid Rate rejected');

// Test Case 2: Too Low
const lowRate = 900;
const res2 = validator.validate(lowRate, 'platinum', marketRate, settings);
if(!res2.isValid && res2.message.includes('between 950 and 1050')) console.log('PASS: Low Rate rejected');
else console.log('FAIL: Low Rate check failed: ' + JSON.stringify(res2));

// Test Case 3: Too High
const highRate = 1100;
const res3 = validator.validate(highRate, 'platinum', marketRate, settings);
if(!res3.isValid) console.log('PASS: High Rate rejected');
else console.log('FAIL: High Rate check failed');

// Test Case 4: Non-Platinum (Gold)
const res4 = validator.validate(99999, 'gold', marketRate, settings);
if(res4.isValid) console.log('PASS: Gold ignored (as expected for MVP)');
else console.log('FAIL: Gold impacted');

console.log('--- Done ---');
