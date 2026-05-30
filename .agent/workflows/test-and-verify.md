---
description: Test and verify a bug fix with category-specific test requirements and module smoke tests
version: 1.4
last_updated: 2026-02-20
---

# Test & Verify Workflow

## Purpose

Standardize testing after every bug fix. Every fix must pass prerequisite checks, syntax checks, automated tests, and a smoke test before approval. This is called by `/fix-single-bug` at Step 6, but can also run standalone.

## Prerequisites

- A bug fix has been applied (code change is in place)
- Bug ID, category, and severity are known

## Environment Config

> **Shared values** (`{PHPUNIT_PATH}`, `{TEST_DIR}`) are in `.agent/config.md`.
> **Local values** (`{PHP_PATH}`, `{PROJECT_ROOT}`, `{LOCALHOST_URL}`) are auto-detected in `.agent/.env`.

| Variable             | Source                           | Change Per Project             |
| -------------------- | -------------------------------- | ------------------------------ |
| `{PHP_PATH}`         | `.agent/.env` (auto-detected)    | Auto-detected by `/validate-workflows` |
| `{PHPUNIT_PATH}`     | `admin/tests/vendor/bin/phpunit` | Client's test runner           |
| `{PHPUNIT_VERSION}`  | `9.6`                            | `9.6` for PHP7, `10+` for PHP8 |
| `{COMPOSER_VERSION}` | `2.2`                            | LTS for PHP7, latest for PHP8  |
| `{TEST_DIR}`         | `admin/tests/`                   | Client's test directory        |

## Test Environment

```
PHP:     Use {PHP_PATH} (XAMPP PHP7 — same binary, CLI mode only)
PHPUnit: Use {PHPUNIT_PATH} (v9.6 for PHP7 compatibility)
Composer: v2.2 LTS (PHP7 compatible)
Tests:   {TEST_DIR}
Run:     cd {TEST_DIR} && & "{PHP_PATH}" {PHPUNIT_PATH} --no-configuration {TestFile}.php --testdox
```

## Steps

### Step 0: Prerequisite Check [Antigravity]

Before any testing, verify that PHP CLI and PHPUnit are available. This step runs once per project and is skipped on subsequent runs if already passing.

#### Step 0a: PHP CLI Check

// turbo

```powershell
& "{PHP_PATH}" -v
```

- ✅ Output shows `PHP 7.x` or `PHP 8.x` → note the version and continue to Step 0b
- ❌ Command fails or PHP not found → **auto-detect** by probing common locations:

**Auto-detect sequence** (try each in order, stop at first match):
// turbo

```powershell
$candidates = @(
    "C:\xampp\php\php.exe",
    "D:\xampp\php\php.exe",
    "E:\xampp\php\php.exe",
    "C:\wamp64\bin\php\php7.4\php.exe",
    "C:\php\php.exe",
    "D:\php\php.exe"
)
foreach ($path in $candidates) {
    if (Test-Path $path) {
        Write-Host "FOUND: $path"
        & $path -v
        break
    }
}
if (-not (Test-Path $path)) { Write-Host "NOT_FOUND" }
```

- ✅ `FOUND:` → use the detected path. **Antigravity must update `.agent/config.md`** with the detected `{PHP_PATH}` value, then continue to Step 0b.
- ❌ `NOT_FOUND` → **STOP** and prompt the user:

> [!CAUTION]
> **PHP CLI was not found in any common location.**
> PHP CLI is required for syntax checking and running PHPUnit tests.

Ask the user:

1. **Where is XAMPP installed?** (e.g., `F:\xampp\`, custom path)
2. **Or is there a standalone PHP installation?** (provide the full path to `php.exe`)

Once the user provides the path:

- Verify it works: `& "{USER_PROVIDED_PATH}" -v`
- Update `.agent/config.md` with the correct `{PHP_PATH}`
- If no PHP exists at all → see [Appendix A: Standalone PHP Install](#appendix-a-standalone-php-install-optional)

**After resolving** → re-run Step 0a to verify.

#### Step 0b: Determine PHPUnit Version

Based on the PHP version detected in Step 0a:

| PHP Version   | PHPUnit Version              | Composer Version       |
| ------------- | ---------------------------- | ---------------------- |
| PHP 7.3 – 7.4 | PHPUnit **9.6**              | Composer **2.2** (LTS) |
| PHP 8.1+      | PHPUnit **10.x** or **11.x** | Composer **latest**    |

> [!NOTE]
> Most client environments run XAMPP with PHP 7.4. PHPUnit 9.6 is fully functional for all test categories in this workflow. No need to upgrade unless you specifically need PHP8 features.

#### Step 0c: PHPUnit Check

// turbo

```powershell
& "{PHP_PATH}" "{PROJECT_ROOT}\{PHPUNIT_PATH}" --version
```

- ✅ Output shows PHPUnit version → continue to Step 1
- ❌ PHPUnit not found → **auto-install** via Composer:

**Auto-install sequence:**

1. Ensure `{TEST_DIR}` exists:

   ```powershell
   # turbo
   New-Item -ItemType Directory -Path "{PROJECT_ROOT}\{TEST_DIR}" -Force
   ```

2. Check if Composer is available locally:

   ```powershell
   # turbo
   & "{PHP_PATH}" -r "echo file_exists('{PROJECT_ROOT}\{TEST_DIR}composer.phar') ? 'EXISTS' : 'MISSING';"
   ```

3. If Composer is MISSING, download Composer 2.2 LTS:

   ```powershell
   # turbo
   & "{PHP_PATH}" -r "copy('https://getcomposer.org/download/2.2.24/composer.phar', '{PROJECT_ROOT}\{TEST_DIR}composer.phar');"
   ```

   > If PHP8 is detected, use latest Composer instead:
   >
   > ```powershell
   > & "{PHP_PATH}" -r "copy('https://getcomposer.org/installer', '{PROJECT_ROOT}\{TEST_DIR}composer-setup.php');"
   > & "{PHP_PATH}" "{PROJECT_ROOT}\{TEST_DIR}composer-setup.php" --install-dir="{PROJECT_ROOT}\{TEST_DIR}" --filename=composer.phar
   > Remove-Item "{PROJECT_ROOT}\{TEST_DIR}composer-setup.php"
   > ```

4. Initialize `composer.json` if missing and install PHPUnit:

   ```powershell
   cd "{PROJECT_ROOT}\{TEST_DIR}"
   ```

   For PHP7 (default):

   ```powershell
   & "{PHP_PATH}" composer.phar init --no-interaction --name="logimax/tests" --description="PHPUnit tests for bug remediation" --require-dev="phpunit/phpunit:^9.6"
   & "{PHP_PATH}" composer.phar install --no-interaction
   ```

   For PHP8 (if detected):

   ```powershell
   & "{PHP_PATH}" composer.phar init --no-interaction --name="logimax/tests" --description="PHPUnit tests for bug remediation" --require-dev="phpunit/phpunit:^10.0"
   & "{PHP_PATH}" composer.phar install --no-interaction
   ```

5. Verify PHPUnit works:
   ```powershell
   # turbo
   & "{PHP_PATH}" "{PROJECT_ROOT}\{PHPUNIT_PATH}" --version
   ```

   - ✅ PHPUnit version displayed → continue to Step 1
   - ❌ Still fails → prompt user with error output for manual resolution

> [!NOTE]
> Composer and PHPUnit are installed **locally** inside `admin/tests/` only.
> Nothing is installed globally. No system PATH changes. No impact on XAMPP's web server.

---

### Step 1: Syntax Check [Antigravity]

// turbo
For **every** PHP file changed:

```powershell
& "{PHP_PATH}" -l {affected_php_file}
```

- ✅ Pass → continue
- ❌ Fail → fix syntax error → re-check → loop until pass

For SQL → validate syntax only, do NOT execute

#### Step 1a: JS Syntax Check [Antigravity]

For **every** JS file changed, use one of these methods (in priority order):

**Method 1 — Node.js** (if available):

```powershell
# turbo
node --check {affected_js_file}
```

- ✅ No output = valid syntax
- ❌ `SyntaxError` → fix and re-check

**Method 2 — Browser Console** (if Node.js not available):

1. Open the affected page in browser (use `{LOCALHOST_URL}`)
2. Open DevTools → Console (F12 or Ctrl+Shift+J)
3. Filter by the JS filename (e.g., `ret_billing.js`)
4. Check for:
   - ❌ `SyntaxError` — parsing failed
   - ❌ `ReferenceError` — undefined variable (scope bug)
   - ❌ `TypeError` — null/undefined property access
   - ⚠️ `The select2('val') method was called on an element that is not using Select2` — select2 initialization order bug
5. Trigger the affected flow (e.g., change dropdown, click button)
6. Re-check console after each interaction

- ✅ No errors in console → continue
- ❌ Errors found → fix → clear cache → reload → re-check → loop until clean

> [!NOTE]
> Always **clear browser cache** (Ctrl+Shift+Del or hard reload Ctrl+Shift+R) before testing JS changes. Browsers aggressively cache `.js` files.

### Step 1.5: Select Test Approach [Antigravity — MANDATORY]

> [!IMPORTANT]
> **Do NOT default to PHPUnit for every bug.** Select the right approach based on bug type.
> **NEVER use production URLs for testing.** Always use `{LOCALHOST_URL}` from `.agent/.env`.

| Bug Type                     | Test Approach                     | Tool             | Example                                |
| ---------------------------- | --------------------------------- | ---------------- | -------------------------------------- |
| **Report data mismatch**     | CI3 bootstrap comparison script   | `{PHP_PATH}` CLI | Compare two functions with same params |
| **Report variant mismatch**  | CI3 bootstrap comparison script   | `{PHP_PATH}` CLI | Stone vs non-stone, tag vs nontag      |
| **Cross-report consistency** | CI3 bootstrap + consistency rules | `{PHP_PATH}` CLI | Month-wise O/W = category-wise O/W     |
| **Pure logic / calculation** | PHPUnit unit test                 | PHPUnit          | Math formula, string manipulation      |
| **SQL query bug**            | CI3 bootstrap script              | `{PHP_PATH}` CLI | Wrong JOIN, missing WHERE              |
| **UI / AJAX bug**            | Browser test on localhost         | Browser subagent | Click flow, form submission            |
| **Security (input)**         | PHPUnit + browser                 | Both             | Injection, XSS                         |
| **JS calculation bug**       | Browser test on localhost         | Browser subagent | Verify displayed values                |
| **JS scope / integration**   | Browser console + flow test       | Browser/Manual   | Variable scope, select2 init, AJAX cascade |
| **Print/view layout bug**    | Browser screenshot comparison     | Browser subagent | Column alignment, CSS widths           |

**CI3 bootstrap scripts** use `application/tests/ci3_bootstrap.php` to load the framework and call model functions directly. See `application/tests/test_RPT_INT01.php` for a working example.

**If the bug involves model functions with raw SQL** → use CI3 bootstrap, NOT PHPUnit. PHPUnit without DB connection cannot test SQL queries.

### Step 1.5b: Print/View Bug → Browser Verification [MANDATORY]

For bugs in view/print files (CSS, column layout, image rendering):

1. **Before fix**: Open the page in browser subagent → capture screenshot of CURRENT (broken) state
2. **After fix**: Reload the page → capture screenshot of FIXED state
3. **Compare**: Does fixed state match expected layout?
4. **If not** → adjust ONE CSS property at a time → re-capture → repeat
5. **Only present to user** after screenshot confirms the layout is correct

> [!IMPORTANT]
> This step is MANDATORY for any change to a `print.php`, `est_print*.php`, or any view that produces a PDF or print output. Skipping this step causes iteration loops.

### Step 1.5c: CI3 Bootstrap Template [For Model/SQL Bug Tests]

When creating a new CI3 bootstrap test script, copy this template:

```php
<?php
// CI3 Bootstrap Test — {BUG_ID}: {TITLE}
// Usage: & "{PHP_PATH}" application/tests/{test_file}.php

$_SERVER['CI_ENV'] = 'testing';
define('ENVIRONMENT', 'testing');
define('FCPATH', realpath(dirname(__FILE__) . '/../../') . DIRECTORY_SEPARATOR);
define('APPPATH', FCPATH . 'application' . DIRECTORY_SEPARATOR);
define('BASEPATH', FCPATH . 'system' . DIRECTORY_SEPARATOR);
define('VIEWPATH', APPPATH . 'views' . DIRECTORY_SEPARATOR);

require_once BASEPATH . 'core/Common.php';
require_once APPPATH . 'config/database.php';

// Connect to database
$db_config = $db[$active_group];
$dsn = "mysql:host={$db_config['hostname']};dbname={$db_config['database']};charset=utf8";
$pdo = new PDO($dsn, $db_config['username'], $db_config['password']);

// --- TEST CODE HERE ---
echo "\n=== Test: {BUG_ID} — {TITLE} ===\n";
// Call model functions or run queries
// Compare expected vs actual results
// echo PASS/FAIL
```

> See `application/tests/test_RPT_INT01.php` for a working example specific to this project.

### Step 1.5d: JS Bug Testing Procedure [For JS Scope/AJAX/Integration Bugs]

When a bug is **purely or primarily in JavaScript** (variable scope, select2 initialization, AJAX cascade, jQuery selector errors), PHPUnit is NOT applicable. Use this structured browser-based procedure instead:

#### JS Pre-Flight Checklist

```
1. Clear browser cache completely (Ctrl+Shift+Del → All time → Cached images and files)
2. Open DevTools Console (F12 → Console tab)
3. Enable "Preserve log" in Console settings
4. Navigate to the affected page on {LOCALHOST_URL}
5. Verify the JS file loaded is the UPDATED version (check Network tab → find the .js file → verify modified date or content)
```

#### JS Functional Test Template

For each affected flow, execute this checklist:

```markdown
| # | Action | Expected Result | Console Errors? | Status |
|---|--------|-----------------|-----------------|--------|
| 1 | {Trigger action, e.g. change dropdown} | {Expected UI change} | None | ✅/❌ |
| 2 | {Next cascade action} | {Expected population} | None | ✅/❌ |
| ... | ... | ... | ... | ... |
```

#### Common JS Bug Patterns to Verify

| Pattern | What to Check | How to Verify |
|---------|---------------|---------------|
| **Variable scope** | Function params match body refs | `console.log(paramName)` inside function |
| **select2 init order** | `select2('val')` only after `.select2({...})` | Check for select2 warning in console |
| **AJAX cascade** | Child dropdown populates after parent AJAX completes | Network tab → verify XHR completes → options appear |
| **jQuery selector** | Selector string is valid (no `$('.')` etc.) | Console shows `Syntax error, unrecognized expression` |
| **Event delegation** | Dynamic elements respond to events | Click/change dynamically added elements |
| **Stale closure** | Callback references current value, not stale | Log variable inside callback vs outside |

#### JS Test Reporting

Since there's no automated JS test runner, document results in the bug report:

```
JS Test Results — {BUG_ID}
  File: {js_file}
  Syntax: ✅ No console errors on page load
  Flow 1 ({description}): ✅ {N} steps verified, 0 errors
  Flow 2 ({description}): ✅ {N} steps verified, 0 errors
  Regression: ✅ Existing flows unaffected
```

### Step 2: Identify Required Tests by Category [Antigravity]

Based on the bug's category, these tests are **required**:

| Category                     | Required Automated Tests                                                        |
| ---------------------------- | ------------------------------------------------------------------------------- |
| **Security** (SQLi, XSS)     | Whitelist enforcement, malicious payload blocking, binding verification         |
| **Transaction**              | Success path commits, failure path rollbacks, partial failure handling          |
| **Logic / Calculation**      | Correct result for normal input, edge cases (0, negative, max), boundary values |
| **Data Validation**          | Valid input passes, invalid input blocked, boundary conditions, null handling   |
| **Schema**                   | Data preservation post-migration, type correctness, constraint enforcement      |
| **Variable / Typo**          | Correct value assigned, old behavior no longer occurs                           |
| **Integration**              | Mock external responses, timeout handling, error response handling              |
| **JS Integration / Scope**   | Browser console clean, all cascading dropdowns populate, select2 calls guarded  |
| **Concurrency**              | Lock acquisition, concurrent access simulation                                  |
| **Cross-Report Consistency** | All defined consistency rules pass for affected reports                         |

### Step 3: Check for Existing Tests [Antigravity]

// turbo

1. Search `{TEST_DIR}` for test files related to the module
2. If found → run existing tests first to establish baseline:

```powershell
cd "{PROJECT_ROOT}\{TEST_DIR}" && & "{PHP_PATH}" vendor/bin/phpunit --no-configuration {TestFile}.php --testdox
```

3. Note: existing tests passing = no regression introduced

### Step 4: Create New Tests (if P0/P1) [Antigravity]

For P0 and P1 bugs → create a test file if none exists:

**File naming**: `{TEST_DIR}{ModuleName}{BugCategory}Test.php`

**Test structure**:

```php
<?php
use PHPUnit\Framework\TestCase;

class {ModuleName}{BugCategory}Test extends TestCase
{
    /**
     * Bug {BUG_ID}: {Title}
     * Category: {Category}
     * Tests that the fix resolves the issue
     */
    public function test_{bug_id}_fix_correct_behavior(): void
    {
        // Arrange — set up the conditions that trigger the bug
        // Act — execute the code path that was broken
        // Assert — verify the correct behavior
    }

    public function test_{bug_id}_edge_case_zero(): void { ... }
    public function test_{bug_id}_edge_case_negative(): void { ... }
    public function test_{bug_id}_edge_case_boundary(): void { ... }
}
```

**Minimum test cases per category**:

| Category    | Tests Required                                                             |
| ----------- | -------------------------------------------------------------------------- |
| Security    | Normal input passes, malicious input rejected, boundary input handled      |
| Transaction | Success commits, failure rolls back, partial failure handled               |
| Logic       | Normal case correct, zero/null input, negative input, large input          |
| Validation  | Valid accepted, invalid rejected, boundary accepted/rejected, null handled |
| Schema      | Pre-migration data preserved, new constraints enforced                     |
| Variable    | Correct variable used, old incorrect behavior impossible                   |

### Step 5: Run All Tests [Antigravity]

// turbo

```powershell
cd "{PROJECT_ROOT}\{TEST_DIR}" && & "{PHP_PATH}" vendor/bin/phpunit --no-configuration {TestFile}.php --testdox
```

- ✅ All pass → continue
- ❌ Any fail → diagnose failure → fix → re-run → loop until pass

### Step 6: Module Smoke Test Checklist [Human + Antigravity]

Present this checklist to the user. These are **manual** checks:

- [ ] Primary **create/save** action works
- [ ] Primary **edit/update** action preserves ALL existing data (no field loss)
- [ ] Primary **delete** action cleans up ALL child records
- [ ] **Search** functions return correct results (tag, customer, product, order)
- [ ] **Print/export** output matches database values exactly
- [ ] **Negative test** — insert malicious input via browser dev tools → verify rejection

**Module-specific additions** (customize per module):

| Module       | Extra Smoke Tests                                                         |
| ------------ | ------------------------------------------------------------------------- |
| Estimation   | Old metal calculations, chit scheme deductions, tag split/merge, EDA flow |
| Billing      | Tax calculations, credit bill flow, advance adjustments, GST output       |
| Sales Return | Return quantity validation, old metal return, credit note generation      |
| Inventory    | Stock level updates, inter-branch transfer, lot tracking                  |
| Order        | Advance payment linking, order-to-estimation conversion                   |

### Step 7: Sign-Off Criteria [Antigravity]

All must be ✅ before approval gate:

| Criterion                                                | Status |
| -------------------------------------------------------- | ------ |
| Prerequisites verified (PHP CLI + PHPUnit)               |        |
| Syntax check passes (all changed files)                  |        |
| Automated tests pass (all assertions)                    |        |
| Smoke test items checked                                 |        |
| No unintended side effects observed                      |        |
| Rollback plan documented (from `/fix-single-bug` Step 4) |        |
| Bug tracking updated                                     |        |

## Completion Report

```
✅ Testing complete for {BUG_ID}
   Prerequisites: PHP {version} ✅ | PHPUnit {version} ✅ | Composer {version} ✅
   Syntax: {N} files checked — all pass
   Tests: {N} tests, {M} assertions — all pass
   Smoke: {N}/{TOTAL} items checked
   Sign-off: {Ready / Blocked on: {reason}}

   Next: Approval gate in /fix-single-bug Step 7
```

---

## Appendix A: Standalone PHP Install (Optional)

> This appendix is only needed if XAMPP is not installed or you want to upgrade to PHP8 for testing purposes.

**PHP8 Side-by-Side Install Guide (does NOT touch XAMPP PHP7)**

1. **Download** PHP 8.2+ (VS16 x64 Thread Safe) from https://windows.php.net/download/
2. **Extract** to a standalone directory — pick ONE:
   - `C:\php8\` (recommended)
   - `D:\php8\` (if C drive space is limited)
3. **Configure** — copy and rename config:
   ```powershell
   Copy-Item "{INSTALL_DIR}\php.ini-development" "{INSTALL_DIR}\php.ini"
   ```
4. **Enable required extensions** — edit `php.ini`, uncomment these lines:
   ```ini
   extension=mbstring
   extension=openssl
   extension=curl
   extension=mysqli
   extension=pdo_mysql
   extension=tokenizer
   extension=xml
   extension=xmlwriter
   ```
5. **Verify** the install:
   ```powershell
   & "{INSTALL_DIR}\php.exe" -v
   # Expected: PHP 8.2.x (cli)
   ```
6. **Update config** — edit `.agent/config.md` and set:
   ```
   | `{PHP_PATH}` | `{INSTALL_DIR}\php.exe` |
   | `{PHPUNIT_VERSION}` | `10` |
   | `{COMPOSER_VERSION}` | `latest` |
   ```

> [!IMPORTANT]
> **What this does NOT do:**
>
> - Does NOT modify `C:\xampp\php\` — XAMPP PHP7 is completely untouched
> - Does NOT change system PATH — PHP8 is called explicitly by full path only
> - Does NOT affect Apache/XAMPP config — the web server keeps using PHP7
> - Does NOT require admin rights — just extract and use
>
> PHP8 is used **only** by Antigravity for CLI tasks (syntax lint, PHPUnit). The application still runs on XAMPP PHP7 as usual.

---

## Appendix B: Node.js + ESLint Setup for JS Testing

> This appendix adds **static analysis** for JavaScript files. Unlike PHP (where `php -l` catches syntax errors), JS has no built-in linter — Node.js fills this gap.

### B.1: Check if Node.js is Available

// turbo

```powershell
node --version
npm --version
```

- ✅ Versions displayed → continue to B.3
- ❌ Not found → see B.2

### B.2: Install Node.js (if missing)

1. **Download** Node.js LTS from https://nodejs.org/en/download/
2. **Install** with default settings (adds to system PATH automatically)
3. **Verify**:

   ```powershell
   node --version
   npm --version
   ```

> [!NOTE]
> Node.js runs **only** in CLI mode for linting/testing. It does NOT affect XAMPP, Apache, PHP, or the web application in any way. The application still runs on XAMPP's Apache + PHP stack.

### B.3: JS Syntax Check (Zero Setup)

For **every** JS file changed:

// turbo

```powershell
node --check {affected_js_file}
```

- ✅ No output = valid syntax (no parsing errors)
- ❌ `SyntaxError: Unexpected token...` → fix the syntax error and re-check

> **This is the equivalent of `php -l` for JavaScript.** Zero dependencies, instant results.

### B.4: ESLint Targeted Scan (First-Time Setup Required)

> [!WARNING]
> **Monolithic JS files (10K+ lines) will produce thousands of false positives** with the `no-undef` rule because ESLint can't track variable scope across inline functions in a single file. Use **targeted scanning** only on the affected function/area, not the entire file.

#### First-Time Setup

Create `.eslintrc.json` in the project root:

```json
{
  "rules": {
    "no-undef": "warn"
  },
  "env": {
    "browser": true,
    "jquery": true
  },
  "globals": {
    "base_url": "readonly",
    "Swal": "readonly",
    "toastr": "readonly",
    "moment": "readonly"
  },
  "parserOptions": {
    "ecmaVersion": 2020
  }
}
```

> Add project-specific globals as needed to reduce noise. The `globals` section declares variables that are defined elsewhere (e.g., in other script files or inline `<script>` blocks).

#### Install ESLint v8 (one-time)

```powershell
npm install --save-dev eslint@8.57.0
```

> [!IMPORTANT]
> Use **ESLint v8**, not v9+. ESLint v9 uses "flat config" which is incompatible with `.eslintrc.json`. ESLint v8 is the last version supporting the legacy config format.

#### Running a Targeted Scan

Instead of scanning the entire monolith, extract the affected function to a temp file and scan that:

```powershell
# Option 1: Scan entire file (noisy on monoliths, useful on smaller JS files)
npx eslint -f compact {js_file}

# Option 2: Use grep + manual review for specific patterns (recommended for monoliths)
# Find all functions that use curRow but don't declare it as a parameter:
Select-String -Path {js_file} -Pattern "curRow" | Select-Object LineNumber, Line
```

#### What ESLint CAN Catch

| Rule | What It Finds | BIL-CLT01 Equivalent |
|------|--------------|---------------------|
| `no-undef` | Variable used but never declared/passed | `curRow` used in function that doesn't have it as param |
| `no-unused-vars` | Parameter declared but never used | `row` param in function that uses `curRow` instead |
| `no-redeclare` | Variable declared twice in same scope | Shadowing bugs |
| `eqeqeq` | `==` instead of `===` | Type coercion bugs |

#### Practical Alternative: Pattern Grep

For large monolithic files, a targeted `grep`/`Select-String` search is often more productive than ESLint:

```powershell
# Find functions where param name ≠ body variable name
# Example: function takes 'row' but body uses 'curRow'
Select-String -Path {js_file} -Pattern "function\s+\w+\(row\)" | Select-Object LineNumber, Line

# Find all select2('val') calls (common latent bug)
Select-String -Path {js_file} -Pattern "select2\(.val." | Select-Object LineNumber, Line

# Find jQuery selectors with empty class names: $(".")
Select-String -Path {js_file} -Pattern '\$\(\"\."\)' | Select-Object LineNumber, Line
```

> [!TIP]
> **Lesson from BIL-CLT01**: The `no-undef` rule would have caught `getNonTagproducts()` using `curRow` without it being a parameter. But on a 43K-line file it produced 2363 warnings — mostly false positives from global function/variable cross-references. The **grep approach** is faster and more precise for this codebase.

