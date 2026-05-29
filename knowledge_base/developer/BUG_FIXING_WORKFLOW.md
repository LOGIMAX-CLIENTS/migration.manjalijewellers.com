# Bug-Fixing Workflow & Tools

> Guide to debugging, error tracking, and code quality tools.

---

## Quick Reference

| Tool           | Command                | Purpose                   |
| -------------- | ---------------------- | ------------------------- |
| **Lint All**   | `run_lint.bat`         | Check PHP & JS code style |
| **Lint Fix**   | `run_lint.bat fix`     | Auto-fix style issues     |
| **Debug Log**  | `DebugLogger::error()` | Log errors in PHP         |
| **Pre-commit** | Automatic              | Tests run before commit   |

---

## 1. Code Linting

### Run Linters

```bash
# From project root
run_lint.bat              # Check all (PHP + JS)
run_lint.bat fix          # Auto-fix all

run_lint.bat php          # PHP only
run_lint.bat js           # JavaScript only

run_lint.bat php fix      # Fix PHP issues
run_lint.bat js fix       # Fix JS issues
```

### PHP_CodeSniffer

Checks PHP code against PSR-12 standard.

```bash
cd admin/tests
composer lint             # Check
composer lint:fix         # Auto-fix
```

**Config**: `admin/tests/phpcs.xml`

### ESLint

Checks JavaScript code for errors and style.

```bash
cd admin/tests
npm run lint              # Check + fix
npm run lint:check        # Check only
```

**Config**: `admin/tests/.eslintrc.json`

---

## 2. Debug Logger

### Location

`admin/application/libraries/DebugLogger.php`

### Usage

```php
// Load the library (auto-loaded in most controllers)
$this->load->library('DebugLogger');

// Basic logging
DebugLogger::info('User logged in', ['user_id' => $id]);
DebugLogger::warning('Low stock detected', ['product' => $name]);
DebugLogger::error('Payment failed', $exception);
DebugLogger::critical('Database connection lost');

// Debug (only in development)
DebugLogger::debug('Variable value', ['data' => $complex_array]);

// Performance tracking
DebugLogger::performance('database_query');  // Start timer
// ... do work ...
$duration = DebugLogger::performance('database_query', true);  // End + log

// SQL query logging
DebugLogger::sql($query, $params, $duration);

// API call logging
DebugLogger::api('POST', '/api/payments', $request, $response, 200);
```

### Log Levels

| Level      | Use Case                      |
| ---------- | ----------------------------- |
| `DEBUG`    | Detailed info for development |
| `INFO`     | General operational events    |
| `WARNING`  | Potential issues              |
| `ERROR`    | Errors that need attention    |
| `CRITICAL` | System failures               |

### Log Files

| File                              | Content             |
| --------------------------------- | ------------------- |
| `admin/logs/debug.log`            | All logs (rotating) |
| `admin/logs/debug_YYYY-MM-DD.log` | Daily logs          |

### View Recent Logs

```php
// Get last 50 log entries
$logs = DebugLogger::getRecentLogs(50);

// Get only errors
$errors = DebugLogger::getRecentLogs(50, 'ERROR');

// Cleanup old logs (keep 7 days)
DebugLogger::cleanup(7);
```

---

## 3. Pre-commit Hooks

### How It Works

When you run `git commit`:

1. Hook checks staged files
2. Runs syntax checks
3. Runs linters (if available)
4. Runs related tests
5. Blocks commit if issues found

### Bypass (Emergency Only)

```bash
git commit --no-verify    # Skip pre-commit hooks
```

### Hook Location

`.git/hooks/pre-commit`

---

## 4. Bug-Fixing Workflow

### Step 1: Reproduce

```php
// Add debug logging to understand the issue
DebugLogger::debug('Starting process', ['input' => $data]);
DebugLogger::performance('suspect_function');
// ... suspect code ...
DebugLogger::performance('suspect_function', true);
DebugLogger::debug('Process result', ['output' => $result]);
```

### Step 2: Analyze Impact

```bash
# Use LCA to find what might be affected
cd logimax-code-analyzer
uv run lca impact "buggy_function" -i master_index.json
```

### Step 3: Write Test First

Create a failing test that reproduces the bug:

```php
public function test_bug_123_edge_case()
{
    // Arrange - setup that reproduces bug
    $data = $this->makeBuggyData();

    // Act
    $result = $this->controller->process($data);

    // Assert - expected correct behavior
    $this->assertEquals('expected', $result);
}
```

### Step 4: Fix the Bug

Make the minimal change to fix the issue.

### Step 5: Run Lint + Tests

```bash
# Check your changes
run_lint.bat
run_tests.bat
```

### Step 6: Commit

```bash
git add .
git commit -m "fix: Bug #123 - Brief description"
```

Pre-commit hook will verify everything passes.

---

## 5. Common Bug Patterns

### PHP Issues ESLint/PHPCS Catches

| Issue               | Rule             |
| ------------------- | ---------------- |
| Undefined variables | `no-undef`       |
| Unused variables    | `no-unused-vars` |
| Missing semicolons  | PSR-12           |
| Loose comparisons   | `eqeqeq`         |
| Direct superglobals | PHPCS rule       |

### JavaScript Issues

| Issue               | Rule             |
| ------------------- | ---------------- |
| Console.log left in | `no-console`     |
| Debugger statements | `no-debugger`    |
| Unused variables    | `no-unused-vars` |
| Missing `const/let` | `no-var`         |

---

## 6. Commands Summary

```bash
# Linting
run_lint.bat              # All linters
run_lint.bat fix          # With auto-fix

# Testing
run_tests.bat             # All tests
run_tests.bat coverage    # With coverage

# Debug logs location
admin/logs/debug_YYYY-MM-DD.log

# Pre-commit (automatic)
# Bypass: git commit --no-verify
```

---

_Last Updated: January 31, 2026_
