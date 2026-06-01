# Recipe: Birthday/Wedding Day Report Branch Filtering & MySQL 1525 Fix

## Metadata
- **Pattern ID**: PAT-RPT-012
- **Severity**: HIGH
- **Modules Affected**: Reports (Customer Celebration Wishes)
- **Auto-fixable**: No

## Client Scope
- **Applies to**: ALL
- **Reason**: All CodeIgniter-based retail projects that query customer birthday/wedding dates under MySQL 5.7+ / 8.0+ strict mode configurations suffer from MySQL 1525 string date comparison crashes.

## Created By
- **Developer**: Antigravity
- **Client**: navratnajewellery
- **Date**: 2026-05-27
- **Source Bug ID**: PR 130

## Symptom
1. **MySQL Crash (Error 1525)**: Querying the Birthday & Wedding Day report causes a strict-mode SQL crash because the code executes raw string comparisons on invalid zero-dates: `and (c.date_of_birth != '0000-00-00' or c.date_of_wed != '0000-00-00')`.
2. **Missing Branch Isolation**: All customers are displayed globally, violating multi-branch access privileges where single-branch users are restricted to their location.

## Root Cause
1. **Strict Date Parsing**: Under MySQL strict mode (`NO_ZERO_IN_DATE` / `NO_ZERO_DATE`), comparing invalid dates like `'0000-00-00'` with comparison operators or passing them to formatting functions (`DATE_FORMAT`) triggers syntax/value runtime errors.
2. **Lack of DB Isolation in Query**: The `get_all_cus_celeb_dates` SQL query lacked a branch filter join and session role verification, allowing branch isolation bypass.

## Detection
Run the following commands to detect vulnerable celeb date comparisons:
```powershell
grep -rn "c.date_of_birth != '0000-00-00'" admin/application/models/
```

## Files
- `admin/application/models/admin_report_model.php`
- `admin/application/views/reports/celeb_days.php`
- `admin/assets/js/reports.js`

## Fix

### 1. Backend Model (`admin_report_model.php`)

#### Before
```php
function get_all_cus_celeb_dates($postData){
        $response = [];
	    $from_date = $postData['from_date']; 
	    $to_date = $postData['to_date'];
	    
	    $company_settings = $this->session->userdata('company_settings');
        $id_company = $this->session->userdata('id_company');
        
        $sql = $this->db->query("SELECT c.id_customer, c.firstname,c.mobile, IFNULL(ct.name,'') as city_name, 
        IFNULL(Date_format(c.date_of_birth, '%d-%m-%Y'),'') as birthday, 
        IFNULL(Date_format(c.date_of_wed, '%d-%m-%Y'),'') as wedday, 
        IFNULL((select count(sa.id_scheme_account) from scheme_account sa where sa.active = 1 and sa.is_closed = 0 and sa.id_customer = c.id_customer),0) as active_acc, 
        IFNULL((select count(sa.id_scheme_account) from scheme_account sa where sa.active = 0 and sa.is_closed = 1 and sa.id_customer = c.id_customer),0) as closed_acc 
        from customer c 
        left join address ad on ad.id_address = c.id_address 
        left join city ct on ct.id_city = ad.id_city 
        WHERE (c.date_of_birth is not null or c.date_of_wed is not null) 
        and (c.date_of_birth != '0000-00-00' or c.date_of_wed != '0000-00-00')
        
        and (
            (date_format(c.date_of_birth,'%m%d') BETWEEN date_format('".date('Y-m-d',strtotime($from_date))."','%m%d') AND date_format('".date('Y-m-d',strtotime($to_date))."','%m%d'))
        or (date_format(c.date_of_wed,'%m%d') BETWEEN date_format('".date('Y-m-d',strtotime($from_date))."','%m%d') AND date_format('".date('Y-m-d',strtotime($to_date))."','%m%d')))
        
        ".($id_company!='' &&  $company_settings == 1? " and c.id_company='".$id_company."'":'')."
       ");
```

#### After
```php
function get_all_cus_celeb_dates($postData){
        $response = [];
	    $from_date = $postData['from_date']; 
	    $to_date = $postData['to_date'];
	    
	    $company_settings = $this->session->userdata('company_settings');
        $id_company = $this->session->userdata('id_company');
        
        // Branch configurations
        $branchWiseLogin = $this->session->userdata('branchWiseLogin');
        $is_branchwise_cus_reg = $this->session->userdata('is_branchwise_cus_reg');
        $uid = $this->session->userdata('uid');
        $id_branch = $this->session->userdata('id_branch');
        $selected_branch = isset($postData['id_branch']) ? $postData['id_branch'] : '';
        
        $branch_where = "";
        if ($is_branchwise_cus_reg == 1) {
            if ($uid != 1 && $branchWiseLogin == 1 && $id_branch != '' && $id_branch > 0) {
                // Restrict branch-login users to their assigned branch
                $branch_where = " AND c.id_branch = " . $id_branch;
            } else {
                // Allow all-branch users to filter by selected branch
                if ($selected_branch != '' && $selected_branch > 0) {
                    $branch_where = " AND c.id_branch = " . $selected_branch;
                }
            }
        }
        
        $sql = $this->db->query("SELECT c.id_customer, c.firstname,c.mobile, IFNULL(ct.name,'') as city_name, 
        IFNULL(b.name,'') as branch_name,
        IFNULL(Date_format(c.date_of_birth, '%d-%m-%Y'),'') as birthday, 
        IFNULL(Date_format(c.date_of_wed, '%d-%m-%Y'),'') as wedday, 
        IFNULL((select count(sa.id_scheme_account) from scheme_account sa where sa.active = 1 and sa.is_closed = 0 and sa.id_customer = c.id_customer),0) as active_acc, 
        IFNULL((select count(sa.id_scheme_account) from scheme_account sa where sa.active = 0 and sa.is_closed = 1 and sa.id_customer = c.id_customer),0) as closed_acc 
        from customer c 
        left join address ad on ad.id_address = c.id_address 
        left join city ct on ct.id_city = ad.id_city 
        left join branch b on b.id_branch = c.id_branch
        WHERE (
            (c.date_of_birth IS NOT NULL AND c.date_of_birth >= '1000-01-01' AND date_format(c.date_of_birth,'%m%d') BETWEEN date_format('".date('Y-m-d',strtotime($from_date))."','%m%d') AND date_format('".date('Y-m-d',strtotime($to_date))."','%m%d'))
            OR 
            (c.date_of_wed IS NOT NULL AND c.date_of_wed >= '1000-01-01' AND date_format(c.date_of_wed,'%m%d') BETWEEN date_format('".date('Y-m-d',strtotime($from_date))."','%m%d') AND date_format('".date('Y-m-d',strtotime($to_date))."','%m%d'))
        )
        
        " . ($id_company != '' && $company_settings == 1 ? " and c.id_company='" . $id_company . "'" : '') . "
        " . $branch_where . "
       ");
```

### 2. View Layer (`celeb_days.php`)

#### Dropdown & Session Filters injection:
```php
<?php if ($this->session->userdata('branch_settings') == 1 && $this->session->userdata('is_branchwise_cus_reg') == 1) { ?>
    <?php if ($this->session->userdata('id_branch') == 0) { ?>
        <div class="col-md-2">
            <div class="form-group tagged">
                <label>Select Branch</label>
                <select id="branch_select" class="form-control branch_filter"></select>
            </div>
        </div>
    <?php } else { ?>
        <input type="hidden" id="branch_filter" value="<?php echo $this->session->userdata('id_branch'); ?>">
    <?php } ?>
<?php } else { ?>
    <input type="hidden" id="branch_filter" value="">
<?php } ?>

<input type="hidden" id="login_branch_name" value="<?php echo $this->session->userdata('branch_name'); ?>">
<input type="hidden" id="branchwise_cus_reg" value="<?php echo $this->session->userdata('is_branchwise_cus_reg'); ?>">
<input type="hidden" id="branch_set" value="<?php echo $this->session->userdata('branch_settings'); ?>">
```

#### Conditional Table Column Header:
```php
<thead>
  <tr>
    <th>S.No</th>
    <th>Customer</th> 
    <th>Mobile</th>
    <th>City</th>
    <?php if ($this->session->userdata('branch_settings') == 1 && $this->session->userdata('is_branchwise_cus_reg') == 1) { ?>
        <th>Branch</th>
    <?php } ?>
    <th>D.O.B</th>
    <th>D.O.W</th>
    <th>Active Account(s)</th>
    <th>Closed Account(s)</th>
  </tr>
</thead>
```

### 3. JavaScript Asset Layer (`reports.js`)

#### Dropdown Change Listener:
```javascript
// Trigger load when the branch dropdown changes
$(document).on("change", "#branch_select", function () {
  getCelebDates();
});
```

#### Dynamic Datatable & AJAX Redraw:
```javascript
function getCelebDates() {
  $("div.overlay").css("display", "block");
  var is_branch_active = $("#branch_set").val() == 1 && $("#branchwise_cus_reg").val() == 1;
  var id_branch = is_branch_active ? ($("#branch_select").val() != null ? $("#branch_select").val() : $("#branch_filter").val()) : "";
  var colspan_val = is_branch_active ? 9 : 8;
  var onHTML = "";
  onHTML +=
    '<tr><td colspan=' + colspan_val + ' style="color:red;font-weight:bold;text-align:center;">No Data Available</td></tr>';
  $("#celebration_list > tbody").html(onHTML);
  my_Date = new Date();
  $.ajax({
    url:
      base_url +
      "index.php/admin_reports/cus_celeb_dates?nocache=" +
      my_Date.getUTCSeconds(),
    data: {
      from_date: $("#celeb_date1").html(),
      to_date: $("#celeb_date2").html(),
      id_branch: id_branch,
    },
    dataType: "JSON",
    type: "POST",
    success: function (data) {
      var data = data.data;
      $("#celeb_report_date_range").text(
        $("#celeb_date1").html() + " to " + $("#celeb_date2").html()
      );
      var title = "";
      title += get_title(
        $("#celeb_date1").html(),
        $("#celeb_date2").html(),
        "Birthday/Wedding Day Report" + (is_branch_active ? " - " + getBranchTitle() : "")
      );
      $("div.overlay").css("display", "none");
      $("#celebration_list > tbody > tr").remove();
      $("#celebration_list").dataTable().fnClearTable();
      $("#celebration_list").dataTable().fnDestroy();
      trHTML = "";
      $.each(data, function (key, items) {
        trHTML +=
          "<tr>" +
          "<td>" +
          parseInt(key + 1) +
          "</td>" +
          "<td>" +
          items.firstname +
          "</td>" +
          "<td>" +
          items.mobile +
          "</td>" +
          "<td>" +
          items.city_name +
          "</td>" +
          (is_branch_active ? "<td>" + items.branch_name + "</td>" : "") +
          "<td>" +
          items.birthday +
          "</td>" +
          "<td>" +
          items.wedday +
          "</td>" +
          "<td>" +
          items.active_acc +
          "</td>" +
          "<td>" +
          items.closed_acc +
          "</td>" +
          "</tr>";
      });
      $("#celebration_list > tbody").html(trHTML);
      if (!$.fn.DataTable.isDataTable("#celebration_list")) {
        if (data.length > 0) {
          var right_targets = is_branch_active ? [0, 7, 8] : [0, 6, 7];
          var left_targets = is_branch_active ? [1, 2, 3, 4, 5, 6] : [1, 2, 3, 4, 5];
          oTable = $("#celebration_list").dataTable({
            bSort: false,
            bInfo: false,
            dom: "lBfrtip",
            pageLength: 25,
            lengthMenu: [
              [-1, 25, 50, 100, 250],
              ["All", 25, 50, 100, 250],
            ],
            buttons: [
              {
                extend: "print",
                footer: true,
                title: title,
                orientation: "landscape",
                exportOptions: { columns: ":visible" },
              },
              {
                extend: 'excel',
                footer: true,
                title: 'Birthday/Wedding Day Report' + (is_branch_active ? " - " + getBranchTitle() : "") + " " + $('#celeb_date1').html() + ' - ' + $('#celeb_date2').html(),
              },
              {
                extend: "colvis",
                collectionLayout: "fixed columns",
                collectionTitle: "Column visibility control",
              },
            ],
            columnDefs: [
              {
                targets: right_targets,
                className: "dt-right",
              },
              {
                targets: left_targets,
                className: "dt-left",
              },
            ],
          });
        } else {
          var onHTML = "";
          onHTML +=
            '<tr><td colspan=' + colspan_val + ' style="color:red;font-weight:bold;text-align:center;">No Data Available</td></tr>';
          $("#celebration_list > tbody").html(onHTML);
        }
      }
    },
    error: function (error) {
      $("div.overlay").css("display", "none");
    },
  });
}
```

---

## Verification
1. **Automated Testing Suite**:
   Run the dedicated PHPUnit test suite inside the test environment:
   ```bash
   vendor/bin/phpunit tests/CelebrationDatesTest.php
   ```
2. **Access Security Verification**:
   Navigate to `Reports` -> `Birthday/Wedding Day Report` under a single-branch restriction. Confirm that the Select2 Branch Selector is completely hidden, and only records matching the user's branch ID are retrieved.
3. **Multi-Store Management Filtering**:
   Log in under an All-Branch administrative profile. Select individual branches from the dropdown selector and confirm AJAX data triggers refresh the table and customize the printed headers correctly.
4. **Strict SQL Enforcement**:
   Verify zero database warning outputs or MySQL 1525 crash screens across boundary searches, Leap Days, and null dates.

