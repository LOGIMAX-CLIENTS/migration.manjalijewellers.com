# Scheme Module — Flow Risk Matrix
> **Created**: 2026-03-24 | **Round**: 2 | 🔄 NEW in Upgrade

## 3b-1. State Machine: `scheme.active` × `scheme.visible`

| State | active | visible | Behavior | Set By |
|---|---|---|---|---|
| Active + Visible | 1 | 1 | Appears in dropdowns, accounts can be created, payments accepted | `sch_post('Add')`, `sch_post('Edit')` |
| Active + Hidden | 1 | 0 | Existing accounts work, new joins blocked from dropdown | `sch_post('Edit')` |
| Inactive + Visible | 0 | 1 | ⚠️ Undefined — visible but inactive; may appear in dropdowns | `sch_post('Edit')` |
| Inactive + Hidden | 0 | 0 | Fully disabled, not visible anywhere | `sch_post('Edit')` |

> **Note**: There is NO explicit status field (`status` column) — activation is controlled by `active` and `visible` flags independently. No guard prevents setting `active=0, visible=1`, which is an ambiguous state.

### State Machine: `scheme_custom_payable_settings.range_status`

| State | Value | Set By | Can Transition To | Guard |
|---|---|---|---|---|
| Active | 1 | `insert_batch()` on Add | Inactive(0) on Edit | — |
| Inactive | 0 | `topUpSchemeChartEditProcess()` soft-delete | Active(1) on re-insert | ⚠️ Old rows stay with status=0 forever |

---

## 3b-2. Inbound Contracts (What Scheme Module Expects from Upstream)

| Upstream Module | Data/State Expected | Precondition Check in Code? | Line | Risk if Violated |
|---|---|---|---|---|
| **Settings** | `chit_settings` row with id=1 exists | NO — `sch_limit()` + `branchwise_scheme()` assume existence | Model L400-414 | Fatal error / NULL in empty_record() |
| **Settings** | `limit_sch`, `sch_max_count` valid values | PARTIAL — checked only if `limit_sch==1` | Controller L142-143 | Unlimited scheme creation if limit_sch != 1 |
| **Settings** | `get_gstsettings()` returns valid GST config | NO explicit check | Controller L140 | Empty GST dropdown |
| **Metal** | `metal` table has active records | NO check | Controller L1022+ | Empty dropdown on scheme form |
| **Branch** | `branch` table has records (if branch_settings=1) | PARTIAL — branch loop skips if empty | Controller L399-410 | No branches mapped on save |
| **Purity** | `ret_metal_purity_rate` + `ret_purity` valid for selected metal | NO check | Controller L1210+ | Empty purity dropdown |
| **Weight** | `weight` table has records for range | NO check | Model L790-799 | Empty weight slabs |
| **Payment Model** | `payment_model.get_metalrate_by_branch()` returns rate > 0 | NO check | Controller L1096 | NULL metal rate in scheme business response |

---

## 3b-3. Outbound Contracts (What Scheme Module Guarantees to Downstream)

| Downstream Module | What This Module Guarantees | Enforced How? | Risk if Broken |
|---|---|---|---|
| **Account** | `scheme.total_installments > 0` for fixed schemes | NO validation | ⚠️ CONTRACT GAP — Division by zero in Account module installment calcs |
| **Account** | `scheme.scheme_type` is valid (0,1,2,3) | NO enum check — form allows any value | ⚠️ Unknown scheme types silently accepted |
| **Account** | `scheme.amount > 0` for Amount schemes | NO validation — `number_format()` silently converts empty to 0 | ⚠️ Zero-amount schemes can be created |
| **Payment** | Benefit chart has no overlapping ranges | NO validation | ⚠️ Overlapping ranges → wrong interest calculation |
| **Payment** | `scheme.gst` matches sum of `gst_splitup_detail` percentages | PARTIAL — `gst` field updated from type=NULL row only | ⚠️ Mismatch if GST rows manually edited in DB |
| **Reports** | `scheme.code` is unique per scheme | NO unique constraint check in code | ⚠️ Duplicate codes confuse reports |
| **Settlement** | Fix-weight schemes have valid `id_metal` | YES — filtered by `get_fixweight_schemes()` JOIN | ✅ Handled |
| **All Consumers** | Child settings tables (9 tables) are consistent with `scheme` flags | NO cross-validation | ⚠️ e.g., `apply_benefit_by_chart=0` but old chart rows still exist |

---

## 3b-4. Reversal Contracts (Cancel/Delete/Reverse)

### Operation: Delete Scheme (`sch_post('Delete')`)

| Tables Written During CREATE | Restored During DELETE? | Method & Line | Gap? |
|---|---|---|---|
| `scheme` → INSERT | ✅ YES (hard delete) | Model `delete_scheme()` L526 | — |
| `gst_splitup_detail` → INSERT | ✅ YES (hard delete) | Model `delete_scheme()` L531 | — |
| `scheme_branch` → INSERT | ❌ NO | — | ⚠️ Orphaned branch mappings |
| `scheme_benefit_deduct_settings` → INSERT | ❌ NO | — | ⚠️ Orphaned benefit chart |
| `scheme_debit_settings` → INSERT | ❌ NO | — | ⚠️ Orphaned deduction chart |
| `scheme_agent_benefit` → INSERT | ❌ NO | — | ⚠️ Orphaned agent benefit |
| `scheme_incentive_settings` → INSERT | ❌ NO | — | ⚠️ Orphaned incentive |
| `scheme_flexi_settings` → INSERT | ❌ NO | — | ⚠️ Orphaned flexi settings |
| `emp_closing_incentive` → INSERT | ❌ NO | — | ⚠️ Orphaned emp incentive |
| `scheme_general_advance_benefit_settings` → INSERT | ❌ NO | — | ⚠️ Orphaned GA benefit |
| `scheme_custom_payable_settings` → INSERT | ❌ NO | — | ⚠️ Orphaned TopUp chart |

**DELETE REVERSAL SCORE: 2/11 tables restored = ❌ 18% — CRITICAL GAP**

### Operation: Edit Scheme (Delete-then-Insert for child tables)

The Edit operation uses a **delete-then-insert** pattern for all child tables. This means:
- All `date_add` timestamps are lost on every edit
- If transaction fails midway, child data may be partially deleted
- `scheme_custom_payable_settings` uses soft-delete (status=0), so old rows accumulate

---

## 3b-5. Flow Risk Checklist (QA-Ready Test Scenarios)

| ID | Test Scenario | Expected Result | Priority | Verified? |
|---|---|---|---|---|
| FR-SCH-001 | CREATE scheme with `total_installments=0` | REJECT or warn — downstream division by zero | 🔴 HIGH | ❌ |
| FR-SCH-002 | CREATE scheme with `amount=0` for Amount-based type | REJECT or warn | 🔴 HIGH | ❌ |
| FR-SCH-003 | CREATE scheme when `chit_settings` table is empty | Should show clear error | 🔴 HIGH | ❌ |
| FR-SCH-004 | CREATE with emp_closing_incentive (Add path) | Verify `id_scheme` is NOT NULL in `emp_closing_incentive` rows | 🔴 HIGH | ❌ |
| FR-SCH-005 | CREATE with GA benefit chart (Add path) | Verify `deleteData()` doesn't delete wrong records (empty $id) | 🔴 HIGH | ❌ |
| FR-SCH-006 | DELETE scheme → verify ALL 11 child tables cleaned up | Full cleanup (currently only 2/11) | 🔴 HIGH | ❌ |
| FR-SCH-007 | DELETE scheme with existing `scheme_account` records | Should be blocked by `check_acc_records()` | 🟡 MED | ❌ |
| FR-SCH-008 | EDIT scheme → verify child table timestamps preserved | `date_add` should not change (currently lost) | 🟡 MED | ❌ |
| FR-SCH-009 | EDIT TopUp scheme → verify no duplicate commit | Check double commit at L966 + L984 | 🔴 HIGH | ❌ |
| FR-SCH-010 | CREATE DigiGold scheme when one already exists for same metal | Should be blocked by `enableDigiGold()` | 🟡 MED | ❌ |
| FR-SCH-011 | Set `apply_benefit_by_chart=1` AND `apply_debit_on_preclose=1` | Undefined behavior — code treats as mutually exclusive | 🔴 HIGH | ❌ |
| FR-SCH-012 | Concurrent scheme creation at exactly the limit | One should succeed, one should fail (race on `scheme_count()`) | 🟡 MED | ❌ |
| FR-SCH-013 | Benefit chart with overlapping installment ranges | Should validate non-overlapping ranges | 🟡 MED | ❌ |
| FR-SCH-014 | DELETE via bookmark/direct GET URL | CSRF vulnerability — should require POST | 🟡 MED | ❌ |
| FR-SCH-015 | EDIT crashes midway during child table delete-then-insert | Verify `trans_begin/commit` coverage (partial cleanup risk) | 🔴 HIGH | ❌ |
| FR-SCH-016 | View `form.php` — Aadhar Required Amount input | Verify `id="aadhaar_required_amt"` (was `pan_req_amt`, fixed in 3ccb4ac1) | ✅ LOW | ✅ |
