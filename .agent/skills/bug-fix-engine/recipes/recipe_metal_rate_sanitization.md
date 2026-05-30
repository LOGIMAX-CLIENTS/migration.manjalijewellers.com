# Recipe: Metal Rate Update Sanitization (Empty Decimals & Missing ID)

## Metadata
- **Pattern ID**: PAT-DB-1366-METAL
- **Severity**: HIGH
- **Modules Affected**: Today Rates (admin_settings)
- **Auto-fixable**: Yes

## Client Scope
- **Applies to**: ALL
- **Reason**: Common issue in older CI3 modules where decimal fields are not sanitized before DB insert.

## Created By
- **Developer**: Antigravity AI
- **Client**: LOGIMAX-CLIENTS/erp.lakshmanaacharison.in
- **Date**: 2026-04-22
- **Source Bug ID**: Conversation 877c7375

## Symptom
When updating "Today Rates" in Admin Settings, the system throws a database error:
`Error Number: 1366. Incorrect decimal value: '' for column 'gold_18' at row 1`
OR
`Error Number: 1364. Field 'id_metalrate' doesn't have a default value.`

## Root Cause
1. **Empty Strings**: The form sends empty strings for fields that the user doesn't fill (e.g., Diamond Rate, Purchase Rate). The database expects a decimal value. PHP's `''` is not valid for MySQL `DECIMAL` columns when strict mode is on.
2. **Missing Primary Key**: In some versions, the `ret_daily_rate_updates` table requires `id_metalrate` to be provided manually if it's not set to AUTO_INCREMENT.

## Detection
```bash
grep -n "metal_rates_update" admin/application/controllers/admin_settings.php
```

## Files
- `admin/application/controllers/admin_settings.php`
- `admin/application/models/admin_settings_model.php`

## Fix

### Controller Fix (admin_settings.php)
Sanitize inputs to ensure they are numeric.

#### Before
```php
$data_daily = array(
    'gold_24' => $this->input->post('gold_24'),
    'gold_22' => $this->input->post('gold_22'),
    // ... other fields
);
```

#### After
```php
$data_daily = array(
    'gold_24' => (float)$this->input->post('gold_24'),
    'gold_22' => (float)$this->input->post('gold_22'),
    'gold_18' => (float)$this->input->post('gold_18'),
    'silver' => (float)$this->input->post('silver'),
    'diamond' => (float)$this->input->post('diamond'),
    'platinum' => (float)$this->input->post('platinum'),
    'board_rate' => (float)$this->input->post('board_rate'),
    'exchange_rate' => (float)$this->input->post('exchange_rate'),
    'purchase_rate' => (float)$this->input->post('purchase_rate'),
    'is_branchwise_rate' => (int)$this->input->post('is_branchwise_rate'),
    'id_metalrate' => (int)$this->input->post('id_metalrate'), // Ensure ID is passed
    'updated_at' => date('Y-m-d H:i:s')
);
```

### Model Fix (admin_settings_model.php)
Handle empty data states and parameter passing.

#### Before
```php
public function metal_rates_update($data) {
    $this->db->insert('ret_daily_rate_updates', $data);
}
```

#### After
```php
public function metal_rates_update($data_daily = array()) {
    if(!empty($data_daily)){
        // Ensure table exists and has data or handle insert
        $this->db->insert('ret_daily_rate_updates', $data_daily);
        return $this->db->insert_id();
    }
}
```

## Verification
1. Go to Admin → Today Rates
2. Leave some fields empty (e.g., Diamond, Platinum)
3. Save the rates
4. Verify success message and check DB for numeric 0.00 in empty fields.

## Notes
- Always cast to `(float)` or `(double)` for currency/weight fields in CI3 to avoid 1366 errors.
- If using `is_branchwise_rate`, ensure it defaults to 0 if not checked.
