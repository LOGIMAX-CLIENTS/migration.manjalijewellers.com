# Payment CRM — Module Brain (Master Index)

> **Module:** Payment CRM (Manual Scheme Payment)  
> **Generated:** 2026-02-18  
> **Status:** ✅ Complete (7/7 Components)  
> **Total Analysis:** ~22,000 lines of source code across 4 primary files

---

## Module Overview

The Payment CRM module handles **manual scheme payment collection** — the core revenue-generating flow of the application. It processes single and multi-installment payments across 8 payment modes (CSH, CC, DC, CHQ, NB, VCH, ADV_ADJ, REF_WALLET, MULTI), supports 3 scheme types with 6 flexible sub-types, handles GST (inclusive/exclusive), DigiGold benefits, wallet redemption, advance adjustment, and multi-branch operations.

**Critical Stats:**
- Controller: 6,275 lines — `payment()` method alone is 2,036 lines
- Model: 8,812 lines — `get_paymentContent()` is 1,043 lines
- JS: 6,627 lines — 304 functions
- View: 1,338 lines — 50+ hidden form fields

---

## Component Index

| # | Component | File | Purpose |
|---|-----------|------|---------|
| 1 | **[Project Skeleton](PROJECT_SKELETON.md)** | `PROJECT_SKELETON.md` | File inventory, architecture diagram, constructor dependencies, file-to-file connections |
| 2 | **[Data Flow](DATA_FLOW.md)** | `DATA_FLOW.md` | 9 complete flows traced: UI Load, Customer Search, Account Detail, Pre-Save Validation, SaveAll, Post-Save, Update, Delete, General Advance |
| 3 | **[Business Rules](BUSINESS_RULES.md)** | `BUSINESS_RULES.md` | 11 rule categories with formulas, JS/PHP comparison, known discrepancies |
| 4 | **[Cross-Module Map](CROSS_MODULE_MAP.md)** | `CROSS_MODULE_MAP.md` | 10 external module dependencies, 7 dangerous connections, data IN/OUT summary |
| 5 | **[Invariant Matrix](INVARIANT_MATRIX.md)** | `INVARIANT_MATRIX.md` | 11 variant dimensions, 7 behavior grids covering all type combinations |
| 6 | **[DB Truth Protocol](DB_TRUTH_PROTOCOL.sql)** | `DB_TRUTH_PROTOCOL.sql` | 25 diagnostic SQL queries across 7 sections for data integrity verification |
| 7 | **[Forensic Template](FORENSIC_TEMPLATE.md)** | `FORENSIC_TEMPLATE.md` | 6-layer investigation template, symptom mapping, bug ticket template |

**Supporting Document:**
- **[Module Variables](../../SOP/modules/payment_crm_variables.md)** | All routes, models, tables, modes, session vars, config items

---

## Known Bugs Identified During Brain Building

| # | ID | Description | Location | Severity |
|---|----|-------------|----------|----------|
| 1 | **BUG-BRAIN-001** | GST inclusive/exclusive use identical formula (`amt - gst_amt`) in weight calculation — exclusive should NOT subtract GST from base | Controller L1486, L1494 | 🔴 High |
| 2 | **BUG-BRAIN-002** | DigiGold other-commodity fixed benefit uses `× other_rate` instead of `/ other_rate` for weight | Controller L1614 | 🔴 High |
| 3 | **BUG-BRAIN-003** | Multi-installment payment mode detail only splits FIRST detail per mode; additional details (e.g., multiple cards) are silently ignored | Controller ~L1800+ | 🟡 Medium |
| 4 | **BUG-BRAIN-004** | `$payment_mode` variable used before initialization in card-only mode detection (L1355) | Controller L1355 | 🟡 Medium |
| 5 | **BUG-BRAIN-005** | JS GST calculation always uses inclusive formula, then conditionally applies — differs from PHP dual-formula approach | JS L2961-2968 | 🟡 Medium |

---

## Quick Reference — File:Line Lookup

### Most Critical Code Sections
| What | File | Lines | Size |
|------|------|-------|------|
| SaveAll main flow | `admin_payment.php` | 1300-2100 | ~800 lines |
| Payment mode detection | `admin_payment.php` | 1350-1402 | ~52 lines |
| GST + Weight calculation | `admin_payment.php` | 1468-1505 | ~37 lines |
| Branch resolution | `admin_payment.php` | 1546-1567 | ~21 lines |
| DigiGold benefits | `admin_payment.php` | 1584-1622 | ~38 lines |
| pay_array build | `admin_payment.php` | 1624-1677 | ~53 lines |
| The Big Query | `payment_model.php` | 1400-2443 | ~1043 lines |
| paymentDB insert | `payment_model.php` | 732-840 | ~108 lines |
| Client validation | `payment.js` | 3111-3266 | ~155 lines |
| payment_success AJAX | `payment.js` | 3318-3382 | ~64 lines |
| Installment +/- | `payment.js` | 3577-3637 | ~60 lines |
| Amount calc (sel_due change) | `payment.js` | 2900-2978 | ~78 lines |
| Weight selection calc | `payment.js` | 2980-3030 | ~50 lines |

---

## Usage Guide

### For Bug Fixing
1. Read **Forensic Template** → follow 6-layer investigation
2. Use **Business Rules** → verify expected calculation
3. Use **Invariant Matrix** → check variant-specific behavior
4. Run **DB Truth Protocol** → verify data state
5. Use **Data Flow** → trace the exact execution path
6. Check **Cross-Module Map** → assess side effects of fix

### For Feature Development
1. Read **Project Skeleton** → understand architecture
2. Read **Data Flow** → identify insertion points
3. Read **Business Rules** → understand existing calculations
4. Read **Cross-Module Map** → identify dependencies to extend
5. Read **Invariant Matrix** → ensure new feature handles all variants

### For Code Review
1. Read **Business Rules** → verify correctness of calculations
2. Read **Invariant Matrix** → ensure all variants covered
3. Run **DB Truth Protocol** → before/after data check
4. Check **Cross-Module Map** → assess blast radius

---

## Metadata

| Key | Value |
|-----|-------|
| Created | 2026-02-18 |
| Updated | 2026-02-18 (Verified DB Schema) |
| Source Conversation | Payment Form Analysis / Payment Module Analysis |
| Previous Analysis | Conversations: 3a3587ad, 32b28e85, dddec674, 8765380e, f4a2ec0c, 1570d9a6 |
| Applied Fixes | BUG-008, BUG-005, BUG-S31-007, BUG-S31-008, EC-6, EC-7, DB-SCHEMA-VERIFY |
