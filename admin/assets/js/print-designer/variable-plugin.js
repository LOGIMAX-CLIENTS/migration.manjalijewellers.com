/* Invoice Variable Picker Plugin for V2 Builder */

grapesjs.plugins.add('invoice-variable-plugin', (editor, options) => {
    
    // Add custom Action to RTE Toolbar
    editor.RichTextEditor.add('insert-variable', {
        icon: '<div style="pointer-events:none; padding: 0 4px; display:flex; align-items:center; color: #3c8dbc; font-size:13px; font-family:sans-serif; white-space:nowrap;"><b>{ }</b>&nbsp;Insert Variable</div>',
        attributes: { title: 'Insert Invoice Variable' },
        result: (rte, action) => {
            // Show the custom modal/dropdown to select a variable
            showVariablePicker(rte);
        }
    });

    let fieldsCache = null;

    function showVariablePicker(rte) {
        const modal = editor.Modal;
        
        if (fieldsCache) {
            renderModal(modal, fieldsCache, rte);
        } else {
            // Fetch fields from the server
            // Using the global TEMPLATE_CONFIG set in designer.php
            fetch(`${TEMPLATE_CONFIG.baseUrl}invoice-builder/fields/${TEMPLATE_CONFIG.category}`)
                .then(res => res.json())
                .then(data => {
                    fieldsCache = data;
                    renderModal(modal, fieldsCache, rte);
                })
                .catch(err => {
                    console.error("Error loading fields:", err);
                    alert("Failed to load invoice variables.");
                });
        }
    }

    function renderModal(modal, data, rte) {
        let html = '<div style="padding: 10px; max-height: 400px; overflow-y: auto;">';
        
        // Render groups
        for (const [groupName, fields] of Object.entries(data)) {
            html += `<h4 style="margin-top:15px; border-bottom:1px solid #ddd; padding-bottom:5px; color:#333; font-family: Roboto, sans-serif;">${groupName}</h4>`;
            html += `<div style="display:flex; flex-wrap:wrap; gap:10px;">`;
            
            fields.forEach(f => {
                html += `
                    <button class="var-btn" data-tag="{{${f.tag}}}" 
                            style="padding:6px 12px; background:#f4f6f9; border:1px solid #d2d6de; border-radius:4px; cursor:pointer; color:#444; font-family: Roboto, sans-serif;"
                            title="${f.description || f.name}">
                        ${f.name}
                    </button>`;
            });
            html += `</div>`;
        }
        
        html += '</div>';

        modal.setTitle('Select Invoice Variable');
        modal.setContent(html);
        modal.open();

        // Bind clicks to buttons inside the modal
        const contentEl = modal.getContentEl();
        const btns = contentEl.querySelectorAll('.var-btn');
        btns.forEach(btn => {
            btn.addEventListener('click', function() {
                const tag = this.getAttribute('data-tag');
                rte.insertHTML(tag);
                modal.close();
            });
        });
    }
});
