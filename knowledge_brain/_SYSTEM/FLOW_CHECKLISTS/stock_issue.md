# Flow Checklist: Stock Issue / Receipt

> **Last Updated:** 2026-03-27
> **Controller:** `admin_ret_stock_issue.php`

---

## Overview

Stock Issue = sending non-tag items (packaging, accessories, raw materials) to departments or external parties. Stock Receipt = receiving them back.

## SAVE Checklist (Issue)

| # | Table | Expected Action | Status |
|---|---|---|---|
| 1 | `ret_stock_issue` | INSERT header (issue_date, branch, issue_to, type) | ⬜ |
| 2 | `ret_stock_issue_items` | INSERT line items | ⬜ |
| 3 | `ret_nontag_item` (-) | Reduce NT stock | ⬜ |
| 4 | `ret_nontag_item_log` | Log stock outflow | ⬜ |

## SAVE Checklist (Receipt)

| # | Table | Expected Action | Status |
|---|---|---|---|
| 1 | `ret_stock_issue.receipt_*` | Mark received | ⬜ |
| 2 | `ret_nontag_item` (+) | Add back NT stock | ⬜ |
| 3 | `ret_nontag_item_log` | Log stock inflow | ⬜ |

## CANCEL Checklist

| # | Table | Expected Reversal | Status |
|---|---|---|---|
| 1 | Issue cancel → NT stock add back | ⬜ **VERIFY** |
| 2 | Receipt cancel → NT stock subtract | ⬜ **VERIFY** |
| 3 | Both logged in `ret_nontag_item_log` | ⬜ |

## Known Risks

| Risk | Description | Severity |
|---|---|---|
| SI-001 | NT stock goes negative without guard | 🟡 MED |
