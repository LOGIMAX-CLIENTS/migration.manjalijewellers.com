# Recipe: Akshaya Tritiya Performance & UX Patch

## Metadata
- **Pattern ID**: PAT-PERF-007
- **Severity**: MEDIUM
- **Modules Affected**: Estimation, Billing, Dashboard
- **Auto-fixable**: Yes (all 4 changes are deterministic string replacements)

## Client Scope
- **Applies to**: ALL
- **Reason**: These are generic performance/UX improvements applicable to every jewellery ERP client running the standard codebase.

## Created By
- **Developer**: Gokul (Garuda-n)
- **Client**: erp.lakshmanaacharison.in
- **Date**: 2026-04-18
- **Source Commit**: 43b3e1e1a70cc1d262211bd26474a575029abfe3

## What this patch does (in plain English)
1. **Billing list query** — Adds `LIMIT 50` to the recent bills query to prevent slow page loads on high-volume days (like Akshaya Tritiya).
2. **Dashboard auto-refresh** — Disables the 5-minute auto-page-refresh on busy festival days so staff aren't interrupted mid-transaction.
3. **Customer search threshold** — Raises minimum characters to trigger search from 1 → 8, preventing rapid-fire AJAX calls while the user is still typing.
4. **Customer search spinner** — Shows a loading spinner while AJAX is running, hides it when done.
5. **After-save redirect** — After saving an estimation, redirects to `estimation/add` (new form) instead of `estimation/list`, so staff can immediately start the next estimation.

---

## Change 1 — Billing Model: Add LIMIT 50

### File
`admin/application/models/ret_billing_model.php`

### Detection
```powershell
grep -n "ORDER BY bill.bill_id desc" admin/application/models/ret_billing_model.php
```

### Before
```php
         ORDER BY bill.bill_id desc");
```

### After
```php
         ORDER BY bill.bill_id desc LIMIT 50");
```

### Notes
Only replace the **last** occurrence of `ORDER BY bill.bill_id desc");` (around line 595). Do NOT replace other similar patterns in the same file.

---

## Change 2 — Dashboard Footer: Disable Auto-Refresh

### File
`admin/application/views/layout/footer.php`

### Detection
```powershell
grep -n "var time = new Date().getTime();" admin/application/views/layout/footer.php
```

### Before
```php
             var time = new Date().getTime();
             $(document.body).bind("mousemove keypress", function(e) {
                 time = new Date().getTime();
             });

             function refresh() {
                 if(new Date().getTime() - time >= 300000)
                     window.location.reload(true);
                 else
                     setTimeout(refresh, 1000);
             }
             setTimeout(refresh, 1000);
```

### After
```php
             /* var time = new Date().getTime();
             $(document.body).bind("mousemove keypress", function(e) {
                 time = new Date().getTime();
             });

             function refresh() {
                 if(new Date().getTime() - time >= 300000)
                     window.location.reload(true);
                 else
                     setTimeout(refresh, 1000);
             }
             setTimeout(refresh, 1000); */
```

---

## Change 3 — Estimation Form: Add Spinner Element + position:relative

### File
`admin/application/views/estimation/form.php`

### Detection
```powershell
grep -n "est_cus_name" admin/application/views/estimation/form.php
```

### Before
```html
								<div class="input-group " style="width: 100%;">

      								<input class="form-control" id="est_cus_name" name="estimation[cus_name]" type="text" placeholder="Customer Name / Mobile" value="<?php echo set_value('estimation[cus_name]', isset($estimation['cus_name']) ? $estimation['cus_name'] : NULL); ?>" required autocomplete="off" />

      								<input class="form-control" id="cus_id" name="estimation[cus_id]" type="hidden" value="<?php echo set_value('estimation[cus_id]', $estimation['cus_id']); ?>" />
```

### After
```html
								<div class="input-group " style="width: 100%; position: relative;">

      								<input class="form-control" id="est_cus_name" name="estimation[cus_name]" type="text" placeholder="Customer Name / Mobile" value="<?php echo set_value('estimation[cus_name]', isset($estimation['cus_name']) ? $estimation['cus_name'] : NULL); ?>" required autocomplete="off" />

      								<input class="form-control" id="cus_id" name="estimation[cus_id]" type="hidden" value="<?php echo set_value('estimation[cus_id]', $estimation['cus_id']); ?>" />
								<i id="cus_search_spinner" class="fa fa-spinner fa-spin" style="display:none; position:absolute; right:90px; top:50%; transform:translateY(-50%);margin-top: -4px; color:#888; pointer-events:none; font-size:15px; z-index:9;"></i>
```

---

## Change 4 — ret_estimation.js: 3 sub-changes

### File
`admin/assets/js/ret_estimation.js`

### Sub-change 4a — Min chars for customer search: 1 → 8

#### Detection
```powershell
grep -n "customer.length >= 1" admin/assets/js/ret_estimation.js
```

#### Before
```js
		if (customer.length >= 1) {
```

#### After
```js
		if (customer.length >= 8) {
```

---

### Sub-change 4b — After-save redirect: list → add

#### Detection
```powershell
grep -n "estimation/list" admin/assets/js/ret_estimation.js
```

#### Before
```js
					window.location.href = base_url + 'index.php/admin_ret_estimation/estimation/list';
```

#### After
```js
					window.location.href = base_url + 'index.php/admin_ret_estimation/estimation/add';
```

---

### Sub-change 4c — Spinner show/hide in getSearchCustomers()

#### Detection
```powershell
grep -n "my_Date = new Date();" admin/assets/js/ret_estimation.js
```

#### Before
```js
	my_Date = new Date();

	$.ajax({
```
*(and the closing of the ajax block, search for the lone `});` followed by empty line then `}`)*

#### Before (closing)
```js
		}

	});

}
```

#### After (opening — replace `my_Date` line)
```js
	$('#cus_search_spinner').show();

	$.ajax({
```

#### After (closing — add `.always` handler)
```js
		}
		}).always(function () {
		$('#cus_search_spinner').hide();
	});

	};
```

---

## Verification
1. Open Estimation → start typing a customer name — spinner should appear after 8+ chars and disappear when results load.
2. Submit/save an estimation → should redirect to a blank Add form, not the list.
3. Open Dashboard → page should NOT auto-reload every 5 minutes.
4. Open Billing → recent bills list should load fast even on high-transaction days.

## Notes
- This patch is typically applied on high-traffic festival days (Akshaya Tritiya, Dhanteras, etc.).
- Can be reverted after the rush by un-commenting the dashboard refresh and dropping the LIMIT.
- The `LIMIT 50` target is specifically the **last** `bill_id desc` query in `ret_billing_model.php` (~line 595).
