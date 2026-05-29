# eTail v3 Testing Infrastructure

> Complete guide to unit testing, coverage, mutation testing, and test automation.

---

## Quick Reference

| Language   | Framework    | Coverage   | Watch           | Mutation  |
| ---------- | ------------ | ---------- | --------------- | --------- |
| PHP        | PHPUnit 12.5 | Xdebug 3.5 | phpunit-watcher | Infection |
| JavaScript | Jest 29.7    | Built-in   | Built-in        | Stryker   |
| Python     | pytest 9.0   | pytest-cov | pytest-watch    | mutmut    |

---

## Unified Test Runner

Use `run_tests.bat` from project root:

```bash
run_tests.bat              # Run all tests
run_tests.bat php          # PHP tests only
run_tests.bat js           # JavaScript tests only
run_tests.bat python       # Python tests only
run_tests.bat watch        # Start watch mode (all languages)
run_tests.bat coverage     # Run with coverage
run_tests.bat mutation     # Run mutation testing
```

---

## PHP Testing

### Location

- **Tests**: `admin/tests/`
- **Mocks**: `admin/tests/mocks/`
- **Factories**: `admin/tests/factories/TestDataFactory.php`
- **Config**: `admin/tests/phpunit.xml`

### Commands

```bash
cd admin/tests

# Run tests
vendor/bin/phpunit --testdox

# With coverage
vendor/bin/phpunit --coverage-text
vendor/bin/phpunit --coverage-html coverage/

# Watch mode
vendor/bin/phpunit-watcher watch

# Mutation testing
vendor/bin/infection --min-msi=70
```

### Using Test Data Factories

```php
<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/factories/TestDataFactory.php';

class MyControllerTest extends TestCase
{
    use TestDataFactory;  // Includes all factories

    public function test_save_estimation()
    {
        // Create test data with defaults
        $estimation = $this->makeValidEstimation();

        // Or with custom values
        $estimation = $this->makeValidEstimation([
            'discount' => 500,
            'total_cost' => 75000
        ]);

        // Create related data
        $customer = $this->makeCustomer(['cusName' => 'John']);
        $items = $this->makeEstimationItems(3);
    }
}
```

### Available PHP Factories

| Method                                    | Description               |
| ----------------------------------------- | ------------------------- |
| `makeValidEstimation($overrides)`         | Create estimation data    |
| `makeEstimationItems($count, $overrides)` | Create estimation items   |
| `makeCustomer($overrides)`                | Create customer data      |
| `makeCorporateCustomer($overrides)`       | Create corporate customer |
| `makeProduct($overrides)`                 | Create product data       |
| `makeTaggingItem($overrides)`             | Create inventory tag      |
| `makeBill($overrides)`                    | Create bill data          |
| `makeBillItems($count, $overrides)`       | Create bill items         |
| `makeUser($overrides)`                    | Create user data          |
| `makeAdminUser($overrides)`               | Create admin user         |

---

## JavaScript Testing

### Location

- **Tests**: `admin/tests/*.test.js`
- **Factories**: `admin/tests/factories/testDataFactory.js`
- **Config**: `admin/tests/package.json`

### Commands

```bash
cd admin/tests

# Run tests
npm test

# With coverage
npm run test:coverage

# Watch mode
npm run test:watch
npm run test:watchAll

# Mutation testing
npm run test:mutation
```

### Using Test Data Factories

```javascript
const {
  createEstimation,
  createCustomer,
  createEstimationWithItems,
  createSuccessResponse,
  resetFactories,
} = require("./factories/testDataFactory");

describe("Estimation Module", () => {
  beforeEach(() => {
    resetFactories(); // Reset ID counter
  });

  test("should calculate total correctly", () => {
    const estimation = createEstimation({ discount: 500 });
    expect(estimation.discount).toBe(500);
  });

  test("should handle items", () => {
    const estimation = createEstimationWithItems(3);
    expect(estimation.items.length).toBe(3);
  });

  test("should mock API response", () => {
    const response = createSuccessResponse({ id: 1 });
    expect(response.status).toBe("success");
  });
});
```

### Available JS Factories

| Function                                      | Description              |
| --------------------------------------------- | ------------------------ |
| `createEstimation(overrides)`                 | Create estimation object |
| `createEstimationItem(overrides)`             | Create single item       |
| `createEstimationWithItems(count, overrides)` | Estimation with items    |
| `createCustomer(overrides)`                   | Create customer          |
| `createCorporateCustomer(overrides)`          | Corporate customer       |
| `createProduct(overrides)`                    | Create product           |
| `createTaggingItem(overrides)`                | Create inventory tag     |
| `createBill(overrides)`                       | Create bill              |
| `createBillItem(overrides)`                   | Create bill item         |
| `createUser(overrides)`                       | Create user              |
| `createAdminUser(overrides)`                  | Admin user               |
| `createSession(overrides)`                    | Session data             |
| `createSuccessResponse(data, message)`        | API success response     |
| `createErrorResponse(message, code)`          | API error response       |
| `createPaginatedResponse(items, options)`     | Paginated response       |
| `resetFactories()`                            | Reset ID counter         |

---

## Python Testing (LCA)

### Location

- **Tests**: `logimax-code-analyzer/tests/`
- **Factories**: `logimax-code-analyzer/tests/factories.py`
- **Config**: `logimax-code-analyzer/pytest.ini`

### Commands

```bash
cd logimax-code-analyzer

# Run tests
uv run pytest tests/ -v

# With coverage
uv run pytest --cov=lca --cov-report=term-missing
uv run pytest --cov=lca --cov-report=html

# Watch mode
uv run ptw

# Mutation testing
uv run mutmut run
uv run mutmut results
```

### Using Test Data Factories

```python
import pytest
from tests.factories import FunctionFactory, IndexFactory

class TestImpactAnalysis:
    def test_function_creation(self, function_factory):
        func = function_factory.create(
            name="saveEstimation",
            file="estimation.php",
            line=100
        )
        assert func["name"] == "saveEstimation"

    def test_index_creation(self, index_factory):
        index = index_factory.create_with_hierarchy(depth=4)
        assert len(index["functions"]) == 4

    def test_with_sample_fixtures(self, sample_index, sample_functions):
        # Pre-built fixtures from conftest.py
        assert "module" in sample_index
        assert len(sample_functions) == 3
```

### Available Python Factories/Fixtures

| Fixture             | Description                  |
| ------------------- | ---------------------------- |
| `function_factory`  | FunctionFactory instance     |
| `call_factory`      | CallFactory instance         |
| `index_factory`     | IndexFactory instance        |
| `impact_factory`    | ImpactResultFactory instance |
| `sample_index`      | Pre-built index data         |
| `sample_functions`  | List of sample functions     |
| `sample_call_chain` | A→B→C→D call chain           |
| `temp_index_file`   | Temporary index.json file    |
| `sample_php_code`   | Sample PHP for parsing       |
| `sample_js_code`    | Sample JS for parsing        |

---

## Xdebug Configuration

### Installation

- **DLL**: `C:\xampp\php\ext\php_xdebug.dll`
- **Version**: 3.5.0 for PHP 8.4 NTS

### php.ini Configuration

```ini
[xdebug]
zend_extension=C:\xampp\php\ext\php_xdebug.dll
xdebug.mode=coverage
xdebug.start_with_request=no
xdebug.discover_client_host=false
```

> **Note**: PHP CLI uses `C:\php\php-8.4.8\php.ini`, not XAMPP's php.ini.

### Verify Installation

```bash
php -v
# Should show: with Xdebug v3.5.0
```

---

## Coverage Goals

| Metric            | Target | Enforcement     |
| ----------------- | ------ | --------------- |
| Branch Coverage   | ≥80%   | Required for PR |
| Function Coverage | ≥90%   | Recommended     |
| Line Coverage     | ≥75%   | Recommended     |

---

## Mutation Testing

Mutation testing validates test quality by introducing bugs and verifying tests catch them.

### PHP (Infection)

```bash
cd admin/tests
vendor/bin/infection --min-msi=70 --threads=4
```

- Config: `admin/tests/infection.json`
- Reports: `infection.log`, `infection-report.html`

### JavaScript (Stryker)

```bash
cd admin/tests
npm run test:mutation
```

- Config: `admin/tests/stryker.config.json`
- Report: `mutation-report.html`

### Python (mutmut)

```bash
cd logimax-code-analyzer
uv run mutmut run
uv run mutmut results
```

### Score Interpretation

| Score  | Meaning    | Action             |
| ------ | ---------- | ------------------ |
| ≥80%   | Excellent  | Tests are strong   |
| 60-79% | Acceptable | Improve edge cases |
| <60%   | Weak       | Tests need work    |

---

## Watch Mode

Auto-run tests when files change during development.

### Start All Watchers

```bash
run_tests.bat watch
```

This opens 3 terminal windows:

- PHP: phpunit-watcher
- JS: Jest --watch
- Python: ptw (pytest-watch)

### Individual Watchers

```bash
# PHP
cd admin/tests && vendor/bin/phpunit-watcher watch

# JavaScript
cd admin/tests && npm run test:watch

# Python
cd logimax-code-analyzer && uv run ptw
```

---

## Mocking Best Practices

### PHP (CodeIgniter)

```php
// Database - use Mock_DB
$this->controller->db = new Mock_DB();

// Session - use Mock_Session (authenticated by default)
$this->controller->session = new Mock_Session();

// Models - use PHPUnit mocks
$this->modelMock = $this->createMock(SomeModel::class);
$this->modelMock->method('getData')->willReturn($fakeData);
$this->controller->some_model = $this->modelMock;
```

### JavaScript

```javascript
// Mock fetch
global.fetch = jest.fn(() =>
  Promise.resolve({ json: () => Promise.resolve(mockData) }),
);

// Mock module
jest.mock("../path/to/module", () => ({
  functionName: jest.fn().mockReturnValue(expected),
}));
```

### Python

```python
from unittest.mock import patch, MagicMock

@patch('module.external_call')
def test_function(mock_call):
    mock_call.return_value = expected_value
    result = function_under_test()
    assert result == expected
```

---

## File Structure

```
etail_v3/
├── admin/tests/
│   ├── composer.json          # PHP dependencies
│   ├── package.json           # JS dependencies
│   ├── phpunit.xml            # PHPUnit config
│   ├── infection.json         # Mutation testing config
│   ├── stryker.config.json    # JS mutation config
│   ├── mocks/
│   │   ├── CI_Controller.php  # Mock_DB, Mock_Session, etc.
│   │   └── Models.php         # Model mocks
│   ├── factories/
│   │   ├── TestDataFactory.php  # PHP factories
│   │   └── testDataFactory.js   # JS factories
│   └── *Test.php / *.test.js    # Test files
│
├── logimax-code-analyzer/
│   ├── pyproject.toml         # Python dependencies
│   ├── pytest.ini             # pytest config
│   └── tests/
│       ├── conftest.py        # Shared fixtures
│       ├── factories.py       # Python factories
│       └── test_*.py          # Test files
│
└── run_tests.bat              # Unified test runner
```

---

## Troubleshooting

### PHPUnit "No code coverage driver"

- Ensure Xdebug is installed and configured
- Run `php -v` to verify Xdebug appears

### Jest not finding tests

- Check `testMatch` pattern in package.json
- Ensure files end with `.test.js`

### pytest not finding tests

- Check `testpaths` in pytest.ini
- Ensure files start with `test_`

### Mutation testing slow

- Use `--threads=4` for parallel execution
- Limit scope with `--filter` options

---

## References

- [PHPUnit Documentation](https://docs.phpunit.de/)
- [Jest Documentation](https://jestjs.io/docs/getting-started)
- [pytest Documentation](https://docs.pytest.org/)
- [Xdebug Download](https://xdebug.org/download)
- [Infection PHP](https://infection.github.io/)
- [Stryker Mutator](https://stryker-mutator.io/)

---

_Last Updated: January 31, 2026_
