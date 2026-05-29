# Cleanup Gaps — Missing Cascades, Orphan Risks, Dead Code
> Last updated: 2026-03-16
> Source: 9 module brains

---

## Missing Cascades on Delete/Cancel/Reverse

| ID | Module | Operation | Table Modified | Tables NOT Cleaned Up | Risk |
|---|---|---|---|---|---|
| CLN-001 | payment | `payment/delete/{id}` | `payment` (hard delete) | `payment_mode_details`, `payment_status_log`, `general_advance_mode_detail`, wallet transactions, sync records | 🔴 HIGH — orphan mode details, broken audit trail |
| CLN-002 | payment | `revertApproval()` | `payment` (status reverted) | Receipt number NOT reverted, wallet NOT reverted, referral NOT reverted | 🔴 HIGH — inconsistent state after approval revert |
| CLN-003 | scheme | `scheme/delete/{id}` | `scheme` | `scheme_benefit_deduct_settings`, `scheme_debit_settings`, `scheme_agent_benefit`, `scheme_incentive_settings`, `scheme_flexi_settings`, `emp_closing_incentive`, `gst_splitup_detail`, `scheme_branch` | 🔴 HIGH — all child config tables become orphans |
| CLN-004 | account | `account_post('Delete')` | `scheme_account` | `payment` records, `gift_issued`, `wallet_transaction` referencing this account | 🟡 MED — may have referential integrity enforcement |
| CLN-005 | Tagging | Tag Delete | `ret_taging` | `ret_taging_stones`, `ret_taging_material`, lot balance restored but other joins may not be | 🟡 MED |
| CLN-006 | chit_reports | `cancel_payment` | `payment` (status=4) | No reverse of SMS sent, no reverse of wallet debit if payment was wallet-mode | 🟡 MED |
| CLN-007 | Estimation | Estimation Cancel | `ret_estimation` | `ret_estimation_items`, gift voucher reservation, chit balance reservation | 🟡 MED |
| CLN-008 | Billing | Bill Cancel | `ret_billing` | `ret_taging.tag_status` must be reset to Available — if this fails, tag is stuck as Sold | 🔴 HIGH |

---

## Dead Code / Orphaned Methods

| ID | Module | File | Location | Description | Risk |
|---|---|---|---|---|---|
| CLN-020 | chit_reports | `admin_report_model.php` | L114-204 | `get_gift_list_old()` — 90-line dead method never called | 🟢 LOW — dead code, misleads readers |
| CLN-021 | chit_reports | `admin_reports.php` | L1562-1574 | `is_lucky_draw` if/else assigns same value — dead logic remnant | 🟢 LOW |
| CLN-022 | payment | `admin_payment.php` | L19, L25 | `chitadmin_model` loaded twice as `ADM_MODEL` and `CHIT_MODEL` | 🟢 LOW — duplicate load |
| CLN-023 | Tagging | `admin_ret_tagging.php` | L12892, L16589 | `product/delect_prodDetail` and `admin_catalog/remove_img` legacy routes | 🟡 MED — may be dead routes that 404 silently |
| CLN-024 | chit_reports | `admin_reports.php` | `email_model` (MAIL_MODEL) | Loaded in constructor, zero email calls found in entire controller | 🟢 LOW |
| CLN-025 | chit_reports | `admin_reports.php` | `chitadmin_model` (ADM_MODEL) | Loaded in constructor, rarely invoked | 🟢 LOW |

---

## Duplicate Route Registrations

| ID | Module | Routes File | Conflict | Risk |
|---|---|---|---|---|
| CLN-030 | chit_reports | `routes.php` | L1596 + L1600: `reports/inter_table/list` registered twice | 🟡 MED — second registration silently overwrites first |
| CLN-031 | chit_reports | `routes.php` | Both `/reports/{endpoint}` and `/admin_reports/{endpoint}` active simultaneously | 🔴 HIGH — dual-route risk, unclear which is canonical |

---

## Missing Soft-Delete / Audit on Destructive Operations

| ID | Module | Operation | Current Behavior | Recommended |
|---|---|---|---|---|
| CLN-040 | payment | Delete payment | Hard DELETE from DB | Soft-delete: `is_deleted=1, deleted_at=NOW(), deleted_by=uid` |
| CLN-041 | scheme | Delete scheme | Hard DELETE | Soft-delete with child table cascade |
| CLN-042 | account | Delete scheme_account | Hard DELETE | Soft-delete — closing replaces delete in most cases |
| CLN-043 | Tagging | Delete tag | Hard DELETE (after OTP) | Audit log entry on delete |

---

## Commented-Out Legacy Code Volume

| Module | File | Estimated Lines | Risk |
|---|---|---|---|
| payment | `admin_payment.php` | ~200+ lines commented | Maintenance debt, misleads code readers |
| account | `admin_manage.php` | ~200 lines | Same |
| scheme | `admin_scheme.php` | ~100 lines | |
| chit_reports | `admin_reports.php` | ~50 lines | |
| Estimation | Model + Controller | ~100 lines | |
