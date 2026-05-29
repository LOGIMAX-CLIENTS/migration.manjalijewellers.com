let gjsEditor;

$(document).ready(function() {
    
    // Add paper size class to canvas container
    const paperClass = 'paper-' + TEMPLATE_CONFIG.paperSize.toLowerCase().replace(' ', '-');
    $('.bd-canvas-container').addClass(paperClass);

    gjsEditor = grapesjs.init({
        container: '#gjs',
        height: '100%',
        width: '100%',
        fromElement: false,
        storageManager: false, // We will handle saving manually via AJAX
        
        // Asset Manager for direct image uploads
        assetManager: {
            upload: TEMPLATE_CONFIG.baseUrl + 'invoice-builder/upload-image',
            uploadName: 'files',
            autoAdd: true,
            dropzone: 1, // Allow dropping files
            dropzoneContent: '<div class="gjs-dropzone">Drop images here or click to upload</div>',
        },

        // Remove standard panels so we can build our custom locked-down UI
        panels: { defaults: [] },
        
        // Configure where blocks go
        blockManager: {
            appendTo: '#gjs-blocks'
        },
        
        // Configure where Style Manager goes — Full CSS editing
        styleManager: {
            appendTo: '#tab-styles',
            sectors: [{
                name: 'General',
                open: false,
                buildProps: ['float', 'display', 'position', 'top', 'right', 'bottom', 'left', 'overflow', 'opacity'],
                properties: [
                    { name: 'Display', property: 'display', type: 'select', defaults: 'block',
                      list: [{value: 'block'}, {value: 'flex'}, {value: 'inline-block'}, {value: 'inline'}, {value: 'none'}, {value: 'table'}, {value: 'table-row'}, {value: 'table-cell'}]
                    },
                    { name: 'Position', property: 'position', type: 'select', defaults: 'static',
                      list: [{value: 'static'}, {value: 'relative'}, {value: 'absolute'}, {value: 'fixed'}]
                    },
                    { name: 'Float', property: 'float', type: 'radio', defaults: 'none',
                      list: [{value: 'none', className: 'fa fa-times'}, {value: 'left', className: 'fa fa-align-left'}, {value: 'right', className: 'fa fa-align-right'}]
                    },
                    { name: 'Overflow', property: 'overflow', type: 'select', defaults: 'visible',
                      list: [{value: 'visible'}, {value: 'hidden'}, {value: 'scroll'}, {value: 'auto'}]
                    }
                ]
            }, {
                name: 'Dimension',
                open: false,
                buildProps: ['width', 'min-width', 'max-width', 'height', 'min-height', 'max-height', 'margin', 'padding'],
                properties: [
                    { property: 'margin', properties: [{ name: 'Top' }, { name: 'Right' }, { name: 'Bottom' }, { name: 'Left' }] },
                    { property: 'padding', properties: [{ name: 'Top' }, { name: 'Right' }, { name: 'Bottom' }, { name: 'Left' }] }
                ]
            }, {
                name: 'Typography',
                open: true,
                buildProps: ['font-family', 'font-size', 'font-weight', 'font-style', 'letter-spacing', 'color', 'line-height', 'text-align', 'text-decoration', 'text-transform', 'text-shadow', 'white-space', 'word-spacing'],
                properties: [
                    { name: 'Font', property: 'font-family', type: 'select', defaults: 'Arial, sans-serif',
                      list: [
                          {value: 'Arial, sans-serif', name: 'Arial'},
                          {value: 'Roboto, sans-serif', name: 'Roboto'},
                          {value: 'Times New Roman, serif', name: 'Times New Roman'},
                          {value: 'Georgia, serif', name: 'Georgia'},
                          {value: 'Courier New, monospace', name: 'Courier New'},
                          {value: 'Verdana, sans-serif', name: 'Verdana'},
                          {value: 'Tahoma, sans-serif', name: 'Tahoma'},
                          {value: 'Trebuchet MS, sans-serif', name: 'Trebuchet MS'},
                          {value: 'Impact, sans-serif', name: 'Impact'},
                          {value: 'Noto Sans, sans-serif', name: 'Noto Sans'},
                      ]
                    },
                    { name: 'Weight', property: 'font-weight', type: 'select', defaults: 'normal',
                      list: [{value: '100', name: 'Thin'}, {value: '300', name: 'Light'}, {value: 'normal', name: 'Normal'}, {value: '500', name: 'Medium'}, {value: '600', name: 'Semi-Bold'}, {value: 'bold', name: 'Bold'}, {value: '800', name: 'Extra Bold'}, {value: '900', name: 'Black'}]
                    },
                    { name: 'Style', property: 'font-style', type: 'radio', defaults: 'normal',
                      list: [{value: 'normal', name: 'N', className: 'fa fa-font'}, {value: 'italic', name: 'I', className: 'fa fa-italic'}]
                    },
                    { name: 'Align', property: 'text-align', type: 'radio', defaults: 'left',
                      list: [{value: 'left', className: 'fa fa-align-left'}, {value: 'center', className: 'fa fa-align-center'}, {value: 'right', className: 'fa fa-align-right'}, {value: 'justify', className: 'fa fa-align-justify'}]
                    },
                    { name: 'Decoration', property: 'text-decoration', type: 'radio', defaults: 'none',
                      list: [{value: 'none', name: 'N', className: 'fa fa-times'}, {value: 'underline', name: 'U', className: 'fa fa-underline'}, {value: 'overline', name: 'O', className: 'fa fa-long-arrow-up'}, {value: 'line-through', name: 'S', className: 'fa fa-strikethrough'}]
                    },
                    { name: 'Transform', property: 'text-transform', type: 'select', defaults: 'none',
                      list: [{value: 'none', name: 'None'}, {value: 'uppercase', name: 'UPPERCASE'}, {value: 'lowercase', name: 'lowercase'}, {value: 'capitalize', name: 'Capitalize'}]
                    },
                    { name: 'White Space', property: 'white-space', type: 'select', defaults: 'normal',
                      list: [{value: 'normal'}, {value: 'nowrap'}, {value: 'pre'}, {value: 'pre-wrap'}, {value: 'pre-line'}]
                    }
                ]
            }, {
                name: 'Borders',
                open: false,
                buildProps: ['border-width', 'border-style', 'border-color', 'border-radius', 'border-top-width', 'border-bottom-width', 'border-left-width', 'border-right-width'],
                properties: [
                    { name: 'Border Style', property: 'border-style', type: 'select', defaults: 'none',
                      list: [{value: 'none'}, {value: 'solid'}, {value: 'dashed'}, {value: 'dotted'}, {value: 'double'}, {value: 'groove'}, {value: 'ridge'}]
                    }
                ]
            }, {
                name: 'Background',
                open: false,
                buildProps: ['background-color', 'background-image', 'background-repeat', 'background-position', 'background-size'],
                properties: [
                    { name: 'Repeat', property: 'background-repeat', type: 'select', defaults: 'repeat',
                      list: [{value: 'repeat'}, {value: 'no-repeat'}, {value: 'repeat-x'}, {value: 'repeat-y'}]
                    },
                    { name: 'Size', property: 'background-size', type: 'select', defaults: 'auto',
                      list: [{value: 'auto'}, {value: 'cover'}, {value: 'contain'}, {value: '100%'}]
                    }
                ]
            }, {
                name: 'Flex Container',
                open: false,
                properties: [
                    { name: 'Display', property: 'display', type: 'select', defaults: 'block',
                      list: [{value: 'block', name: 'Block'}, {value: 'flex', name: 'Flex'}]
                    },
                    { name: 'Direction', property: 'flex-direction', type: 'radio', defaults: 'row',
                      list: [{value: 'row', name: 'Row'}, {value: 'row-reverse', name: 'Row Rev'}, {value: 'column', name: 'Col'}, {value: 'column-reverse', name: 'Col Rev'}]
                    },
                    { name: 'Justify', property: 'justify-content', type: 'select', defaults: 'flex-start',
                      list: [{value: 'flex-start', name: 'Start'}, {value: 'center', name: 'Center'}, {value: 'flex-end', name: 'End'}, {value: 'space-between', name: 'Between'}, {value: 'space-around', name: 'Around'}, {value: 'space-evenly', name: 'Evenly'}]
                    },
                    { name: 'Align Items', property: 'align-items', type: 'select', defaults: 'stretch',
                      list: [{value: 'stretch', name: 'Stretch'}, {value: 'flex-start', name: 'Top'}, {value: 'center', name: 'Center'}, {value: 'flex-end', name: 'Bottom'}, {value: 'baseline', name: 'Baseline'}]
                    },
                    { name: 'Wrap', property: 'flex-wrap', type: 'select', defaults: 'nowrap',
                      list: [{value: 'nowrap', name: 'No Wrap'}, {value: 'wrap', name: 'Wrap'}, {value: 'wrap-reverse', name: 'Wrap Rev'}]
                    },
                    { name: 'Gap', property: 'gap' }
                ]
            }, {
                name: 'Table Styles',
                open: false,
                properties: [
                    { name: 'Border Collapse', property: 'border-collapse', type: 'radio', defaults: 'collapse',
                      list: [{value: 'collapse', name: 'Collapse'}, {value: 'separate', name: 'Separate'}]
                    },
                    { name: 'Border Spacing', property: 'border-spacing' },
                    { name: 'Vertical Align', property: 'vertical-align', type: 'select', defaults: 'middle',
                      list: [{value: 'top'}, {value: 'middle'}, {value: 'bottom'}, {value: 'baseline'}]
                    },
                    { name: 'Table Layout', property: 'table-layout', type: 'radio', defaults: 'auto',
                      list: [{value: 'auto', name: 'Auto'}, {value: 'fixed', name: 'Fixed'}]
                    }
                ]
            }, {
                name: 'Print & Page',
                open: false,
                properties: [
                    { name: 'Page Break Before', property: 'page-break-before', type: 'select', defaults: 'auto',
                      list: [{value: 'auto'}, {value: 'always'}, {value: 'avoid'}]
                    },
                    { name: 'Page Break After', property: 'page-break-after', type: 'select', defaults: 'auto',
                      list: [{value: 'auto'}, {value: 'always'}, {value: 'avoid'}]
                    },
                    { name: 'Page Break Inside', property: 'page-break-inside', type: 'select', defaults: 'auto',
                      list: [{value: 'auto'}, {value: 'avoid'}]
                    },
                    { name: 'Box Sizing', property: 'box-sizing', type: 'select', defaults: 'border-box',
                      list: [{value: 'content-box'}, {value: 'border-box'}]
                    }
                ]
            }, {
                name: 'Effects',
                open: false,
                buildProps: ['opacity', 'box-shadow', 'text-shadow', 'transform'],
                properties: [
                    { name: 'Opacity', property: 'opacity', type: 'slider', defaults: 1, step: 0.01, max: 1, min: 0 }
                ]
            }]
        },
        
        // Configure Trait Manager
        traitManager: {
            appendTo: '#tab-traits'
        },
        
        // Layers
        layerManager: {
            appendTo: '#tab-layers'
        },
        
        // Selector Manager
        selectorManager: {
            appendTo: '#tab-styles'
        },

        // Setup the default canvas wrapper paper structure
        components: '<div style="padding: 30px; font-family: Roboto, sans-serif; display: flex; flex-direction: column; gap: 15px;">&nbsp;</div>',

        plugins: ['invoice-components-plugin', 'invoice-variable-plugin']
    });
    
    // Setup Panels Toggle in Right Sidebar
    $('.bd-tab').on('click', function() {
        $('.bd-tab').removeClass('active');
        $(this).addClass('active');
        
        $('.bd-tab-pane').removeClass('active');
        $('#' + $(this).data('tab')).addClass('active');
    });

    // Load Data
    loadDesign();

    // Setup Actions
    $('#btn-save').on('click', saveDesign);
    $('#btn-undo').on('click', () => gjsEditor.UndoManager.undo());
    $('#btn-redo').on('click', () => gjsEditor.UndoManager.redo());

    // New UX Toolbar Actions
    $('#btn-toggle-grid').on('click', function() {
        const cmd = 'core:component-outline';
        if (gjsEditor.Commands.isActive(cmd)) {
            gjsEditor.stopCommand(cmd);
            $(this).css({ 'background': '#fff', 'color': '#444' });
        } else {
            gjsEditor.runCommand(cmd);
            $(this).css({ 'background': '#eef1f6', 'color': '#3c8dbc' });
        }
    });

    $('#btn-clear').on('click', function() {
        if (confirm('Are you sure you want to clear the entire template? This will remove all blocks.')) {
            const wrapper = gjsEditor.getWrapper();
            if (wrapper) {
                wrapper.components(''); // Clear inside the paper wrapper only
            }
        }
    });

    $('#btn-shortcuts').on('click', () => {
        $('#shortcuts-modal').css('display', 'flex');
    });

    $('#close-shortcuts').on('click', () => {
        $('#shortcuts-modal').hide();
    });

    // Custom Save Shortcut
    gjsEditor.Keymaps.add('core:save', '⌘+s, ctrl+s', () => {
        saveDesign();
    });

    $('#btn-zoom-in').on('click', () => {
        gjsEditor.Canvas.setZoom(gjsEditor.Canvas.getZoom() + 10);
    });
    
    $('#btn-zoom-out').on('click', () => {
        gjsEditor.Canvas.setZoom(gjsEditor.Canvas.getZoom() - 10);
    });
    
    $('#btn-zoom-reset').on('click', () => {
        gjsEditor.Canvas.setZoom(100);
    });

    // Lock down paper wrapper and setup empty state watermark
    gjsEditor.on('load', () => {
        // Enable Grid / Outlines by default
        gjsEditor.runCommand('core:component-outline');
        $('#btn-toggle-grid').css({ 'background': '#eef1f6', 'color': '#3c8dbc' });

        const wrapper = gjsEditor.getWrapper();
        if(wrapper) {
            wrapper.set({
                selectable: false,
                hoverable: false,
                removable: false,
                draggable: false,
                copyable: false
            });
            wrapper.addStyle({ 'background-color': '#ffffff', 'min-height': '100%' });
        }
        
        const watermarkStyle = `
            body:empty::before { 
                content: "Drag blocks from the left sidebar here to start building your invoice template."; 
                font-family: Roboto, sans-serif; 
                font-size: 16px; 
                color: #b0b8c4; 
                max-width: 400px;
                text-align: center;
                line-height: 1.5;
                position: absolute; 
                top: 50%; 
                left: 50%; 
                transform: translate(-50%, -50%); 
                transform: translate(-50%, -50%); 
                pointer-events: none; 
            }
            
            /* Crucial Fix: Make empty divs visible and droppable */
            .gjs-dashed * {
                min-height: 40px !important;
            }
            .gjs-dashed div:empty {
                outline: 2px dashed #3c8dbc !important;
                outline-offset: -2px;
                background-color: rgba(60, 141, 188, 0.05);
                position: relative;
            }
            .gjs-dashed div:empty::after {
                content: "Drop elements here";
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                font-family: Roboto, sans-serif;
                font-size: 11px;
                color: #3c8dbc;
                opacity: 0.6;
                pointer-events: none;
            }
        `;
        gjsEditor.Css.addRules(watermarkStyle);
    });
});

function loadDesign() {
    fetch(`${TEMPLATE_CONFIG.baseUrl}invoice-builder/load-design/${TEMPLATE_CONFIG.templateId}`)
        .then(res => res.json())
        .then(data => {
            // Load if data exists and is not just an empty array/object
            if (data && Object.keys(data).length > 0 && !Array.isArray(data)) {
                gjsEditor.loadProjectData(data);
            }
        });
}

function saveDesign() {
    const btn = $('#btn-save');
    const originalText = btn.text();
    btn.text('Saving...').prop('disabled', true);

    const projectData = gjsEditor.getProjectData();
    projectData['gjs-html'] = gjsEditor.getHtml();
    projectData['gjs-css'] = gjsEditor.getCss();

    fetch(`${TEMPLATE_CONFIG.baseUrl}invoice-builder/save-design/${TEMPLATE_CONFIG.templateId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(projectData)
    })
    .then(res => res.json())
    .then(res => {
        if (res.success) {
            btn.text('✅ Saved');
            $('#toast-msg').fadeIn();
            setTimeout(() => { 
                btn.text(originalText).prop('disabled', false); 
                $('#toast-msg').fadeOut();
            }, 2500);
        } else {
            alert('Error saving design.');
            btn.text(originalText).prop('disabled', false);
        }
    })
    .catch(err => {
        console.error(err);
        alert('Network error while saving.');
        btn.text(originalText).prop('disabled', false);
    });
}
