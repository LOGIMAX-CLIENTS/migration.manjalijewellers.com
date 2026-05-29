# Playwright MCP Test Runner — How To Use

## Overview

This directory contains **config-driven Playwright automation tests** that run via the Playwright MCP tool in Antigravity. Unlike traditional Playwright scripts (which need Node.js + `npx playwright test`), these use the MCP browser tools directly.

## Files

| File | Purpose |
|---|---|
| `test_config.json` | Login credentials, test data, selectors, URLs |
| `run_tests.md` | This file — instructions for the agent |
| `ir_payment_edit.test.md` | Test cases for Issue/Receipt Payment Edit |

## How To Run Tests

### Option 1: Ask the agent (recommended)
Tell the agent:
> "Run the Playwright tests for IR payment edit"

The agent reads `test_config.json`, logs in, and executes each test case from `ir_payment_edit.test.md`.

### Option 2: Run a specific test
> "Run AT-4 from the IR payment edit tests"

### Option 3: Run with different credentials
Update `test_config.json` → `credentials` section, then ask the agent to run.

## Test Execution Flow

```
1. Agent reads test_config.json
2. Agent navigates to login URL
3. Agent fills credentials from config
4. Agent navigates to test URL (from config)
5. Agent executes test steps
6. Agent captures screenshot for verification
7. Agent reports PASS/FAIL with evidence
```

## Adding New Test Suites

1. Create `{feature}.test.md` in this directory
2. Add test data records to `test_config.json` → `test_data`
3. Add selectors to `test_config.json` → `selectors`
4. Follow the format in `ir_payment_edit.test.md`

## Config Update Checklist

When deploying to a different client/environment:
- [ ] Update `base_url` (e.g., `http://localhost/coswan/admin/`)
- [ ] Update `credentials` (username/password)
- [ ] Update `test_data` IDs to match that client's database
- [ ] Verify `login.selectors` match the client's login page
