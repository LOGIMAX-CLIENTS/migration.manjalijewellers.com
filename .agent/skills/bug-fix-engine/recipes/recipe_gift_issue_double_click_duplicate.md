---
id: recipe_gift_issue_double_click_duplicate
name: Duplicate Gift Issue on Double-Click Save
version: 1.0
created: 2026-06-13
module: Gift Issue (gift_issue_inv.js, admin_manage.php)
bug_id: GIFT-DUPE-001
severity: P1
category: UI / Duplicate Submission / Race Condition
symptom_keywords: gift issue duplicate, double click save, multiple gift entries, gift issued twice, double submit gift
---

## Symptom

When the Save or Save & Print button is clicked multiple times during gift issue processing, the same gift gets issued multiple times. Each click fires an independent AJAX call to `save_giftissued`, creating duplicate `gift_issued` records in the database.

## Root Cause

The `saveGiftIssued()` function in `gift_issue_inv.js` had **zero protection against concurrent invocations**:
1. No button disable on click
2. No in-flight AJAX guard flag
3. No server-side duplicate detection

Each click enters `saveGiftIssued()` independently. Since the AJAX call takes time, multiple clicks within the request window each serialize the form and POST to the server, resulting in N duplicate inserts.

**Pattern:** This is a textbook double-submit bug — identical to `recipe_pan_duplicate_popup_debounce` but for form submission rather than popup display.

## Detection

```bash
# Check if JS has a save lock guard
grep -n "_giftSaveLock\|saveLock\|saving_gift" admin/assets/js/gift_issue_inv.js

# Check if PHP has duplicate detection
grep -n "DUPLICATE.*GUARD\|DATE_SUB.*INTERVAL.*SECOND" admin/application/controllers/admin_manage.php | grep -i gift
```

If no lock variable exists in JS AND no time-based duplicate check exists in PHP → vulnerable.

## Before (Vulnerable Code)

### JS — No guard at all
```javascript
function saveGiftIssued(triggerPrint)
{
    var content = $('#gift_issue_form').serializeArray();
    // ... validation ...
    if(gift_approved && otp_approved)
    {
        $(".overlay").css('display','block');
        $.ajax({
            type: 'post',
            url: base_url+'index.php/admin_manage/save_giftissued',
            // No button disable, no lock flag
            // Every click fires this independently
        });
    }
}
```

### PHP — No duplicate check
```php
function save_giftissued()
{
    // Immediately processes gifts with no duplicate detection
    $gift_data = $_POST['gift'];
    foreach ($gift_data as $gift) {
        // inserts directly
    }
}
```

## After (Fixed Code)

### JS — Module-level lock + button disable
```javascript
var _giftSaveLock = false; // Module-level lock

function saveGiftIssued(triggerPrint)
{
    // Prevent duplicate submissions
    if (_giftSaveLock) {
        $.toaster({ priority: 'info', title: 'Processing',
            message: 'Gift issue is already being processed. Please wait...',
            settings: { timeout: 3000 }
        });
        return;
    }

    // ... validation ...
    if(gift_approved && otp_approved)
    {
        // Lock immediately
        _giftSaveLock = true;
        $('#save_gift').prop('disabled', true).text('Processing...');
        $('#save_and_print_gift').prop('disabled', true);
        $(".overlay").css('display','block');
        $.ajax({
            // ...
            success: function(data) {
                if(data && data.status) {
                    // Keep locked — page reloads
                } else {
                    // Unlock on data failure
                    _giftSaveLock = false;
                    $('#save_gift').prop('disabled', false).text('Save');
                    $('#save_and_print_gift').prop('disabled', false);
                }
            },
            error: function() {
                // Unlock on AJAX error
                _giftSaveLock = false;
                $('#save_gift').prop('disabled', false).text('Save');
                $('#save_and_print_gift').prop('disabled', false);
            }
        });
    }
}
```

### PHP — 30-second duplicate detection
```php
function save_giftissued()
{
    $id_scheme_account = $this->input->post('id_scheme_account');
    
    // DUPLICATE SUBMISSION GUARD
    if ($id_scheme_account) {
        $recent_gift = $this->db->query(
            "SELECT id_gift_issued FROM gift_issued 
             WHERE id_scheme_account = ? AND status = 1 
             AND date_issued >= DATE_SUB(NOW(), INTERVAL 30 SECOND) 
             LIMIT 1",
            array(intval($id_scheme_account))
        )->row();
        if ($recent_gift) {
            echo json_encode(array(
                'status' => false,
                'duplicate' => true,
                'msg' => 'Gift was already issued moments ago. Please refresh and verify.'
            ));
            return;
        }
    }
    
    // ... proceed with normal processing ...
}
```

**Key design principles:**
1. Lock must be **module-level** (not function-local) — same as `recipe_pan_duplicate_popup_debounce`
2. Unlock only on **failure** — success path reloads the page, so no unlock needed
3. PHP guard is **defense-in-depth** — catches bypasses (multiple tabs, cached JS, browser extensions)
4. 30-second window is generous enough to cover slow connections but won't block legitimate re-issues

## Verification Steps

1. Open Gift Issue form, select a customer and scheme account
2. Add a gift to the issue table
3. Click Save rapidly 3+ times
4. Confirm: only ONE gift_issued record created in DB
5. Confirm: button shows "Processing..." and is disabled after first click
6. Confirm: toaster shows "already being processed" on subsequent clicks
7. Test Save & Print — same behavior, only one entry
8. Test AJAX failure scenario — button re-enables for retry
9. Query DB: `SELECT * FROM gift_issued WHERE id_scheme_account = X ORDER BY date_issued DESC LIMIT 5` — no duplicates

## Files Changed

- `admin/assets/js/gift_issue_inv.js` — Added `_giftSaveLock` module-level flag, button disable/re-enable logic, AJAX error handler
- `admin/application/controllers/admin_manage.php` (`save_giftissued()`) — Added 30-second duplicate detection query before gift insert loop

## Clients Found In

- rajathangamaligai (fixed 2026-06-13)

## Notes

- This pattern applies to ANY save operation that uses AJAX without a guard flag — audit other modules for similar `$.ajax` calls triggered by button clicks without `prop('disabled', true)`
- The PHP 30-second window may need adjustment if a legitimate business workflow requires issuing multiple gifts to the same account within 30 seconds (unlikely but possible) — in that case, add `id_gift` to the duplicate check query
- Related patterns: `recipe_pan_duplicate_popup_debounce` (same class — concurrent event handler race), `recipe_csrf_payment_race_condition` (different class — session race)
