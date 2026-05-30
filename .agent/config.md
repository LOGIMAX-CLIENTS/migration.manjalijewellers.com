# Central Configuration — Bug Remediation System

> **All workflows reference this file for project-level values.**
> **Local/machine-specific values are auto-detected in `.agent/.env` (see Pre-Flight in `/validate-workflows`).**

## Project Identity

| Variable          | Value                                            |
| ----------------- | ------------------------------------------------ |
| `{PROJECT_ROOT}`  | `c:\xampp\htdocs\etail_development_src`           |
| `{PROJECT_NAME}`  | `etail_development_src`                          |
| `{REPO_OWNER}`    | `Logimax-Technologies`                           |
| `{REPO_NAME}`     | `etail_development_src`                          |
| `{FRAMEWORK}`     | `CodeIgniter 3`                                  |
| `{LOCALHOST_URL}` | `http://localhost/etail_development_src/index.php` |

## Environment Paths

| Variable             | Value                                  | Used By                                                 |
| -------------------- | -------------------------------------- | ------------------------------------------------------- |
| `{PHP_PATH}`         | `C:\xampp\php\php.exe`                 | test-and-verify, fix-architecture-bug, fix-business-bug |
| `{PHPUNIT_PATH}`     | `application/tests/vendor/bin/phpunit` | test-and-verify                                         |
| `{PHPUNIT_VERSION}`  | `9.6`                                  | test-and-verify (use `10+` if PHP8)                     |
| `{COMPOSER_VERSION}` | `2.2`                                  | test-and-verify (use `latest` if PHP8)                  |
| `{TEST_DIR}`         | `application/tests/`                   | test-and-verify                                         |

## Directory Structure

| Variable           | Value                      |
| ------------------ | -------------------------- |
| `{CONTROLLER_DIR}` | `application/controllers/` |
| `{MODEL_DIR}`      | `application/models/`      |
| `{VIEW_DIR}`       | `application/views/`       |
| `{JS_DIR}`         | `assets/js/`               |
| `{BRAIN_DIR}`      | `knowledge_brain/`         |
| `{BUG_REPORT_DIR}` | `bug_report_AI/`           |
| `{WORKFLOW_DIR}`   | `.agent/workflows/`        |

## Module Registry

| Module          | Prefix | Controller                  | Model                       | JS                   | Status            |
| --------------- | ------ | --------------------------- | --------------------------- | -------------------- | ----------------- |
| Estimation      | EST    | `admin_ret_estimation.php`  | `ret_estimation_model.php`  | `ret_estimation.js`  | Brain ✅ Audit ✅ |
| Billing         | BIL    | `admin_ret_billing.php`     | `ret_billing_model.php`     | `ret_billing.js`     | Brain ✅          |
| Reports         | RPT    | `admin_ret_reports.php`     | `ret_reports_model.php`     | `ret_reports.js`     | Brain ✅ Audit ✅ |
| Catalog         | CAT    | `admin_ret_catalog.php`     | `ret_catalog_model.php`     | `ret_catalog.js`     | Brain ✅          |
| Branch Transfer | BRN    | `admin_ret_brntransfer.php` | `ret_brntransfer_model.php` | `ret_brntransfer.js` | Brain ✅ Audit ✅ |
| LOT             | LOT    | `admin_ret_lot.php`         | `ret_lot_model.php`         | `ret_lot.js`         | Brain ✅          |
| Stock Issue     | STK    | `admin_ret_stockissue.php`  | `ret_stockissue_model.php`  | `ret_stockissue.js`  | Brain ✅          |
| Tagging         | TAG    | `admin_ret_taging.php`      | `ret_taging_model.php`      | `ret_taging.js`      | Brain ✅          |
| Order           | ORD    | `admin_ret_order.php`       | `ret_order_model.php`       | `ret_order.js`       | Pending           |
| Purchase        | PUR    | `admin_ret_purchase.php`    | `ret_purchase_model.php`    | `ret_purchase.js`    | Pending           |
| Dashboard       | DSH    | `admin_ret_dashboard.php`   | —                           | —                    | Pending           |
| Loyalty         | LYL    | `admin_ret_loyalty.php`     | `ret_loyalty_model.php`     | `ret_loyalty.js`     | Pending           |

## Agent Behavioral Rules

| Rule ID   | Rule                                                                             |
| --------- | -------------------------------------------------------------------------------- |
| AGENT-001 | Never modify database tables directly — always show SQL first                    |
| AGENT-002 | Never delete code — comment with bug ID                                          |
| AGENT-003 | Never change CI framework core files (`system/` directory)                       |
| AGENT-004 | When editing JS files 10K+ lines, test both Add and Edit paths                   |
| AGENT-005 | Never test against production URLs — always use `{LOCALHOST_URL}`                |
| AGENT-006 | Never skip a workflow step silently — state what, why, and impact                |
| AGENT-007 | If `.agent/.env` is missing or wrong, run `/validate-workflows` Pre-Flight first |

> **First time on a new machine?** Run `/validate-workflows` — the Pre-Flight auto-detects all local settings.
