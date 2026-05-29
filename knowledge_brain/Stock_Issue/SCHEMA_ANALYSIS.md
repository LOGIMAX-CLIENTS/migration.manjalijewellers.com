# SCHEMA ANALYSIS — Stock Issue
> Verified Round 9 | 2026-03-19 | Tables and integrity risks confirmed accurate

---

## Part A: Owned Tables (Written by this module)

### `ret_stock_issue` — Issue Header
| Column | Type (est.) | Nullable | Purpose |
|---|---|---|---|
| `id_stock_issue` | INT PK AUTO | No | Primary key |
| `issue_no` | VARCHAR | No | Business key: `{fin_year}-{00001}` |
| `issue_date` | DATETIME | No | When issued (day-close aware) |
| `issue_type` | INT FK | No | → `ret_stock_issue_types.id_stock_issue_type` |
| `stock_type` | INT | No | 1=Tagged, 2=Non-tagged |
| `issued_to` | INT | No | 1=Customer, 2=Employee, 3=Karigar |
| `status` | INT | No | 0=Pending, 1=Issued, 2=Rejected |
| `id_branch` | INT FK | No | Issuing branch |
| `id_employee` | INT FK | Yes | Employee if issued to employee |
| `id_customer` | INT FK | Yes | Customer if issued to customer |
| `id_karigar` | INT FK | Yes | Karigar if issued to karigar |
| `issue_emp` | INT/VARCHAR | Yes | Employee who performed the issue (`sel_emp`) |
| `repair_type` | INT | Yes | Repair type (referenced in list query) |
| `remarks` | TEXT | Yes | Free text remarks |
| `form_secret` | VARCHAR | No | Duplicate submit prevention |
| `fin_year` | VARCHAR | No | Financial year code |
| `created_on` | DATETIME | No | Creation timestamp |
| `created_by` | INT FK | No | User who created |

**Key Indexes**: `issue_no`, `fin_year`, `status`, `id_branch`
**Risks**: No unique constraint on `issue_no` → concurrent duplicate issue numbers possible

---

### `ret_stock_issue_detail` — Issue Line Items
| Column | Type (est.) | Nullable | Purpose |
|---|---|---|---|
| `id_stock_issue_detail` | INT PK AUTO | No | Primary key |
| `id_stock_issue` | INT FK | No | → `ret_stock_issue` |
| `tag_id` | INT FK | Yes | For tagged items → `ret_taging` |
| `id_non_tag_item` | INT FK | Yes | For non-tagged items → `ret_nontag_item` |
| `piece` | DECIMAL | Yes | Piece count |
| `gross_wt` | DECIMAL | Yes | Gross weight |
| `net_wt` | DECIMAL | Yes | Net weight |
| `rate_per_gram` | DECIMAL | Yes | Rate at time of issue |
| `status` | INT | No | 1=Issued, 3=Received/Returned |
| `received_date` | DATETIME | Yes | When received back |
| `received_by` | INT FK | Yes | Who received |
| `received_time` | INT | Yes | UNIX timestamp — batch receipt key |
| `updated_by` | INT FK | Yes | Last updater |

**Key Indexes**: `id_stock_issue`, `tag_id`, `status`, `received_time`
**Risks**: No check constraint that `tag_id` or `id_non_tag_item` is set (one must be)

---

## Part B: Referenced Tables (Read/Written externally, touched by this module)

### `ret_taging` (Tagging Module — owned there)
| Column Used | Direction | Purpose in this module |
|---|---|---|
| `tag_id` | R | Lookup key |
| `tag_code` | R | Display in list, scan lookup |
| `tag_status` | R/W | 0=stock, 7=issued — changed by this module |
| `current_branch` | R | Filter tag scan by branch |
| `id_section` | R | Section log condition |
| `gross_wt`, `net_wt`, `piece` | R | Copied into issue_detail |
| `product_id`, `design_id`, etc. | R | For print data |

### `ret_nontag_item` (Other Inventory — owned there)
| Column Used | Direction | Purpose |
|---|---|---|
| `id_nontag_item` | R/W key | Identified non-tag record |
| `no_of_piece` | W | Arithmetic deduct/add |
| `gross_wt`, `net_wt` | W | Arithmetic deduct/add |
| `branch` | R | Filter by branch in scan |
| `product`, `design`, `id_sub_design`, `id_section` | R | Display info |

### `ret_stock_issue_types`
| Column Used | Direction | Purpose |
|---|---|---|
| `id_stock_issue_type` | R | Lookup key |
| `name` | R | Display |
| `is_remove_from_stock` | R | Controls log write |
| `status` | R | Filter active types |

### `ret_financial_year`
| Column Used | Direction | Purpose |
|---|---|---|
| `fin_year_code` | R | Issue number prefix |
| `fin_status` | R | Filter active year (=1) |

### `ret_taging_status_log`
| Column | W | Purpose |
|---|---|---|
| `tag_id`, `status`, `from_branch`, `to_branch`, `date`, `form_secret`, `created_on`, `created_by` | W | Audit trail per tag movement |

### `ret_section_tag_status_log`
| Column | W | Purpose |
|---|---|---|
| `tag_id`, `status`, `from_branch`, `to_branch`, `from_section`, `to_section`, `date`, etc. | W | Section-level tag movement audit |

### `ret_nontag_item_log`
| Column | W | Purpose |
|---|---|---|
| `product`, `design`, `id_sub_design`, `no_of_piece`, `gross_wt`, `net_wt`, `status`, `from_branch`, `to_branch`, `date` | W | Non-tag stock movement audit |

### `otp`
| Column | R/W | Purpose |
|---|---|---|
| `mobile`, `otp_code`, `otp_gen_time`, `module`, `send_resend`, `id_emp` | W | OTP record on send |
| `is_verified`, `verified_time` | W | Updated on verify |

### `profile`
| Column | R | Purpose |
|---|---|---|
| `id_profile` | R | Lookup key |
| `stock_issue_otp_req` | R | OTP requirement flag |

---

## DB Integrity Risks

| Risk | Table | Description |
|---|---|---|
| No tag uniqueness check | `ret_stock_issue_detail` | Same tag_id can appear in multiple open issues |
| No negative stock guard | `ret_nontag_item` | Arithmetic deduct without checking available > qty |
| Orphaned detail rows | `ret_stock_issue_detail` | No CASCADE DELETE on header |
| Concurrent issue_no | `ret_stock_issue` | MAX() race condition on concurrent inserts |
| Partial commit detection | `ret_taging_status_log` | Each tag is in its own trans_begin in receipt flow |
