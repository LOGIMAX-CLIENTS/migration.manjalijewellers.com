## RPT-CLT04 — Tag Status Not Updated as "Metal Issue" in Tag History & Tag Movement Reports

| Field         | Value                                             |
| ------------- | ------------------------------------------------- |
| Severity      | P1                                                |
| Track         | A (System)                                        |
| Category      | Logic (Missing status mapping in report query)    |
| Sprint        | Sprint 2                                          |
| Pattern Match | PAT-QRY-005 (closest — NULL/Empty Fallback)       |
| Module Brain  | ✅ Ready (Purchase + Ret_Reports)                 |
| Reporter      | Client                                            |
| Source        | Client                                            |

⛔ DANGER ZONE: Status transitions (`tag_status`). See DZ-005, DZ-037.
→ AI confidence: LOW → Require senior developer review before applying fix.

### Steps to Reproduce

1. Navigate to `admin_ret_metal_process/metal_process_issue/add`
2. Perform a Smith Metal Issue using tag code (e.g., `GBT-01272`)
3. Transaction saves successfully, receipt generated, Metal Issue appears in Metal Issue List
4. Navigate to **Tag History Report** (`admin_ret_reports/tag_history`)
5. Search for the tag code `GBT-01272`
6. Observe the **Status** column in the movement history table

### Expected Behavior

The Status column should display **"Metal Issue"** for the metal issue transaction entry.

### Actual Behavior

The Status column displays **"-"** (dash/empty) — the metal issue status is not mapped in the tag history/movement report query.

### Evidence

- Screenshot 1: Tag History Report showing Status = "-" for GBT-01272
- Screenshot 2: Metal Issue receipt (REF NO: 25-00497) confirming successful issue of GBT-01272

### Root Cause Hypothesis

The Tag History / Tag Movement report query in `ret_reports_model.php` uses a CASE/IF statement to map `tag_status` values to display labels. The value `17` (Metal Issue, per TAG_STATUS_MAP.md) is likely **missing from that CASE statement**, causing it to fall through to the default (empty/dash).

### Cross-Module Risk

- `tag_status=17` is set by Metal Process module (see TAG_STATUS_MAP.md)
- Tag History is read by Ret_Reports module
- This is a **read-only report bug** — no data corruption risk, but wrong display to users
# RPT-CLT04 — Chit Closing Details: Column Misalignment, Missing Columns & JS Concatenation Bugs

| Field         | Value                                    |
| ------------- | ---------------------------------------- |
| Severity      | P2                                       |
| Track         | A (System)                               |
| Category      | UI/Display + JavaScript                  |
| Sprint        | Sprint 2                                 |
| Pattern Match | NEW — PAT-JS-006 (Unary Plus in String Concatenation) |
| Module Brain  | ✅ Ready                                 |
| Reporter      | Internal (Developer)                     |
| Source        | Internal                                 |

### Steps to Reproduce

1. Navigate to `/admin_ret_reports/chit_closing_details/list`
2. Select branch and date range
3. Click Search
4. Observe the table columns and their alignment

### Expected Behavior

- Columns should appear in order: Bill Date, Bill No, Customer, Mobile, Scheme Name, Acc No, **Closing Ref No**, Paid Installments, Chit Amount, **Bonus**, **Rate Benefits**, **Apx MC/VA Discount**, Additional Benefits, Deductions, **Total**
- Financial columns (Chit Amount through Total) should be right-aligned
- Non-financial columns should be left-aligned
- Grand Total row should show totals for all financial columns
- All rows should have the correct number of cells (15 columns)

### Actual Behavior

1. **Missing columns**: Bonus, Rate Benefits, Apx MC/VA Discount, Total were not displayed
2. **Wrong column position**: Closing Ref No was at the end instead of after Acc No
3. **Column naming**: "Benefits" was ambiguous (renamed to "Rate Benefits")
4. **JS concatenation bug**: `'<tr...>' + +'<td></td>'` — double `+` caused unary-plus on string, producing `NaN` and losing a `<td>` cell (DataTables error: "Requested unknown parameter '14' for row 21")
5. **Missing string concatenation**: `'</tr>';` was a standalone expression instead of `+ '</tr>';` — closing `</tr>` tags not appended to HTML
6. **Alignment**: Financial columns were not right-aligned; only columns 7-9 were targeted instead of all financial columns
7. **Totals**: Grand Total row only totaled 3 columns instead of all 7 financial columns

### Evidence

- DataTables warning: `table id=chit_closing_list - Requested unknown parameter '14' for row 21`
- Screenshot provided by user showing the error dialog

### Root Cause

1. **View** (`chit_closing.php`): Table headers didn't include Bonus, Rate Benefits, Apx MC/VA Discount, Total columns
2. **JS** (`ret_reports.js`): `set_chit_closing_table()` function had:
   - Missing columns in row rendering
   - Unary-plus bug: `+ +'<td>'` evaluates as string concat + unary-plus = `NaN`
   - Missing `+` concatenation operator for `</tr>` closing tags
   - Incorrect DataTable columnDefs alignment targets

### Fix Applied

- **View** (`chit_closing.php`): Added 4 new column headers, moved Closing Ref No position
- **JS** (`ret_reports.js`): 
  - Added all 7 financial column data cells per row
  - Fixed `+ +'<td>'` → `+ '<td>'` (removed unary-plus)
  - Fixed `'</tr>';` → `+ '</tr>';` (added concatenation)
  - Updated columnDefs: targets [0-7] left-aligned, [8-14] right-aligned
  - Added Grand Total for all 7 financial columns
  - Total formula: `Chit Amount + Bonus + Rate Benefits + Apx MC/VA Discount + Additional Benefits - Deductions`
