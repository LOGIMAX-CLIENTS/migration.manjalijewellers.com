# Danger Zones — Things AI Must NEVER Do

> **Purpose**: Hard rules that prevent AI (and junior developers) from making catastrophic mistakes.
> **Used by**: `/bug-intake-triage` Step 6b, `/fix-single-bug` Step 1b (Confidence Gate)
> **Last Updated**: 2026-03-16

---

## How to Use This File

Before applying ANY fix, scan this list. If your fix matches a NEVER rule → **STOP and reconsider**.

---

## Zone 1: Financial / Payment Data

| # | Rule | Why |
|---|---|---|
| DZ-001 | **NEVER** update payment tables (`ret_billing_payment`, `ret_issue_receipt`, `ret_wallet_transcation`) directly via SQL | Payment data must come through the application layer for audit trail, validation, and consistency |
| DZ-002 | **NEVER** assume a webhook always arrives | Webhooks are unreliable by design — always build for missing webhooks |
| DZ-003 | **NEVER** fix a wrong bill total by updating the total column | The total is CALCULATED from line items. Fix the calculation, not the result |
| DZ-004 | **NEVER** delete a financial record to "fix" it | Soft-delete or void. Financial records must have audit trail |
| DZ-005 | **NEVER** change tax rates or tax calculation without business confirmation | Tax is regulatory — wrong GST = legal liability |

---

## Zone 2: Report Fixes

| # | Rule | Why |
|---|---|---|
| DZ-006 | **NEVER** fix only ONE column in a report without checking subtotal, grand total, footer, print, and export | One NaN column cascades to ALL totals. Fix one = must verify all |
| DZ-007 | **NEVER** fix a report display when the underlying DATA is wrong | Fix the data source (query/model), not the view |
| DZ-008 | **NEVER** add a column to a DataTable without checking `footerCallback` indices | PAT-DT-001 — all column indices after the insertion point shift by +1 |
| DZ-009 | **NEVER** change a report query without running the old query first to compare results | You need a baseline to verify your fix didn't break other numbers |

---

## Zone 3: Status Updates / Tag Lifecycle

| # | Rule | Why |
|---|---|---|
| DZ-010 | **NEVER** change `tag_status` directly via SQL | 4 modules read/write tag_status. Direct update breaks the status log and other modules |
| DZ-011 | **NEVER** change tag_status without adding a `ret_taging_status_log` entry | Status history is used by reports, auditing, and 4 modules |
| DZ-012 | **NEVER** assume a `tag_status` value means only one thing | tag_status=7 means BOTH "issued to karigar" (StockIssue) AND "retagged" (Tagging) — check context |

---

## Zone 4: Database / Schema

| # | Rule | Why |
|---|---|---|
| DZ-013 | **NEVER** run ALTER TABLE on production without backup | Schema changes can't be rolled back easily |
| DZ-014 | **NEVER** delete records from tables listed in `SHARED_TABLES.md` without checking all reading modules | Other modules will break with missing foreign key references |
| DZ-015 | **NEVER** add a column without checking all queries that use `SELECT *` on that table | New column may break array index assumptions |

---

## Zone 5: Code Changes

| # | Rule | Why |
|---|---|---|
| DZ-016 | **NEVER** fix only the Add path (save) without checking the Edit path (update) | They often have duplicate logic — fix must be applied to both (PAT-VAL-002) |
| DZ-017 | **NEVER** fix JS calculation without checking if PHP also calculates the same value | Mismatched JS/PHP formulas = data corruption |
| DZ-018 | **NEVER** fix a desktop controller function without checking if an API version exists | API drift = PAT-BIL-001 |
| DZ-019 | **NEVER** add a new hardcoded user ID check (`uid != 169`) | Use roles/permissions instead. Hardcoded UIDs are unmaintainable |
| DZ-020 | **NEVER** use `exit` or `die()` inside a `trans_begin()`/`trans_complete()` block | Transaction will not be rolled back — data corruption |

---

## Zone 6: Concurrency / Integration

| # | Rule | Why |
|---|---|---|
| DZ-021 | **NEVER** assume only one request hits an endpoint at a time | Webhooks, double-clicks, AJAX polls can arrive simultaneously |
| DZ-022 | **NEVER** use customer_id + amount as a duplicate detection key for payments | Same-amount payments (chit installments) will be falsely deduplicated — use payment_id |
| DZ-023 | **NEVER** retry a payment operation without idempotency protection | Retry without idempotency = customer charged twice |

---

## Zone 7: AI-Specific Rules

| # | Rule | Why |
|---|---|---|
| DZ-024 | **NEVER** suggest a fix with high confidence when the bug involves payment/webhook/concurrency | These are automatically 🔴 LOW confidence — present options, don't prescribe |
| DZ-025 | **NEVER** say "just update the database" as a fix for missing/wrong data | The data is a SYMPTOM. Find the CAUSE in the code pipeline |
| DZ-026 | **NEVER** mark a fix as "complete" without verifying downstream impacts | Use the Ripple Check from DIAGNOSTIC_PLAYBOOK |
| DZ-027 | **NEVER** fix a symptom without asking "WHY is this wrong?" | Surface fix = the bug returns or creates a new bug |
| DZ-028 | **NEVER** apply a business logic fix without human confirmation | Track B rules — human validates, AI proposes |

---

## Automatic Confidence Override

If a bug involves ANY of these areas, AI confidence is **automatically 🔴 LOW** regardless of pattern match:

- [ ] Payment / financial data discrepancy
- [ ] Webhook / API integration
- [ ] Concurrency (intermittent failures)
- [ ] Cross-module data flow (3+ modules)
- [ ] Status transitions (`tag_status`, `purchase_status`, `orderstatus`)
- [ ] Tax / GST calculations
- [ ] Delete / cancel / reverse operations

**When confidence is 🔴 LOW**: Present 2-3 possible causes with evidence. Let the developer choose which to investigate. Do NOT prescribe a solution.
