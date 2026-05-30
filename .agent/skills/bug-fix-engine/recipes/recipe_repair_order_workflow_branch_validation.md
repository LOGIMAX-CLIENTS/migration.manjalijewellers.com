# Repair Order Workflow — Missing Branch Validation & Visibility

## Metadata
- **Pattern ID**: PAT-WKF-001
- **Severity**: CRITICAL
- **Modules Affected**: Order (admin_ret_order), Billing (admin_ret_billing), Order Model (ret_order_model)
- **Auto-fixable**: No (multiple coordinated changes across 3 files)

## Client Scope
- **Applies to**: ALL
- **Reason**: Core repair workflow logic — any client using repair orders with branch transfers is affected

## Created By
- **Developer**: Antigravity
- **Client**: nprthangamaligai.in
- **Date**: 2026-04-24
- **Source Bug ID**: N/A

## Symptom
1. After transferring a repair order to Head Office, karigar allotment page at HO shows no orders — cannot assign karigars
2. After repair completion and manual transfer back, delivery can be done from HO or any branch instead of only the original creation branch
3. Karigar can be assigned from any branch even when the item isn't physically at that branch

## Root Cause

**Three separate but related issues:**

1. **Neworder list query filters by `order_from` instead of `current_branch`**: `get_new_orderlist()` uses `o.order_from` (the branch where the order was created, never changes) instead of `od.current_branch` (updated by branch transfer). HO never sees transferred items.

2. **No branch validation on delivery**: The billing save handler sets `orderstatus=5` (delivered) without checking if the billing branch matches the original creation branch (`order_from`). Any branch can deliver.

3. **No branch ownership check on karigar assignment**: `assign_customer_order()` sets `orderstatus=3` (WIP) without verifying the order's `current_branch` matches the logged-in user's branch.

## Detection
```command
grep -rn "o\.order_from=.\$branch" admin/application/models/ret_order_model.php
grep -rn "orderstatus.*=>.*5" admin/application/controllers/admin_ret_billing.php
grep -rn "orderstatus.*=>.*3.*Work in Progress" admin/application/controllers/admin_ret_order.php
```

## Files
- `admin/application/models/ret_order_model.php` — `get_new_orderlist()`
- `admin/application/controllers/admin_ret_order.php` — `assign_customer_order()`
- `admin/application/controllers/admin_ret_billing.php` — `billing/save` (repair delivery section + order delivery section)

## Fix

### Fix 1: Neworder List — Branch Filter (ret_order_model.php)

#### Before
```php
where  o.order_type=3 and od.orderstatus=0 and (date(o.order_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."') ".($branchWiseLogin==1&&$branch!='' && $branch!=0?  " and o.order_from=".$branch."" :'')."
```

#### After
```php
where  o.order_type=3 and od.orderstatus=0 and (date(o.order_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."') ".($branchWiseLogin==1&&$branch!='' && $branch!=0?  " and od.current_branch=".$branch."" :'')."
```

---

### Fix 2: Karigar Assignment — Branch Validation (admin_ret_order.php)

#### Before
```php
    if($req_status==1)
    {
		foreach ($req_data as $data) 
		{
			$assigned = TRUE;
			$customer_order_details=$this->$model->get_customerorder_details($data['id_orderdetails']);
		    $smith_due_dt=date_create($data['smith_due_dt']);
```

#### After
```php
    if($req_status==1)
    {
		$user_branch = $this->session->userdata('id_branch');
		foreach ($req_data as $data) 
		{
			$assigned = TRUE;
			$customer_order_details=$this->$model->get_customerorder_details($data['id_orderdetails']);
			
			// Validate: item must be at user's branch for karigar assignment
			if($user_branch > 0 && isset($customer_order_details['current_branch']) && $customer_order_details['current_branch'] != $user_branch) {
				$response_data=array('status'=>false,'msg'=>'Order is not at your branch. Cannot assign karigar.');
				echo json_encode($response_data);
				return;
			}
			
		    $smith_due_dt=date_create($data['smith_due_dt']);
```

---

### Fix 3: Order Delivery — Branch Check (admin_ret_billing.php, ~line 1237)

#### Before
```php
if($id_orderdetails!='')
{
	$this->$model->updateData(array('orderstatus'=>5,'delivered_date'=>date("Y-m-d H:i:s")),'id_orderdetails',$billSale['id_orderdetails'][$key], 'customerorderdetails');
}
```

#### After
```php
if($id_orderdetails!='')
{
	// Validate: repair order delivery only at creation branch
	$chkOrdDet = $this->db->query("SELECT od.id_customerorder, o.order_from, o.order_type FROM customerorderdetails od LEFT JOIN customerorder o ON o.id_customerorder = od.id_customerorder WHERE od.id_orderdetails = ".$id_orderdetails)->row();
	if($chkOrdDet && $chkOrdDet->order_type == 3 && $chkOrdDet->order_from != $addData['id_branch']) {
		$this->db->trans_rollback();
		$return_data=array('status'=>FALSE,'msg'=>'Repair order delivery allowed only at creation branch');
		echo json_encode($return_data);
		return;
	}
	$this->$model->updateData(array('orderstatus'=>5,'delivered_date'=>date("Y-m-d H:i:s")),'id_orderdetails',$billSale['id_orderdetails'][$key], 'customerorderdetails');
}
```

---

### Fix 4: Repair Orders Section — Branch Check (admin_ret_billing.php, ~line 1475)

#### Before
```php
if(!empty($repair_orders))
{
	$repairOrderArray=array();
	
	//Update Ref No
	$ref_no=$this->$model->generateRefNo(...)
```

#### After
```php
if(!empty($repair_orders))
{
	$repairOrderArray=array();
	
	// Validate: repair delivery only at creation branch
	if(isset($repair_orders['id_orderdetails'])) {
		foreach($repair_orders['id_orderdetails'] as $rKey => $rVal) {
			if($rVal != '') {
				$chkRepOrd = $this->db->query("SELECT od.id_customerorder, o.order_from FROM customerorderdetails od LEFT JOIN customerorder o ON o.id_customerorder = od.id_customerorder WHERE od.id_orderdetails = ".$rVal)->row();
				if($chkRepOrd && $chkRepOrd->order_from != $addData['id_branch']) {
					$this->db->trans_rollback();
					$return_data=array('status'=>FALSE,'msg'=>'Repair order delivery allowed only at creation branch');
					echo json_encode($return_data);
					return;
				}
			}
		}
	}
	
	//Update Ref No
	$ref_no=$this->$model->generateRefNo(...)
```

## Verification
1. Create repair order at Branch X → verify `order_from = X`, `current_branch = X`
2. Transfer to HO via branch transfer → verify `current_branch` updated to HO
3. Login at HO → open New Orders list → should see the transferred order
4. Assign karigar at HO → should succeed
5. Try assigning karigar from Branch Y → should be blocked with error message
6. Complete repair at HO → `orderstatus = 4`
7. Manual transfer back to Branch X via branch transfer
8. Try delivery at HO billing → should be blocked: "Repair order delivery allowed only at creation branch"
9. Delivery at Branch X billing → should succeed, `orderstatus = 5`

## Notes
- Karigars are common across all branches — no karigar filtering needed
- Return transfer after repair is done manually via branch transfer module (not automated)
- `order_from` on `customerorder` table never changes — it's the permanent creation branch
- `current_branch` on `customerorderdetails` tracks where the item physically is — updated by branch transfers
- The `order_type = 3` check in Fix 3 ensures only repair orders are restricted; regular customer orders are unaffected
