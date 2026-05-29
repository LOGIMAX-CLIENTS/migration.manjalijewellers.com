<script>
  import { onMount } from "svelte";
  import { API_URL } from "$lib/config";
  export let client;
  export let dbName = '';

  import { createEventDispatcher } from "svelte";
  const dispatch = createEventDispatcher();

  let file = null;
  let fileName = "";
  let fileId = null;

  let state = "idle"; // idle | uploaded | validating | validated | has_errors | ready | backing_up | importing | done | error
  let validRowCount = 0;
  let errorRowCount = 0;
  let validateErrors = [];
  let previewRows = [];
  let importProgress = 0;
  let validationProgress = 0; // ✅ Added for real-time validation progress
  let validationStartTime;
  let importResult = null;
  let generalError = "";
  let validateErrMessage = "";

  let configList = [];
  let selectedConfig = "";

  let allrows = [];
  let skippedRows = [];

  let reportGenerated = false;

  // Backup state
  let backupStatus = '';
  let backupTables = [];
  let backupCompleted = false;
  let backupDownloadUrl = '';
  let sqlDownloadUrl = '';
  // Drag & drop handlers
  let dragOver = false;

  // Import confirmation modal
  let showConfirmModal = false;

  // Searchable dropdown state
  let dropdownOpen = false;
  let searchQuery = "";
  let dropdownRef;

  $: filteredConfigs = searchQuery
    ? configList.filter(c => c.name.toLowerCase().includes(searchQuery.toLowerCase()))
    : configList;

  $: selectedConfigName = configList.find(c => c.file === selectedConfig)?.name || "";

  function selectEntity(c) {
    selectedConfig = c.file;
    searchQuery = "";
    dropdownOpen = false;
  }

  function handleClickOutside(e) {
    if (dropdownRef && !dropdownRef.contains(e.target)) {
      dropdownOpen = false;
      searchQuery = "";
    }
  }

  onMount(() => {
    loadConfigs();
    if (!dbName) dbName = sessionStorage.getItem('dbName') || '';
  });
  function onDrop(e) {
    e.preventDefault();
    dragOver = false;
    const f = e.dataTransfer.files && e.dataTransfer.files[0];
    if (f) selectFile(f);
  }

  function onFileChange(e) {
    const f = e.target.files && e.target.files[0];
    if (f) selectFile(f);
  }

  function selectFile(f) {
    // basic extension check
    if (!/\.xlsx?$|\.csv$|\.ods$/i.test(f.name)) {
      generalError = "Unsupported file type. Use .xlsx, .xls, .ods or .csv.";
      state = "error";
      return;
    }
    file = f;
    fileName = f.name;
    fileId = null;
    generalError = "";
    validateErrors = [];
    previewRows = [];
    importProgress = 0;
    validationProgress = 0;
    importResult = null;
    // Auto upload on file select
    uploadFile();
  }

  function downloadTemplate() {
    if (!selectedConfig) {
      generalError = "Please select an entity first";
      state = "error";
      return;
    }
    const url = API_URL + "ImportController/downloadTemplate?config=" + encodeURIComponent(selectedConfig);
    window.open(url, '_blank');
  }

  async function loadConfigs() {
    try {
      const res = await fetch(API_URL + "ImportController/listConfigs", {
        method: "GET",
        credentials: "include", // 🔑 IMPORTANT
      });

      if (!res.ok) {
        throw new Error(await res.text());
      }

      const data = await res.json();

      if (data.success) {
        configList = data.configs;
      } else {
        console.error("Config load failed:", data.message);
      }
    } catch (err) {
      console.error("Failed to load configs", err);
    }
  }

  async function uploadFile() {
    if (!file) {
      generalError = "Select a file first";
      state = "error";
      return;
    }
    state = "uploading";
    generalError = "";
    try {
      const fd = new FormData();
      fd.append("file", file);
      fd.append("client", client);
      const res = await fetch(API_URL + "ImportController/uploadFile", {
        credentials: "include",
        method: "POST",
        body: fd,
      });
      if (!res.ok) throw new Error(await res.text());
      const data = await res.json();
      if(data.success){
        fileId = data.fileId;
        sessionStorage.setItem("fileId", fileId);
        fileName = data.filename || fileName;
        state = "uploaded";
      }else{
        generalError = data.error;
        state = "error";
      }
      
    } catch (err) {
      console.error(err);
      generalError = "Upload failed: " + (err.message || err);
      state = "error";
    }
  }

  async function validate() {
    if (!fileId && !file) {
      generalError = "Upload the file first";
      state = "error";
      return;
    }
    // If user never pressed upload, try uploading first
    if (!fileId && file) await uploadFile();

    if (!fileId) return;
    if (!selectedConfig) {
      generalError = "Please select config template";
      state = "error";
      return;
    }
    state = "validating";
    validateErrors = [];
    previewRows = [];
    validationProgress = 0; // ✅ Reset progress
    validationStartTime = Date.now();
    try {
      const res = await fetch(API_URL + "ImportController/readExcel", {
        headers: { "Content-Type": "application/json" },
        credentials: "include",
        method: "POST",
        body: JSON.stringify({
          configFile: selectedConfig,
          excelFile: fileName,
          client: client,
          fileId: fileId,
        }),
      });

      // console.log("✅ Fetch completed, status:", res.status, res.statusText);

      if (!res.ok) {
        const errorText = await res.text();
        // console.error("❌ Response not OK:", errorText);
        throw new Error(errorText);
      }

      // console.log("📄 Parsing JSON response...");
      const data = await res.json();

      // console.log("📥 readExcel response:", data);

      if (data.status === "processing") {
        // console.log("🔄 Status is 'processing', starting polling with taskId:", data.taskId);
        pollValidationStatus(data.taskId);
      } else {
        generalError = data.message || "Validation failed";
        state = "error";
        return;
      }
    } catch (err) {
      console.error("❌ VALIDATION ERROR:", err);
      console.error("Error message:", err.message);
      console.error("Error stack:", err.stack);
      generalError = "Validation failed: " + (err.message || err);
      state = "error";
    }
  }

  async function pollValidationStatusold(taskId) {
    console.log("🔍 pollValidationStatus CALLED with taskId:", taskId);

    const startTime = Date.now();
    const MAX_TIME = 15 * 60 * 1000; // 15 minutes

    while (tries < maxTries) {
      // Start with 500ms, gradually increase to max 3 seconds
      const delay = Math.min(500 + tries * 100, 3000);
      await new Promise((r) => setTimeout(r, delay));

      // console.log(`📡 Polling attempt ${tries + 1}/${maxTries} for taskId: ${taskId}`);

      try {
        const res = await fetch(
          API_URL +
            `ImportController/import_status?taskId=${encodeURIComponent(taskId)}`,
          {
            credentials: "include", // 🔑 VERY IMPORTANT
          },
        );
        if (!res.ok) throw new Error(await res.text());
        const data = await res.json();

        console.log("📊 Polling response:", data);

        // ✅ Update progress in real-time
        if (typeof data.progress === "number") {
          validationProgress = data.progress;
        }

        if (data.status === "validated" || data.status === "failed") {
          // console.log("✅ Validation completed with status:", data.status);
          taskId = "";
          if (data.status === "validated") {
            state = "ready";
            previewRows = data.data.message || [];
            allrows = data.allrows || [];
            skippedRows = data.data.SkipedRows || [];
            validationProgress = 100; // Ensure it shows 100%
          } else {
            state = "validated";
            if (!data.data || Object.keys(data.data).length === 0) {
              generalError = data.message || "Validation failed";
              state = "error";
              return;
            }
            validateErrMessage = data.data.ErrorSummary || "";
            const err = data.data.ErrorRows || [];
            const skipped = data.data.SkipedRows || [];
            const child = data.data.ChildErrorRows || [];
            if (!validateErrMessage) {
              validateErrors = [...err, ...skipped, ...child];
            }
          }
          return;
        }
      } catch (err) {
        console.error("❌ polling error", err);
      }
      tries++;
    }
    console.error(
      "⏱️ Validation polling timed out after",
      maxTries,
      "attempts",
    );
    generalError = "Validation polling timed out.";
    state = "error";
  }

  async function pollValidationStatus(taskId) {
    console.log("🔍 pollValidationStatus CALLED:", taskId);

    const startTime = Date.now();
    const MAX_TIME = 30 * 60 * 1000; // 15 minutes

    while (true) {
      // ⏳ Stop only if REALLY stuck
      if (Date.now() - startTime > MAX_TIME) {
        console.error("⏱️ Validation polling timed out (hard limit)");
        generalError = "Validation is taking too long. Please try again.";
        state = "error";
        return;
      }

      await new Promise((r) => setTimeout(r, 1000)); // fixed 2s polling

      try {
        const res = await fetch(
          API_URL +
            `ImportController/import_status?taskId=${encodeURIComponent(taskId)}&ts=${Date.now()}`,
          { credentials: "include",cache: "no-store" },
        );

        if (!res.ok) throw new Error(await res.text());
        const data = await res.json();

        console.log("📊 Polling response:", data);

        // ✅ Proper progress update
        if (typeof data.progress === "number") {
          validationProgress = Math.max(validationProgress, data.progress);
        } else if (typeof data.validated === "number" && data.validated > 0) {
          // Backend sends progress as string "Streaming..." during processing
          // Use validated row count to estimate progress
          if (typeof data.total === "number" && data.total > 0) {
            validationProgress = Math.min(95, Math.round((data.validated / data.total) * 100));
          } else {
            // Total not yet known — show incremental progress up to 90%
            validationProgress = Math.min(90, Math.max(validationProgress, Math.round(data.validated / 10)));
          }
        }

        // ✅ Completion check
        if (data.status === "validated" || data.status === "failed" || data.status === "has_errors") {
          taskId = "";

          if (data.status === "validated") {
            previewRows = data.data.message || [];
            allrows = data.allrows || [];
            skippedRows = data.data.SkipedRows || [];
            validationProgress = 100;
            // Auto-trigger backup before enabling import
            backupTablesBeforeImport();
          } else if (data.status === "has_errors") {
            // Errors exist but valid rows are available — let user choose
            allrows = data.allrows || [];
            validRowCount = data.validRowCount || 0;
            errorRowCount = data.errorRowCount || 0;
            skippedRows = data.data.SkipedRows || [];
            validateErrMessage = data.data.ErrorSummary || "";
            const err = data.data.ErrorRows || [];
            const skipped = data.data.SkipedRows || [];
            const child = data.data.ChildErrorRows || [];
            if (!validateErrMessage) {
              validateErrors = [...err, ...skipped, ...child].sort((a, b) => a.row - b.row);
            }
            validationProgress = 100;
            state = "has_errors";
          } else {
            state = "validated";
            if (!data.data || Object.keys(data.data).length === 0) {
              generalError = data.message || "Validation failed";
              state = "error";
              return;
            }
            validateErrMessage = data.data.ErrorSummary || "";
            const err = data.data.ErrorRows || [];
            const skipped = data.data.SkipedRows || [];
            const child = data.data.ChildErrorRows || [];
            if (!validateErrMessage) {
              validateErrors = [...err, ...skipped, ...child].sort((a, b) => a.row - b.row);
            }
          }
          return;
        }
      } catch (err) {
        console.error("❌ polling error", err);
      }
    }
  }

  function skipErrorsAndImport() {
    // User chose to skip error rows — proceed with valid rows only
    state = "backing_up";
    backupTablesBeforeImport();
  }

  async function backupTablesBeforeImport() {
    state = "backing_up";
    backupStatus = "Backing up entity tables to Excel...";
    backupTables = [];
    backupCompleted = false;
    backupDownloadUrl = '';
    sqlDownloadUrl = '';
    try {
      const entity = (selectedConfig || '').replace('.json', '');
      const res = await fetch(API_URL + "ImportController/backupEntityTables", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        credentials: "include",
        body: JSON.stringify({ client, entity }),
      });
      const data = await res.json();
      if (data.success) {
        backupTables = data.tables || [];
        backupDownloadUrl = data.downloadUrl || '';
        sqlDownloadUrl = data.sqlDownloadUrl || '';
        backupStatus = "✅ " + (data.message || "Backup completed");
        backupCompleted = true;
        state = "ready";
      } else {
        backupStatus = "⚠️ Backup failed: " + (data.message || "Unknown error");
        backupCompleted = true; // Still allow import
        state = "ready";
      }
    } catch (err) {
      console.error("Backup error:", err);
      backupStatus = "⚠️ Backup failed: " + (err.message || err);
      backupCompleted = true; // Still allow import
      state = "ready";
    }
  }

  function startImport() {
    if (!fileId && !file) {
      generalError = "Upload and validate first";
      state = "error";
      return;
    }
    showConfirmModal = true;
  }

  async function confirmImport() {
    showConfirmModal = false;
    state = "importing";
    importProgress = 0;
    importResult = null;
    generalError = "";
    try {
      const res = await fetch(API_URL + "ImportController/insertAllRows", {
        headers: { "Content-Type": "application/json" },
        credentials: "include",
        method: "POST",
        body: JSON.stringify({
          configFile: selectedConfig,
          allrows: allrows,
          fileId: fileId,
          client: client,
        }),
      });
      if (!res.ok) throw new Error(await res.text());
      const data = await res.json();
      // If backend returns immediate status 'processing', poll
      if (data.status === "processing") {
        await pollImportStatus(data.taskId || data.fileId);
      } else {
        finishImport(data);
      }
    } catch (err) {
      console.error(err);
      generalError = "Import failed: " + (err.message || err);
      state = "error";
    }
  }

  async function pollImportStatusold(taskId) {
    console.log("🔍 pollImportStatus CALLED with taskId:", taskId);
    let originalTaskId = taskId;
    let tries = 0;
    let maxTries = 120; // Increased
    while (tries < maxTries) {
      // Faster polling: start 500ms, max 3s
      const delay = Math.min(500 + tries * 100, 3000);
      await new Promise((r) => setTimeout(r, delay));
      try {
        const res = await fetch(
          API_URL +
            `ImportController/import_status?taskId=${encodeURIComponent(originalTaskId)}`,
          {
            credentials: "include", // 🔑 VERY IMPORTANT
          },
        );
        if (!res.ok) throw new Error(await res.text());
        const data = await res.json();
        console.log("📊 Import Polling response:", data);
        importProgress = data.progress || importProgress;
        if (data.status === "completed" || data.status === "failed") {
          console.log("✅ Import completed with status:", data.status);
          finishImport(data);

          // Cleanup: clear temp file AND archive file if completed
          await fetch(API_URL + "ImportController/cleanupResources", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            credentials: "include",
            body: JSON.stringify({
              taskId: originalTaskId,
              client: client,
              file: fileName,
              action: data.status === "completed" ? "archive" : null, // Archive only on success
            }),
          });

          return;
        }
      } catch (err) {
        console.error("❌ polling error", err);
      }
      tries++;
    }
    console.error("⏱️ Import polling timed out after", maxTries, "attempts");
    generalError = "Import polling timed out.";
    state = "error";
  }

  async function pollImportStatus(taskId) {
  console.log("🔍 pollImportStatus CALLED:", taskId);

  const startTime = Date.now();
  const MAX_TIME = 30 * 60 * 1000; // ⏱️ 30 minutes (imports are heavier)

  while (true) {
    // ⛔ Hard safety cutoff
    if (Date.now() - startTime > MAX_TIME) {
      console.error("⏱️ Import polling timed out (hard limit)");
      generalError = "Import is taking too long. Please try again.";
      state = "error";
      return;
    }

    // ⏳ Poll every 3 seconds
    await new Promise((r) => setTimeout(r, 3000));

    try {
      const res = await fetch(
        API_URL +
          `ImportController/import_status?taskId=${encodeURIComponent(taskId)}`,
        { credentials: "include" }
      );

      if (!res.ok) throw new Error(await res.text());
      const data = await res.json();

      console.log("📊 Import Polling response:", data);

      // ✅ Correct progress handling
      if (typeof data.progress === "number") {
        importProgress = data.progress;
      }

      // ✅ Completion
      if (data.status === "completed" || data.status === "failed" || data.status === "error") {
        console.log("✅ Import completed with status:", data.status);

        finishImport(data);

        // 🧹 Cleanup AFTER completion
        await fetch(API_URL + "ImportController/cleanupResources", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          credentials: "include",
          body: JSON.stringify({
            taskId,
            client,
            file: fileName,
            action: data.status === "completed" ? "archive" : null,
          }),
        });

        return;
      }
    } catch (err) {
      console.error("❌ Import polling error", err);
    }
  }
}


  function finishImport(data) {
    importResult = data;
    if (data.status === "completed") {
      state = "done";
      importProgress = 100;
      // resetAll();
    } else {
      state = "error";
      generalError = data.message || "Import failed";
    }
  }

  async function resetAll() {
    // If we have a file and it wasn't successfully imported (state 'done'), delete it from server
    if (fileId && fileName && state !== "done") {
      try {
        await fetch(API_URL + "ImportController/cleanupResources", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          credentials: "include",
          body: JSON.stringify({
            client: client,
            file: fileName,
            action: "delete",
          }),
        });
      } catch (e) {
        console.error("Cleanup failed", e);
      }
    }

    const input = document.getElementById("fileInput");
    if (input) input.value = "";

    file = null;
    fileName = "";
    fileId = null;
    state = "idle";
    validateErrors = [];
    previewRows = [];
    importProgress = 0;
    validationProgress = 0;
    importResult = null;
    generalError = "";
    selectedConfig = "";
  }

  async function generateReport() {
    resetAll();
    const fileId = sessionStorage.getItem("fileId");
    dispatch("success", { fileId: fileId });
  }

  async function disconnect() {
    if (!confirm("Are you sure you want to disconnect?")) return;
    resetAll();
    dispatch("disconnect");
  }
</script>

<svelte:window on:click={handleClickOutside} />

<div class="container">
  <div class="header">
    <div>
      <h1>PortMax - {client}</h1>
      {#if dbName}
        <span class="db-badge">🗄 Destination DB: {dbName}</span>
      {/if}
    </div>

    <div style="display:flex; gap:10px; align-items:center;">
      <button
        class="btn-manual"
        on:click={() => window.open('/port_max/user_manual.html', '_blank')}
      >
        📖 User Manual
      </button>
      <button
        class="btn-disconnect"
        disabled={state === "uploading" ||
          state === "ready" ||
          state === "validating" ||
          state === "importing"}
        on:click={disconnect}
      >
        Disconnect / Logout
      </button>
    </div>
  </div>

  <!-- Disclaimer Banner -->
  <div class="disclaimer">
    <div class="disclaimer-icon">⚠️</div>
    <div>
      <strong>Disclaimer:</strong>
      Before proceeding, please ensure that all configuration and setup has been completed as per the client's import requirements.
      After import, for any additional changes or modifications to the imported data, please contact the developer.
    </div>
  </div>

  <!-- Select Entity -->
  <div class="card">
    <div style="display:flex; gap:12px; align-items:center">
      <div class="dropdown-wrapper" bind:this={dropdownRef} style="flex:1">
        <button
          class="dropdown-trigger"
          class:active={dropdownOpen}
          disabled={state === "validating" || state === "importing" || state === "uploading"}
          on:click|stopPropagation={() => { dropdownOpen = !dropdownOpen; searchQuery = ""; }}
        >
          <span>{selectedConfigName || "📁 Choose what to import..."}</span>
          <svg width="12" height="12" viewBox="0 0 12 12" fill="none" style="flex-shrink:0">
            <path d="M3 4.5L6 7.5L9 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </button>

        {#if dropdownOpen}
          <div class="dropdown-menu">
            <div class="dropdown-search">
              <input
                type="text"
                placeholder="🔍 Search entity..."
                bind:value={searchQuery}
                on:click|stopPropagation
                autofocus
              />
            </div>
            <div class="dropdown-list">
              {#each filteredConfigs as c}
                <button
                  class="dropdown-item"
                  class:selected={selectedConfig === c.file}
                  on:click|stopPropagation={() => selectEntity(c)}
                >
                  {c.name}
                  {#if selectedConfig === c.file}
                    <span style="margin-left:auto; color:#4ade80">✓</span>
                  {/if}
                </button>
              {:else}
                <div class="dropdown-empty">No matches found</div>
              {/each}
            </div>
          </div>
        {/if}
      </div>
      <button
        class="btn btn-template"
        on:click={downloadTemplate}
        disabled={!selectedConfig}
      >📥 Download Template</button>
      <button
        class="btn btn-ghost"
        disabled={state === "validating" || state === "importing"}
        on:click={resetAll}
      >↺ Reset</button>
    </div>
  </div>

  <!-- Step 2: Upload File (shown only after entity selected) -->
  {#if selectedConfig}
  <div
    class="card"
    style="margin-top:18px"
    on:drop|preventDefault={onDrop}
    on:dragover|preventDefault={() => (dragOver = true)}
    on:dragleave|preventDefault={() => (dragOver = false)}
  >
    <div style="margin-bottom:12px">
      <div style="font-size:16px; font-weight:600">Upload Data File</div>
    </div>
    <div
      class="upload-area"
      class:dragover={dragOver}
      class:uploading={state === "uploading" || state === "validating"}
      on:click={() => {
        if (state !== "uploading" && state !== "validating" && state !== "importing")
          document.getElementById("fileInput").click();
      }}
    >
      <input
        id="fileInput"
        type="file"
        accept=".xlsx,.xls,.csv,.ods"
        on:change={onFileChange}
        style="display:none"
      />
      {#if state === "uploading"}
        <div class="spinner"></div>
        <div style="font-size:15px; font-weight:600; margin-top:8px">Uploading {fileName}...</div>
      {:else}
        <svg
          width="48"
          height="48"
          viewBox="0 0 24 24"
          fill="none"
          style="vertical-align:middle; margin-bottom:6px"
          ><path
            d="M12 3v10"
            stroke="#9dd6ff"
            stroke-width="1.6"
            stroke-linecap="round"
            stroke-linejoin="round"
          ></path><path
            d="M8 7l4-4 4 4"
            stroke="#9dd6ff"
            stroke-width="1.6"
            stroke-linecap="round"
            stroke-linejoin="round"
          ></path><rect
            x="3"
            y="13"
            width="18"
            height="7"
            rx="2"
            stroke="#7fb5ff"
            stroke-width="1.2"
          /></svg
        >
        <div style="font-size:16px; font-weight:600">
          {fileName || "Click or drop to upload Excel (.xlsx/.csv/.ods)"}
        </div>
        <div class="muted small" style="margin-top:6px">File will auto-upload on selection</div>
      {/if}
    </div>

    {#if generalError && state === "error"}
      <div style="margin-top:12px" class="errors">⚠ {generalError}</div>
    {/if}

    {#if state === "uploaded"}
      <div class="upload-success" style="margin-top:12px">
        ✅ <strong>{fileName}</strong> uploaded successfully — Ready to validate.
      </div>
    {/if}
  </div>
  {/if}

  <!-- Validate Card -->
  {#if selectedConfig && (state === "uploaded" || state === "validating" || state === "validated" || state === "has_errors" || state === "ready" || state === "backing_up" || state === "importing" || state === "done")}
  <div class="card" style="margin-top:18px">
    <div
      style="display:flex; justify-content:space-between; align-items:center"
    >
      <div>
        <div style="font-size:16px; font-weight:600">Validate Data</div>
        <div class="muted small">
          Checks: required columns, types, duplicates, config rules
        </div>
      </div>
      {#if state === "uploaded"}
        <button
          class="btn btn-primary"
          on:click={validate}
        >Start Validation</button>
      {:else if state === "ready" || state === "backing_up" || state === "importing" || state === "done"}
        <span style="color:#4ade80; font-weight:600; font-size:14px">✅ Passed</span>
      {/if}
    </div>

    <!-- ✅ Validation Progress Bar -->
    {#if state === "validating"}
      <div style="margin-top:16px">
        <div class="progress-wrap">
          <div class="progress">
            <div style="width:{validationProgress}%"></div>
          </div>
          <div class="small muted" style="margin-top:8px">
            Validating... {validationProgress}%
          </div>
        </div>
      </div>
    {/if}

    {#if state === "validated"}
      <!-- <div style="margin-top:12px" class="errors"> -->
      <div class="errors-wrap">
        <div
          style="margin-top:12px; max-height:300px; overflow-y:auto"
          class="errors"
        >
          <strong>Validation failed</strong>
          <ul style="margin-top:8px; padding-left:18px">
            {#if validateErrMessage}
              <li>
                {validateErrMessage}
              </li>
            {/if}
            {#if validateErrors.length > 0}
              {#each validateErrors.slice(0, 100) as e}
                <li>
                  Row {e.row} — {e.field || "General"}:
                  <ul>
                    {#each e.message as m}
                      <li>{@html m}</li>
                    {/each}
                  </ul>
                </li>
              {/each}
              {#if validateErrors.length > 100}
                <li style="color:#ffb3b3; font-weight:600; margin-top:10px">
                  ... and {validateErrors.length - 100} more errors. Please download
                  the error file for the full list.
                </li>
              {/if}
            {/if}
          </ul>
        </div>

        <div style="margin-top:12px; display:flex; gap:8px">
          <button
            class="btn btn-primary"
            on:click={() =>
              window.open(
                API_URL +
                  "ImportController/download?file=" +
                  fileName +
                  "&client=" +
                  client,
              )}>Download Errors</button
          >
          <p>
            <strong>Note: </strong>You can download the file and view the
            row-wise errors in the Error column.
          </p>
        </div>
      </div>
    {/if}

    <!-- Has Errors — User chooses: Skip errors or Fix -->
    {#if state === "has_errors"}
      <div class="errors-wrap">
        <div
          style="margin-top:12px; max-height:300px; overflow-y:auto"
          class="errors"
        >
          <strong>⚠️ Validation found errors</strong>
          <div style="margin-top:8px; font-size:14px; color:#ffd580;">
            <strong>{errorRowCount}</strong> row(s) have errors · <strong>{validRowCount}</strong> row(s) are valid and ready to import
          </div>
          <ul style="margin-top:8px; padding-left:18px">
            {#if validateErrMessage}
              <li>{validateErrMessage}</li>
            {/if}
            {#if validateErrors.length > 0}
              {#each validateErrors.slice(0, 50) as e}
                <li>
                  Row {e.row} — {e.field || "General"}:
                  <ul>
                    {#each e.message as m}
                      <li>{@html m}</li>
                    {/each}
                  </ul>
                </li>
              {/each}
              {#if validateErrors.length > 50}
                <li style="color:#ffb3b3; font-weight:600; margin-top:10px">
                  ... and {validateErrors.length - 50} more errors. Download the file for full list.
                </li>
              {/if}
            {/if}
          </ul>
        </div>

        <div style="margin-top:14px; display:flex; gap:12px; align-items:center; flex-wrap:wrap">
          {#if validRowCount > 0}
            <button
              style="background:linear-gradient(135deg,#2ecc71,#27ae60); color:#fff; border:none; padding:12px 22px; border-radius:10px; font-size:14px; font-weight:700; cursor:pointer; box-shadow:0 4px 16px rgba(46,204,113,0.4); transition:all 0.2s;"
              on:click={skipErrorsAndImport}
            >✅ Skip Errors & Import {validRowCount} Valid Row(s)</button>
          {/if}
          <button
            style="background:linear-gradient(135deg,#e67e22,#d35400); color:#fff; border:none; padding:12px 22px; border-radius:10px; font-size:14px; font-weight:700; cursor:pointer; box-shadow:0 4px 16px rgba(230,126,34,0.4); transition:all 0.2s;"
            on:click={() => { state = "validated"; }}
          >🔧 Fix Errors First</button>
          <button
            style="background:linear-gradient(135deg,#3498db,#2980b9); color:#fff; border:none; padding:12px 22px; border-radius:10px; font-size:14px; font-weight:700; cursor:pointer; box-shadow:0 4px 16px rgba(52,152,219,0.4); transition:all 0.2s;"
            on:click={() => window.open(API_URL + 'ImportController/download?file=' + fileName + '&client=' + client)}
          >📥 Download Errors</button>
        </div>
      </div>
    {/if}

    <!-- Backup Card -->
    {#if state === "backing_up"}
      <div class="card" style="margin-top:14px; border-left: 3px solid #FFA726;">
        <div style="display:flex; align-items:center; gap:10px">
          <div style="font-size:20px; animation: pulse 1s infinite;">⏳</div>
          <div>
            <div style="font-size:14px; font-weight:600">🔄 {backupStatus}</div>
            <div class="muted">Creating .sql backup of entity tables before import...</div>
          </div>
        </div>
      </div>
    {/if}

    {#if (state === "ready" || state === "importing" || state == "done") && backupCompleted}
      <!-- Backup Result -->
      {#if backupTables.length > 0}
        <div class="card" style="margin-top:14px; border-left: 3px solid #66BB6A;">
          <div style="display:flex; justify-content:space-between; align-items:center">
            <div style="font-size:14px; font-weight:600">{backupStatus}</div>
            {#if backupDownloadUrl}
              <a
                href={API_URL + backupDownloadUrl}
                download
                class="btn btn-template"
                style="text-decoration:none; font-size:12px; padding:8px 14px;"
              >📥 Excel Backup</a>
            {/if}
            {#if sqlDownloadUrl}
              <a
                href={API_URL + sqlDownloadUrl}
                download
                class="btn btn-ghost"
                style="text-decoration:none; font-size:12px; padding:8px 14px;"
              >🗃️ SQL Backup</a>
            {/if}
          </div>
          <div class="muted" style="margin-top:6px;">
            {#each backupTables as t}
              <span style="margin-right:14px;">📁 <strong>{t.table}</strong> ({t.rows.toLocaleString('en-IN')} rows)</span>
            {/each}
          </div>
        </div>
      {:else if backupStatus.startsWith('⚠️')}
        <div class="card" style="margin-top:14px; border-left: 3px solid #FF7043;">
          <div style="font-size:14px;">{backupStatus}</div>
          <div class="muted">Import can still proceed.</div>
        </div>
      {/if}
    {/if}

    {#if (state === "ready" || state == "done") && allrows.length > 0}
      <div style="margin-top:16px; text-align:center">
        <button
          class="btn-import"
          disabled={!file || state == "done"}
          on:click={startImport}
        >⚡ Import {allrows.length} Row(s) Now</button>
        <div class="muted small" style="margin-top:8px">This will insert directly into the database</div>
      </div>
    {/if}
  </div>
  {/if}

  <!-- Import Card -->
{#if state === "importing" || state == "done"}
  <div class="card" style="margin-top:18px">
    <div
      style="display:flex; justify-content:space-between; align-items:center"
    >
      <div>
        <div style="font-size:16px; font-weight:600">Import</div>
        <div class="muted small">
          Server-side insert. Shows progress & errors.
        </div>
      </div>
    </div>

    <div style="margin-top:12px">
      <div class="progress-wrap">
        <div class="progress"><div style="width:{importProgress}%"></div></div>
        <div class="small muted" style="margin-top:8px">{importProgress}%</div>
      </div>

      {#if state === "importing"}
        <div style="margin-top:12px" class="muted small">
          Importing... please wait.
        </div>
      {/if}
      

      {#if state === "done" && importResult}
        <div
          style="margin-top:12px; background: rgba(14,203,129,0.07); border:1px solid rgba(14,203,129,0.14); color:#bff3dc; padding:12px; border-radius:8px"
        >
          ✅ {importResult.inserted || 0} rows(s) inserted successfully.
        </div>

        <div
          style="margin-top:16px; border-top:1px solid rgba(255,255,255,0.05); padding-top:16px"
        >
          <div style="font-size:15px; font-weight:600; margin-bottom:8px">
            Summary Report
          </div>

          <!-- Step 1: Generate -->
          <div style="margin-bottom:12px">
            <button class="btn btn-primary" on:click={generateReport}>
              {!reportGenerated ? "Generate Report" : "Download Report"}
            </button>
          </div>
        </div>
      {/if}

      {#if state === "error" && importResult && importResult.errors && importResult.errors.length}
        <div style="margin-top:12px" class="errors">
          <strong>Import errors:</strong>
          <ul style="margin-top:8px; padding-left:18px">
            {#each importResult.errors.slice(0, 100) as er}
              <li>Row {er.row}: {er.message}</li>
            {/each}
            {#if importResult.errors.length > 100}
              <li style="color:#ffb3b3; font-weight:600; margin-top:10px">
                ... and {importResult.errors.length - 100} more errors.
              </li>
            {/if}
          </ul>
        </div>
      {/if}
    </div>
  </div>
{/if}
</div>

<!-- Import Confirmation Modal -->
{#if showConfirmModal}
<div class="modal-overlay" on:click={() => showConfirmModal = false}>
  <div class="modal-box" on:click|stopPropagation>
    <div class="modal-icon">⚠️</div>
    <div class="modal-title">Confirm Import</div>
    <div class="modal-body">
      You are about to import <strong>{allrows.length} row(s)</strong> into
      <strong>{selectedConfigName || 'database'}</strong> table.
    </div>
    <div class="modal-warning">
      This will insert data directly into the production database. This action cannot be undone.
    </div>
    <div class="modal-actions">
      <button class="modal-btn modal-btn-cancel" on:click={() => showConfirmModal = false}>Cancel</button>
      <button class="modal-btn modal-btn-confirm" on:click={confirmImport}>Yes, Import Now</button>
    </div>
  </div>
</div>
{/if}

<style>
  :global(body) {
    background: #0f1724; /* dark blue-gray */
    color: #e6eef8;
    font-family:
      Inter,
      system-ui,
      -apple-system,
      "Segoe UI",
      Roboto,
      "Helvetica Neue",
      Arial;
  }

  .card {
    background: linear-gradient(
      180deg,
      rgba(255, 255, 255, 0.03),
      rgba(255, 255, 255, 0.02)
    );
    border: 1px solid rgba(255, 255, 255, 0.04);
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 6px 18px rgba(2, 6, 23, 0.6);
  }

  .container {
    max-width: 920px;
    margin: 32px auto;
    padding: 20px;
  }

  h1 {
    font-size: 28px;
    margin-bottom: 18px;
    color: #f8fafc;
  }

  .upload-area {
    border: 2px dashed rgba(255, 255, 255, 0.06);
    border-radius: 10px;
    padding: 20px 40px;
    text-align: center;
    cursor: pointer;
    transition: all 0.25s ease;
  }

  .upload-area:hover {
    border-color: rgba(99, 102, 241, 0.4);
    background: rgba(99, 102, 241, 0.03);
  }

  .upload-area.dragover {
    background: rgba(99, 102, 241, 0.08);
    border-color: #818cf8;
    border-style: solid;
    box-shadow: 0 0 20px rgba(99, 102, 241, 0.25), inset 0 0 20px rgba(99, 102, 241, 0.05);
    transform: scale(1.01);
  }

  .upload-area.uploading {
    pointer-events: none;
    opacity: 0.7;
    border-color: rgba(59, 130, 246, 0.5);
  }

  .spinner {
    width: 36px;
    height: 36px;
    border: 3px solid rgba(255, 255, 255, 0.1);
    border-top: 3px solid #60a5fa;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
    margin: 0 auto;
  }

  @keyframes spin {
    to { transform: rotate(360deg); }
  }

  .btn-template {
    background: rgba(34, 197, 94, 0.12);
    color: #4ade80;
    border: 1px solid rgba(34, 197, 94, 0.35);
    padding: 12px 18px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.25s ease;
    white-space: nowrap;
  }

  .btn-template:hover {
    background: rgba(34, 197, 94, 0.22);
    border-color: #4ade80;
    transform: translateY(-1px);
  }

  .btn-template:disabled {
    opacity: 0.4;
    cursor: not-allowed;
    transform: none;
  }

  .btn {
    padding: 12px 18px;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    border: none;
  }

  .btn-primary {
    background: linear-gradient(90deg, #2563eb, #4f46e5);
    color: white;
  }

  .btn-ghost {
    background: transparent;
    border: 1px solid rgba(255, 255, 255, 0.06);
    color: #dbeafe;
  }

  .btn-import {
    width: 100%;
    padding: 16px 24px;
    border-radius: 12px;
    font-weight: 700;
    font-size: 16px;
    cursor: pointer;
    border: none;
    color: white;
    background: linear-gradient(135deg, #f59e0b, #ef4444);
    box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
    transition: all 0.3s ease;
    letter-spacing: 0.5px;
  }

  .btn-import:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 6px 25px rgba(245, 158, 11, 0.5);
  }

  .btn-import:active:not(:disabled) {
    transform: translateY(0);
  }

  .btn-import:disabled {
    opacity: 0.4;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
  }

  button:disabled,
  .btn:disabled {
    opacity: 0.45;
    cursor: not-allowed;
    pointer-events: none;
  }

  .muted {
    color: #9aa8bf;
  }

  .row {
    display: flex;
    gap: 16px;
    align-items: center;
  }
  .col {
    flex: 1;
  }

  .errors {
    background: rgba(255, 40, 70, 0.06);
    border: 1px solid rgba(255, 40, 70, 0.12);
    color: #ffd8d8;
    padding: 12px;
    border-radius: 8px;
  }

  .progress-wrap {
    background: rgba(255, 255, 255, 0.03);
    padding: 8px;
    border-radius: 8px;
  }
  .progress {
    height: 12px;
    background: rgba(255, 255, 255, 0.04);
    border-radius: 8px;
    overflow: hidden;
  }
  .progress > div {
    height: 100%;
    background: linear-gradient(90deg, #06b6d4, #60a5fa);
    width: 0%;
  }

  table {
    width: 100%;
    border-collapse: collapse;
  }
  th,
  td {
    padding: 8px 10px;
    text-align: left;
    font-size: 13px;
    color: #e6eef8;
  }
  th {
    color: #9fb2d9;
    font-weight: 600;
  }
  tr:nth-child(even) td {
    background: rgba(255, 255, 255, 0.01);
  }

  .small {
    font-size: 12px;
  }

  .kbd {
    background: rgba(255, 255, 255, 0.03);
    border-radius: 6px;
    padding: 6px 8px;
    font-size: 12px;
  }

  /* Searchable Dropdown */
  .dropdown-wrapper {
    position: relative;
  }

  .dropdown-trigger {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    background: rgba(255, 255, 255, 0.05);
    color: #e6eef8;
    padding: 12px 14px;
    border-radius: 10px;
    border: 1px solid rgba(255, 255, 255, 0.08);
    font-size: 15px;
    font-weight: 500;
    cursor: pointer;
    outline: none;
    transition: all 0.2s ease;
    text-align: left;
  }

  .dropdown-trigger:hover {
    border-color: rgba(99, 102, 241, 0.6);
    background: rgba(255, 255, 255, 0.07);
  }

  .dropdown-trigger.active {
    border-color: #60a5fa;
    box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.25);
  }

  .dropdown-trigger:disabled {
    opacity: 0.45;
    cursor: not-allowed;
  }

  .dropdown-menu {
    position: absolute;
    top: calc(100% + 6px);
    left: 0;
    right: 0;
    background: #1a2332;
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 10px;
    box-shadow: 0 12px 36px rgba(0, 0, 0, 0.5);
    z-index: 100;
    overflow: hidden;
    animation: dropIn 0.15s ease;
  }

  @keyframes dropIn {
    from { opacity: 0; transform: translateY(-6px); }
    to { opacity: 1; transform: translateY(0); }
  }

  .dropdown-search {
    padding: 8px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.06);
  }

  .dropdown-search input {
    width: 100%;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    color: #e6eef8;
    padding: 10px 12px;
    border-radius: 8px;
    font-size: 14px;
    outline: none;
    box-sizing: border-box;
  }

  .dropdown-search input:focus {
    border-color: #60a5fa;
  }

  .dropdown-list {
    max-height: 240px;
    overflow-y: auto;
    padding: 4px;
  }

  .dropdown-item {
    display: flex;
    align-items: center;
    width: 100%;
    padding: 10px 12px;
    background: transparent;
    border: none;
    color: #c8d6e5;
    font-size: 14px;
    cursor: pointer;
    border-radius: 6px;
    text-align: left;
    transition: background 0.15s;
  }

  .dropdown-item:hover {
    background: rgba(99, 102, 241, 0.15);
    color: #fff;
  }

  .dropdown-item.selected {
    background: rgba(34, 197, 94, 0.1);
    color: #4ade80;
    font-weight: 600;
  }

  .dropdown-empty {
    padding: 16px;
    text-align: center;
    color: #6b7a8d;
    font-size: 13px;
  }

  .dropdown-list::-webkit-scrollbar {
    width: 5px;
  }
  .dropdown-list::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 4px;
  }

  .header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 13px;
  }

  .progress-wrap {
    width: 100%;
  }
  .progress {
    width: 100%;
    height: 10px;
    background: rgba(255, 255, 255, 0.08);
    border-radius: 6px;
    overflow: hidden;
  }
  .progress div {
    height: 100%;
    background: linear-gradient(90deg, #3b82f6, #60a5fa);
    border-radius: 6px;
    transition: width 0.4s ease;
  }

  .btn-manual {
    background: rgba(59, 130, 246, 0.1);
    color: #60a5fa;
    border: 1px solid rgba(59, 130, 246, 0.3);
    padding: 10px 18px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.25s ease;
  }
  .btn-manual:hover {
    background: rgba(59, 130, 246, 0.2);
    border-color: #60a5fa;
  }

  .upload-success {
    background: rgba(34, 197, 94, 0.08);
    border: 1px solid rgba(34, 197, 94, 0.25);
    color: #86efac;
    padding: 10px 14px;
    border-radius: 8px;
    font-size: 13px;
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

  .disclaimer {
    background: rgba(245, 158, 11, 0.08);
    border: 1px solid rgba(245, 158, 11, 0.3);
    border-left: 4px solid #f59e0b;
    border-radius: 8px;
    padding: 12px 16px;
    margin-bottom: 16px;
    display: flex;
    gap: 10px;
    align-items: flex-start;
    font-size: 13px;
    color: #fcd34d;
    line-height: 1.6;
  }
  .disclaimer-icon {
    font-size: 18px;
    flex-shrink: 0;
    margin-top: 1px;
  }
  .disclaimer strong {
    color: #fbbf24;
  }

  .db-badge {
    display: inline-block;
    margin-top: 4px;
    padding: 2px 10px;
    background: rgba(34, 197, 94, 0.12);
    border: 1px solid rgba(34, 197, 94, 0.3);
    border-radius: 12px;
    font-size: 12px;
    color: #4ade80;
    font-weight: 500;
  }

  /* Confirmation Modal */
  .modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.7);
    backdrop-filter: blur(6px);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    animation: fadeIn 0.2s ease;
  }

  @keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
  }

  .modal-box {
    background: linear-gradient(180deg, #1e293b, #0f172a);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 16px;
    padding: 32px;
    max-width: 440px;
    width: 90%;
    text-align: center;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
    animation: slideUp 0.25s ease;
  }

  @keyframes slideUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
  }

  .modal-icon {
    font-size: 48px;
    margin-bottom: 12px;
  }

  .modal-title {
    font-size: 20px;
    font-weight: 700;
    color: #f8fafc;
    margin-bottom: 12px;
  }

  .modal-body {
    font-size: 14px;
    color: #cbd5e1;
    line-height: 1.6;
    margin-bottom: 12px;
  }

  .modal-body strong {
    color: #60a5fa;
  }

  .modal-warning {
    font-size: 12px;
    color: #fbbf24;
    background: rgba(251, 191, 36, 0.08);
    border: 1px solid rgba(251, 191, 36, 0.15);
    border-radius: 8px;
    padding: 10px 14px;
    margin-bottom: 24px;
  }

  .modal-actions {
    display: flex;
    gap: 12px;
  }

  .modal-btn {
    flex: 1;
    padding: 12px 20px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    border: none;
    transition: all 0.2s ease;
  }

  .modal-btn-cancel {
    background: rgba(255, 255, 255, 0.06);
    color: #94a3b8;
    border: 1px solid rgba(255, 255, 255, 0.08);
  }

  .modal-btn-cancel:hover {
    background: rgba(255, 255, 255, 0.1);
    color: #e2e8f0;
  }

  .modal-btn-confirm {
    background: linear-gradient(135deg, #f59e0b, #ef4444);
    color: white;
    box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
  }

  .modal-btn-confirm:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(245, 158, 11, 0.5);
  }
</style>
