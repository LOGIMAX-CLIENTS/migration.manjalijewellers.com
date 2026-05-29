# SCHEMA ANALYSIS — customer
> Round R3-Upgrade — 2026-03-25

---

## Part A: Owned Tables (customer module performs CRUD)

### Table: `customer`
| Column | Type | Notes |
|---|---|---|
| `id_customer` | INT PK AUTO | Primary key |
| `firstname` | VARCHAR | Required |
| `lastname` | VARCHAR | Nullable |
| `mobile` | VARCHAR | Unique per company; used as identity key for sync |
| `phone` | VARCHAR | Landline |
| `email` | VARCHAR | Optional, unique check exists |
| `username` | VARCHAR | Login username, unique check via `username_available()` |
| `passwd` | VARCHAR | ⚠️ Stored as `base64_encode()` — NOT encrypted (CUS-BUG-009) |
| `gender` | INT | 0=Male, 1=Female, 3=Other |
| `date_of_birth` | DATE | Used in age calc, celeb reports |
| `date_of_wed` | DATE | Wedding anniversary |
| `pan` | VARCHAR | PAN number |
| `aadharid` | VARCHAR | Aadhar number |
| `driving_license_no` | VARCHAR | DL number |
| `passport_no` | VARCHAR | Passport number |
| `voterid` | VARCHAR | Voter ID |
| `rationcard` | VARCHAR | Ration card |
| `gst_number` | VARCHAR | GST number |
| `religion` | VARCHAR | Religion |
| `cus_type` | INT | 1=Individual (default), other types possible |
| `is_new` | VARCHAR | Y/N — New or existing customer |
| `active` | INT | 0=Inactive, 1=Active |
| `profile_complete` | INT | 0=Incomplete, 1=Complete |
| `added_by` | INT | 0=WebApp, 1=Admin, 2=MobileApp, 3=CollectionApp, 4=RetailApp, 5=Sync, 6=Import. ⚠️ Always set to 1 in admin add (CUS-BUG-008) |
| `id_branch` | INT FK | → `branch.id_branch` |
| `id_village` | INT FK | → `village.id_village` |
| `id_agent` | INT FK | → `agent.id_agent` (allocated agent) |
| `allocated_employee` | INT FK | → `employee.id_employee` |
| `id_employee` | INT | Employee who created customer |
| `id_address` | INT FK | → `address.id_address` |
| `id_company` | INT FK | → `company.id_company` (multi-tenant) |
| `id_profession` | INT FK | → `profession.id_profession` |
| `cus_img` | VARCHAR | Path to customer image |
| `pan_ImgName` | VARCHAR | PAN image filename |
| `aadhar_ImgName` | VARCHAR | Aadhar image filename |
| `dl_ImgName` | VARCHAR | DL image filename |
| `pp_ImgName` | VARCHAR | Passport image filename |
| `nominee_name` | VARCHAR | Nominee details |
| `nominee_relationship` | VARCHAR | |
| `nominee_mobile` | VARCHAR | |
| `nominee_pan` | VARCHAR | |
| `nominee_address1` | VARCHAR | |
| `nominee_address2` | VARCHAR | |
| `comments` | TEXT | Admin notes |
| `is_cus_synced` | INT | 0=Not synced, 1=Synced |
| `is_vip` | INT | 0=Normal, 1=VIP |
| `fin_year_code` | VARCHAR | Financial year code at registration |
| `opening_balance_amount` | DECIMAL | Pre-loaded balance from migration |
| `marital_status` | INT | 0=default |
| `languages_known` | VARCHAR | |
| `title` | VARCHAR | Mr/Mrs/Ms |
| `send_promo_sms` | INT | SMS marketing consent |
| `custom_entry_date` | DATE | Admin-set registration date |
| `kyc_status` | INT | 0=Pending, 1=Verified |
| `date_add` | DATETIME | Creation timestamp |
| `date_upd` | DATETIME | Last update timestamp |

### Table: `address`
| Column | Type | Notes |
|---|---|---|
| `id_address` | INT PK AUTO | |
| `id_customer` | INT FK | → `customer.id_customer` |
| `address1` | VARCHAR | Line 1 |
| `address2` | VARCHAR | Line 2 |
| `address3` | VARCHAR | Line 3 |
| `pincode` | VARCHAR | |
| `id_country` | INT FK | → `country.id_country` |
| `id_state` | INT FK | → `state.id_state` |
| `id_city` | INT FK | → `city.id_city` |
| `company_name` | VARCHAR | Company/business name |
| `post_office` | VARCHAR | Via village join |
| `taluk` | VARCHAR | Via village join |

### Table: `kyc`
| Column | Type | Notes |
|---|---|---|
| `id_kyc` | INT PK AUTO | |
| `id_customer` | INT FK | → `customer.id_customer` |
| `kyc_type` | INT | 1=Bank/Passbook/Cheque, 2=PAN, 3=Aadhar |
| `number` | VARCHAR | Document number (account no, PAN no, etc.) |
| `img_url` | VARCHAR | Front image path |
| `back_img_url` | VARCHAR | Back image path |
| `document_url` | VARCHAR | Document file path |
| `status` | INT | 0=Pending, 1=Verified |
| `verification_type` | VARCHAR | Type of verification |
| `bank_name` | VARCHAR | For kyc_type=1 |
| `ifsc_code` | VARCHAR | For kyc_type=1 |
| `branch_name` | VARCHAR | For kyc_type=1 |
| `acc_type` | VARCHAR | For kyc_type=1 |
| `emp_verified_by` | INT | Employee who verified |
| `last_update` | DATETIME | |

### Table: `zone`
| Column | Type | Notes |
|---|---|---|
| `id_zone` | INT PK AUTO | |
| `zone_name` | VARCHAR | |
| `date_add` | DATE | ⚠️ Uses `date('y-m-d')` = 2-digit year (CUS-BUG-026) |
| `date_upd` | DATE | Same bug (CUS-BUG-027) |
| `active` | INT | 0/1 |

---

## Part B: Referenced Tables (other modules, read by customer)

| Table | Module | Used In | Key Columns Read |
|---|---|---|---|
| `branch` | Settings | Branch filter, display | `id_branch`, `name`, `short_name`, `show_to_all` |
| `agent` | Agent | Agent dropdown, allocation | `id_agent`, `firstname`, `lastname`, `agent_code`, `active` |
| `employee` | HR | Employee dropdown, allocation | `id_employee`, `firstname`, `emp_code` |
| `village` | Settings | Village lookup, zone assignment | `id_village`, `village_name`, `post_office`, `taluk` |
| `country` | Settings | Address dropdown | `id_country`, `name` |
| `state` | Settings | Address dropdown | `id_state`, `name` |
| `city` | Settings | Address dropdown | `id_city`, `name` |
| `scheme_account` | Scheme | Delete guard, sync, dropdown | `id_scheme_account`, `id_customer`, `active`, `is_closed` |
| `payment` | Payment | Delete guard (indirect), sync write | `id_payment`, `payment_status`, `id_scheme_account` |
| `ret_billing` | Billing | Delete guard | `bill_id`, `bill_cus_id`, `bill_status` |
| `customerorder` | Orders | Delete guard | `id_customerorder`, `order_to`, `order_status` |
| `ret_estimation` | Estimation | Delete guard | `estimation_id`, `cus_id` |
| `gift_card` | Gift | Delete guard | `id_gift_card`, `purchased_by` |
| `wallet_account` | Wallet | Create on add, delete cleanup | `id_wallet_account`, `id_customer`, `wallet_acc_number` |
| `customer_reg` | ERP Sync | Sync staging reads/updates | `clientid`, `mobile`, `is_registered_online`, `is_transferred` |
| `transaction` | ERP Sync | Payment sync staging | `id_transaction`, `client_id`, `is_transferred`, `is_modified` |
| `chit_settings` | Settings | Config flags | `wallet_account_type`, `allow_join_multiple`, `branchWiseLogin`, etc. |
| `ret_day_closing` | Day Close | Custom entry date | `entry_date`, `id_branch` |
| `ret_financial_year` | Finance | Financial year code | `fin_year_code`, `fin_status` |
| `scheme` | Scheme | Sync scheme lookup | `id_scheme`, `sync_scheme_code` |
| `scheme_branch` | Scheme | Branch-wise scheme filter | `id_scheme`, `id_branch` |

---

## Part C: Index Analysis (Suspected Missing)

| Table | Column(s) in WHERE | Query Pattern | Index Needed? | Why Critical |
|---|---|---|---|---|
| `customer` | `mobile` | Uniqueness check, sync match | ✅ Unique index on `(mobile, id_company)` | Most frequent lookup — every add/edit checks this |
| `customer` | `id_village` | Village filter in list | ✅ FK index | Village-wise customer list filter |
| `customer` | `id_branch` | Branch filter in all list queries | ✅ FK index | Branch-wise login enforcement |
| `customer` | `active` | Active/inactive filter | ⚠️ Low cardinality — index may not help | But used in every list query |
| `address` | `id_customer` | 1:1 join from customer | ✅ FK index expected | Every customer query JOINs address |
| `kyc` | `id_customer`, `kyc_type` | KYC lookup per type | ✅ Compound `(id_customer, kyc_type)` | Multiple KYC types per customer |
| `customer_reg` | `mobile`, `is_registered_online` | Sync matching | ✅ Compound index | ERP sync performance |
| `transaction` | `client_id`, `is_transferred` | Payment sync matching | ✅ Compound index | ERP sync performance |

---

## Part D: Write Operation Inventory

| Method | Table(s) Written | Columns Changed | Guards Present | Risk |
|---|---|---|---|---|
| `insert_customer()` | `customer` (INSERT), `address` (INSERT) | All info + address fields | ✅ trans_begin/commit | — |
| `update_customer()` | `customer` (UPDATE), `address` (UPDATE or INSERT) | All info + address fields | ✅ trans_begin/commit | — |
| `delete_customer()` | `address` (DELETE), `customer` (DELETE), `wallet_account` (DELETE) | Entire rows | PARTIAL — dependency check separate | 🔴 kyc NOT cleaned (CUS-BUG-006) |
| `allocate_agent()` | `customer` (UPDATE) | `id_agent` | ❌ `$total` overwrite bug | 🔴 Always silently fails (CUS-BUG-001) |
| `allocate_employee()` | `customer` (UPDATE) | `allocated_employee` | ❌ Same `$total` bug | 🔴 Always fails (CUS-BUG-018) |
| `insert_kyc()` | `kyc` (INSERT) | All KYC fields | ✅ kyc_exists() dedup | — |
| `update_kyc()` / `updkycData()` | `kyc` (UPDATE) | Image URLs, numbers | PARTIAL — dedup on number | — |
| `update_images()` | `customer` (UPDATE) | Image path columns | ❌ No call from `set_image()` | 🔴 Images lost (CUS-BUG-019) |
| `insExisAcByMobile()` | `scheme_account` (INSERT) | Full account from staging | ✅ existing account check | — |
| `syncPayData()` | `payment` (INSERT) | Full payment from staging | ❌ No transaction wrap | 🟡 Partial sync possible |
| `updateInterTableStatus()` | `customer_reg`, `transaction` (UPDATE) | Sync flags | ❌ No transaction wrap | 🟡 Partial status update |
| KYC image storage | Filesystem | Image files | ❌ mkdir 0777 | 🟡 World-writable dirs (CUS-BUG-011) |

---

## Risk Flags

| Table | Risk | Details |
|---|---|---|
| `customer` | HIGH | Central identity table — all modules depend on it |
| `customer.passwd` | HIGH | base64 only — trivially reversible (CUS-BUG-009) |
| `kyc` | MEDIUM | Orphaned on customer delete (CUS-BUG-006) |
| `customer_reg` / `transaction` | MEDIUM | ERP sync without transaction wrapping — partial sync possible |
| `zone.date_add` | LOW | 2-digit year format (CUS-BUG-026/027) |
