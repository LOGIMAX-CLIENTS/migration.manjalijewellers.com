# SCHEMA ANALYSIS — Section Transfer
> Built: 2026-03-14 | Round 1

---

## Part A: Tables Owned/Written By This Module

### ret_section_tag_status_log
**Purpose**: Audit log for every tagged item section transfer

| Column | Type (inferred) | Notes |
|---|---|---|
| `tag_id` | INT | FK → ret_taging.tag_id |
| `from_branch` | INT / NULL | NULL for section-only transfer |
| `to_branch` | INT | Branch of destination |
| `from_section` | INT / NULL | Source section (NULL if tag had none) |
| `to_section` | INT | Destination section |
| `created_by` | INT | session uid |
| `created_on` | DATETIME | wall-clock insert time |
| `status` | INT | 0 = section transfer |
| `date` | DATETIME | day-closing adjusted date |

**Risk**: `from_branch` is always NULL for ST (BT controller sets it); `from_section` is NULL when original tag had no section.

---

### ret_home_section_item_log
**Purpose**: Log for tagged items entering/leaving home-counter sections

| Column | Type (inferred) | Notes |
|---|---|---|
| `id_product` | INT | product_id from ret_taging |
| `no_of_piece` | INT | quantity |
| `gross_wt` | DECIMAL | gross weight |
| `net_wt` | DECIMAL | net weight |
| `tag_id` | INT | FK → ret_taging.tag_id |
| `status` | INT | 0 = transfer in |
| `from_branch` | INT / NULL | NULL |
| `to_branch` | INT | destination branch |
| `from_section` | INT / NULL | NULL (hardcoded — see risk) |
| `to_section` | INT | destination section |
| `created_by` | INT | session uid |
| `created_on` | DATETIME | wall-clock |
| `date` | DATETIME | day-closing adjusted |

**Risk**: `from_section` is hardcoded NULL at Controller L269 — origin section not recorded in log.

---

### ret_taging_status_log
**Purpose**: Tag status history (shared with other modules)

| Column | Type (inferred) | Notes |
|---|---|---|
| `tag_id` | INT | FK → ret_taging |
| `status` | INT | 16 = home counter transfer |
| `from_branch` | INT | source branch |
| `to_branch` | INT / NULL | NULL |
| `created_by` | INT | session uid |
| `created_on` | DATETIME | wall-clock |
| `date` | DATETIME | day-closing adjusted |

---

### ret_section_nontag_item_log
**Purpose**: Log for non-tagged item section transfers

| Column | Type (inferred) | Notes |
|---|---|---|
| `product` | INT | product FK |
| `design` | INT | design FK |
| `id_sub_design` | INT | sub-design FK |
| `no_of_piece` | INT | quantity transferred |
| `gross_wt` | DECIMAL | gross weight |
| `net_wt` | DECIMAL | net weight |
| `status` | INT | 4=deduct from source, 0=add to destination |
| `from_branch` | INT / NULL | source branch (or NULL for destination log) |
| `to_branch` | INT / NULL | destination branch (or NULL for source log) |
| `from_section` | INT / NULL | source section |
| `to_section` | INT / NULL | destination section (NULL in deduct log — **BUG**) |
| `created_by` | INT | session uid |
| `created_on` | DATETIME | wall-clock |
| `date` | DATETIME | day-closing adjusted |

**Risk**: In deduct log (status=4), `to_section` is hardcoded NULL at Controller L379, should be `$transfer_to_section`.

---

## Part B: Tables Referenced (Read) By This Module

### ret_taging
**Written by**: this module (id_section update, tag_status update)

| Key Column | Notes |
|---|---|
| `tag_id` | PK |
| `tag_code` | Display/search field |
| `old_tag_id` | Legacy tag search |
| `id_section` | Current section — UPDATED by this module |
| `current_branch` | Current branch |
| `product_id` | FK → ret_product_master |
| `tag_status` | 0=available, 14=home counter, others used by other modules |
| `piece` | Quantity |
| `gross_wt` | Gross weight |
| `net_wt` | Net weight |
| `id_orderdetails` | FK → customerorderdetails (NULL = not reserved) |

---

### ret_section
| Key Column | Notes |
|---|---|
| `id_section` | PK |
| `section_name` | Display |
| `is_home_bill_counter` | 0/1 — triggers special home stock update path |

---

### ret_nontag_item
**Written by**: this module (arith increment/decrement)

| Key Column | Notes |
|---|---|
| `id_nontag_item` | PK |
| `branch` | Branch FK |
| `product` | Product FK |
| `design` | Design FK |
| `id_sub_design` | Sub-design FK |
| `id_section` | Section FK |
| `no_of_piece` | Aggregated count |
| `gross_wt` | Aggregated weight |
| `net_wt` | Aggregated net weight |

---

### ret_home_section_item
**Written by**: this module (updatesecNTData '-' decrement)

| Key Column | Notes |
|---|---|
| `id_hometag_item` | PK |
| `id_branch` | Branch FK |
| `id_section` | Section FK |
| `id_product` | Product FK |
| `no_of_piece` | Aggregated |
| `gross_wt` | Aggregated |
| `net_wt` | Aggregated |

---

### ret_day_closing
| Key Column | Notes |
|---|---|
| `id_branch` | PK |
| `is_day_closed` | Day close flag |
| `entry_date` | Effective business date |

---

### otp
**Written by**: this module

| Key Column | Notes |
|---|---|
| `mobile` | Phone number |
| `otp_code` | 4-digit OTP |
| `otp_gen_time` | Generation timestamp |
| `module` | 'Counter Change OTP' |
| `id_emp` | user ID |
| `is_verified` | 0/1 |
| `verified_time` | Verify timestamp |

---

## Risk Summary

| Table | Risk |
|---|---|
| `ret_taging` | SQL injection in all raw queries; tag_status race condition possible |
| `ret_nontag_item` | Arithmetic SQL injection; no server-side qty cap |
| `ret_home_section_item` | Only DECREMENTED (never INCREMENTED) — net negative stock risk |
| `ret_section_nontag_item_log` | `to_section` NULL in deduct log — incomplete audit trail |
| `otp` | OTP returned in API response — security concern |
