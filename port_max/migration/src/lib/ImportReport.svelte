<script>
    import { onMount } from "svelte";
    import { API_URL } from "$lib/config";
    import { createEventDispatcher } from "svelte";
    const dispatch = createEventDispatcher();
    export let client;
    export let fileId;
    onMount(generateReport);

    let data = null;
    let rawData = [];
    let totalRows = 0;
    let errorRows = 0;
    let skippedRows = 0;
    let importedRows = 0;
    let tables = [];
    let downloading = false;
    let entityName = '';
    let excelFileName = '';
    let entityTables = [];
    let selectedTable = '';
    let downloadingTable = false;

    // Backup state
    let backupStatus = '';
    let backupTables = [];
    let backupCompleted = false;
    let backingUp = false;

    const fmt = (n) => Number(n || 0).toLocaleString('en-IN');

    function returnMigration(){
        const client = sessionStorage.getItem("client");
        dispatch("migration",{client:client});
    }

    async function generateReport() {
        try {
            const res = await fetch(
                API_URL + "ImportController/generateReport",
                {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    credentials: "include",
                    body: JSON.stringify({
                        client: client,
                        fileId: fileId,
                    }),
                },
            );
            const result = await res.json();
            if (result.success) {
                data = result.data;
                rawData = data?.raw_data;
                tables = data?.tables ?? [];

                totalRows = rawData?.SheetRowCount ?? 0;
                errorRows = rawData?.ErrorRowCount ?? 0;
                skippedRows = rawData?.SkippedRowCount ?? 0;
                entityName = (rawData?.Entity ?? '').replace('.json', '').replace(/^\w/, c => c.toUpperCase());
                excelFileName = rawData?.ExcelName ?? '';

                // Load entity tables for export
                loadEntityTables(rawData?.Entity ?? '');

                // Auto backup after import
                backupEntityTables(rawData?.Entity ?? '');

                // Calculate imported from first table's current import
                if (tables.length > 0 && tables[0].rows) {
                    importedRows = tables[0].rows.reduce((sum, r) => sum + Number(r.current || 0), 0);
                } else {
                    // Fallback: calculate from totals when report tables aren't configured
                    importedRows = Math.max(0, totalRows - errorRows - skippedRows);
                }
            } else {
                alert("Failed to generate report: " + result.message);
            }
        } catch (e) {
            console.error(e);
            alert("Error generating report");
        }
    }

    async function downloadExcel() {
        downloading = true;
        try {
            const res = await fetch(
                API_URL + "ImportController/downloadReportExcel",
                {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    credentials: "include",
                    body: JSON.stringify({
                        client: client,
                        fileId: fileId,
                    }),
                },
            );
            const result = await res.json();
            if (result.success) {
                // Trigger download via the existing download endpoint
                const downloadUrl = API_URL + "ImportController/download?file=" + encodeURIComponent(result.file) + "&client=" + encodeURIComponent(result.client) + "&type=report";
                window.open(downloadUrl, '_blank');
            } else {
                alert("Failed to generate Excel: " + result.message);
            }
        } catch (e) {
            console.error(e);
            alert("Error downloading report");
        } finally {
            downloading = false;
        }
    }

    async function disconnect() {
        if (!confirm("Are you sure you want to disconnect?")) return;
        dispatch("disconnect");
    }

    let tableColumns = {};
    let selectedCols = {};
    let downloadingJson = false;
    let entityConfigName = '';

    async function backupEntityTables(entityFile) {
        const entity = (entityFile || '').replace('.json', '');
        if (!entity) return;
        backingUp = true;
        backupStatus = 'Backing up ' + entity + ' tables...';
        try {
            const res = await fetch(API_URL + 'ImportController/backupEntityTables', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                credentials: 'include',
                body: JSON.stringify({ client, entity })
            });
            const result = await res.json();
            if (result.success) {
                backupTables = result.tables || [];
                backupStatus = '✅ Backup completed — ' + entity;
            } else {
                backupStatus = '⚠️ Backup failed: ' + (result.message || 'Unknown error');
            }
        } catch (e) {
            console.error('Backup error:', e);
            backupStatus = '⚠️ Backup failed: ' + e.message;
        } finally {
            backupCompleted = true;
            backingUp = false;
        }
    }

    async function loadEntityTables(entityFile) {
        try {
            const entity = (entityFile || '').replace('.json', '');
            if (!entity) return;
            entityConfigName = entity;
            const res = await fetch(API_URL + 'ImportController/getEntityTables', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                credentials: 'include',
                body: JSON.stringify({ client, entity })
            });
            const result = await res.json();
            if (result.success) {
                entityTables = result.tables;
                tableColumns = result.columns || {};
                if (entityTables.length > 0) {
                    selectedTable = entityTables[0];
                    initAllCols();
                }
            }
        } catch (e) {
            console.error('Failed to load entity tables:', e);
        }
    }

    function initAllCols() {
        selectedCols = {};
        for (const tbl of Object.keys(tableColumns)) {
            for (const col of tableColumns[tbl]) {
                selectedCols[tbl + '.' + col] = true;
            }
        }
        selectedCols = selectedCols;
    }

    function toggleCol(key) {
        selectedCols[key] = !selectedCols[key];
        selectedCols = selectedCols;
    }

    async function downloadTableData() {
        if (!selectedTable) return;
        downloadingTable = true;
        try {
            const res = await fetch(API_URL + 'ImportController/downloadTableData', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                credentials: 'include',
                body: JSON.stringify({ client, table: selectedTable })
            });
            const result = await res.json();
            if (result.success) {
                const downloadUrl = API_URL + 'ImportController/download?file=' + encodeURIComponent(result.file) + '&client=' + encodeURIComponent(result.client) + '&type=' + (result.type || 'report');
                window.open(downloadUrl, '_blank');
            } else {
                alert('Failed: ' + result.message);
            }
        } catch (e) {
            console.error(e);
            alert('Error downloading table data');
        } finally {
            downloadingTable = false;
        }
    }

    async function downloadJsonTableData() {
        // Build columns object grouped by table: { customer: [...], address: [...] }
        const colsByTable = {};
        for (const [key, checked] of Object.entries(selectedCols)) {
            if (!checked) continue;
            const [tbl, col] = key.split('.');
            if (!colsByTable[tbl]) colsByTable[tbl] = [];
            colsByTable[tbl].push(col);
        }

        const totalSelected = Object.values(colsByTable).reduce((s, a) => s + a.length, 0);
        if (totalSelected === 0) {
            alert('Please select at least one column');
            return;
        }

        downloadingJson = true;
        try {
            const res = await fetch(API_URL + 'ImportController/downloadCombinedData', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                credentials: 'include',
                body: JSON.stringify({ client, entity: entityConfigName, columns: colsByTable })
            });
            const result = await res.json();
            if (result.success) {
                const downloadUrl = API_URL + 'ImportController/download?file=' + encodeURIComponent(result.file) + '&client=' + encodeURIComponent(result.client) + '&type=' + (result.type || 'report');
                window.open(downloadUrl, '_blank');
            } else {
                alert('Failed: ' + result.message);
            }
        } catch (e) {
            console.error(e);
            alert('Error downloading combined data');
        } finally {
            downloadingJson = false;
        }
    }
</script>

<div class="container">
    <div class="report-header">
        <div class="header-left">
            <div>
                <h2>Import Report - {entityName}</h2>
                <!-- {#if entityName}
                    <div class="entity-badge">{entityName}</div>
                {/if} -->
            </div>
        </div>

        <div class="header-actions">
            <button class="btn-download" on:click={downloadExcel} disabled={downloading}>
                {#if downloading}
                    <span class="spinner"></span> Generating...
                {:else}
                    📥 Download Excel
                {/if}
            </button>
            <button class="btn-disconnect" on:click={disconnect}>
                Disconnect / Logout
            </button>
        </div>
    </div>

    <div class="report-summary">
        <div class="card">
            <strong>Total Rows (Excel)</strong>
            <div>{fmt(totalRows)}</div>
        </div>

        <div class="card success">
            <strong>Imported</strong>
            <div>{fmt(importedRows)}</div>
        </div>

        <div class="card warning">
            <strong>Skipped</strong>
            <div>{fmt(skippedRows)}</div>
        </div>

        <div class="card error-card">
            <strong>Errors</strong>
            <div>{fmt(errorRows)}</div>
        </div>
    </div>

    {#if (skippedRows > 0 || errorRows > 0) && excelFileName}
        <div style="margin-bottom:16px;">
            <button class="btn btn-error-download"
                on:click={() => window.open(API_URL + 'ImportController/download?file=' + encodeURIComponent(excelFileName) + '&client=' + encodeURIComponent(client), '_blank')}>
                📥 Download Error / Skipped Rows Report
            </button>
        </div>
    {/if}

    <!-- Backup Status Card -->
    {#if backingUp}
        <div class="backup-card" style="border-left: 3px solid #FFA726;">
            <div style="display:flex; align-items:center; gap:10px">
                <div style="font-size:20px; animation: pulse 1s infinite;">⏳</div>
                <div>
                    <div style="font-size:14px; font-weight:600">🔄 {backupStatus}</div>
                    <div class="muted">Creating .sql backup of entity tables after import...</div>
                </div>
            </div>
        </div>
    {:else if backupCompleted}
        {#if backupTables.length > 0}
            <div class="backup-card" style="border-left: 3px solid #66BB6A;">
                <div style="font-size:14px; font-weight:600">{backupStatus}</div>
                <div class="muted" style="margin-top:6px;">
                    {#each backupTables as t}
                        <span style="margin-right:14px;">📁 <strong>{t.table}</strong> ({fmt(t.rows)} rows)</span>
                    {/each}
                </div>
            </div>
        {:else if backupStatus.startsWith('⚠️')}
            <div class="backup-card" style="border-left: 3px solid #FF7043;">
                <div style="font-size:14px;">{backupStatus}</div>
                <div class="muted">Backup could not be completed.</div>
            </div>
        {/if}
    {/if}

    <div class="tables-grid">
    {#each tables as table, idx}
        <div class="report-section">
            <h3>{table.title}</h3>

            {#if !table.rows || table.rows.length === 0}
                <div class="empty-state">No data available for this table</div>
            {:else}
                <table>
                    <thead>
                        <tr>
                            <th>{table.column_header}</th>
                            <th>Overall Count (Before Import)</th>
                            <th>Current Import</th>
                            <th>Overall Count (After Import)</th>
                        </tr>
                    </thead>
                    <tbody>
                        {#each table.rows as row}
                            <tr>
                                <td>{row.label}</td>
                                <td class="num">{fmt(row.before)}</td>
                                <td class="num highlight">{fmt(row.current)}</td>
                                <td class="num">{fmt(row.after)}</td>
                            </tr>
                        {/each}
                    </tbody>
                    <tfoot>
                        <tr class="total-row">
                            <td><strong>Total</strong></td>
                            <td class="num"><strong>{fmt(table.rows.reduce((s, r) => s + Number(r.before || 0), 0))}</strong></td>
                            <td class="num highlight"><strong>{fmt(table.rows.reduce((s, r) => s + Number(r.current || 0), 0))}</strong></td>
                            <td class="num"><strong>{fmt(table.rows.reduce((s, r) => s + Number(r.after || 0), 0))}</strong></td>
                        </tr>
                    </tfoot>
                </table>
            {/if}
        </div>
    {/each}
    </div>

    {#if tables.length === 0 && data}
        <div class="empty-state" style="margin-top: 24px;">No report tables available. Please ensure the import was completed.</div>
    {/if}

    <!-- Export Table Data Section -->
    {#if entityTables.length > 0}
        <div class="export-section">
            <h3>Export Raw Table Data</h3>
            <div class="export-controls">
                <select bind:value={selectedTable} class="table-select">
                    {#each entityTables as tbl}
                        <option value={tbl}>{tbl}</option>
                    {/each}
                </select>
                <button class="btn btn-export" on:click={downloadTableData} disabled={downloadingTable}>
                    {downloadingTable ? '⏳ Exporting...' : '📥 Export All Columns'}
                </button>
            </div>

            <h3 style="margin-top: 18px;">Export Combined Data (JSON Mapped Columns)</h3>
            {#each Object.keys(tableColumns) as tbl}
                <div class="table-group-label">{tbl}</div>
                <div class="column-checkboxes">
                    {#each tableColumns[tbl] as col}
                        <label class="col-check">
                            <input type="checkbox" checked={selectedCols[tbl + '.' + col]} on:change={() => toggleCol(tbl + '.' + col)} />
                            {col}
                        </label>
                    {/each}
                </div>
            {/each}
            <div style="margin-top: 10px;">
                <button class="btn btn-export" on:click={downloadJsonTableData} disabled={downloadingJson}>
                    {downloadingJson ? '⏳ Exporting...' : '📥 Export Combined'}
                </button>
            </div>
        </div>
    {/if}

    <div
        style="margin-top:18px; display:flex; gap:10px; align-items:center; justify-content:flex-end"
    >
        <button class="btn btn-ghost" on:click={returnMigration}
            >Start New Import</button
        >
    </div>
</div>

<style>
    .backup-card {
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 10px;
        padding: 16px 20px;
        margin-top: 16px;
    }
    .muted {
        font-size: 12px;
        color: #94a3b8;
    }
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.4; }
    }

    .entity-badge {
        display: inline-block;
        margin-top: 6px;
        padding: 4px 12px;
        background: rgba(91, 124, 255, 0.15);
        border: 1px solid rgba(91, 124, 255, 0.3);
        border-radius: 20px;
        color: #93b4ff;
        font-size: 12px;
        font-weight: 600;
        text-transform: capitalize;
    }
    .card-download-btn {
        position: absolute;
        top: 6px;
        right: 6px;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 6px;
        padding: 3px 6px;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s;
        line-height: 1;
    }
    .card-download-btn:hover {
        background: rgba(255, 255, 255, 0.18);
        transform: scale(1.1);
    }
    .btn {
        padding: 12px 18px;
        border-radius: 10px;
        font-weight: 600;
        cursor: pointer;
        border: none;
    }
    .btn-ghost {
        background: transparent;
        border: 1px solid rgba(255, 255, 255, 0.06);
        color: #dbeafe;
    }
    .empty-state {
        padding: 16px;
        border: 1px dashed rgba(255, 255, 255, 0.15);
        border-radius: 8px;
        color: #9fb2d9;
        font-size: 13px;
    }

    .header-left {
        display: flex;
        gap: 14px;
        align-items: center;
    }

    .header-actions {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .container {
        max-width: 1200px;
        margin: 32px auto;
        padding: 20px;
    }

    .report-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
    }

    .tables-grid {
        display: flex;
        gap: 16px;
        margin-top: 16px;
        flex-wrap: wrap;
    }

    .report-section {
        flex: 1;
        min-width: 300px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 6px;
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 8px;
        overflow: hidden;
    }

    th,
    td {
        padding: 6px 10px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        font-size: 12px;
    }

    th {
        color: #9fb2d9;
        font-weight: 600;
        text-transform: capitalize;
        background: rgba(255, 255, 255, 0.05);
    }

    .column-checkboxes {
        display: flex;
        flex-wrap: wrap;
        gap: 8px 16px;
        margin-top: 10px;
        padding: 10px;
        background: rgba(255, 255, 255, 0.04);
        border-radius: 8px;
    }

    .col-check {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 12px;
        color: #c0cde0;
        cursor: pointer;
        padding: 4px 8px;
        border-radius: 6px;
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.1);
        transition: background 0.15s;
    }

    .col-check:hover {
        background: rgba(255, 255, 255, 0.1);
    }

    .col-check input[type="checkbox"] {
        accent-color: #5b7cff;
        cursor: pointer;
    }

    td.num {
        text-align: right;
        font-variant-numeric: tabular-nums;
    }

    td.highlight {
        color: #5bff8a;
        font-weight: 600;
    }

    tr:nth-child(even) td {
        background: rgba(255, 255, 255, 0.02);
    }

    h3 {
        margin-bottom: 4px;
        font-size: 14px;
    }

    .total-row td {
        border-top: 2px solid rgba(255, 255, 255, 0.2);
        background: rgba(255, 255, 255, 0.06);
    }    .export-section {
        margin-top: 24px;
        padding: 16px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.03);
    }

    .table-group-label {
        font-size: 12px;
        font-weight: 600;
        color: #7eb8ff;
        margin-top: 10px;
        margin-bottom: 4px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .export-controls {
        display: flex;
        gap: 10px;
        align-items: center;
        margin-top: 10px;
    }

    .table-select {
        padding: 8px 12px;
        border-radius: 8px;
        border: 1px solid rgba(255, 255, 255, 0.15);
        background: rgba(255, 255, 255, 0.06);
        color: #e0e6f0;
        font-size: 13px;
        min-width: 180px;
        cursor: pointer;
    }

    .table-select:focus {
        outline: none;
        border-color: #5b7cff;
    }

    .btn-export {
        background: linear-gradient(135deg, #1565C0, #1E88E5);
        color: white;
        font-size: 13px;
    }

    .btn-export:hover {
        opacity: 0.9;
    }

    .btn-export:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .report-summary {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 16px;
        margin: 20px 0 28px;
    }

    .report-summary .card {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 10px;
        padding: 14px 16px;
        position: relative;
        transition:
            transform 0.15s ease,
            box-shadow 0.15s ease;
    }

    .report-summary .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 18px rgba(0, 0, 0, 0.25);
    }

    .report-summary .card strong {
        font-size: 12px;
        font-weight: 600;
        color: #9fb2d9;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .report-summary .card div {
        margin-top: 6px;
        font-size: 28px;
        font-weight: 700;
        color: #ffffff;
    }

    /* Accent bars */
    .report-summary .card::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        height: 4px;
        width: 100%;
        border-radius: 10px 10px 0 0;
        background: #5b7cff;
    }

    /* Status colors */
    .report-summary .card.success::before {
        background: #2ecc71;
    }

    .report-summary .card.warning::before {
        background: #f1c40f;
    }

    .report-summary .card.error-card::before {
        background: #e74c3c;
    }

    .btn-error-download {
        background: linear-gradient(135deg, #e67e22, #d35400);
        color: #fff;
        border: none;
        padding: 10px 18px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.25s ease;
        box-shadow: 0 4px 14px rgba(230, 126, 34, 0.3);
    }

    .btn-error-download:hover {
        background: linear-gradient(135deg, #f39c12, #e67e22);
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(230, 126, 34, 0.45);
    }

    .btn-disconnect {
        background: linear-gradient(135deg, #ff4d4f, #d9363e);
        color: #fff;
        border: none;
        padding: 10px 18px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.25s ease;
        box-shadow: 0 6px 18px rgba(255, 77, 79, 0.35);
    }

    .btn-disconnect:hover {
        background: linear-gradient(135deg, #ff6b6d, #e04850);
        transform: translateY(-1px);
        box-shadow: 0 10px 22px rgba(255, 77, 79, 0.45);
    }

    .btn-disconnect:active {
        transform: translateY(0);
        box-shadow: 0 4px 12px rgba(255, 77, 79, 0.3);
    }

    .btn-disconnect:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(255, 77, 79, 0.35);
    }

    .btn-download {
        background: linear-gradient(135deg, #2ecc71, #27ae60);
        color: #fff;
        border: none;
        padding: 10px 18px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.25s ease;
        box-shadow: 0 6px 18px rgba(46, 204, 113, 0.35);
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .btn-download:hover:not(:disabled) {
        background: linear-gradient(135deg, #3ddb84, #2ecc71);
        transform: translateY(-1px);
        box-shadow: 0 10px 22px rgba(46, 204, 113, 0.45);
    }

    .btn-download:disabled {
        opacity: 0.7;
        cursor: wait;
    }

    .spinner {
        display: inline-block;
        width: 14px;
        height: 14px;
        border: 2px solid rgba(255,255,255,0.3);
        border-top-color: #fff;
        border-radius: 50%;
        animation: spin 0.6s linear infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }
</style>
