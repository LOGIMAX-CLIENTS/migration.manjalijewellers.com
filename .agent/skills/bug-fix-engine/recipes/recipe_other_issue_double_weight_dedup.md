# Other Issue Report Double Weight — Dedup Fix

> Tags in multiple `other_issue` transfers cause doubled weight/piece in the Other Issue report.

## Metadata
- **Pattern ID**: OI-RPT-001
- **Severity**: HIGH
- **Modules Affected**: Reports (Other Issue), Branch Transfer (Save)
- **Auto-fixable**: Partial (query fix is auto-fixable; save guard needs manual verification of POST key name)

## Client Scope
- **Applies to**: ALL
- **Reason**: Core query pattern — any client using Other Issue transfers can hit this

## Created By
- **Developer**: Antigravity AI
- **Client**: RTM (source)
- **Date**: 2026-06-18
- **Source Bug ID**: N/A

## Symptom
In the Other Issue report (`admin_ret_reports/other_issue/list`), specific tags show **exactly double** their actual gross_wt, net_wt, and piece count. For example, a tag with 47.890g shows as 95.780g.

## Root Cause

### Data Layer
The save function in `admin_ret_brntransfer.php` has no idempotency check. When a tag is added to an Other Issue transfer, it inserts into `ret_brch_transfer_tag_items` without checking if the tag already exists in another active `other_issue` transfer. This creates duplicate rows across transfers for the same tag.

### Query Layer
The report query in `ret_reports_model.php` (`getOtherIssueList`) joins `ret_branch_transfer → ret_brch_transfer_tag_items → ret_taging` directly. When a tag has N transfer rows, the JOIN produces N rows per tag, and `SUM(t.gross_wt)` multiplies the weight by N.

### UX Layer
When searching for a tag already transferred, the `getTagsByFilter` query filters by `tag_status=0` (available). Tags with `tag_status=3` (other issue) return no results, but the toaster shows generic "No Record Found" instead of a specific reason.

## Detection

### Detect the query vulnerability (report model)
```command
grep -n "FROM.*ret_branch_transfer.*bt" admin/application/models/ret_reports_model.php | grep -i "other_issue\|getOtherIssueList"
```

### Detect missing save guard (controller)
```command
grep -n "insertData.*ret_brch_transfer_tag_items" admin/application/controllers/admin_ret_brntransfer.php
```
Then check if there's a `SELECT COUNT` or duplicate check before the insert.

### Detect affected data (SQL)
```sql
SELECT bti.tag_id, COUNT(*) as transfer_count 
FROM ret_brch_transfer_tag_items bti
INNER JOIN ret_branch_transfer bt ON bt.branch_transfer_id = bti.transfer_id
WHERE bt.is_other_issue = 1 AND bt.status != 3
GROUP BY bti.tag_id
HAVING COUNT(*) > 1;
```

## Files
- `admin/application/models/ret_reports_model.php` — Report query
- `admin/application/controllers/admin_ret_brntransfer.php` — Save logic + tag search
- `admin/assets/js/ret_branch_transfer.js` — Frontend toaster

## Fix

### Fix 1: Report Query Dedup

#### Before
```php
$sql_tg = $this->db->query("SELECT t.tag_code as tag_id,
    ...
    IFNULL(sum(t.gross_wt),0) as gross_wt,
    IFNULL(sum(t.net_wt),0) as net_wt,
    IFNULL(sum(t.piece),0) as piece,
    ...
    FROM `ret_branch_transfer` bt
    Left join ret_brch_transfer_tag_items bti on bti.transfer_id = bt.branch_transfer_id
    Left join ret_taging t on t.tag_id = bti.tag_id
    ...
    WHERE is_other_issue=1 AND t.tag_status = 3 and transfer_item_type =1
    AND (date(bt.created_time) BETWEEN ...)
    ...
    GROUP BY bti.tag_id
");
```

#### After
```php
$sql_tg = $this->db->query("SELECT t.tag_code as tag_id,
    ...
    IFNULL(sum(t.gross_wt),0) as gross_wt,
    IFNULL(sum(t.net_wt),0) as net_wt,
    IFNULL(sum(t.piece),0) as piece,
    ...
    FROM (SELECT bti_inner.tag_id, MAX(bt_inner.branch_transfer_id) as branch_transfer_id
        FROM ret_branch_transfer bt_inner
        INNER JOIN ret_brch_transfer_tag_items bti_inner ON bti_inner.transfer_id = bt_inner.branch_transfer_id
        WHERE bt_inner.is_other_issue=1 AND bt_inner.transfer_item_type=1
        AND (date(bt_inner.created_time) BETWEEN ...)
        GROUP BY bti_inner.tag_id
    ) as dedup
    INNER JOIN ret_branch_transfer bt ON bt.branch_transfer_id = dedup.branch_transfer_id
    INNER JOIN ret_brch_transfer_tag_items bti ON bti.tag_id = dedup.tag_id AND bti.transfer_id = dedup.branch_transfer_id
    Left join ret_taging t on t.tag_id = dedup.tag_id
    ...
    WHERE t.tag_status = 3
    ...
    GROUP BY dedup.tag_id
");
```

### Fix 2: Save Prevention Guard

#### Before
```php
if ($tag_data['tag_id'] > 0) {
    $tagDet = $this->$model->get_tag_details($tag_data['tag_id']);
    $total_tag_pcs += $tag_data['piece'];
    $items = array(
        'transfer_id' => $branch_transfer_id,
        'tag_id'      => $tag_data['tag_id'],
        ...
    );
    $status = $this->$model->insertData($items, 'ret_brch_transfer_tag_items');
```

#### After
```php
if ($tag_data['tag_id'] > 0) {
    $tagDet = $this->$model->get_tag_details($tag_data['tag_id']);
    // Prevent duplicate: skip if tag already in an active other_issue transfer
    if (isset($_POST['isOtherIssue']) && $_POST['isOtherIssue'] == 1) {
        $dup_check = $this->db->query("SELECT COUNT(*) as cnt FROM ret_brch_transfer_tag_items bti
            INNER JOIN ret_branch_transfer bt ON bt.branch_transfer_id = bti.transfer_id
            WHERE bti.tag_id = ? AND bt.is_other_issue = 1 AND bt.status != 3",
            array($tag_data['tag_id']))->row()->cnt;
        if ($dup_check > 0) {
            $failed++;
            continue;
        }
    }
    $total_tag_pcs += $tag_data['piece'];
    $items = array(
        'transfer_id' => $branch_transfer_id,
        'tag_id'      => $tag_data['tag_id'],
        ...
    );
    $status = $this->$model->insertData($items, 'ret_brch_transfer_tag_items');
```

### Fix 3: Specific Toaster Messages

#### Controller — Before
```php
case 'getTagsByFilter':
    $data = $this->$model->fetchTagsByFilter($_POST);
    echo json_encode($data);
    break;
```

#### Controller — After
```php
case 'getTagsByFilter':
    $data = $this->$model->fetchTagsByFilter($_POST);
    if (empty($data) && isset($_POST['tag_no']) && $_POST['tag_no'] != '') {
        $tag_check = $this->db->query("SELECT t.tag_status,
            CASE t.tag_status
                WHEN 3 THEN 'Tag is already issued via Other Issue'
                WHEN 4 THEN 'Tag is currently In-Transit'
                WHEN 2 THEN 'Tag is Sold'
                WHEN 1 THEN 'Tag is Inactive'
                ELSE 'Tag not available for transfer'
            END as reason
            FROM ret_taging t WHERE t.tag_code = ?", array($_POST['tag_no']));
        if ($tag_check->num_rows() > 0) {
            $row = $tag_check->row();
            if ($row->tag_status != 0) {
                echo json_encode(array('error' => true, 'message' => $row->reason));
                break;
            }
        }
    }
    echo json_encode($data);
    break;
```

#### JS — Add error check at start of success handler (before `data.length` check)
```javascript
// Check for server-side error message (e.g., tag already in other issue)
if (data != null && data.error === true) {
    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+data.message});
    $('#tag_no').val('');
    $("#tag_no").focus();
    $(".overlay").css("display", "none");
    return;
}
```

## Verification

1. Open Other Issue report → search for a known duplicate tag → weight should now be single (not doubled)
2. Open Other Issue report → Group by Metal → totals should match sum of individual tags
3. Open Other Issue report → Group by Section → totals should match sum of individual tags
4. Open BT Add → check Other Issue → search for an already-issued tag → should show "Tag is already issued via Other Issue"
5. Open BT Add → check Other Issue → search for a valid available tag → should load normally
6. Run detection query — results show tags with duplicate transfers (existing data, harmless now since query deduplicates)

## Notes
- The dedup subquery uses `MAX(branch_transfer_id)` to pick the latest transfer per tag. This means the report will show metadata (from_branch, to_branch, remark, employee) from the latest transfer.
- The diamond weight subquery was also cleaned up — it was joining to `ret_brch_transfer_tag_items` and `ret_branch_transfer` unnecessarily, which could also cause doubling for diamond weights.
- 310+ tags have historical duplicate data. The query fix handles all of them without needing individual data cleanup. Targeted cleanup was done only for the 2 user-reported tags.
- The save prevention guard only checks for `isOtherIssue == 1` transfers, so normal branch transfers are completely unaffected.
