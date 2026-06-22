# Recipe: Gift Issue Sticker Print (Save & Print)

## Metadata
- **Pattern ID**: PAT-PRINT-TSPL-001
- **Severity**: N/A (Change Request / New Feature)
- **Modules Affected**: Scheme Management > Gift Issue (`admin_manage` controller, `gift_issue_inv.js`, `gift_issue_form.php`)
- **Auto-fixable**: No (Feature implementation, not a bug pattern)

## Client Scope
- **Applies to**: ALL (clients using Gift Issue module with TSC label printers)
- **Reason**: Gift Issue module is shared across all client codebases. PRN generation follows the established tag label print pattern.

## Created By
- **Developer**: Antigravity AI
- **Client**: RTM Source
- **Date**: 2026-06-13
- **Source Task ID**: N/A (Change Request)

## Requirement
1. Add a **"Save & Print"** button to the Gift Issue form page.
2. On click, save the gift issue transaction AND download a `.prn` sticker file (TSPL format for TSC label printers).
3. The sticker must display: Customer name + mobile, formatted scheme account number, gift name, and gift amount.
4. Account number must use the `customer_model->format_accRcptNo()` display formatting (respects branch codes, scheme codes, financial year, custom formats, lucky draw formats).
5. PRN generation must follow the same server-side pattern as tag label printing in `admin_ret_tagging.php`.

## Architecture Decision
### Why Server-Side PRN (not client-side JS)?

The tag label system (`admin_ret_tagging.php`) uses the correct approach:
1. **PHP builds raw printer commands** (`get_printer_code()`)
2. **PHP sends as `.prn` file download** (`downloadFile()` with `Content-Disposition: attachment`)
3. OS/printer utility routes the `.prn` file to the label printer

Client-side browser `window.print()` does NOT work for label printers because:
- Browser print sends formatted HTML through the OS print driver
- TSC/Zebra label printers require raw TSPL/ZPL byte streams
- Browser print dialog adds margins, headers, and page formatting

## Files
- `admin/application/controllers/admin_manage.php` — Controller (save response + PRN generation)
- `admin/assets/js/gift_issue_inv.js` — JavaScript (save handler + PRN download trigger)
- `admin/application/views/scheme/opening/gift_issue_form.php` — View (button)

## Implementation

### Step 1: Add "Save & Print" Button to View

**File:** `admin/application/views/scheme/opening/gift_issue_form.php`

Add the button between Save and Cancel:

```html
<button type="button" id="save_and_print_gift" class="btn btn-primary"><i class="fa fa-print"></i> Save & Print</button>
```

---

### Step 2: Enrich `save_giftissued()` Response

**File:** `admin/application/controllers/admin_manage.php`

After the gift insert loop, fetch sticker data and return it in the JSON response:

> [!WARNING]
> The variable `$gift_data` is **overwritten** at line ~4151 inside the insert loop (`$gift_data = array('status' => 1)`). Use `$_POST['gift']` directly in the sticker data loop, NOT `$gift_data`.

```php
// After the insert loop...

// Build sticker print data for Save & Print
$sticker_data = array();
if ($insert_gift) {
    $acc_info = $this->db->query("
        SELECT sa.scheme_acc_number, 
               IFNULL(CONCAT(c.firstname, ' ', IFNULL(c.lastname,'')), c.firstname) AS customer_name, 
               c.mobile, s.code AS scheme_code
        FROM scheme_account sa
        LEFT JOIN customer c ON c.id_customer = sa.id_customer
        LEFT JOIN scheme s ON s.id_scheme = sa.id_scheme
        WHERE sa.id_scheme_account = " . intval($id_scheme_account)
    )->row_array();

    $sticker_gifts = array();
    foreach ($_POST['gift'] as $gift) {  // Use $_POST, NOT $gift_data
        if (isset($gift['id_gift'])) {
            $sticker_gifts[] = array(
                'gift_name' => $gift['gift_name'],
                'gift_amount' => $gift['gift_amount']
            );
        }
    }

    $formatted_acc_no = $this->customer_model->format_accRcptNo('Account', intval($id_scheme_account));

    $sticker_data = array(
        'status' => true,
        'customer_name' => $acc_info['customer_name'] ?? '',
        'mobile' => $acc_info['mobile'] ?? '',
        'scheme_acc_display' => $formatted_acc_no ?: ($acc_info['scheme_acc_number'] ?? ''),
        'gifts' => $sticker_gifts
    );
} else {
    $sticker_data = array('status' => false);
}

echo json_encode($sticker_data);
```

---

### Step 3: Add PRN Generation Methods to Controller

**File:** `admin/application/controllers/admin_manage.php`

Three methods following the tag label pattern:

```php
function generate_gift_sticker_prn() {
    $data = json_decode($this->input->post('sticker_data'), true);
    if (!$data || empty($data['gifts'])) { echo "No data"; return; }
    
    $content = "";
    $i = 1;
    foreach ($data['gifts'] as $gift) {
        $printCode = $this->get_gift_sticker_code($data, $gift);
        if ($printCode != "") {
            if ($i != 1) $content .= "\r\n";
            $content .= $printCode;
            $i++;
        }
    }
    if ($content != "") {
        $this->downloadGiftStickerFile($content, uniqid() . '_gift_sticker.prn');
    }
}

function get_gift_sticker_code($data, $gift) {
    // TSPL commands — 24mm x 20mm sticker, ROMAN.TTF, 180° rotation
    $custDisplay = substr($data['mobile'] . ' ' . $data['customer_name'], 0, 30);
    $schemeDisplay = substr($data['scheme_acc_display'], 0, 30);
    $giftName = substr(trim($gift['gift_name']), 0, 25);
    $giftAmount = number_format((float)$gift['gift_amount'], 2, '.', '');
    
    return "SIZE 24 mm, 20 mm\r\n"
        . "GAP 3 mm, 0 mm\r\n" . "SPEED 1\r\n" . "DENSITY 18\r\n"
        . "DIRECTION 0,0\r\n" . "REFERENCE 0,0\r\n" . "OFFSET 0 mm\r\n" . "SHIFT 0\r\n"
        . "SET PEEL OFF\r\n" . "SET CUTTER OFF\r\n" . "SET TEAR ON\r\n"
        . "CLS\r\n" . "CODEPAGE 850\r\n"
        . 'TEXT 190,140,"ROMAN.TTF",180,2,6,"Cust: ' . $custDisplay . '"' . "\r\n"
        . 'TEXT 190,116,"ROMAN.TTF",180,2,6,"Mob : ' . $data['mobile'] . '"' . "\r\n"
        . 'TEXT 190,88,"ROMAN.TTF",180,3,6,"Scheme: ' . $schemeDisplay . '"' . "\r\n"
        . 'TEXT 190,60,"ROMAN.TTF",180,2,6,"Gift: ' . $giftName . '"' . "\r\n"
        . 'TEXT 190,28,"ROMAN.TTF",180,2,7,"Rs. ' . $giftAmount . '"' . "\r\n"
        . "PRINT 1,1\r\n" . "E\r\n";
}

function downloadGiftStickerFile($content, $filename) {
    header('Content-Type: text/plain');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header("Pragma: no-cache");
    ob_clean();
    echo $content;
}
```

---

### Step 4: Refactor JS Save Handler + Add PRN Download Trigger

**File:** `admin/assets/js/gift_issue_inv.js`

1. Extract save logic into shared `saveGiftIssued(triggerPrint)` function
2. Both `#save_gift` and `#save_and_print_gift` call it with `false`/`true`
3. Update success check from `if(data)` to `if(data && data.status)` (response is now an object)
4. Replace browser print window with hidden form POST:

```javascript
function printGiftSticker(data) {
    if (!data || !data.gifts || data.gifts.length === 0) {
        $.toaster({ priority: 'warning', title: 'Print Warning', 
                    message: 'No gift data available for printing' });
        return;
    }
    var form = $('<form>', {
        action: base_url + 'index.php/admin_manage/generate_gift_sticker_prn',
        method: 'POST', target: '_blank'
    });
    form.append($('<input>', {
        type: 'hidden', name: 'sticker_data', value: JSON.stringify(data)
    }));
    $('body').append(form);
    form.submit();
    form.remove();
}
```

## Gotchas

> [!CAUTION]
> **Variable Name Collision**: In `save_giftissued()`, `$gift_data` is overwritten to `array('status' => 1)` at line ~4151 during the insert loop's `updateData` call. Always use `$_POST['gift']` when reading the original gift POST data after the loop.

> [!NOTE]
> **5-second Reload Delay**: The existing save handler has `setTimeout(window.location.reload, 5*1000)`. This is intentional — it gives the user time to read the success toast. Not a bug.

## Verification
1. Open Gift Issue form (`admin/index.php/admin_manage/gift_issue_form`)
2. Select a customer, scheme account, and add a gift to the table
3. Click **Save** → gift saves, page reloads after 5s → no `.prn` download (existing behavior preserved)
4. Repeat with a new gift, click **Save & Print** → gift saves, `.prn` file downloads, page reloads after 5s
5. Open the `.prn` file in a text editor → verify TSPL commands with correct customer, mobile, formatted account number, gift name, and amount
6. Send `.prn` file to TSC printer → verify sticker prints correctly on 24mm x 20mm label

## Notes
- Tag label printing uses ZPL (Zebra) format (`^XA`, `^FT`, `^FS`). Gift sticker uses TSPL (TSC) format (`SIZE`, `TEXT`, `PRINT`). These are different printer languages for different printer brands.
- The `format_accRcptNo()` function from `customer_model` handles all display format configurations including lucky draw, branch-wise, scheme-wise, financial year, and custom formats. Always use this instead of raw `scheme_acc_number`.
