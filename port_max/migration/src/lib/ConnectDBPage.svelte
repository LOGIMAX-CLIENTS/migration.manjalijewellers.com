<script>

  import { onMount } from "svelte";
  import { API_URL } from "$lib/config";
  import AuthPage from "$lib/AuthPage.svelte";
  import DataMigration from "$lib/DataMigration.svelte";

  let step = "db"; // db | login | migrate
  let dbError = "";
  let loginError = "";
  let dbHost = "";
  let dbName = "";
  let dbUser = "";
  let dbPass = "";
  let state = "idle";
  let client = "";

  // Restore step + client on page load
  onMount(() => {
    const savedStep = sessionStorage.getItem("step");
    const savedClient = sessionStorage.getItem("client");

    if (savedStep) step = savedStep;
    if (savedClient) client = savedClient;
  });

  function saveState() {
    sessionStorage.setItem("step", step);
    sessionStorage.setItem("client", client);
  }

  async function connectDB() {
    state = "connecting";
    dbError = "";

    try {
      const res = await fetch(API_URL + "AuthController/connDB", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        credentials: "include",
        body: JSON.stringify({
          host: dbHost,
          database: dbName,
          username: dbUser,
          password: dbPass
        })
      });

      if (!res.ok) throw new Error(await res.text());
      const data = await res.json();

      if (data.success) {
        client = data.client;
        step = "login";
        saveState(); // ✅ persist after DB connect
        state = "connected";
      } else {
        state = "error";
        dbError = data.message;
      }

    } catch (err) {
      state = "error";
      dbError = "Connection failed";
    }
  }

  function onLoginSuccess() {
    step = "migrate";
    saveState(); // ✅ persist after login
  }

  function onDisconnect() {
    step = "db";
    client = "";
    sessionStorage.clear();
  }
</script>

{#if step === "db"}
    <div class="auth-container">
        <h2>Database Connection</h2>

        {#if dbError}
            <div class="error">{dbError}</div>
        {/if}

        <div class="field">
            <label>Host</label>
            <input bind:value={dbHost} placeholder="localhost" />
        </div>

        <div class="field">
            <label>Database Name</label>
            <input bind:value={dbName} />
        </div>

        <div class="field">
            <label>Username</label>
            <input bind:value={dbUser} />
        </div>

        <div class="field">
            <label>Password</label>
            <input type="password" bind:value={dbPass} />
        </div>

        <button class="btn-primary" on:click={connectDB}>
            Connect Database
        </button>
    </div>
{:else if step === "login"}
  <AuthPage client={client} on:success={onLoginSuccess} on:disconnect={onDisconnect} />

{:else if step === "migrate"}
  <DataMigration client={client} on:disconnect={onDisconnect} />
{/if}


<style>
    .auth-container {
    max-width: 380px;
    margin: 80px auto;
    padding: 28px;
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.06);
    border-radius: 12px;
    box-shadow: 0 8px 24px rgba(2, 6, 23, 0.6);
}

.auth-container h2 {
    margin-bottom: 14px;
    color: #f1f5f9;
    text-align: center;
}

.field {
    margin-bottom: 18px;
}

.field label {
    display: block;
    margin-bottom: 6px;
    font-size: 14px;
    color: #cbd5e1;
}

input {
        width: 93%;
        padding: 12px 14px;
        border-radius: 10px;
        border: 1px solid rgba(255, 255, 255, 0.08);
        background: rgba(255, 255, 255, 0.05);
        color: #e6eef8;
        font-size: 15px;
        outline: none;
    }

select {
    width: 100%;
    padding: 12px 14px;
    border-radius: 10px;
    border: 1px solid rgba(255, 255, 255, 0.08);
    background: rgba(255, 255, 255, 0.05);
    color: #e6eef8;
    font-size: 15px;
    outline: none;
}

input:focus,
select:focus {
    border-color: #60a5fa;
    box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.25);
}

.error {
    background: rgba(255, 40, 70, 0.08);
    border: 1px solid rgba(255, 40, 70, 0.2);
    padding: 10px;
    color: #ffb3b3;
    margin-bottom: 16px;
    border-radius: 8px;
}

.btn-primary {
    width: 100%;
    background: linear-gradient(90deg, #2563eb, #4f46e5);
    color: white;
    padding: 12px 18px;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
}

</style>