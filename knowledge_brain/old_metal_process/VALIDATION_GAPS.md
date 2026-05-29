# Validation Gaps — Old Metal Process Module

## Overview
This document maps every user-facing input field to its validation coverage (server-side vs JS-only vs absent).

| Priority | Risk |
|---|---|
| 🔴 HIGH | No validation — direct DB write |
| 🟠 MEDIUM | JS-only — bypassable |
| 🟡 LOW | Partial validation |
| 🟢 SAFE | Server validates |

---

## 1. Pocket Creation (`metal_pocket/save`)

| Field | Source | JS Validate | Server Validate | Risk |
|---|---|---|---|---|
| `process[piece]` total pieces | POST | Sum from checked rows | ❌ None | 🟠 |
| `process[gross_wt]` total GWT | POST | Sum from checked rows | ❌ None | 🟠 |
| `process[net_wt]` total NWT | POST | Sum; NWT ≤ GWT | ❌ None | 🟠 |
| `pocket[piece][]` per item | POST | Capped at balance | ❌ None | 🔴 |
| `pocket[gross_wt][]` per item | POST | Capped at balance | ❌ None | 🔴 |
| `pocket[purity][]` per item | POST | Capped at balance | ❌ None | 🔴 |
| `pocket[item_cost][]` | POST | Capped at balance | ❌ None | 🟠 |
| `pocket[id_metal_pocket][]` | POST | Present in dropdown | ❌ No FK check | 🔴 |

---

## 2. Process Issue — Melting

| Field | JS | Server | Risk |
|---|---|---|---|
| `process[id_metal_process]` | Required | ❌ No process type check | 🟠 |
| `process[id_karigar]` | Required (JS toast) | ❌ None | 🟠 |
| `pocket[issue_gwt][]` | ≤ gross_wt (JS) | ❌ None | 🔴 |
| `pocket[issue_nwt][]` | ≤ net_wt (JS) | ❌ None | 🔴 |
| `pocket[issue_pcs][]` | ≤ piece (JS) | ❌ None | 🔴 |
| `pocket[avg_purity][]` | ≤ balance purity | ❌ None | 🟠 |
| `pocket[id_metal_pocket][]` | From AJAX dropdown | ❌ No FK verify | 🔴 |

---

## 3. Melting Receipt

| Field | JS | Server | Risk |
|---|---|---|---|
| `receipt[is_melting_select][]` | At least one checked | ❌ None | 🟠 |
| `receipt[recd_gwt][]` | ≤ net_wt (keyup) | ❌ None | 🔴 |
| `receipt[category_details][]` JSON | Modal validates category required | ❌ None | 🟠 |
| `receipt[charge][]` | No constraint | ❌ None | 🟠 |
| `receipt_payment[net_banking_amount]` | Sums to total | ❌ None (+ field saves wrong value) | 🔴 |

---

## 4. Testing Issue

| Field | JS | Server | Risk |
|---|---|---|---|
| `testing_issue[weight][]` | ≤ blc_weight (keyup) | ❌ None | 🔴 |
| `testing_issue[id_melting_recd][]` | From AJAX list | ❌ No status pre-check | 🔴 |
| Karigar field | Required (toast) | ❌ None | 🟠 |

---

## 5. Testing Receipt

| Field | JS | Server | Risk |
|---|---|---|---|
| `testing_receipt[recd_gwt][]` | ≤ actual_wt (keyup) | ❌ None | 🔴 |
| `testing_receipt[received_purity][]` | Required (validateRow) | ❌ None — not even range check | 🔴 |
| `testing_receipt[receipt_charges][]` | Required (validateRow) | ❌ None | 🟠 |

> **Note**: `validateTestingReceiptRow()` checks for `.receipt_charges` class but the field has class `.testing_receipt_charges` — **the JS validation for charges is broken** (field name mismatch). This means Testing Receipt can be saved with no charges entered.

---

## 6. Refining Issue

| Field | JS | Server | Risk |
|---|---|---|---|
| `refining_issue[weight][]` | ≤ blc_weight | ❌ None | 🔴 |
| `refining_issue[is_melting_select][]` | At least one checked | ❌ None | 🟠 |
| melting_status pre-check | ❌ None | ❌ None | 🔴 |

---

## 7. Refining Receipt

| Field | JS | Server | Risk |
|---|---|---|---|
| `refining_receipt[recd_gwt][]` | validateRefiningReceiptRow checks | ❌ None server | 🟠 |
| `refining_receipt[receipt_charges][]` | validateRefiningReceiptRow checks `.receipt_charges` class | ❌ Field class is `.refining_receipt_charges` — **JS validation broken** | 🔴 |
| `refining_receipt[chg_tax_perc][]` | No constraint | ❌ None | 🟠 |
| `category_details` JSON | validateRefiningReceiptCategoryRow | Purity, weight, product all required | 🟡 |
| GST type (`chg_tax_type`) | auto-set by AJAX | ❌ Wrong field (OMP-002) | 🔴 |

---

## 8. Polishing Issue

| Field | JS | Server | Risk |
|---|---|---|---|
| `pocket[issue_gwt][]` | ≤ gross_wt (keyup) | ❌ None | 🔴 |
| `pocket[issue_nwt][]` | ≤ net_wt (keyup) | ❌ None | 🔴 |
| `pocket[issue_pcs][]` | ≤ piece (keyup) | ❌ None | 🔴 |
| `pocket[issue_purity][]` | ≤ 100 (keyup) | ❌ None | 🟠 |
| `valiDatePolishingIssue()` | checks all fields non-zero | ❌ None | 🟡 |

---

## 9. Polishing Receipt

| Field | JS | Server | Risk |
|---|---|---|---|
| `polishing_receipt[recd_gwt][]` | validatePolishingReceiptRow | ❌ None | 🟠 |
| `polishing_receipt[recd_pcs][]` | validatePolishingReceiptRow | ❌ None | 🟠 |
| `category_details` JSON | Modal validation | ❌ None | 🟠 |

---

## Critical Validation Class Mismatch Bugs

| Validator Function | Checks Class | Actual Field Class | Effect |
|---|---|---|---|
| `validateTestingReceiptRow()` | `.receipt_charges` | `.testing_receipt_charges` | Charges never validated → can save empty |
| `validateRefiningReceiptRow()` | `.receipt_charges` | `.refining_receipt_charges` | Same — charges skip validation |

These are **new bugs** not in the original BUG_PATTERNS.md:
- **BUG-OMP-020**: Testing receipt charges bypass JS validation (class name typo)
- **BUG-OMP-021**: Refining receipt charges bypass JS validation (class name typo)

---

## Summary Counts

| Severity | Count |
|---|---|
| 🔴 No validation — direct risk | 15 fields |
| 🟠 JS-only (bypassable) | 12 fields |
| 🟡 Partial | 4 fields |
| 🟢 Safe | 2 fields |
