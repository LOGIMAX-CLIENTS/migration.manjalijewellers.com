---
id: recipe_duplicate_barcode_scan
name: Duplicate Tag Entries From Barcode Scanner
version: 1.0
created: 2026-04-21
module: Reports (scan_report)
bug_id: RPT-CLT01
severity: P1
category: Concurrency / Duplicate Submission
symptom_keywords: duplicate scan, barcode, double entry, piece count wrong, tag scanned twice
---

## Symptom

When using a **barcode scanner machine** to scan tags in the Tag Scan Check Report, the system records the same tag **twice** per scan. The displayed piece count is double the actual count (5 scans → shows 10).

## Root Cause

Barcode scanners emit the scanned value followed by an `Enter` keystroke. The scan page has **two event listeners** that both call the scan AJAX function:

1. `keypress(e.which == 13)` on `#tag_id` → triggers AJAX call
2. `click` on `#tag_scan_search` button → also triggered by Enter key bubbling in the browser

Both AJAX requests fire nearly simultaneously. Since both pass the server-side duplicate-check (`get_scanned_details()` returns TRUE for both — the first hasn't committed yet), both proceed to insert into `ret_tag_scanned`, causing a duplicate row.

**Pattern:** Classic JS double-submit race condition specific to barcode scanners.

## Detection

Search for this pattern:
```bash
grep -n "keypress.*13" admin/assets/js/ret_reports.js | grep tag
grep -n "tag_scan_search.*click" admin/assets/js/ret_reports.js
```
If both exist without a request-lock guard → this bug is present.

## Before (Vulnerable Code)

```javascript
// admin/assets/js/ret_reports.js

$("#tag_scan_search").on("click", function (e) {
  var tag_id = input.split("/")[0];
  var validate = validate_Tag_Scan();
  if (validate) {
    get_tag_scan_details(tag_id, old_tag_id); // fires via button click (includes Enter bubble)
  }
});

$("#tag_id,#old_tag_id").keypress(function (e) {
  if (e.which == 13) {
    // No e.preventDefault() — Enter bubbles up to click the button
    if (validate) {
      get_tag_scan_details($("#tag_id").val(), old_tag_id); // ALSO fires from keypress
    }
  }
});

function get_tag_scan_details(tag_id, old_tag_id) {
  // No guard — always fires AJAX
  $("div.overlay").css("display", "block");
  $.ajax({ ... });
}
```

## After (Fixed Code)

```javascript
// admin/assets/js/ret_reports.js

// RPT-CLT01 FIX: in-flight request lock
var isTagScanPending = false;

$("#tag_scan_search").on("click", function (e) {
  var tag_id = input.split("/")[0];
  var validate = validate_Tag_Scan();
  if (validate) {
    get_tag_scan_details(tag_id, old_tag_id);
  }
});

$("#tag_id,#old_tag_id").keypress(function (e) {
  if (e.which == 13) {
    e.preventDefault(); // Stops Enter from also triggering button click
    if (validate) {
      get_tag_scan_details($("#tag_id").val(), old_tag_id);
    }
  }
});

function get_tag_scan_details(tag_id, old_tag_id) {
  if (isTagScanPending) { return; } // Block second call
  isTagScanPending = true;
  $("div.overlay").css("display", "block");
  $.ajax({
    ...
    error: function () { $("div.overlay").css("display", "none"); },
    complete: function () { isTagScanPending = false; }, // Always reset
  });
}
```

## Verification Steps

1. Open `admin_ret_reports/scan_report/list`
2. Select Branch, Product, Section
3. Scan 5 different tags using the barcode scanner machine
4. Confirm the list shows exactly 5 rows (not 10)
5. Check DB: `SELECT count(*) FROM ret_tag_scanned WHERE id_scanned = [latest_scan_id]` → should equal number of tags scanned

## Clients Found In

- vrsjewellery.com (fixed 2026-04-21)

## Notes

- The server-side `get_scanned_details()` check cannot prevent this race — both requests arrive before either commits
- The `e.preventDefault()` alone may not be sufficient on all browsers — the lock flag is the primary guard
- Reset happens in `complete:` (not `success:`/`error:`) to ensure it always fires
