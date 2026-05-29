/**
 * ============================================================
 * PRINT DESIGNER — API Layer
 * Handles all AJAX communication with the CI controller
 * ============================================================
 */
var API = (function() {
    'use strict';

    var BASE = (typeof BASE_URL !== 'undefined') ? BASE_URL : '';

    function _ajax(method, url, data, onSuccess, onError) {
        var xhr = new XMLHttpRequest();
        xhr.open(method, BASE + 'index.php/' + url, true);

        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                if (xhr.status >= 200 && xhr.status < 300) {
                    try {
                        var json = JSON.parse(xhr.responseText);
                        if (onSuccess) onSuccess(json);
                    } catch(e) {
                        // If response isn't JSON (e.g. PDF blob), pass raw
                        if (onSuccess) onSuccess(xhr.response);
                    }
                } else {
                    if (onError) onError(xhr.statusText, xhr.status);
                    else console.error('API Error:', xhr.status, xhr.statusText);
                }
            }
        };

        if (method === 'POST') {
            if (typeof data === 'string') {
                xhr.setRequestHeader('Content-Type', 'application/json');
                xhr.send(data);
            } else if (data instanceof FormData) {
                xhr.send(data);
            } else {
                xhr.setRequestHeader('Content-Type', 'application/json');
                xhr.send(JSON.stringify(data));
            }
        } else {
            xhr.send();
        }
    }

    function _ajaxBlob(url, data, onSuccess, onError, onProgress) {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', BASE + 'index.php/' + url, true);
        xhr.responseType = 'blob';
        xhr.setRequestHeader('Content-Type', 'application/json');

        if (onProgress) {
            xhr.onprogress = function(e) {
                if (e.lengthComputable) {
                    onProgress(Math.round((e.loaded / e.total) * 100));
                }
            };
        }

        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                if (xhr.status >= 200 && xhr.status < 300) {
                    if (onSuccess) onSuccess(xhr.response);
                } else {
                    if (onError) onError(xhr.statusText);
                }
            }
        };

        xhr.send(JSON.stringify(data));
    }

    return {
        /**
         * Fetch all database tables
         */
        getTables: function(callback) {
            _ajax('GET', 'print_designer/get_tables', null, callback);
        },

        /**
         * Fetch variables (columns) for a table
         */
        getVariables: function(tableName, callback) {
            _ajax('GET', 'print_designer/get_variables/' + encodeURIComponent(tableName), null, callback);
        },

        /**
         * Fetch full billing data for a given bill_id.
         * Used when the 'Bill (bill_format_2)' source is selected.
         * Returns a flat { variable_name: value } map matching every {{tag}} in bill.php.
         */
        getBillData: function(billId, callback) {
            _ajax('POST', 'print_designer/get_bill_data', { bill_id: parseInt(billId, 10) }, callback);
        },

        /**
         * Fetch actual row data for preview
         */
        getRowData: function(tableName, rowId, callback) {
            _ajax('POST', 'print_designer/get_row_data', {
                table_name: tableName,
                row_id: rowId
            }, callback);
        },

        /**
         * Save template (create or update)
         */
        saveTemplate: function(data, callback) {
            _ajax('POST', 'print_designer/save', data, callback);
        },

        /**
         * Load a template by ID
         */
        loadTemplate: function(id, callback) {
            _ajax('GET', 'print_designer/load/' + id, null, callback);
        },

        /**
         * List all templates
         */
        listTemplates: function(callback) {
            _ajax('GET', 'print_designer/template_list', null, callback);
        },

        /**
         * Delete a template
         */
        deleteTemplate: function(id, callback) {
            _ajax('GET', 'print_designer/delete/' + id, null, callback);
        },

        /**
         * Duplicate a template
         */
        duplicateTemplate: function(id, callback) {
            _ajax('GET', 'print_designer/duplicate/' + id, null, callback);
        },

        /**
         * Set template as default
         */
        setDefault: function(id, callback) {
            _ajax('GET', 'print_designer/set_default/' + id, null, callback);
        },

        /**
         * Export PDF — returns blob
         */
        exportPDF: function(data, onSuccess, onError, onProgress) {
            _ajaxBlob('print_designer/export_pdf', data, onSuccess, onError, onProgress);
        },

        /**
         * Download a blob as file
         */
        downloadBlob: function(blob, filename) {
            var url = window.URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.href = url;
            a.download = filename || 'print-designer-export.pdf';
            document.body.appendChild(a);
            a.click();
            setTimeout(function() {
                document.body.removeChild(a);
                window.URL.revokeObjectURL(url);
            }, 100);
        }
    };

})();
