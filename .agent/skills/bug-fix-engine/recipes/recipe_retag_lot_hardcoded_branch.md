# Recipe: Retagging Lot Created Under Wrong Branch (Hardcoded Branch ID)

## Metadata
- **Pattern ID**: PAT-RETAG-001
- **Severity**: HIGH
- **Modules Affected**: Retagging (admin_ret_tagging)
- **Auto-fixable**: Yes

## Client Scope
- **Applies to**: ALL
- **Reason**: Hardcoded `value="1"` in retagging form view; affects all clients using multi-branch setup

## Created By
- **Developer**: Antigravity AI
- **Client**: etail_development_src
- **Date**: 2026-04-18
- **Source Bug ID**: N/A (reported via Ticket Context)

## Symptom
When a user creates a lot using the Retagging process (Sales Return, Partly Sale, Old Metal, etc.), the created lot is stored under branch ID = 1 (Palanai branch) instead of the Head Office branch. This causes the tagging progress to fail because the lot is associated with the wrong branch. Other flows like Purchase Lot Generate and Old Metal Process correctly create lots under Head Office.

## Root Cause
Two `<input type="hidden" id="id_branch" value="1">` elements in `retagging_form.php` always emit `id_branch = 1` regardless of which branch is the actual Head Office. The value `1` happens to be the first branch inserted in the DB (Palanai branch) — not the Head Office. The `create_retag()` controller then reads this POST value directly and passes it to `generateRetaglot()` / `generateRetagNontaglot()`, so all created lots are assigned to branch ID 1.

By contrast, Purchase Lot calls `get_headOffice()` → `SELECT id_branch FROM branch WHERE is_ho=1` to dynamically resolve the HO branch.

## Detection
```bash
grep -n 'value="1"' admin/application/views/tagging/retagging_form.php
# Look for: <input type="hidden" id="id_branch"  value="1">
```

## Files
- `admin/application/views/tagging/retagging_form.php` — lines ~97, ~103
- `admin/application/controllers/admin_ret_tagging.php` — `retagging()` add case + `create_retag()` line ~6799

## Fix

### Before (retagging_form.php — both occurrences)
```php
<input type="hidden" id="id_branch"  value="1">
```

### After (retagging_form.php)
```php
<input type="hidden" id="id_branch"  value="<?php echo isset($ho_branch_id) ? $ho_branch_id : 0; ?>">
```

### Before (admin_ret_tagging.php — retagging() add case)
```php
case 'add':
    $data['main_content'] = "tagging/retagging_form" ;
    $this->load->view('layout/template', $data);
break;
```

### After (admin_ret_tagging.php — retagging() add case)
```php
case 'add':
    $data['main_content'] = "tagging/retagging_form" ;
    // Resolve Head Office branch dynamically (same as Purchase Lot / Old Metal flow)
    $ho_branch = $this->db->query("SELECT id_branch FROM branch WHERE is_ho=1 LIMIT 1")->row_array();
    $data['ho_branch_id'] = !empty($ho_branch['id_branch']) ? $ho_branch['id_branch'] : 0;
    $this->load->view('layout/template', $data);
break;
```

### Before (admin_ret_tagging.php — create_retag())
```php
$id_branch = $this->input->post('id_branch');
```

### After (admin_ret_tagging.php — create_retag())
```php
// Backend safety: always resolve HO branch from DB (same as Purchase Lot / Old Metal flow)
// This prevents incorrect branch assignment regardless of what the frontend POST sends.
$ho_branch_row  = $this->db->query("SELECT id_branch FROM branch WHERE is_ho=1 LIMIT 1")->row_array();
$id_branch      = !empty($ho_branch_row['id_branch']) ? $ho_branch_row['id_branch'] : $this->input->post('id_branch');
```

## Verification
1. Open Retagging Process → Add
2. Select any report type (Sales Return, Old Metal, etc.) and process (Add to ReTag)
3. Click Save → confirm success
4. In DB: `SELECT id_branch, created_branch, lot_received_at FROM ret_lot_inwards ORDER BY lot_inw_id DESC LIMIT 1;`
5. Verify `id_branch` = Head Office branch ID (matches `SELECT id_branch FROM branch WHERE is_ho=1`)
6. Verify the lot appears in Tagging process correctly and doesn't block progress

## Notes
- The session value `$this->session->userdata('id_branch')` cannot be used as a fallback because branch_settings=1 super-admins have `id_branch=0` (meaning all branches). The only correct source of truth is `branch.is_ho=1`.
- `generateRetaglot()` and `generateRetagNontaglot()` both inherit `id_branch` from the POST data (via `$data['id_branch']`), so fixing the source in `create_retag()` covers both functions.
- If `is_ho` is not configured for any branch, the backend falls back to the POST-submitted value as a safety net.
