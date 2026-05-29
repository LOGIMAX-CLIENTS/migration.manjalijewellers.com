# Custom Blocks and GrapesJS Integration

This document explains how the Print Designer's frontend is structured and how to extend it with new custom blocks and components.

---

## 1. GrapesJS Initialization
The designer is initialized in `designer-init.js`. Key configurations include:
- **Canvas View:** Set to fit the requested paper size (A4, A5, etc.).
- **Storage Manager:** Configured to sync with the `Admin_print_template/save_design` and `load_design` endpoints.
- **Plugins:** Uses standard GrapesJS plugins for basic styling and layout.

---

## 2. Defining Custom Blocks
Custom blocks are defined in `custom-blocks.js`. A block consists of:
1. **Configuration:** Label, category, and icon.
2. **Content:** The HTML/CSS structure that gets dropped into the canvas.
3. **Component Type:** (Optional) A custom component type defined to add specific behaviors.

### Example: Simple Text Block
```javascript
bm.add('simple-text', {
  label: 'Text Block',
  category: 'Basic',
  content: '<div class="text-block">Enter text here</div>',
  attributes: { class: 'fa fa-text-height' }
});
```

---

## 3. Defining Custom Components
Custom components allow for advanced features like specialized traits (side-bar settings) or dynamic behavior.

### Example: Resizable Text Block
The `resizable-text-block` component type adds a "Font Size" trait to the sidebar:
```javascript
editor.DomComponents.addType('resizable-text-block', {
    model: {
        defaults: {
            traits: [
                { type: 'number', name: 'fontsize', label: 'Font Size (px)' }
            ],
            // ...
        },
        // ...
    }
});
```

---

## 4. How to Add a New Dynamic Block
If you want to add a block that displays a new set of data (e.g., "Branch Details"):

1. **Backend:**
   - Ensure the required fields (e.g., `branch_name`, `branch_phone`) are added to the `$mapped` array in `receipt_helper.php`.
2. **Placeholders:**
   - Add these keys to the `print_template_placeholders` table so they appear in the designer's sidebar.
3. **Frontend Block:**
   - Add a new entry in `custom-blocks.js`:
     ```javascript
     bm.add('branch-info', {
       label: 'Branch Info',
       category: 'Info',
       content: `
         <div class="branch-info">
           <strong>{{branch_name}}</strong><br>
           Phone: {{branch_phone}}
         </div>
       `
     });
     ```

---

## 5. Development Tips
- **CSS Isolation:** Use specific class names for custom blocks to avoid styles leaking from the admin panel into the template.
- **Placeholder Safety:** Always use the double-bracket syntax `{{...}}`. The backend render engine uses a simple `str_replace`, so ensure your keys are unique and don't overlap with other content.
- **Testing:** Use the "Preview" button in the template list to see how the template renders with sample data before using it in a real transaction.
