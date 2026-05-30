# Playbook: CSS & Print Layout Debugging

> For CSS issues, print/PDF layout bugs, and DomPDF quirks.
> Start at **Layer 1** (visual) — this is the only category where you look at the output FIRST.

---

## The Golden Rule

> **Find a WORKING print/layout first. Copy its CSS. Never invent from scratch.**

```
1. Identify a WORKING print view of the same type:
   - Fixing packing list print? → Find a working packing list (different bill type)
   - Fixing stock issue print? → Compare with working sales print

2. Open BOTH view files side by side:
   - Working: admin/application/views/{working_module}/print_view.php
   - Broken:  admin/application/views/{broken_module}/print_view.php

3. DIFF the CSS sections — the difference IS the fix

4. Copy exact CSS values: class names, widths, margins, image handling
```

---

## CSS Debugging Workflow

### Step 1: Isolate the Problem
```
F12 → Elements → select the broken element:

1. Computed tab → what styles are ACTUALLY applied?
2. Styles tab → which rules are winning?
   - Crossed-out = overridden by more specific rule
   - Orange warning = invalid property

3. Toggle individual CSS properties ON/OFF (checkbox)
   → Find which property causes the issue
   → Only change ONE property at a time
```

### Step 2: Specificity Check
```
Specificity order (most specific wins):
  inline style (style="...")     → 1000
  #id selector                   → 100
  .class selector                → 10
  tag selector (div, p, table)   → 1

Common problem in eTail:
  - Inline styles on elements override everything
  - Multiple CSS files with duplicate class names
  - !important used excessively → cascade broken

Debug:
  F12 → Elements → select element → Styles panel
  → Shows ALL matching rules in specificity order
  → The top rule wins (unless overridden by !important)
```

### Step 3: One Change at a Time
```
❌ DON'T: change width + margin + font-size + padding in one edit
✅ DO: change width → verify → change margin → verify → ...

For each change:
  1. Edit CSS
  2. Refresh page (Ctrl+Shift+R for hard refresh)
  3. Screenshot or visual check
  4. If correct → next change. If wrong → revert.
```

---

## Print CSS Debugging

### @media print
```css
/* Print-specific overrides — only apply when printing */
@media print {
    .no-print { display: none; }
    .page-break { page-break-before: always; }
    body { font-size: 12px; }
    table { width: 100%; }
}

/* Debug: add a visible border to see element boundaries */
@media print {
    * { border: 1px solid red !important; }
}
```

### Browser Print Preview vs Actual
```
1. Ctrl+P → Print Preview in Chrome
   → Shows what browser sees
   → Use this for layout debugging

2. BUT: PDF generation (DomPDF) may differ!
   → DomPDF has limited CSS support
   → Always test BOTH browser print AND generated PDF
```

---

## DomPDF Quirks (eTail-specific)

DomPDF does NOT support all CSS. Common gotchas:

| CSS Feature | Browser | DomPDF | Workaround |
|-------------|---------|--------|------------|
| `float: left/right` | ✅ | ⚠️ Partial | Use `<table>` layout instead |
| `flexbox` | ✅ | ❌ No | Use `<table>` layout |
| `max-width` on images | ✅ | ✅ | Always use with `max-height` + `overflow: hidden` |
| `background-image` | ✅ | ⚠️ | May not render; use `<img>` tag instead |
| `box-shadow` | ✅ | ❌ | Use `border` instead |
| `border-radius` | ✅ | ⚠️ | May not render on all elements |
| `position: fixed` | ✅ | ❌ | Use `position: absolute` |
| `@font-face` | ✅ | ⚠️ | Must be embedded; check font path |
| `opacity` | ✅ | ⚠️ Partial | May not work on all elements |
| CSS Grid | ✅ | ❌ | Use `<table>` layout |

### Image in Print (The Pattern)
```php
// ALWAYS use this pattern for images in DomPDF prints:
$img_path = FCPATH . 'uploads/logo.png';
if (file_exists($img_path)) {
    $img_data = base64_encode(file_get_contents($img_path));
    $img_type = pathinfo($img_path, PATHINFO_EXTENSION);
    echo '<img src="data:image/' . $img_type . ';base64,' . $img_data . '"
          style="max-width: 150px; max-height: 80px; overflow: hidden;">';
}
```

**Always use**: `max-width` + `max-height` + `overflow: hidden` together.

---

## Column Width & Dashed Lines

### Column Width Calculation
```
Total table width = 100%
Each column = percentage of total

For print with many columns:
- 9 columns typical in eTail prints
- Distribute %: item(20%), qty(8%), wt(10%), rate(12%), amount(15%), ...
- All must sum to 100%

Common bug: column widths don't sum to 100% → table overflows
```

### Dashed Line Width
```
The dashed separator line width depends on column count:
  Formula: column_count × base_width + buffer

  9 columns → width: 1800%  (approx)
  7 columns → width: 1400%

  Find the working reference width → copy it exactly
  Don't calculate from scratch — copy from a working print
```

---

## Quick Diagnosis

| Symptom | Check | Fix |
|---------|-------|-----|
| Element invisible | `display: none` or `visibility: hidden` in Computed styles | Remove/override the rule |
| Element in wrong position | Check `position`, `margin`, `float` | Compare with working reference |
| Text overlapping | Container too small or `overflow` not set | Add `overflow: hidden` or increase size |
| Image not showing in PDF | Not using `base64_encode` pattern | Use the image pattern above |
| Page break in wrong place | Missing `page-break-inside: avoid` | Add to the element or parent |
| Different on screen vs print | Missing `@media print` rules | Add print-specific overrides |
| Font different in PDF | DomPDF font not embedded | Check DomPDF font configuration |
