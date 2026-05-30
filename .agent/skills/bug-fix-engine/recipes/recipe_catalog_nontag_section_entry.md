# Section-Wise Non-Tagged Stock Entry Mismatch

## Metadata
- **Pattern ID**: PAT-QRY-007
- **Severity**: HIGH
- **Modules Affected**: Catalog / Physical Stock Entry
- **Auto-fixable**: No

## Client Scope
- **Applies to**: ALL
- **Reason**: Static catalog-master grouping instead of physical stock-based section grouping is a design flaw that affects all clients using section-wise physical stock audits.

## Created By
- **Developer**: Antigravity
- **Client**: Logimax
- **Date**: 2026-05-18
- **Source Bug ID**: CAT-S01

## Symptom
Active non-tagged product items (like GOLD HOOK) with balances physically residing in dynamic storage sections do not display in the physical product stock entry page under those sections. They only appear in their static product master master-category section (or don't appear at all if no product master has that section assigned as a master category). This makes it impossible for users to perform section-based physical stock entries or audits.

## Root Cause
The retrieval query `getProductBySection_NonTag` in `ret_catalog_model.php` grouped and filtered non-tagged products based on the static catalog master category section (`p.id_section` in `ret_product_master`). It completely ignored the dynamic physical location (`ni.id_section` in `ret_nontag_item`) where the non-tagged stock actually resides and is audited.

Additionally, branch-wide aggregate queries were being run for single physical sections, causing stock counts to overlap or leak across different dynamic sections.

## Detection
Identify where `getProductBySection_NonTag` or similar physical stock loader joins the category section:
```php
$this->db->join('ret_section s', 's.sec_id = p.id_section', 'left'); // Vulnerable join on catalog master section p.id_section
```

## Files
- `admin/application/models/ret_catalog_model.php`

## Fix

### Before
```php
	public function getProductBySection_NonTag($id_section,$id_branch,$id_employee)
	{
		$today=date('Y-m-d');
		$entered_wt_sub = "(SELECT SUM(esp.pcs) FROM ret_employee_stock_product esp WHERE esp.id_product = p.pro_id AND esp.id_employee = '".$id_employee."' AND esp.date = '".$today."')";
		$entered_is_updated = "(SELECT count(esp.id_employee_stock_product) FROM ret_employee_stock_product esp WHERE esp.id_product = p.pro_id AND esp.id_employee = '".$id_employee."' AND esp.date = '".$today."')";
		$entered_updated_on = "(SELECT esp.updated_on FROM ret_employee_stock_product esp WHERE esp.id_product = p.pro_id AND esp.id_employee = '".$id_employee."' AND esp.date = '".$today."' LIMIT 1)";
		$this->db->select("p.pro_id,p.product_name,p.product_code,s.sec_name,s.sec_id,IFNULL(ni.bal_qty,0) as bal_qty,IFNULL(ni.bal_qty,0) as qty,
			IFNULL($entered_wt_sub, 0) AS entered_qty,
			IFNULL($entered_is_updated, 0) AS is_updated,
			IFNULL($entered_updated_on, '') AS updated_on
			",false);
		$this->db->from('ret_product_master p');
		$this->db->join('ret_section s', 's.sec_id = p.id_section', 'left');
		$this->db->join('ret_nontag_item ni', 'p.pro_id = ni.product', 'left');
		$this->db->where('p.item_type',2);
		$this->db->where('p.pro_status',1);
		$this->db->where('s.sec_id',$id_section);
		$this->db->where('ni.branch',$id_branch);
		$this->db->group_by('p.pro_id');
		$query = $this->db->get();
		return $query->result_array();
	}
```

### After
```php
	public function getProductBySection_NonTag($id_section,$id_branch,$id_employee)
	{
		$today=date('Y-m-d');
		$entered_wt_sub = "(SELECT SUM(esp.pcs) FROM ret_employee_stock_product esp WHERE esp.id_product = p.pro_id AND esp.id_section = ni.id_section AND esp.id_employee = '".$id_employee."' AND esp.date = '".$today."')";
		$entered_is_updated = "(SELECT count(esp.id_employee_stock_product) FROM ret_employee_stock_product esp WHERE esp.id_product = p.pro_id AND esp.id_section = ni.id_section AND esp.id_employee = '".$id_employee."' AND esp.date = '".$today."')";
		$entered_updated_on = "(SELECT esp.updated_on FROM ret_employee_stock_product esp WHERE esp.id_product = p.pro_id AND esp.id_section = ni.id_section AND esp.id_employee = '".$id_employee."' AND esp.date = '".$today."' LIMIT 1)";
		$this->db->select("p.pro_id,p.product_name,p.product_code,s.sec_name,s.sec_id,IFNULL(SUM(ni.bal_qty),0) as bal_qty,IFNULL(SUM(ni.bal_qty),0) as qty,
			IFNULL($entered_wt_sub, 0) AS entered_qty,
			IFNULL($entered_is_updated, 0) AS is_updated,
			IFNULL($entered_updated_on, '') AS updated_on
			",false);
		$this->db->from('ret_nontag_item ni');
		$this->db->join('ret_product_master p', 'p.pro_id = ni.product', 'left');
		$this->db->join('ret_section s', 's.sec_id = ni.id_section', 'left');
		$this->db->where('p.item_type',2);
		$this->db->where('p.pro_status',1);
		$this->db->where('ni.id_section',$id_section);
		$this->db->where('ni.branch',$id_branch);
		$this->db->group_by('p.pro_id, ni.id_section');
		$query = $this->db->get();
		return $query->result_array();
	}
```

## Verification
1. Navigate to physical stock entry page.
2. Select a branch and audit section that has non-tagged items stored dynamically in sections other than their catalog-default section.
3. Confirm that the items are displayed in the section's grid with correct local stock balances.
4. Input and save a count in one section, select a different section containing the same product code, and verify that the count in the second section is distinct and does not bleed or conflict.

## Notes
Dynamic location audits must ALWAYS drive physical stock entry forms based on current dynamic balances (e.g., `ret_nontag_item` or `ret_tag`), rather than static catalog categorization tables (`ret_product_master.id_section`).
