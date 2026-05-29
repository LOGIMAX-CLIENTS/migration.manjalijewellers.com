# Ret_Reports — Data Flow

> **Module**: Ret_Reports
> **Last Updated**: 2026-03-26 — Round 8 (header sync)

---

## Overview

Ret_Reports is a **read-only reporting module**. It does NOT follow the standard CREATE/EDIT/DELETE flows. Instead, its primary flows are:

1. **REPORT VIEW** — Load an HTML page with filters → User selects filters → AJAX fetch data → DataTable render
2. **EXPORT** — Generate PDF/Excel from report data
3. **GREEN TAG UPDATE** — The only write operation in the module

---

## Flow 1: Report View (Standard Pattern — ~220 methods)

### Trigger
User navigates to a report page URL, e.g., `/admin_ret_reports/stock_age/list`

### Step-by-Step Trace

```
1. Browser → GET /admin_ret_reports/{report_name}/list
2. Controller → {report_name}($type="list")
   - switch case 'list':
   - Sets $data['main_content'] = 'ret_reports/{view_file}'
   - Loads layout/template view
3. Browser ← Full HTML page with:
   - Date range picker
   - Branch dropdown
   - Product/category/design filters
   - Empty DataTable container
   
4. User selects filters → clicks "Show" / "Search"
5. JS (ret_reports.js) →
   - Collects form values: from_date, to_date, id_branch, etc.
   - $.ajax({ url: base_url + 'admin_ret_reports/{report_name}/ajax', type: 'POST', data: {...} })

6. Controller → {report_name}($type="ajax")
   - switch case 'ajax':
   - $list = $this->$model->get{ReportData}($_POST);
   - $access = $this->admin_settings_model->get_access('admin_ret_reports/{report_name}/list');
   - echo json_encode(['list' => $list, 'access' => $access]);

7. Model → get{ReportData}($data)
   - Builds SELECT query from $data filters
   - Executes against ret_taging / ret_billing / etc.
   - Returns result array

8. Browser ← JSON { list: [...], access: {...} }
9. JS → DataTable.fnClearTable() + fnAddData(list)
   - Renders table rows
   - Shows/hides action buttons based on access
```

### Key Variables
| Variable | Where | Purpose |
|---|---|---|
| `$_POST['from_date']` | Controller AJAX case | Report start date |
| `$_POST['to_date']` | Controller AJAX case | Report end date |
| `$_POST['id_branch']` | Controller AJAX case | Branch filter (0 = all) |
| `$_POST['id_product']` | Controller AJAX case | Product filter |
| `$_POST['id_design']` | Controller AJAX case | Design filter |
| `access` | JSON response | Determines which action buttons show |

---

## Flow 2: PDF Export (Cash Abstract)

### Trigger
User clicks PDF export button for Cash Abstract report.

### Step-by-Step Trace
```
1. Browser → GET /admin_ret_reports/generate_cash_abstract/{id_branch}/{from_date}/{to_date}

2. Controller → generate_cash_abstract($id_branch, $from_date, $to_date) [L406-449]
   - $data['billing'] = $model->getBillDetails($from_date, $to_date, $id_branch)
   - $data['category'] = $model->getallCategory()
   - $data['comp_details'] = $model->getCompanyDetails($id_branch)
   - $this->load->helper(['dompdf', 'file'])
   - new DOMPDF()
   - Load view: ret_reports/print/cash_abstract → HTML string
   - $dompdf->load_html($html)
   - $dompdf->set_paper("a4", "portriat")  ← TYPO: "portriat" instead of "portrait"
   - $dompdf->render()
   - $dompdf->stream("Receipt.pdf", ['Attachment' => 0])  → inline display
```

### ⚠️ Known Issue
- `set_paper("a4", "portriat")` — typo in orientation. DOMPDF may silently ignore this.

---

## Flow 3: Excel Export (Cash Abstract CSV)

### Trigger
User clicks Excel export button.

### Step-by-Step Trace
```
1. Browser → GET /admin_ret_reports/export_csv/{id_branch}/{from_date}/{to_date}

2. Controller → export_csv() [L453-514]
   - Same data fetch as PDF flow
   - Loads view ret_reports/print/export → HTML string
   - Creates temp HTML file on disk: time() . '.html'
   - $this->load->library('Excel')
   - PHPExcel_Reader_HTML reads temp file
   - PHPExcel_IOFactory::createWriter → Excel2007
   - Streams as .xls download
   - Deletes temp file with unlink()
```

### ⚠️ Known Issues
- **Race condition**: Temp file name is `time() . '.html'` — concurrent exports could collide (same second)
- **PHP deprecation**: Uses PHPExcel (abandoned 2017). Should migrate to PhpSpreadsheet.
- **Extension mismatch**: Outputs as Excel2007 format but names file `.xls` (should be `.xlsx`)

---

## Flow 4: Green Tag Update (Only Write Operation)

### Trigger
User marks/unmarks tags as "Green Tag" from the green tag report.

### Step-by-Step Trace
```
1. Browser → JS collects array of { tag_id, req_status } pairs
2. POST /admin_ret_reports/update_green_tag
   - Controller → update_green_tag() [L183-240]

3. $this->db->trans_begin()
4. foreach ($reqdata as $tag):
   - Build update array:
     - tag_mark = req_status
     - green_tag_date = now (if marking) / NULL (if unmarking)
     - green_tag_marked_by = user ID (if marking) / NULL (if unmarking)
     - unmark_by, unmark_date = set if unmarking
   - $model->updateData($data, 'tag_id', $tag['tag_id'], 'ret_taging')

5. if (trans_status === TRUE):
   - Log event via log_model->log_detail()
   - trans_commit()
   - Flash success message
6. else:
   - trans_rollback()
   - Flash error message
```

### Tables Written
| Table | Operation | Fields Changed |
|---|---|---|
| `ret_taging` | UPDATE | `tag_mark`, `green_tag_date`, `green_tag_marked_by`, `unmark_by`, `unmark_date` |

---

## Flow 5: File Tag Import (Old Tags)

### Trigger
User uploads an Excel file with old tag data for import.

### Step-by-Step Trace
```
1. Browser → POST /admin_ret_reports/file_upload_old_tags
   - Controller → file_upload_old_tags() [L5071-5329]
   
2. Reads uploaded Excel file row by row
3. For each row:
   - Validates old_tag_code against DB
   - Checks if tag exists in ret_old_tag_import
   - If new: INSERT into ret_old_tag_import
   - If existing: UPDATE
4. Returns summary of imported/skipped/failed rows
```

> ⚠️ This is ~260 lines of complex import logic with inline SQL — high risk area.

---

## Flow 6: HO Daily Stock Book (Complex Aggregation)

### Trigger
User requests Head Office Daily Stock Book report.

### Step-by-Step Trace
```
1. POST /admin_ret_reports/ho_daily_stock_book/ajax

2. Controller → ho_daily_stock_book() [L8686-9087] — 400 lines!
3. Calls 11 different model methods:
   - ret_purchase_order_model->get_headoffice_valut_report()
   - ret_purchase_order_model->get_retagging_details()
   - model->get_section_wise_stock_inout_details()
   - model->get_SectionTag_InwardOutward_Details() × 2 (inward + outward)
   - model->get_nontag_section_details()
   - model->get_SectionNonTag_InwardOutward_Details() × 2
   - model->getLotwiseTaggedVault()
   - model->get_PurchaseReturnItems()
   - model->get_MetalIssueItems()

4. Aggregates data in controller:
   - HO vault: opening + inward + closing (grswt, nwt, diawt)
   - BT vault: opening + inward + pocket + closing
   - Section tag: opening + inward + outward + closing (5 weight types)
   - Section non-tag: opening + inward + outward + closing
   - Purchase return: grswt, nwt, diawt
   - Metal issue: grswt, nwt, diawt
   - Profit/loss: calculated from lot vs tag vs receipt vs lot-merge

5. Returns comprehensive stock book JSON
```

### ⚠️ Known Issue
- **Business logic in controller**: This 400-line aggregation belongs in the model. The controller is doing data processing that should be separated.
- **Closing formula** (L8848-8852): `closing = opening + inward - sold - branch_out - section_out - return - issue` — any missing component silently zeros out.

---

## JS Function Map (Key Functions)

> The JS file is ~2.73 MB with DataTable configurations for each report.

### Common Pattern
```javascript
function load_{report_name}() {
    $.ajax({
        url: base_url + 'admin_ret_reports/{report_name}/ajax',
        type: 'POST',
        data: { /* filter params */ },
        success: function(data) {
            var response = JSON.parse(data);
            // Clear and populate DataTable
            oTable.fnClearTable();
            $.each(response.list, function(i, row) {
                oTable.fnAddData([/* column values */]);
            });
        }
    });
}
```

### Export Functions
```javascript
function export_to_excel() {
    // Redirects to controller export method
    window.location = base_url + 'admin_ret_reports/export_csv/' + branch + '/' + from + '/' + to;
}
```
