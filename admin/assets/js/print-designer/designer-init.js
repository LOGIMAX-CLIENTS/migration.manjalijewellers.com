// Initialize GrapesJS

const templateId = document.getElementById('template_id').value;
const category = document.getElementById('template_category').value;
// Just relative path since base_url might be tricky in JS file without processing
// We assume this script is loaded in a page where these endpoints work relative to base 
const saveUrl = 'admin/print-templates/save-design/' + templateId; // Adjust based on actual route
const loadUrl = 'admin/print-templates/load-design/' + templateId;

// Check if we are in admin subfolder or root (simple check)
// const baseUrl = ... (Removed, using API_URL from PHP)
console.log("DEBUG: API_URL =", API_URL);
console.log("DEBUG: loadUrl =", API_URL + 'admin_print_template/load_design/' + templateId);


const editor = grapesjs.init({
    container: '#gjs',
    dragMode: 'absolute', // Enable absolute positioning
    fromElement: false, // We load via AJAX
    noticeOnUnload: false, // Disable unsaved changes warning
    height: '100vh',
    width: 'auto',
    
    storageManager: {
        type: 'remote',
        stepsBeforeSave: 10,
        autosave: false, // Disabled to prevent format mismatch. Rely on manual save or custom autosave.
        autoload: false, // Disabled to use manual loadProjectData
        urlStore: API_URL + 'print-templates/save-design/' + templateId,
        urlLoad: API_URL + 'print-templates/load-design/' + templateId,
        contentTypeJson: true,
    },


    // Force box-sizing for all elements in canvas to ensure highlighter works correctly
    canvas: {
        styles: [
             // 'https://unpkg.com/grapesjs/dist/css/grapes.min.css',
        ],
        scripts: []
    },
    // Inject custom CSS to fix box model
    canvasCss: `
      * { box-sizing: border-box; }
      body { margin: 0; }
      .gjs-resizer-hdl {
        border: 1px solid #3b97e3;
        background-color: #fff;
      }
    `,

    domComponents: {
        model: {
            defaults: {
                resizable: true,
                editable: true,
                badgable: true,
            }
        }
    },

    deviceManager: {
        devices: [
            { name: 'A45 Portrait', width: '210mm', height: '297mm', widthMedia: '' },
            { name: 'A4 Landscape', width: '297mm', height: '210mm', widthMedia: '' },
            { name: 'A5 Portrait', width: '148mm', height: '210mm', widthMedia: '' },
            { name: 'Thermal 80mm', width: '80mm', height: '100vh', widthMedia: '' }, 
            { name: 'Mobile', width: '320px', widthMedia: '480px' }
        ]
    },

    styleManager: {
        sectors: [{
            name: 'General',
            buildProps: ['float', 'display', 'position', 'top', 'right', 'bottom', 'left'],
            properties: [{
                name: 'Alignment',
                property: 'float',
                type: 'radio',
                defaults: 'none',
                list: [
                    { value: 'none', className: 'fa fa-times' },
                    { value: 'left', className: 'fa fa-align-left' },
                    { value: 'right', className: 'fa fa-align-right' }
                ],
            },
            { property: 'position', type: 'select' }
            ],
        },{
            name: 'Dimension',
            open: false,
            buildProps: ['width', 'max-width', 'min-width', 'height', 'max-height', 'min-height', 'margin', 'padding'],
            properties: [
                { property: 'margin', properties: [{ name: 'Top' }, { name: 'Right' }, { name: 'Bottom' }, { name: 'Left' }] },
                { property: 'padding', properties: [{ name: 'Top' }, { name: 'Right' }, { name: 'Bottom' }, { name: 'Left' }] }
            ]
        },{
            name: 'Typography',
            open: false,
            buildProps: ['font-family', 'font-size', 'font-weight', 'letter-spacing', 'color', 'line-height', 'text-align', 'text-decoration', 'text-shadow'],
            properties: [
                { name: 'Font', property: 'font-family' },
                { name: 'Weight', property: 'font-weight' },
                { name: 'Font Color', property: 'color' },
                {
                    property: 'text-align',
                    type: 'radio',
                    defaults: 'left',
                    list: [
                        { value: 'left', name: 'Left', className: 'fa fa-align-left' },
                        { value: 'center', name: 'Center', className: 'fa fa-align-center' },
                        { value: 'right', name: 'Right', className: 'fa fa-align-right' },
                        { value: 'justify', name: 'Justify', className: 'fa fa-align-justify' }
                    ],
                },{
                    property: 'text-decoration',
                    type: 'radio',
                    defaults: 'none',
                    list: [
                        { value: 'none', name: 'None', className: 'fa fa-times' },
                        { value: 'underline', name: 'underline', className: 'fa fa-underline' },
                        { value: 'line-through', name: 'Line-through', className: 'fa fa-strikethrough' }
                    ],
                }
            ]
        },{
            name: 'Decorations',
            open: false,
            buildProps: ['opacity', 'background-color', 'border-radius', 'border', 'box-shadow', 'background'],
            properties: [
                { property: 'opacity', type: 'slider', defaults: 1, min: 0, max: 1, step: 0.01 },
                { property: 'border-radius', properties: [{ name: 'Top Left' }, { name: 'Top Right' }, { name: 'Bottom Left' }, { name: 'Bottom Right' }] },
                { property: 'box-shadow', properties: [{ name: 'X-Offset' }, { name: 'Y-Offset' }, { name: 'Blur' }, { name: 'Spread' }, { name: 'Color' }] },
                { property: 'background-color' }
            ]
        },{
            name: 'Extra',
            open: false,
            buildProps: ['transition', 'perspective', 'transform'],
        },{
            name: 'Flex',
            open: false,
            properties: [{
                name: 'Flex Container',
                property: 'display',
                type: 'select',
                defaults: 'block',
                list: [
                    { value: 'block', name: 'Disable' },
                    { value: 'flex', name: 'Enable' }
                ],
            },{
                name: 'Flex Parent',
                property: 'label-parent-flex',
                type: 'integer',
            },{
                name: 'Direction',
                property: 'flex-direction',
                type: 'radio',
                defaults: 'row',
                list: [{
                    value: 'row',
                    name: 'Row',
                    className: 'icons-flex icon-dir-row',
                    title: 'Row',
                },{
                    value: 'row-reverse',
                    name: 'Row Reverse',
                    className: 'icons-flex icon-dir-row-rev',
                    title: 'Row Reverse',
                },{
                    value: 'column',
                    name: 'Column',
                    title: 'Column',
                    className: 'icons-flex icon-dir-col',
                },{
                    value: 'column-reverse',
                    name: 'Column Reverse',
                    title: 'Column Reverse',
                    className: 'icons-flex icon-dir-col-rev',
                }],
            },{
                name: 'Justify',
                property: 'justify-content',
                type: 'radio',
                defaults: 'flex-start',
                list: [{
                    value: 'flex-start',
                    className: 'icons-flex icon-just-start',
                    title: 'Start',
                },{
                    value: 'flex-end',
                    title: 'End',
                    className: 'icons-flex icon-just-end',
                },{
                    value: 'space-between',
                    title: 'Space Between',
                    className: 'icons-flex icon-just-sp-bet',
                },{
                    value: 'space-around',
                    title: 'Space Around',
                    className: 'icons-flex icon-just-sp-ar',
                },{
                    value: 'center',
                    title: 'Center',
                    className: 'icons-flex icon-just-cent',
                }],
            },{
                name: 'Align',
                property: 'align-items',
                type: 'radio',
                defaults: 'center',
                list: [{
                    value: 'flex-start',
                    title: 'Start',
                    className: 'icons-flex icon-al-start',
                },{
                    value: 'flex-end',
                    title: 'End',
                    className: 'icons-flex icon-al-end',
                },{
                    value: 'stretch',
                    title: 'Stretch',
                    className: 'icons-flex icon-al-str',
                },{
                    value: 'center',
                    title: 'Center',
                    className: 'icons-flex icon-al-center',
                }],
            }]
        }
        ]
    },



    plugins: ['gjs-preset-webpage', 'grapesjs-ruler'],
    pluginsOpts: {
        'gjs-preset-webpage': {
            blocksBasicOpts: { flexGrid: true },
            navbarOpts: false,
            countdownOpts: false,
            formsOpts: false,
            exportOpts: true
        },
        'grapesjs-ruler': {
             // options for ruler if needed
        }
    },
    canvas: {
         styles: [
            // CDN for basic print styles if needed
            // 'https://unpkg.com/grapesjs/dist/css/grapes.min.css' 
        ]
    }
});

// Register custom components BEFORE loading data
registerCustomComponents(editor);

// Load dynamic placeholders
fetch(API_URL + 'admin_print_template/get_fields/' + category)
    .then(response => response.json())
    .then(fieldsData => {

        // Fetch custom block templates
        console.log("Fetching templates from:", API_URL + 'admin_print_template/get_block_templates');
        return fetch(API_URL + 'admin_print_template/get_block_templates')
            .then(response => {
                console.log("Templates response status:", response.status);
                return response.json();
            })
            .then(templates => {
                 console.log("Loaded templates:", templates);
                 // Add custom blocks with dynamic templates
                 addPrintBlocks(editor, templates);

                 // Add Fields
                 const bm = editor.BlockManager;
                 fieldsData.forEach(field => {
                     bm.add('ph-' + field.placeholder_key, {
                        label: field.placeholder_label,
                        category: 'Fields (' + (field.placeholder_group || 'General') + ')',
                        content: {
                            type: 'resizable-text-block',
                            content: '{{' + field.placeholder_key + '}}',
                            attributes: { class: 'ph-' + field.placeholder_key + '-block' },
                            style: { padding: '5px', border: '1px dashed #ece7e7ff', display:'inline-block' }
                        },
                        attributes: { title: field.description }
                    });
                });
            });

    })
    .catch(err => console.error('Error loading fields or templates:', err));

// Add save button manually if needed (autosave is on)
editor.Panels.addButton('options', {
    id: 'save-db',
    className: 'fa fa-floppy-o',
    command: 'save-db',
    attributes: { title: 'Save Template' }
});

editor.Commands.add('save-db', {
    run: function(editor, sender) {
        sender && sender.set('active', 0); // turn off the button
        
        // Manual Save to ensure HTML/CSS are sent
        const html = editor.getHtml();
        const css = editor.getCss();
        const components = editor.getComponents();
        const style = editor.getStyle();
        const projectData = editor.getProjectData();

        // Prepare payload
        const payload = {
            ...projectData,
            'gjs-html': html,
            'gjs-css': css
        };

        console.log("payload",payload);

        const url = API_URL + 'admin_print_template/save_design/' + templateId;

        // Show loading state (optional)
        const btn = document.querySelector('.fa-floppy-o');
        if(btn) btn.style.opacity = '0.5';

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        })
        .then(response => {
            if (!response.ok) {
                 return response.text().then(text => { throw new Error(text) });
            }
            return response.json();
        })
        .then(data => {
            if(data.success) {
                // editor.Store.clear(); // Clear dirty state (Store is undefined)
                $.toaster({ priority: 'success', title: 'Success', message: 'Template Saved Successfully! Redirecting...' });
                
                // Remove unsaved changes warning
                $(window).off('beforeunload');
                window.onbeforeunload = null;

                // Redirect after short delay
                setTimeout(function() {
                    window.location.href = API_URL + 'print-templates'; 
                }, 1000);

            } else {
                $.toaster({ priority: 'danger', title: 'Error', message: 'Save Failed: ' + (data.message || 'Unknown error') });
            }
        })
        .catch(error => {
            console.error('Save Error:', error);
            $.toaster({ priority: 'danger', title: 'Error', message: 'Error saving template. Check console for details.' });
        })
        .finally(() => {
            if(btn) btn.style.opacity = '1';
        });
    }
});

// Manual Load to ensure compatibility with getProjectData() format
const loadUrlRouted = API_URL + 'print-templates/load-design/' + templateId;
console.log('Fetching design from:', loadUrlRouted);

fetch(loadUrlRouted, {
    method: 'GET',
    headers: {
        'Content-Type': 'application/json'
    }
})
.then(response => response.json())
.then(data => {
    if (data && (Object.keys(data).length > 0 || Array.isArray(data))) {
        console.log('Loading design data:', data);
        editor.loadProjectData(data);
    } else {
        console.log('No saved design found or empty data.');
    }
})
.catch(err => console.error('Error loading design:', err));

