# Recipe: Metal Pocket Weight and Rate Fix

## Metadata
- **Pattern ID**: PAT-DAT-004
- **Severity**: HIGH
- **Modules Affected**: Retagging, Metal Process, Retail Old Metal Pocket
- **Auto-fixable**: No

## Client Scope
- **Applies to**: ALL
- **Reason**: Standard Codeigniter retail ERP sites that save `ret_old_metal_pocket` headers without client-side total calculations or that suffer from MySQL Error 1366 constraint violations due to `'undefined'` string values for `rate_per_gram`.

## Created By
- **Developer**: Antigravity
- **Client**: SR Jewellery (erp.srjewellery.in)
- **Date**: 2026-05-28
- **Source Bug ID**: N/A

## Symptom
1. Metal pocket records show zero values for total pieces (`piece`), gross weight (`gross_wt`), net weight (`net_wt`), or amount in the pocket list page, despite having populated records in the child/details table (`ret_old_metal_pocket_details`).
2. Saving pocket entries fails with a database constraint error: MySQL Error 1366: "Incorrect decimal value: 'undefined' for column 'rate_per_gram' at row 1".

## Root Cause
1. The frontend pocket-saving/adding workflows did not calculate totals (total pieces, total weight, average purity) and populate the corresponding hidden form inputs. As a result, the form POST data contained empty values (`total_pcs`, `total_gross_wt`, etc.), which the Codeigniter controllers inserted as zeroes in the header table (`ret_old_metal_pocket`).
2. The frontend JavaScript extracted `.rate_per_grm` values for detail items, but when these input elements did not exist (e.g. in some workflow views), they evaluated to `undefined`, which got serialized as a literal string `'undefined'` and sent in the POST request. PHP did not sanitize this value, causing MySQL decimal conversion/constraint errors on insertion.

## Detection
```command
grep -rn "pocket_no.*code_number_generator" admin/application/controllers/
```

## Files
- `admin/application/controllers/admin_ret_metal_process.php`
- `admin/application/controllers/admin_ret_tagging.php`
- `admin/application/models/ret_metal_process_model.php`
- `admin/assets/js/ret_tagging.js`

## Fix

### PHP Total Calculations
#### Before
```php
				$insData=array(
					'date' 		 => $branchDetails['entry_date'],
					'id_branch'   => $addData['id_branch'],
					'trans_type' => $addData['trans_type'],
                    'is_against_opening' => ($addData['trans_type']==1 ? $addData['is_against_opening'] :0),
					'pocket_no'  => $pocket_no,
					'piece'	    =>$addData['total_pcs'],
					'gross_wt'	=>$addData['total_gross_wt'],
					'net_wt'	=>$addData['total_net_wt'],
                    'dia_wt'    =>$addData['total_dia_wt'],
					'avg_purity'=>$addData['avg_purity_per'],
					'total_purity'=>$addData['total_item_purity'],
					'amount'    =>$addData['total_amount'],
					'created_by'=>$this->session->userdata('uid'),
					'created_on' => date("Y-m-d H:i:s"),
				);
```

#### After
```php
				// Compute totals from detail items
				$total_pcs=0;
				$total_gwt=0;
				$total_nwt=0;
				$total_diawt=0;
				$total_purity_sum=0;
				$total_amt=0;
				if(!empty($addData['req_data']))
				{
					foreach($addData['req_data'] as $item)
					{
						$item_pcs = floatval(isset($item['piece']) ? $item['piece'] : 0);
						$total_pcs += $item_pcs;
						$total_gwt += floatval(isset($item['gross_wt']) ? $item['gross_wt'] : 0);
						$total_nwt += floatval(isset($item['net_wt']) ? $item['net_wt'] : (isset($item['net_wgt']) ? $item['net_wgt'] : 0));
						$total_diawt += floatval(isset($item['diawt']) ? $item['diawt'] : (isset($item['dia_wgt']) ? $item['dia_wgt'] : 0));
						$total_amt += floatval(isset($item['item_cost']) ? $item['item_cost'] : 0);
						$item_purity = floatval(isset($item['touch']) ? $item['touch'] : (isset($item['purity_per']) ? $item['purity_per'] : 0));
						$total_purity_sum += ($item_purity * $item_pcs);
					}
				}

				$insData=array(
					'date' 		 => $branchDetails['entry_date'],
					'id_branch'   => $addData['id_branch'],
					'trans_type' => $addData['trans_type'],
                    'is_against_opening' => ($addData['trans_type']==1 ? $addData['is_against_opening'] :0),
					'pocket_no'  => $pocket_no,
					'piece'	    => $total_pcs,
					'gross_wt'	=> number_format($total_gwt,3,'.',''),
					'net_wt'	=> number_format($total_nwt,3,'.',''),
                    'dia_wt'    => number_format($total_diawt,3,'.',''),
					'avg_purity'=> ($total_pcs>0) ? number_format($total_purity_sum/$total_pcs,3,'.','') : 0,
					'total_purity'=> number_format($total_purity_sum,3,'.',''),
					'amount'    => number_format($total_amt,2,'.',''),
					'created_by'=>$this->session->userdata('uid'),
					'created_on' => date("Y-m-d H:i:s"),
				);
```

### Rate Sanitization Fix
#### Before
```php
					'rate_per_gram'     =>$val['rate_per_gram'],
```
#### After
```php
					'rate_per_gram'     =>(isset($val['rate_per_gram']) && $val['rate_per_gram'] !== 'undefined' && $val['rate_per_gram'] !== '' ? $val['rate_per_gram'] : 0),
```

### JavaScript Sanitization Fix
#### Before
```javascript
							'rate_per_gram'        : $(value).find('.rate_per_grm').val(),
```
#### After
```javascript
							'rate_per_gram'        : ($(value).find('.rate_per_grm').val() || 0),
```

## Verification
1. Create a new pocket entry from Retagging (Tag Process 6 - Add to Pocket).
2. Verify that the entry is successfully saved without MySQL Error 1366 decimal constraint errors.
3. Open the Old Metal Pocket list page and verify that the `piece`, `gross_wt`, and `net_wt` columns display non-zero values corresponding exactly to the sum of the saved items.

## Notes
- Calculating totals on the server-side acts as a robust, fail-safe layer that completely bypasses fragile client-side form synchronization issues.
- Always cast numeric inputs in CI models/controllers before committing them to decimal or float database columns.
