# Passbook Print Empty on Reprint / Multi-Due Duplicate Windows

## Metadata
- **Pattern ID**: PAT-PB-001
- **Severity**: HIGH
- **Modules Affected**: Payment, Scheme Account
- **Auto-fixable**: Yes

## Client Scope
- **Applies to**: ALL
- **Reason**: Core passbook_print logic affects all clients using passbook printing

## Created By
- **Developer**: Antigravity
- **Client**: karpagamjewels.com
- **Date**: 2026-04-18
- **Source Bug ID**: N/A

## Symptom
1. Passbook print shows **completely empty/blank page** when accessed from:
   - Account list (`/account/new`) → Action → Passbook Print
   - Payment list (`/payment/list`) → Action → Print Passbook
   - Reports page (`/reports/payment/account/{id}`) → Passbook Print Back button
2. Multi-due payment (paying 2-3 installments at once) opens **multiple duplicate windows** instead of one. Only the first window shows data; subsequent windows are empty.

## Root Cause
Three bugs contributing to the same symptom:

1. **Controller `passbook_print` B page handler** (non-SRINIDHI classification): Does NOT handle `$id_payment` parameter. From payment list, URL `passbook_print/B/{id_scheme_account}/{id_payment}` is called but `id_payment` is completely ignored.

2. **Controller B page reprint scenario**: After first print, all payments are marked `is_print_taken = 1` in DB. On subsequent access, `passbook_back.php` view finds `lastIndex = -1` (no row with `is_print_taken == 0`) and the rendering loop breaks immediately → empty page.

3. **Payment.js multi-due**: `$.each(data.payid, ...)` loop opens the same URL once per payment ID. First window marks all as printed; subsequent windows show nothing.

## Detection
```command
grep -n "passbook_print/B/" admin/assets/js/payment.js
grep -n "page == 'B'" admin/application/controllers/admin_manage.php
grep -n "lastIndex" admin/application/views/scheme/print/passbook_back.php
```

## Files
- `admin/application/controllers/admin_manage.php` — `passbook_print()` function, B page handler for non-SRINIDHI
- `admin/assets/js/payment.js` — `save_all` success handler, `data.type == 3` branch

## Fix

### Before (admin_manage.php — B page, non-SRINIDHI)
```php
} else if ($page == 'B' && $data['acc']['is_closed'] != 1 && $data['customer']['classification_name'] != 'SRINIDHI') {

    $html = $this->load->view('scheme/print/passbook_back', $data, true);
    $this->load->helper(array('dompdf', 'file'));
    $dompdf = new DOMPDF();
    $dompdf->load_html($html);
    $customPaper = array(0, 0, 529.13, 1035.6);
    $dompdf->set_paper('A4', "portriat");
    $dompdf->render();
    $dompdf->stream("receipt1.pdf", array('Attachment' => 0));

    // Update as print taken
    foreach ($data['payment'] as $pay) {
        $this->$acc_model->updateData(array('is_print_taken' => 1), 'id_payment', $pay['id_payment'], 'payment');
    }
}
```

### After (admin_manage.php — B page, non-SRINIDHI)
```php
} else if ($page == 'B' && $data['acc']['is_closed'] != 1 && $data['customer']['classification_name'] != 'SRINIDHI') {

    $should_update_print_taken = true;

    if ($id_payment != "") {
        // Specific payment requested (from payment list)
        $datapay = [];
        foreach ($data['payment'] as $pay) {
            $pay['is_print_taken'] = ($pay['id_payment'] == $id_payment) ? 0 : 1;
            $datapay[] = $pay;
        }
        $data['payment'] = $datapay;
        $should_update_print_taken = false;
    } else {
        // No specific payment - check if all are already printed
        $has_unprinted = false;
        foreach ($data['payment'] as $pay) {
            if ($pay['is_print_taken'] == 0) {
                $has_unprinted = true;
                break;
            }
        }
        if (!$has_unprinted && count($data['payment']) > 0) {
            // All already printed - reprint mode: show all rows with data
            $datapay = [];
            foreach ($data['payment'] as $pay) {
                $pay['is_print_taken'] = 0;
                $datapay[] = $pay;
            }
            $data['payment'] = $datapay;
            $should_update_print_taken = false;
        }
    }

    $html = $this->load->view('scheme/print/passbook_back', $data, true);
    $this->load->helper(array('dompdf', 'file'));
    $dompdf = new DOMPDF();
    $dompdf->load_html($html);
    $customPaper = array(0, 0, 529.13, 1035.6);
    $dompdf->set_paper('A4', "portriat");
    $dompdf->render();
    $dompdf->stream("receipt1.pdf", array('Attachment' => 0));

    // Update as print taken (only for first-time normal prints)
    if ($should_update_print_taken) {
        foreach ($data['payment'] as $pay) {
            $this->$acc_model->updateData(array('is_print_taken' => 1), 'id_payment', $pay['id_payment'], 'payment');
        }
    }
}
```

### Before (payment.js — multi-due)
```javascript
} else if (data.type == 3 && data.payment_status == 1)
{
    $.each(data.payid, function(index, value) {
        window.open(base_url + 'index.php/admin_manage/passbook_print/B/' + id_scheme_account);
    });
    $("div.overlay").css("display", "none");
    window.location.href = base_url + 'index.php/payment/list';
}
```

### After (payment.js — multi-due)
```javascript
} else if (data.type == 3 && data.payment_status == 1)
{
    window.open(base_url + 'index.php/admin_manage/passbook_print/B/' + id_scheme_account);
    $("div.overlay").css("display", "none");
    window.location.href = base_url + 'index.php/payment/list';
}
```

## Verification
1. Open `/admin_manage/passbook_print/B/{id_scheme_account}` for an account where all payments are already printed → should show all rows with data (reprint mode)
2. Open `/admin_manage/passbook_print/B/{id_scheme_account}/{id_payment}` → should show only that specific payment row
3. Add a multi-due payment (2-3 dues) with "Save and Print Passbook" → ONE window opens showing all new dues
4. Verify `is_print_taken` in DB stays `1` after reprint (no unnecessary DB writes)

## Notes
- The `passbook_back.php` view uses `is_print_taken` flag to control which rows show data vs blank spacers. Rows with `is_print_taken == 1` render as empty rows (for physical passbook alignment).
- The view's `lastIndex` logic (finding last `is_print_taken == 0` row) is preserved — it works correctly when the controller properly sets the flags before rendering.
- This fix does NOT touch the SRINIDHI classification branch or the `passbook_scheme` view — those may have the same reprint issue but were not reported.
- The `PAY` page handler was already correct — it properly handles `id_payment` for both SRINIDHI and non-SRINIDHI.
