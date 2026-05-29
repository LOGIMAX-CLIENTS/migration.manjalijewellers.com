# Making Charges & Value Addition Edit Access Control

**Feature ID:** MC-VA-LOCK-001  
**Date:** 2026-01-13  
**Version:** v0.0.1  
**Status:** Implemented  

---

## 📋 Overview

This feature introduces **field-level access control** for Making Charges (MC) and Value Addition (VA) fields in the tagging module. It prevents unauthorized or accidental modifications to wastage and making charge calculations once they have been set in the system.

---

## 🎯 Business Problem

### Current Issue
- Users can modify wastage percentages, wastage values, MC types, and MC values even after they have been calculated and saved
- This leads to:
  - **Data inconsistency** in pricing calculations
  - **Unauthorized modifications** to financial data
  - **Audit trail issues** when values are changed without proper authorization
  - **Pricing errors** in jewelry items

### Impact
- **Financial Risk:** Incorrect pricing due to modified wastage/MC values
- **Compliance Risk:** Lack of control over who can modify critical pricing fields
- **Operational Risk:** Accidental changes by users without proper permissions

---

## ✅ Solution

### Two New Settings

We've introduced **two separate settings** to control edit access for selling and purchasing workflows:

| Setting Name | Description | Default Value |
|-------------|-------------|---------------|
| `sell_mc_va_edit_access` | Controls edit access for wastage & MC during **SELLING** operations | `0` (Disabled) |
| `pur_mc_va_edit_access` | Controls edit access for wastage & MC during **PURCHASE** operations | `0` (Disabled) |

**Values:**
- `0` = **Disabled** (Fields are locked when values exist)
- `1` = **Enabled** (Fields remain editable)

---

## 🔧 Technical Implementation

### 1. Database Changes

**Table:** `ret_settings`

```sql
-- Selling MC/VA Edit Access
INSERT INTO `ret_settings` 
(`id_ret_settings`, `name`, `value`, `description`, `created_on`, `created_by`, `updated_on`, `updated_by`) 
VALUES 
(NULL, 'sell_mc_va_edit_access', '0', "0 -> disabled, 1 -> enabled", NOW(), 1, NULL, NULL);

-- Purchase MC/VA Edit Access
INSERT INTO `ret_settings` 
(`id_ret_settings`, `name`, `value`, `description`, `created_on`, `created_by`, `updated_on`, `updated_by`) 
VALUES 
(NULL, 'pur_mc_va_edit_access', '0', "0 -> disabled, 1 -> enabled", NOW(), 1, NULL, NULL);
```

### 2. Backend Changes

**File:** `admin/application/models/ret_tag_model.php`

**Function:** `get_empty_record()`

```php
// Retrieve settings
$sell_mc_va_edit_access = $this->get_ret_settings('sell_mc_va_edit_access');
$pur_mc_va_edit_access = $this->get_ret_settings('pur_mc_va_edit_access');

// Add to empty data array
$emptydata['sell_mc_va_edit_access'] = $sell_mc_va_edit_access;
$emptydata['pur_mc_va_edit_access'] = $pur_mc_va_edit_access;
```

### 3. Frontend Changes

**File:** `admin/application/views/tagging/form.php`

```php
<!-- Hidden inputs to pass settings to JavaScript -->
<input type="hidden" id="sell_mc_va_edit_access" value="<?php echo $sell_mc_va_edit_access; ?>">
<input type="hidden" id="pur_mc_va_edit_access" value="<?php echo $pur_mc_va_edit_access; ?>">
```

### 4. JavaScript Logic

**File:** `admin/assets/js/ret_tagging.js`

#### A. Selling Wastage & MC Control

**Function:** `set_tagging_wastage_and_mc()`

```javascript
if(items.wastag_method == 1) {
    sell_mc_va_edit_access = parseFloat($("#sell_mc_va_edit_access").val());
    
    // Lock fields if wastage value exists AND access is disabled
    if (items.wastag_value != null && items.wastag_value !== "" && sell_mc_va_edit_access == 0) {
        $('#tag_wast_perc').prop('readonly', true);
        $('#tag_wast_value').prop('readonly', true);
        $('#tag_id_mc_type').prop('disabled', true);
        $('#tag_mc_value').prop('readonly', true);
    }
}
```

**Locked Fields (Selling):**
- `tag_wast_perc` - Wastage Percentage
- `tag_wast_value` - Wastage Value
- `tag_id_mc_type` - Making Charge Type (dropdown)
- `tag_mc_value` - Making Charge Value

#### B. Purchase Wastage & MC Control

**Function:** `calc_pur_wastage()`

```javascript
pur_mc_va_edit_access = parseFloat($("#pur_mc_va_edit_access").val());

// Lock fields if purchase VA exists AND access is disabled
if ((pur_va !== '' && pur_va !== null && pur_va !== 0) && pur_mc_va_edit_access == 0) {
    $('#tag_pur_wast_perc').prop('readonly', true);
    $('#tag_pur_wast_value').prop('readonly', true);
    $('#tag_pur_id_mc_type').prop('disabled', true);
    $('#tag_pur_mc_value').prop('readonly', true);
}
```

**Locked Fields (Purchase):**
- `tag_pur_wast_perc` - Purchase Wastage Percentage
- `tag_pur_wast_value` - Purchase Wastage Value
- `tag_pur_id_mc_type` - Purchase Making Charge Type (dropdown)
- `tag_pur_mc_value` - Purchase Making Charge Value

---

## 🔄 Workflow

### Selling Workflow

```
┌─────────────────────────────────────────────────────────────┐
│ User opens Tagging Form                                     │
└────────────────────┬────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────┐
│ JavaScript checks: sell_mc_va_edit_access setting           │
└────────────────────┬────────────────────────────────────────┘
                     │
        ┌────────────┴────────────┐
        │                         │
        ▼                         ▼
┌───────────────┐         ┌───────────────┐
│ Setting = 1   │         │ Setting = 0   │
│ (Enabled)     │         │ (Disabled)    │
└───────┬───────┘         └───────┬───────┘
        │                         │
        ▼                         ▼
┌───────────────┐         ┌───────────────────────────┐
│ Fields remain │         │ Check if wastage_value    │
│ EDITABLE      │         │ exists                    │
└───────────────┘         └───────┬───────────────────┘
                                  │
                     ┌────────────┴────────────┐
                     │                         │
                     ▼                         ▼
              ┌─────────────┐         ┌─────────────┐
              │ Value exists│         │ No value    │
              └──────┬──────┘         └──────┬──────┘
                     │                       │
                     ▼                       ▼
              ┌─────────────┐         ┌─────────────┐
              │ Fields      │         │ Fields      │
              │ LOCKED      │         │ LOCKED      │
              └─────────────┘         └─────────────┘
```

### Purchase Workflow

```
┌─────────────────────────────────────────────────────────────┐
│ User calculates Purchase Wastage                            │
└────────────────────┬────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────┐
│ JavaScript checks: pur_mc_va_edit_access setting            │
└────────────────────┬────────────────────────────────────────┘
                     │
        ┌────────────┴────────────┐
        │                         │
        ▼                         ▼
┌───────────────┐         ┌───────────────┐
│ Setting = 1   │         │ Setting = 0   │
│ (Enabled)     │         │ (Disabled)    │
└───────┬───────┘         └───────┬───────┘
        │                         │
        ▼                         ▼
┌───────────────┐         ┌───────────────────────────┐
│ Fields remain │         │ Check if pur_va exists    │
│ EDITABLE      │         │ (not null, not empty,     │
└───────────────┘         │  not zero)                │
                          └───────┬───────────────────┘
                                  │
                     ┌────────────┴────────────┐
                     │                         │
                     ▼                         ▼
              ┌─────────────┐         ┌─────────────┐
              │ pur_va      │         │ No pur_va   │
              │ exists      │         │             │
              └──────┬──────┘         └──────┬──────┘
                     │                       │
                     ▼                       ▼
              ┌─────────────┐         ┌─────────────┐
              │ Fields      │         │ Fields      │
              │ LOCKED      │         │ LOCKED      │
              └─────────────┘         └─────────────┘
```

---

## 📊 Use Cases

### Use Case 1: Prevent Accidental Edits
**Scenario:** A user accidentally clicks on wastage percentage field  
**Before:** Value could be changed, causing pricing errors  
**After:** Field is locked (readonly), preventing accidental changes  

### Use Case 2: Audit Compliance
**Scenario:** Management needs to ensure pricing calculations are not modified  
**Before:** No control over field editing  
**After:** Settings can be disabled (0) to lock all fields once values are set  

### Use Case 3: Flexible Access for Authorized Users
**Scenario:** Senior staff needs to correct a genuine error  
**Before:** No way to allow selective editing  
**After:** Admin can enable setting (1) temporarily to allow edits  

### Use Case 4: Separate Selling vs Purchase Controls
**Scenario:** Different access rules for selling and purchasing teams  
**Before:** Single control for both workflows  
**After:** Independent settings for selling (`sell_mc_va_edit_access`) and purchase (`pur_mc_va_edit_access`)  

---

## 🧪 Testing Checklist

### Selling Workflow Tests

- [ ] **Test 1:** Set `sell_mc_va_edit_access = 0`, create new tag with wastage
  - **Expected:** Fields should be locked after wastage value is set
  
- [ ] **Test 2:** Set `sell_mc_va_edit_access = 1`, create new tag with wastage
  - **Expected:** Fields should remain editable
  
- [ ] **Test 3:** Set `sell_mc_va_edit_access = 0`, open existing tag with wastage
  - **Expected:** Fields should be locked (readonly)
  
- [ ] **Test 4:** Set `sell_mc_va_edit_access = 0`, open tag without wastage
  - **Expected:** Fields should be editable

### Purchase Workflow Tests

- [ ] **Test 5:** Set `pur_mc_va_edit_access = 0`, calculate purchase wastage with VA
  - **Expected:** Fields should be locked after pur_va is set
  
- [ ] **Test 6:** Set `pur_mc_va_edit_access = 1`, calculate purchase wastage with VA
  - **Expected:** Fields should remain editable
  
- [ ] **Test 7:** Set `pur_mc_va_edit_access = 0`, open existing tag with pur_va
  - **Expected:** Fields should be locked
  
- [ ] **Test 8:** Set `pur_mc_va_edit_access = 0`, calculate without pur_va
  - **Expected:** Fields should be editable

### Edge Cases

- [ ] **Test 9:** Toggle setting from 0 to 1 while form is open
  - **Expected:** Refresh form to see changes
  
- [ ] **Test 10:** Null/empty wastage values
  - **Expected:** Fields should remain editable regardless of setting

---

## 🔐 Security Considerations

1. **Setting Access:** Only administrators should have access to modify `ret_settings` table
2. **Client-Side Validation:** JavaScript makes fields readonly, but backend validation should also be implemented
3. **Audit Logging:** Consider logging when these settings are changed
4. **Role-Based Access:** Future enhancement could tie this to user roles instead of global settings

---

## 📈 Future Enhancements

1. **Role-Based Control:** Instead of global settings, allow per-role or per-user access
2. **Audit Trail:** Log all changes to wastage and MC fields with user and timestamp
3. **Approval Workflow:** Require manager approval for editing locked fields
4. **Field-Level Permissions:** More granular control (e.g., allow wastage edit but lock MC)
5. **Notification System:** Alert managers when locked fields are unlocked

---

## 📝 Configuration Guide

### For Administrators

**To Enable Editing (Allow users to modify fields):**
```sql
UPDATE ret_settings SET value = '1' WHERE name = 'sell_mc_va_edit_access';
UPDATE ret_settings SET value = '1' WHERE name = 'pur_mc_va_edit_access';
```

**To Disable Editing (Lock fields when values exist):**
```sql
UPDATE ret_settings SET value = '0' WHERE name = 'sell_mc_va_edit_access';
UPDATE ret_settings SET value = '0' WHERE name = 'pur_mc_va_edit_access';
```

**To Check Current Settings:**
```sql
SELECT name, value, description FROM ret_settings 
WHERE name IN ('sell_mc_va_edit_access', 'pur_mc_va_edit_access');
```

---

## 🐛 Troubleshooting

### Issue: Fields are not getting locked
**Solution:** 
1. Check if settings exist in database
2. Verify hidden input fields are present in form
3. Check browser console for JavaScript errors
4. Ensure wastage/VA values actually exist (not null/empty)

### Issue: Fields remain locked even when setting is enabled
**Solution:**
1. Clear browser cache
2. Refresh the page
3. Verify setting value is exactly `'1'` (string)

### Issue: Different behavior in selling vs purchase
**Solution:**
- This is expected! Two separate settings control two separate workflows
- Verify you're checking the correct setting for the workflow

---

## 📞 Support

For questions or issues related to this feature, contact:
- **Development Team:** [Your Team Contact]
- **Documentation:** This file
- **Related Files:**
  - `admin/application/models/ret_tag_model.php`
  - `admin/application/views/tagging/form.php`
  - `admin/assets/js/ret_tagging.js`

---

## 📜 Change Log

| Date | Version | Changes | Author |
|------|---------|---------|--------|
| 2026-01-13 | v0.0.1 | Initial implementation of MC/VA edit access control | Development Team |

---

**Document Version:** 1.0  
**Last Updated:** 2026-01-13  
**Next Review:** 2026-02-13
