# Branch Transfer — Deep Analysis Round 11: View Files

> **Date**: 2026-03-11
> **Focus**: View files — form.php (783 lines), list.php (226 lines)
> **View coverage**: 100% of active views read

---

## Bugs Found: 5

| Bug ID | Severity | Title | Lines | Track |
|---|---|---|---|---|
| BRN-D27 | **P2** | XSS: Flashdata echoed without `htmlspecialchars()` in 2 views | form L58-61, list L80-86 | A |
| BRN-D28 | **P2** | Duplicate `id="id_product"` — jQuery selector collision | form L267 + L288 | A |
| BRN-D29 | **P2** | Repair Orders radio (type5) bypasses profile permission check | form L93 | B |
| BRN-D30 | **P2** | "Trasnfer" typo in list.php breadcrumb + heading | list L21, L41 | A |
| BRN-D31 | **P2** | "Cancell" typo in cancel modal title + body | list L188, L194 | A |

---

### BRN-D27 — XSS: Flashdata Echo Without Escaping [P2]
```php
// form.php L58-61:
<div class="alert alert-<?php echo $message['class']; ?>">
    <h4><i class="icon fa fa-check"></i> <?php echo $message['title']; ?>!</h4>
    <?php echo $message['message']; ?>
</div>

// list.php L80-86 (same pattern, plus echoes 'icon'):
<i class="<?php echo $message['icon']; ?>"></i> <?php echo $message['title']; ?>
```
**Root Cause**: `flashdata` values from `set_flashdata()` are echoed directly into HTML. While flashdata is set server-side (reducing direct XSS risk), if any error response contains user-controlled data (e.g., BRN-D22 leaks `last_query()` which could contain injected SQL with script tags), this becomes exploitable.
**Fix**: Wrap all echoes with `htmlspecialchars($message['class'], ENT_QUOTES, 'UTF-8')`.

### BRN-D28 — Duplicate `id="id_product"` [P2]
```html
<!-- form.php L267 (non-tagged section): -->
<input type="hidden" class="form-control" id="id_product">

<!-- form.php L288 (tagged section): -->
<input type="hidden" class="form-control" id="id_product">
```
**Root Cause**: Both the non-tagged and tagged sections have a hidden input with `id="id_product"`. HTML IDs must be unique. `$('#id_product')` in JS will always select the FIRST one, so non-tagged product selection will work but tagged product will not update the hidden field correctly (or vice versa).
**Impact**: May cause wrong product ID to be sent to server in the transfer request. Depends on which section is visible and JS execution order.

### BRN-D29 — Repair Orders Radio Bypasses Permission [P2]
```php
// form.php L77-93:
<?php if($profile['tag_transfer']==1){?>     // Gated ✓
    <input type="radio" name="transfer_item_type" id="type1" value="1" checked>
<?php }?>
<?php if($profile['non_tag_transfer']==1){?>  // Gated ✓
    <input type="radio" name="transfer_item_type" id="type2" value="2">
<?php }?>
<?php if($profile['purchase_item_transfer']==1){?>  // Gated ✓
    <input type="radio" name="transfer_item_type" id="type3" value="3">
<?php }?>
<?php if($profile['packaging_item_transfer']==1){?>  // Gated ✓
    <input type="radio" name="transfer_item_type" id="type4" value="4">
<?php }?>
// NO PERMISSION CHECK:
<input type="radio" name="transfer_item_type" id="type5" value="5">  // Always visible ✗
```
**Business Rule**: Every transfer type except Repair Orders has a profile-based permission check. Repair Orders is always visible regardless of user profile.
**Impact**: Users without repair order permission can still see and select the Repair Orders transfer type.

### BRN-D30 — "Trasnfer" Typo in List View [P2]
```html
<!-- list.php L21: -->
<li><a href="#">Branch Trasnfer List</a></li>
<!-- list.php L41: -->
<h3 class="box-title">Branch Trasnfer List </h3>
```
Same typo as BRN-D26 (controller cancel logs). Consistent misspelling in multiple places.

### BRN-D31 — "Cancell" Typo in Cancel Modal [P2]
```html
<!-- list.php L188: -->
<h4 class="modal-title" id="myModalLabel">Cancell Bill</h4>
<!-- list.php L194: -->
<strong>Are you sure! You want to Cancell this bill?</strong>
```
User-facing typo. Also, the modal says "bill" but this is a Branch Transfer cancel, not a Billing cancel — appears to be copy-pasted from the Billing module.
