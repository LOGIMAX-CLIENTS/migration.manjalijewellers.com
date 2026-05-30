---
description: Onboard a new mono-repo client — server setup, GitHub config, and deployment pipeline in ~1 hour
version: 1.1
last_updated: 2026-03-07
---

# Onboard New Mono-Repo Client

> **When to use**: A new client needs their own ERP instance deployed from the shared mono-repo.
> **Time**: ~1 hour (down from ~1 day before automation)
> **Prerequisites**: Server access (SSH), GitHub repo access, RDS credentials, source DB dump

---

## Input Required

| Variable | Example | Description |
|----------|---------|-------------|
| `{CLIENT_ID}` | `ashoka` | Lowercase, no spaces |
| `{CLIENT_NAME}` | `Ashoka Jewellers` | Display name |
| `{DOMAIN}` | `erp.ashokajewellery.in` | Production domain |
| `{STAGING_DOMAIN}` | `staging.ashokajewellery.in` | Staging domain |
| `{SERVER_IP}` | `10.0.1.50` | Server IP or hostname |
| `{SSH_KEY_PATH}` | `~/.ssh/ashoka.pem` | Local SSH key to server |
| `{RDS_ENDPOINT}` | `ashoka-rds.xxxxx.rds.amazonaws.com` | RDS host |
| `{DB_NAME_PROD}` | `ashoka_prod` | Production database |
| `{DB_NAME_STAGING}` | `ashoka_staging` | Staging database |
| `{DB_USER}` | `admin` | Database username |
| `{DB_PASS}` | `*****` | Database password |
| `{SOURCE_DUMP}` | `retail_dev.sql` | Source SQL dump for migration |

---

## Phase 1 — Local Setup (5 min)

### Step 1: Create client config JSON

Create `config/clients/{CLIENT_ID}.json`:
```json
{
    "client_name": "{CLIENT_NAME}",
    "features": [],
    "flags": {},
    "deploy": {
        "ssl_verify": false,
        "staging": {
            "webhook_url": "https://{DOMAIN}/webhooks/deploy.php",
            "branch": "support"
        },
        "production": {
            "webhook_url": "https://{DOMAIN}/webhooks/deploy.php",
            "branch": "Production"
        }
    }
}
```

> Set `ssl_verify: false` until ACM cert is ready, then flip to `true`.

### Step 2: Create client override directory

// turbo
```bash
mkdir -p clients/{CLIENT_ID}/assets
touch clients/{CLIENT_ID}/assets/.gitkeep
```

### Step 3: Commit and push

```bash
git add -f config/clients/{CLIENT_ID}.json clients/{CLIENT_ID}/
git commit -m "Add mono-repo client: {CLIENT_ID}"
git push origin support
```

> The GitHub Actions workflow auto-detects the new client on next push. No workflow changes needed.

---

## Phase 2 — SSH Key + GitHub Deploy Key (5 min)

### Step 4: Generate SSH deploy key on server

```bash
ssh -i {SSH_KEY_PATH} ubuntu@{SERVER_IP}

# Generate key (skip if /home/ubuntu/.ssh/id_ed25519 already exists from another client)
ssh-keygen -t ed25519 -C "{CLIENT_ID}-server" -f /home/ubuntu/.ssh/id_ed25519 -N ""

# Display public key
cat /home/ubuntu/.ssh/id_ed25519.pub
```

### Step 5: Add deploy key to GitHub

1. Go to `https://github.com/Logimax-Technologies/etail_development_src/settings/keys`
2. Click **Add deploy key**
3. Title: `{CLIENT_ID}-server`
4. Paste the public key from Step 4
5. Check **Allow write access** (needed for status updates)

### Step 6: Verify

```bash
ssh -i /home/ubuntu/.ssh/id_ed25519 -T git@github.com
# Should say: Hi Logimax-Technologies/etail_development_src!
```

> **If multiple clients share the same server**, this is done ONCE. The same key works for all clients.

---

## Phase 3 — Database Migration (15 min)

### Step 7: Create databases on RDS

```bash
# Connect to RDS from a jump host or the client server
mysql -h {RDS_ENDPOINT} -u {DB_USER} -p

CREATE DATABASE {DB_NAME_PROD} CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
CREATE DATABASE {DB_NAME_STAGING} CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
```

### Step 8: Import source database

```bash
# Export from source (if migrating from another instance)
mysqldump -h {SOURCE_RDS} -u {SOURCE_USER} -p \
  --set-gtid-purged=OFF --single-transaction --routines --triggers \
  {SOURCE_DB} > /tmp/{CLIENT_ID}_dump.sql

# Strip DEFINER clauses (RDS doesn't allow SUPER privilege)
sed -i 's/DEFINER=`[^`]*`@`[^`]*`//g' /tmp/{CLIENT_ID}_dump.sql

# Import into production
mysql -h {RDS_ENDPOINT} -u {DB_USER} -p {DB_NAME_PROD} < /tmp/{CLIENT_ID}_dump.sql

# Import into staging
mysql -h {RDS_ENDPOINT} -u {DB_USER} -p {DB_NAME_STAGING} < /tmp/{CLIENT_ID}_dump.sql
```

> **If migrating from Excel** (not another DB instance), use the application's import tools instead.

---

## Phase 4 — Server Setup (20 min)

### Step 9: Upload and run setup script

```bash
# From local
scp -i {SSH_KEY_PATH} \
  cicd/server/setup-mono-client.sh \
  cicd/server/deploy-mono.sh \
  ubuntu@{SERVER_IP}:/tmp/

# On server
ssh -i {SSH_KEY_PATH} ubuntu@{SERVER_IP}

sudo chmod +x /tmp/setup-mono-client.sh /tmp/deploy-mono.sh
sudo -E bash /tmp/setup-mono-client.sh {CLIENT_ID} \
  git@github.com:Logimax-Technologies/etail_development_src.git \
  --domain {DOMAIN} \
  --staging-domain {STAGING_DOMAIN}
```

### Step 10: Configure database credentials

```bash
sudo nano /var/www/{CLIENT_ID}/prod/shared/config/global_configs.php
```

Set `$hostname`, `$username`, `$password`, `$database` to RDS values.

For staging (if separate DB):
```bash
sudo cp /var/www/{CLIENT_ID}/prod/shared/config/global_configs.php \
       /var/www/{CLIENT_ID}/staging/global_configs.php
sudo nano /var/www/{CLIENT_ID}/staging/global_configs.php
# Change $database to {DB_NAME_STAGING}
```

### Step 11: Activate Apache vhost

```bash
sudo cp /tmp/{CLIENT_ID}-vhost.conf /etc/apache2/sites-available/{CLIENT_ID}.conf
sudo a2ensite {CLIENT_ID}.conf
sudo apache2ctl configtest
sudo systemctl reload apache2
```

### Step 12: Upload assets

```bash
# From another client on the same server
sudo cp -rp /var/www/existing_client/prod/shared/data/assets_img/* \
            /var/www/{CLIENT_ID}/prod/shared/data/assets_img/
sudo cp -rp /var/www/existing_client/prod/shared/data/assets_dist/* \
            /var/www/{CLIENT_ID}/prod/shared/data/assets_dist/
sudo cp -rp /var/www/existing_client/prod/shared/data/assets_plugins/* \
            /var/www/{CLIENT_ID}/prod/shared/data/assets_plugins/
```

### Step 13: Set webhook secret

```bash
# If DEVOPS_WEBHOOK_SECRET is not already set on this server
echo "export DEVOPS_WEBHOOK_SECRET='<same_value_as_MONO_WEBHOOK_SECRET>'" | sudo tee -a /etc/environment
```

> Same value as `MONO_WEBHOOK_SECRET` GitHub secret. Set ONCE per server, reused across all clients.

---

## Phase 5 — DNS + SSL (Senior)

### Step 14: Request from senior

| Task | Details |
|------|---------|
| DNS A/CNAME | Point `{DOMAIN}` and `{STAGING_DOMAIN}` to server |
| ACM cert | Add both domains to the ALB certificate |

> Once ACM cert is configured, change `ssl_verify` to `true` in `config/clients/{CLIENT_ID}.json`, commit, push.

---

## Phase 6 — DevOps Tool Registration (5 min)

### Step 15: Register client

```bash
TOKEN=$(curl -s -X POST https://devops.logimaxindia.com/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"username": "<user>", "password": "<pass>"}' | jq -r '.data.token')

curl -X POST https://devops.logimaxindia.com/api/clients \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -d '{
    "clientId": "{CLIENT_ID}",
    "clientName": "{CLIENT_NAME}",
    "repoName": "etail_development_src",
    "repoOrg": "Logimax-Technologies",
    "stagingBranch": "support",
    "stagingDeployMode": "git_pull",
    "prodBranch": "Production",
    "prodDeployMode": "symlink",
    "baseUrl": "https://{DOMAIN}",
    "serverFolder": "{CLIENT_ID}",
    "webhookPath": "/webhooks/deploy.php"
  }'
```

---

## Phase 7 — Verification (10 min)

### Step 16: Verify access

```bash
curl -Ik https://{DOMAIN}/admin/
curl -Ik https://{STAGING_DOMAIN}/admin/
```

### Step 17: Verify maintenance page

```bash
sudo touch /var/www/{CLIENT_ID}/prod/.maintenance_active
curl -s https://{DOMAIN}/ | head -5
sudo rm /var/www/{CLIENT_ID}/prod/.maintenance_active
```

### Step 18: Test webhook deploy

```bash
curl -X POST http://localhost/webhooks/deploy.php \
  -H "Host: {DOMAIN}" \
  -H "Content-Type: application/json" \
  -d '{"environment": "staging"}'

tail -5 /var/www/{CLIENT_ID}/prod/shared/logs/webhook.log
```

### Step 19: Test GitHub Actions trigger

Push a small change to `support` and verify the workflow:
1. Finds the new client
2. Triggers the webhook
3. Staging deploys successfully

---

## Completion Checklist

- [ ] `config/clients/{CLIENT_ID}.json` committed
- [ ] `clients/{CLIENT_ID}/` directory committed
- [ ] SSH deploy key configured on server + GitHub
- [ ] Databases created and SQL imported on RDS
- [ ] Server directories created via `setup-mono-client.sh`
- [ ] `global_configs.php` configured (prod + staging)
- [ ] Apache vhost activated
- [ ] Assets uploaded (img, dist, plugins)
- [ ] `DEVOPS_WEBHOOK_SECRET` set on server
- [ ] DNS records configured (senior)
- [ ] ACM certificate updated (senior)
- [ ] `ssl_verify` flipped to `true` after ACM
- [ ] Client registered in DevOps Tool
- [ ] Staging accessible
- [ ] Production accessible
- [ ] Maintenance page works
- [ ] Webhook deploy tested
- [ ] GitHub Actions end-to-end tested
