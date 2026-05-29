<script>
    import DataMigration from "$lib/DataMigration.svelte";
    import { onMount } from "svelte";
    import { API_URL } from "$lib/config";
    import { createEventDispatcher } from "svelte";
    const dispatch = createEventDispatcher();
    // export let client;

    let username = "";
    let password = "";
    let isLoggedIn = false;
    let error = "";
    let client = "";
    let showPassword = false;

    // Restore login state on refresh
    onMount(() => {
        const savedLogin = sessionStorage.getItem("isLoggedIn");
        const savedClient = sessionStorage.getItem("client");

        if (savedLogin === "true") isLoggedIn = true;
        if (savedClient) client = savedClient;
    });

    async function login() {
        error = "";
        try {
           /*  if (!client) {
                error = "Please connect database first";
                return;
            } */
            if (!username || !password) {
                error = "Enter username & password";
                return;
            }

            const res = await fetch(API_URL + "AuthController/login", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                credentials: "include",
                body: JSON.stringify({
                    username: username,
                    password: password,
                }),
            });

            if (!res.ok) throw new Error(await res.text());
            const data = await res.json();

            if (data.success) {
                client = data.client;
                isLoggedIn = true;
                sessionStorage.setItem("isLoggedIn", "true");
                sessionStorage.setItem("client", data.client);
                sessionStorage.setItem("dbName", data.database || '');
                // dispatch("success");
                dispatch("success", {client: data.client, dbName: data.database || ''});
            } else {
                error = data.message;
            }
        } catch (err) {
            error = "Login failed";
        }
    }

    function disconnect() {
        isLoggedIn = false;
        username = "";
        password = "";
        sessionStorage.clear(); // clears login + client info
    }
</script>

    <div class="auth-container">
        <h2>Login</h2>

        {#if error}
            <div class="error">{error}</div>
        {/if}

        <div class="field">
            <label>Username</label>
            <input type="text" bind:value={username} />
        </div>

        <div class="field">
            <label>Password</label>
            <div class="password-wrap">
                <input type={showPassword ? 'text' : 'password'} bind:value={password} />
                <button class="eye-btn" type="button" on:click={() => showPassword = !showPassword}>
                    {#if showPassword}
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/>
                            <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/>
                            <line x1="1" y1="1" x2="23" y2="23"/>
                        </svg>
                    {:else}
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    {/if}
                </button>
            </div>
        </div>

        <button class="btn-primary" on:click={login}>Login</button>
    </div>

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
    .readonly-field {
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
    .readonly-field:focus {
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
    .password-wrap {
        position: relative;
        display: flex;
        align-items: center;
    }
    .password-wrap input {
        padding-right: 40px;
    }
    .eye-btn {
        position: absolute;
        right: 24px;
        background: none;
        border: none;
        cursor: pointer;
        font-size: 16px;
        padding: 4px;
        line-height: 1;
        opacity: 0.7;
    }
    .eye-btn:hover {
        opacity: 1;
    }
</style>
