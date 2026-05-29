# Knowledge Gaps — Old Metal Process Module

Areas where analysis is incomplete, uncertain, or requires runtime/DB verification.  
**Status key**: ✅ Resolved | ⚠️ Partially resolved | ❓ Still open

---

## GAP-1: Actual DB Schema ✅ RESOLVED (R5)

| Table | Status | Finding |
|---|---|---|
| `ret_old_metal_pocket` | ✅ | `pocket_no` VARCHAR, **no UNIQUE** — OMP-005 variant applies to pockets |
| `ret_old_metal_process` | ✅ | `process_no` VARCHAR(20) NULLABLE **no UNIQUE** — OMP-005 fully DB-unprotected |
| `ret_old_metal_melting` | ✅ | `melting_status` TINYINT(1), no CHECK constraint, DEFAULT 0 |
| `ret_old_metal_melting_recd_details` | ⚠️ | Not described in R5, but `is_non_tag` confirmed unused in testing/refining (GAP-7) |
| `ret_old_metal_refining` | ⚠️ | DB query timed out — columns inferred from controller code |
| `ret_nontag_item` | ⚠️ | Not described in R5 — assumed composite PK from controller lookups |
| `smith_company_op_balance` | ❓ | Confirmed separate from `company` table — schema still unknown |
| `ret_old_metal_process_payment` | ⚠️ | `type` is INT(11) always=1, `payment_mode` is VARCHAR (not enum), cash ref field has no `name` attribute in view |

**See**: `SCHEMA_ANALYSIS.md` for full field breakdown.

---

## GAP-2: `company` Table Structure ⚠️ PARTIALLY RESOLVED (R5)

```php
"Select id_state from company where id_company =".$id
```
- Confirmed: The table `company` is a **separate** bare-prefixed table (not `ret_company`)
- `admin_settings_model->getCompanyDetails("")` returns this table's data
- OMP-002 fix guidance: in `get_chg_tax_type()`, replace `id_company` comparison with `id_state` comparison using `getCompanyDetails()['id_state']`
- **Still unknown**: exact column list of `company` table — run `DESCRIBE company;` in `nsk` DB to confirm `id_state` column name

---

## GAP-3: Against-Melting Receipt Save Path ✅ RESOLVED (R4)

Controller fully traced (lines 711–937):
- `against_melting == 2` (No) → standard testing receipt path — `testing_status=1`, stock updates run
- `against_melting == 1` (Yes) → `testing_status=1` set, but **entire stock cascade block is commented out** (lines 744–755)
- Against-melting items never get status closure or `ret_purchase_items_log` entries
- **Fix required**: Uncomment lines 744–755, set `melting_status=3` on the linked `ret_old_metal_melting_recd_details` row

---

## GAP-4: `ctrl_page` Variable in JS ✅ RESOLVED (R5)

**Confirmed via view scan**: `form.php` line 98 sets:
```html
<input type="hidden" id="id_branch" value="<?php echo $branch['id_branch']; ?>">
```
But `ctrl_page` is NOT defined in this view. It is set in the global layout/template by the CI controller method name. The URL path `metal_process_issue/add` means `ctrl_page = ['admin_ret_metal_process', 'metal_process_issue', 'add']` — so `ctrl_page[1] === "metal_process_issue"` **IS TRUE** when on the add page. `prod_details` is populated correctly.

---

## GAP-5: Repair Order Integration ⚠️ PARTIALLY RESOLVED (R4)

- Controller traced: no dedicated repair-pocket save path exists in main issue save
- `get_repair_pending_order()` returns data but associated JS forEach is commented out (model lines 1877–1891)
- **Conclusion**: Repair order pocket creation is an **incomplete / abandoned feature** — not a bug to fix, but a feature gap to document
- The regular pocket save (trans_type=1) handles all pockets including repair origins

---

## GAP-6: Lot Inward Number Format ✅ RESOLVED (R5)

`ret_lot_inwards.lot_no` is an **auto_increment primary key** (int, not varchar). The field `lotId = $this->model->insertData($Lotdata, 'ret_lot_inwards')` returns the insert ID. References to this as "lot_no" in model queries use the numeric ID. No prefix/format applied — purely sequential integers.

---

## GAP-7: `is_non_tag` in Testing/Refining Receipts ✅ RESOLVED (R5)

**Confirmed by full controller trace (R4)**:
- Testing receipt: stock type is ALWAYS non-tag (`ret_nontag_item`) — no `is_non_tag` check needed because testing output is always raw metal/processed items (not tagged jewelry)
- Refining receipt: same — always routes to `ret_nontag_item` via `checkNonTagItemExist()`
- Only **Polishing receipt** uses `is_non_tag` checkbox because polishing can produce finished tagged OR non-tagged items
- The stock destination is decided by `is_non_tag` flag in the polishing category modal data

---

## GAP-8: Multi-Branch Behavior ⚠️ PARTIALLY RESOLVED (R5)

- `get_branch_details()` returns the **current session branch**, not HO fallback
- Save operations write `id_branch` from `$addData['id_branch']` (POST), not from session — meaning a crafted POST can save to a different branch
- No branch validation: server trusts the `id_branch` field from the form entirely
- **Risk**: Confirmed branch injection possible — same as OMP-006 for weight, applies to branch selection too
- Cross-branch stock lookup via `from_branch` param confirmed — used only in report/search, not in saves

---

## GAP-9: DomPDF Version and Typo ⚠️ PARTIALLY RESOLVED (R5)

- DomPDF v0.6.x inferred from `autoload.inc.php` path and `DOMPDF()` class name (later versions use `Dompdf\Dompdf`)
- In DomPDF 0.6.x, `set_paper($size, $orientation)` — an invalid orientation string silently **defaults to portrait** → OMP-018 typo causes no visual error but is a latent bug for any future upgrade
- Whether all 8 process PDF acknowledgements tested: **unknown** — no test evidence found

---

## GAP-10: Return/Cancellation Not Implemented ✅ RESOLVED (R5 — Confirmed as Design Gap)

**Definitively confirmed after full 1792-line controller scan**:
- Zero cancellation methods found
- Zero reversal logic exists
- All OMP transactions are **permanently append-only**
- This is intentional design (jewelry business requirement: audit trail preservation)
- **Implication**: Any bug that causes wrong data to be saved (OMP-028, OMP-029, OMP-003 etc.) **cannot be self-corrected by users** — they require direct SQL fixes by an admin

---

## Remaining Open Gaps

| Gap | Status | Action Needed |
|---|---|---|
| `smith_company_op_balance` schema | ❓ Open | Run `DESCRIBE smith_company_op_balance;` in nsk |
| `company.id_state` column name | ⚠️ Partial | Run `DESCRIBE company;` in nsk |
| `ret_nontag_item` indexes | ⚠️ Partial | Run `SHOW INDEX FROM ret_nontag_item;` |
| DomPDF version exact | ⚠️ Partial | Check `composer.json` or `autoload.inc.php` header |
| Tally sync service location | ❓ Open | Search codebase for `outistransfered` references |
