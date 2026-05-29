# Edge Cases & Error Handling

> **Error Scenarios, Validation Failures, and Recovery Actions**  
> Reference for debugging and understanding failure modes

---

## 📋 Quick Reference

| Category             | # of Scenarios |
| -------------------- | -------------- |
| Pre-validation       | 5              |
| Tag Errors           | 6              |
| Rate Validation      | 4              |
| Calculation Errors   | 3              |
| Transaction Failures | 3              |
| Duplicate Prevention | 2              |

---

## 🚫 Pre-Validation Errors

### E1: Branch Not Selected

| Trigger                    | Check                         | Error Message   |
| -------------------------- | ----------------------------- | --------------- |
| Form submit without branch | `$('#id_branch').val() == ''` | "Select Branch" |

**Recovery**: User must select branch from dropdown.

---

### E2: Employee Not Selected

| Trigger                   | Check                           | Error Message                       |
| ------------------------- | ------------------------------- | ----------------------------------- |
| Add item without employee | `$('#id_employee').val() == ''` | "Please Select Branch and Employee" |

**Recovery**: User must select employee from dropdown.

---

### E3: Customer Not Selected (for Chit)

| Trigger                   | Check                      | Error Message                |
| ------------------------- | -------------------------- | ---------------------------- |
| Add chit without customer | `$('#cus_id').val() == ''` | "Please Select The Customer" |

**Recovery**: Search and select customer first.

---

### E4: No Items Added

| Trigger                | Check                 | Error Message            |
| ---------------------- | --------------------- | ------------------------ |
| Save with empty tables | All item tables empty | "Item Details Not Found" |

**Recovery**: Add at least one item (tag/catalog/custom).

---

### E5: Day Closing Missing

| Trigger                  | Check                  | Error Message                                      |
| ------------------------ | ---------------------- | -------------------------------------------------- |
| Save without day closing | `sizeof($dCData) == 0` | "Kindly update Day closing data to add estimation" |

**Recovery**: Complete day closing for the branch first.

```php
// Controller check
$dCData = $this->admin_settings_model->getBranchDayClosingData($branch_id);
if (sizeof($dCData) == 0) {
    // Block save
}
```

---

## 🏷️ Tag Validation Errors

### E6: Tag Already Sold

| Trigger       | Check             | Error Message      |
| ------------- | ----------------- | ------------------ |
| Scan sold tag | `tag_status == 1` | "Tag already sold" |

**Recovery**: Tag cannot be added. Inform customer.

---

### E7: Tag Not Found

| Trigger            | Check        | Error Message   |
| ------------------ | ------------ | --------------- |
| Invalid tag number | No DB record | "Tag not found" |

**Recovery**: Verify tag number, check if exists in system.

---

### E8: Tag Already in Estimation

| Trigger        | Check                      | Error Message                     |
| -------------- | -------------------------- | --------------------------------- |
| Duplicate scan | Tag exists in current form | "Tag already added to estimation" |

**Recovery**: None needed, tag already in list.

```javascript
// JS check in getTagDetails callback
if (existingTags.includes(tag_id)) {
  $.toaster({ priority: "danger", message: "Tag already added" });
  return;
}
```

---

### E9: Tag From Different Branch

| Trigger         | Check                              | Error Message                     |
| --------------- | ---------------------------------- | --------------------------------- |
| Branch mismatch | `tag.id_branch != selected_branch` | "Tag belongs to different branch" |

**Recovery**: Change branch or use correct tag.

---

### E10: Tag Reserved/Held

| Trigger                   | Check             | Error Message                          |
| ------------------------- | ----------------- | -------------------------------------- |
| Tag in another estimation | Reserved flag set | "Tag is reserved for another customer" |

**Recovery**: Release from other estimation first.

---

### E11: Tag GST Info Missing

| Trigger          | Check             | Error Message                                 |
| ---------------- | ----------------- | --------------------------------------------- |
| Save without tax | `tax_price` empty | "GST information are wrong to add estimation" |

**Recovery**: Ensure tax group is assigned and calculated.

```php
if (!empty($estTag['tag_id'][$key]) && empty($estTag['tax_price'][$key])) {
    $allow_submit = FALSE;
}
```

---

## 💰 Rate Validation Errors

### E12: Gold Rate Out of Range

| Trigger                | Check                      | Error Message           |
| ---------------------- | -------------------------- | ----------------------- |
| Old metal rate invalid | `rate < min OR rate > max` | "Enter valid Gold rate" |

**Limits from settings**:

- `min_old_gold_rate`
- `max_old_gold_rate`

```javascript
if (id_metal == 1) {
  if (rate < min_gold || rate > max_gold) {
    $.toaster({ priority: "danger", message: "Enter valid Gold rate" });
    curRow.find(".old_rate").val("");
  }
}
```

---

### E13: Silver Rate Out of Range

| Trigger                | Check                      | Error Message             |
| ---------------------- | -------------------------- | ------------------------- |
| Old metal rate invalid | `rate < min OR rate > max` | "Enter valid Silver rate" |

**Limits from settings**:

- `min_old_silver_rate`
- `max_old_silver_rate`

---

### E14: Purity Out of Range

| Trigger        | Check                         | Error Message                   |
| -------------- | ----------------------------- | ------------------------------- |
| Invalid purity | `purity > 100 OR purity < 10` | "Please enter The Valid Purity" |

**Recovery**: Enter purity between 10-100%.

---

### E15: Wastage Over 100%

| Trigger         | Check           | Error Message                           |
| --------------- | --------------- | --------------------------------------- |
| Invalid wastage | `wastage > 100` | "Entered Wastage % must be within 100%" |

**Recovery**: Enter valid wastage percentage.

---

## 📊 Calculation Errors

### E16: Negative Total

| Trigger              | Result                |
| -------------------- | --------------------- |
| Old metal > purchase | Negative `total_cost` |

**Behavior**: System allows this (customer gets money back).

---

### E17: Chit Exceeds Balance

| Trigger           | Check                      | Error Message            |
| ----------------- | -------------------------- | ------------------------ |
| Over-utilize chit | `amount > closing_balance` | "Amount exceeds balance" |

**Recovery**: Enter amount ≤ available balance.

---

### E18: Division by Zero

| Trigger        | Risk Area                 |
| -------------- | ------------------------- |
| Net weight = 0 | Rate per gram calculation |

**Protection**: Check for zero before division.

---

## 🔄 Transaction Failures

### E19: Database Error

| Trigger                 | Handler            |
| ----------------------- | ------------------ |
| Any INSERT/UPDATE fails | `trans_rollback()` |

```php
if ($this->db->trans_status() === TRUE) {
    $this->db->trans_commit();
} else {
    $this->db->trans_rollback();
    // Flash error message
}
```

**Message**: "Unable to proceed the requested process"

---

### E20: Already Billed

| Trigger                | Check             | Error Message |
| ---------------------- | ----------------- | ------------- |
| Edit billed estimation | `estbillid != ''` | Cannot edit   |

**Protection in controller**:

```php
if ($estimation['estbillid'] == '') {
    // Allow edit
}
```

---

### E21: Concurrent Modification

| Risk                              | Protection          |
| --------------------------------- | ------------------- |
| Two users editing same estimation | `form_secret` token |

**Behavior**: Second save may fail or overwrite.

---

## 🔐 Duplicate Prevention

### E22: Double Submit

| Protection          | Mechanism                  |
| ------------------- | -------------------------- |
| `form_secret` field | Unique token per form load |

```php
$form_secret = isset($addData["form_secret"]) ? $addData["form_secret"] : '';

if ($this->session->userdata('FORM_SECRET')) {
    if (strcasecmp($form_secret, $this->session->userdata('FORM_SECRET')) === 0) {
        // Allow - first valid submit
    }
}
```

---

### E23: Browser Back Button

| Risk           | User resubmits after back    |
| -------------- | ---------------------------- |
| **Protection** | `form_secret` should prevent |

---

## 🔍 Error Message Locations

| Layer      | Method            | Example                                            |
| ---------- | ----------------- | -------------------------------------------------- |
| JavaScript | `$.toaster()`     | `$.toaster({priority: 'danger', message: '...'})`  |
| JavaScript | `alert()`         | `alert("Select Branch")`                           |
| PHP        | `set_flashdata()` | `$this->session->set_flashdata('chit_alert', ...)` |

---

## ✅ Document Complete

- [x] Pre-validation errors (5)
- [x] Tag errors (6)
- [x] Rate validation errors (4)
- [x] Calculation errors (3)
- [x] Transaction failures (3)
- [x] Duplicate prevention (2)
