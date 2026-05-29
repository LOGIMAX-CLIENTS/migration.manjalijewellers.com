# eTail v3 - Developer Setup Guide

> Complete guide to set up the development environment with testing infrastructure.

---

## Prerequisites

Before starting, ensure you have the following installed:

| Software     | Version | Download                                            |
| ------------ | ------- | --------------------------------------------------- |
| **XAMPP**    | 8.4+    | [apachefriends.org](https://www.apachefriends.org/) |
| **Node.js**  | 18+     | [nodejs.org](https://nodejs.org/)                   |
| **Python**   | 3.10+   | [python.org](https://www.python.org/)               |
| **uv**       | Latest  | [astral.sh/uv](https://astral.sh/uv)                |
| **Composer** | 2.x     | [getcomposer.org](https://getcomposer.org/)         |
| **Git**      | Latest  | [git-scm.com](https://git-scm.com/)                 |

---

## Step 1: Clone the Repository

```bash
cd C:\xampp\htdocs
git clone <repository-url> etail_v3
cd etail_v3
```

---

## Step 2: Install PHP Dependencies

### Main Application

```bash
cd admin
composer install
```

### Testing Framework

```bash
cd admin/tests
composer install
```

This installs:

- PHPUnit 12.5
- Infection (mutation testing)
- phpunit-watcher (watch mode)

---

## Step 3: Install JavaScript Dependencies

```bash
cd admin/tests
npm install
```

This installs:

- Jest 29.7
- Stryker (mutation testing)

---

## Step 4: Set Up Python Environment (LCA)

```bash
cd logimax-code-analyzer

# Create virtual environment and install dependencies
uv venv
uv sync --extra dev
```

This installs:

- pytest 9.0
- pytest-cov (coverage)
- pytest-watch (watch mode)
- mutmut (mutation testing)
- LCA code analyzer

---

## Step 5: Configure Xdebug for PHP Coverage

### 5.1 Download Xdebug

Download the correct version for your PHP:

| PHP Version | Download                                                                                                      |
| ----------- | ------------------------------------------------------------------------------------------------------------- |
| PHP 8.4 NTS | [php_xdebug-3.5.0-8.4-nts-vs17-x86_64.dll](https://xdebug.org/files/php_xdebug-3.5.0-8.4-nts-vs17-x86_64.dll) |
| PHP 8.3 NTS | [php_xdebug-3.5.0-8.3-nts-vs16-x86_64.dll](https://xdebug.org/files/php_xdebug-3.5.0-8.3-nts-vs16-x86_64.dll) |

> Check your PHP version with `php -v`

### 5.2 Install the DLL

1. Copy the downloaded file to `C:\xampp\php\ext\php_xdebug.dll`

### 5.3 Configure php.ini

Find your active php.ini:

```bash
php -i | findstr "Loaded Configuration"
```

Add to the **end** of that php.ini file:

```ini
[xdebug]
zend_extension=C:\xampp\php\ext\php_xdebug.dll
xdebug.mode=coverage
xdebug.start_with_request=no
xdebug.discover_client_host=false
```

### 5.4 Verify Installation

```bash
php -v
```

You should see:

```
PHP 8.4.x with Xdebug v3.5.0
```

---

## Step 6: Create LCA Index

```bash
cd logimax-code-analyzer

# Index the admin application
uv run lca index ../admin/application -o master_index.json
```

This creates the function call graph used for impact analysis.

---

## Step 7: Verify Setup

### Run All Tests

```bash
# From project root
run_tests.bat
```

### Expected Output

- PHP: PHPUnit runs (may show 0 tests if none created yet)
- JavaScript: Jest runs with estimation tests passing
- Python: pytest runs with 12 factory tests passing

### Verify Individual Frameworks

**PHP:**

```bash
cd admin/tests
vendor\bin\phpunit --version
# PHPUnit 12.5.x
```

**JavaScript:**

```bash
cd admin/tests
npm test
# All tests should pass
```

**Python:**

```bash
cd logimax-code-analyzer
uv run pytest tests/ -v
# 12 tests should pass
```

---

## Quick Reference: Test Commands

### Run Tests

| Command                | Description           |
| ---------------------- | --------------------- |
| `run_tests.bat`        | Run all tests         |
| `run_tests.bat php`    | PHP tests only        |
| `run_tests.bat js`     | JavaScript tests only |
| `run_tests.bat python` | Python tests only     |

### Coverage

| Command                              | Description       |
| ------------------------------------ | ----------------- |
| `run_tests.bat coverage`             | All with coverage |
| `vendor\bin\phpunit --coverage-text` | PHP coverage      |
| `npm run test:coverage`              | JS coverage       |
| `uv run pytest --cov=lca`            | Python coverage   |

### Watch Mode

| Command                            | Description  |
| ---------------------------------- | ------------ |
| `run_tests.bat watch`              | All watchers |
| `vendor\bin\phpunit-watcher watch` | PHP watch    |
| `npm run test:watch`               | JS watch     |
| `uv run ptw`                       | Python watch |

### Mutation Testing

| Command                  | Description        |
| ------------------------ | ------------------ |
| `run_tests.bat mutation` | All mutation tests |
| `vendor\bin\infection`   | PHP mutation       |
| `npm run test:mutation`  | JS mutation        |
| `uv run mutmut run`      | Python mutation    |

---

## LCA Cockpit (Web UI)

To start the visual code analysis dashboard:

```bash
cd logimax-code-analyzer
uv run lca ui -i master_index.json
```

Open http://localhost:8765 in your browser.

---

## Folder Structure

```
etail_v3/
├── admin/
│   ├── application/          # CodeIgniter application
│   └── tests/
│       ├── composer.json     # PHP test dependencies
│       ├── package.json      # JS test dependencies
│       ├── phpunit.xml       # PHPUnit config
│       ├── mocks/            # Mock classes
│       ├── factories/        # Test data factories
│       └── *.test.js         # JS test files
│
├── logimax-code-analyzer/    # LCA tool
│   ├── pyproject.toml        # Python dependencies
│   ├── pytest.ini            # pytest config
│   ├── lca/                  # LCA source code
│   └── tests/                # Python tests
│
├── knowledge_base/           # Documentation
│   ├── developer/            # Dev docs (LCA, Testing)
│   ├── management/           # Management docs (SUPERADMIN)
│   └── client_docs/          # Client documentation
│
├── run_tests.bat             # Unified test runner
└── .gitignore                # Git exclusions
```

---

## Troubleshooting

### "composer: command not found"

- Add Composer to PATH or use full path: `C:\composer\composer.phar`

### "npm: command not found"

- Install Node.js from nodejs.org

### "uv: command not found"

- Install: `pip install uv` or `pipx install uv`

### "No code coverage driver available"

- Xdebug not properly installed. Check:
  1. DLL exists in `C:\xampp\php\ext\`
  2. php.ini configuration is correct
  3. You edited the RIGHT php.ini (run `php -i | findstr "Loaded Configuration"`)

### "PHPUnit tests discovering vendor files"

- Ensure phpunit.xml has `<exclude>./vendor</exclude>` in testsuite

### "LCA index not found"

- Run: `uv run lca index ../admin/application -o master_index.json`

### Python version error

- LCA requires Python 3.10+. Check with `python --version`

---

## Getting Help

- **Testing Docs**: `knowledge_base/developer/TESTING_INFRASTRUCTURE.md`
- **LCA Docs**: `knowledge_base/developer/LCA_CODE_ANALYZER.md`
- **Knowledge Base**: Open in browser via KB viewer

---

## Quick Setup Script

Run this from project root after cloning:

```powershell
# PowerShell Quick Setup
cd admin/tests
composer install
npm install

cd ../../logimax-code-analyzer
uv venv
uv sync --extra dev
uv run lca index ../admin/application -o master_index.json

cd ..
echo "Setup complete! Run: run_tests.bat"
```

---

_Last Updated: January 31, 2026_
