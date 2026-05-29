<script>
  import { onMount } from "svelte";
  // import ConnectDBPage from "$lib/ConnectDBPage.svelte";
  // import ConnectDBPage from "$lib/ConnectDBPage.svelte";
  import AuthPage from "$lib/AuthPage.svelte";
  import DataMigration from "$lib/DataMigration.svelte";
  import ImportReport from "$lib/ImportReport.svelte";
  import { API_URL } from "$lib/config";

  // Step can be: 'db', 'login', 'migrate'
  let step = "login";
  let client = "";
  let fileId = "";
  let dbName = "";

  // Restore state on page refresh
  onMount(() => {
    const savedStep = sessionStorage.getItem("step");
    const savedClient = sessionStorage.getItem("client");
    const savedfileId = sessionStorage.getItem("fileId");

    if (savedStep) step = savedStep;
    if (savedClient) client = savedClient;
    if (savedfileId) fileId = savedfileId;
    dbName = sessionStorage.getItem('dbName') || '';
  });

  // Save state whenever it changes
  function saveState() {
    sessionStorage.setItem("step", step);
    sessionStorage.setItem("client", client);
    sessionStorage.setItem("fileId", fileId);
  }

  // Call this after DB connect
  function onDBConnect(selectedClient) {
    client = selectedClient;
    step = "login";
    saveState();
  }

  // Call this after login success
  function onLoginSuccess() {
    step = "migrate";
    saveState();
  }

  // Call this on disconnect
  async function onDisconnect() {
   
      const res = await fetch(API_URL + "AuthController/logout", {
        headers: { "Content-Type": "application/json" },
        credentials: "include",
        method: "POST",
      });

      const data = await res.json();

      if (data.success) {
        step = "login";
        client = "";
        sessionStorage.clear();
      } 
  }

  function onReport(e) {
    fileId = e.detail.fileId
    step = "report";
    saveState();
  }
</script>

{#if step === "db"}
  <ConnectDBPage on:connect={e => onDBConnect(e.detail.client)} />

{:else if step === "login"}
  <AuthPage 
    client={client} 
    on:success={(e) => {
      client = e.detail.client;
      dbName = e.detail.dbName || '';
      onLoginSuccess();
    }}
    on:disconnect={onDisconnect} />

{:else if step === "migrate"}
  <DataMigration 
    client={client}
    dbName={dbName}
    on:success={onReport}
    on:disconnect={onDisconnect} />

{:else if step === "report"}
  <ImportReport
    client={client}
    fileId={fileId}
    on:migration={onLoginSuccess}
    on:disconnect={onDisconnect} />
{/if}

<footer class="app-footer">
  <span>PortMax v2.1</span>
  <span class="sep">·</span>
  <span>© {new Date().getFullYear()} Logimax Technologies. All rights reserved.</span>
</footer>

<style>
  .app-footer {
    text-align: center;
    padding: 18px 20px;
    margin-top: 32px;
    font-size: 12px;
    color: #64748b;
    letter-spacing: 0.3px;
    border-top: 1px solid rgba(255,255,255,0.06);
  }
  .app-footer .sep {
    margin: 0 6px;
    color: #475569;
  }
</style>
